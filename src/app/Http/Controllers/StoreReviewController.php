<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreReview;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Reservation;
use App\Http\Requests\StoreReviewRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class StoreReviewController extends Controller
{
    public function index($storeId)
    {
        // 店舗の口コミを取得
         $reviews = StoreReview::where('store_id', $storeId)
                               ->where('user_id', Auth::id()) 
                               ->get();

        // 店舗情報を取得
        $store = Store::find($storeId);

        // 口コミ一覧をビューに渡す
        return view('store_reviews.index', compact('reviews', 'store'));
    }

    // PC口コミ画面表示
    public function review_index($id)
    {
        $store = Store::find($id);
        $reviews = StoreReview::where('store_id', $id)->get(); // 口コミを取得

        return view('store_review', compact('store', 'reviews')); // 'reviews' をビューに渡す
    }

    // 口コミを保存するメソッド
    public function store(StoreReviewRequest $request)
    {
        // 現在のログインユーザーのIDを取得
        $userId = Auth::id();
        $storeId = $request->store_id;
        $now = Carbon::now();

        // 既に同じユーザーが同じ店舗に口コミを投稿しているかチェック
        $exists = StoreReview::where('user_id', $userId)->where('store_id', $storeId)->exists();

       if ($exists) {
            return redirect()->back()->withErrors(['review' => 'この店舗にはすでに口コミを投稿しています。']);
        }


        // 予約情報を取得（最新の予約を取得）
        $reservation = Reservation::where('user_id', $userId)
                              ->where('store_id', $storeId)
                              ->whereNotNull('date')
                              ->whereNotNull('time')
                              ->orderBy('date', 'desc')
                              ->orderBy('time', 'desc')
                              ->first();
        if (!$reservation) {
            return redirect()->back()->withErrors(['review' => '店舗来店予定日時終了後から口コミ機能の投稿が可能です。']);
        }

        // **フォーマットを統一**
        $reservationDate = Carbon::parse($reservation->date)->format('Y-m-d');
        $reservationTime = Carbon::parse($reservation->time)->format('H:i:s');

        // **デバッグログで値を確認**
         Log::info('予約情報:', ['date' => $reservationDate, 'time' => $reservationTime]);

        try {
            // 予約日時を作成
            $reservationDateTime = Carbon::parse("$reservationDate $reservationTime");

            // 現在の時刻と比較
            if ($now->lt($reservationDateTime)) {
                return redirect()->back()->withErrors(['review' => '店舗来店予定日時終了後から口コミ機能の投稿が可能です。']);
            }
        } catch (\Exception $e) {
            Log::error('予約日時の解析に失敗:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['review' => '予約日時のフォーマットに問題があります。']);
        }

        // 予約情報を取得
        $reservation = Reservation::where('user_id', $userId)
                              ->where('store_id', $storeId)
                              ->whereNotNull('date')
                              ->whereNotNull('time')
                              ->orderBy('date', 'desc')
                              ->orderBy('time', 'desc')
                              ->first();

        if (!$reservation) {
            return redirect()->back()->withErrors(['review' => 'この店舗の予約がありません。']);
        }

        // 画像の保存
        $imagePaths = [];
        if ($request->hasFile('review_image')) {
            foreach ($request->file('review_image') as $image) {
                $imagePaths[] = $image->store('reviews', 'public');
            }
        }

        // 口コミデータを保存
        StoreReview::create([
            'user_id' => $userId,
            'store_id' => $storeId,
            'stars' => $request->stars,  // 星の評価（1〜5）
            'comment' => $request->review_content,  // コメント
            'image' => !empty($imagePaths) ? json_encode($imagePaths) : null,  // 画像があればJSON形式で保存
        ]);

        return back()->with('success', '口コミを投稿しました');
    }


    public function edit($id)
    {
        // ログインユーザーの口コミを取得
        $review = StoreReview::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        // ログインユーザーの口コミを取得
        $review = StoreReview::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // 画像の処理
        $imagePaths = json_decode($review->image, true) ?? [];
        if ($request->hasFile('review_image')) {
            foreach ($request->file('review_image') as $image) {
                if ($image->isValid()) {
                    $imagePaths[] = $image->store('reviews', 'public');
                }
            }
        }

        // データを更新
        $review->update([
            'stars' => $request->stars,
            'comment' => $request->comment,
            'image' => !empty($imagePaths) ? json_encode($imagePaths) : null,
        ]);

        return back()->with('success', '口コミを投稿しました');
    }

    public function destroy($id)
    {
        $review = StoreReview::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $review->delete();

        return back()->with('success', '口コミを削除しました');
    }

}