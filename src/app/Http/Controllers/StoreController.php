<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use App\Models\Reservation;
use App\Models\Genre;
use App\Models\Region;
use App\Http\Requests\ReservationRequest;
use App\Models\Review;
use App\Models\StoreReview;


class StoreController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // 地域・ジャンルのデータを取得
        $regions = Region::all();
        $genres = Genre::all();
        $store = Store::first();

        // クエリビルダーの初期化
        $query = Store::with(['region', 'genre'])
            ->withAvg('storeReviews', 'stars');

        // ソートの処理
        $sort = $request->input('sort');

        if ($sort === 'random') {
            $query->inRandomOrder();
        } elseif ($sort === 'high_rating') {
            $query->orderByDesc('store_reviews_avg_stars')
                  ->orderBy('id', 'asc');
        } elseif ($sort === 'low_rating') {
            $query->orderBy('store_reviews_avg_stars', 'asc')
                  ->orderBy('id', 'asc');
        }

        $cards = $query->get();

        return view('index', compact('cards', 'regions', 'genres', 'store'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $store = Store::with(['region','genre'])->find($id);
        $storeReviews = StoreReview::where('store_id', $id)->with('user')->get();
        $cards = Store::with(['region', 'genre'])->get();
        $regions = Region::all();
        $genres = Genre::all();

        return view('detail',compact('store', 'cards', 'regions', 'genres', 'storeReviews'));
    }

    public function create(Request $request, $id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('login_required', 'ログインしてください');
        }

        $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'number' => 'required|integer|min:1',
        ]);

        $reservation = new Reservation();
        $reservation->user_id = auth()->id();
        $reservation->store_id = $id;
        $reservation->date = $request->input('date');
        $reservation->time = $request->input('time');
        $reservation->number = $request->input('number');
        $reservation->save();

        return redirect()->route('done')->with('success', '予約が完了しました');
    }

    public function search(Request $request)
    {
        // クエリビルダーの初期化
        $query = Store::query();

        // 条件付きクエリ
        if ($request->filled('region')) {
            $query->where('region_id', $request->input('region'));
        }

        if ($request->filled('genre')) {
            $query->where('genre_id', $request->input('genre'));
        }

        if ($request->filled('keyword')) {
            $query->where('store', 'LIKE', '%' . $request->input('keyword') . '%');
        }

        // クエリの実行と関連モデルの読み込み
        $cards = $query->with(['region', 'genre'])->get();
        // データの取得
        $regions = Region::all();
        $genres = Genre::all();

        return view('index', compact('cards', 'regions', 'genres'));
    }

    public function checkin($reservationId)
    {
        // 予約IDを使って予約を取得
        $reservation = Reservation::find($reservationId);
    
        if ($reservation) {
            // `check` カラムを1に更新
            $reservation->check = 1;
            $reservation->save();
        
            return response('チェックインが完了しました', 200);
        }
    
        return response('予約が見つかりません', 404);
    }

    

    
}
