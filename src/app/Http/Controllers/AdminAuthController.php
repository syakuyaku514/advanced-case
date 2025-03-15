<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use App\Models\Role;
use App\Models\Owner;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralNotificationMail;
use App\Models\StoreReview;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use App\Imports\StoreImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;


class AdminAuthController extends Controller
{
    // 管理者ログインフォームの表示
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function index()
    {
        $owners = Owner::all();
        $stores = Store::with('storeReviews')->get();
        $regions = Region::all();
        $genres = Genre::all(); 

        return view('admin.index', compact('owners', 'stores', 'regions', 'genres'));
    }

    // 管理者ログイン処理
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
        Log::info('Admin login successful', ['admin' => Auth::guard('admin')->user()]);
        return redirect()->route('admin.index');
    }
        // 失敗時にリダイレクト
        return redirect()->route('admin.login');
    }

    // 管理者ログアウト処理
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    // 管理者登録フォームの表示
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    // 管理者登録処理
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        // ログイン処理を実行
        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.index');
    }

    // 店舗代表者登録処理
    public function registerOwner(Request $request)
    {
        // バリデーション
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:owners',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 新しいオーナーを作成
        $owner = Owner::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // 成功メッセージをセッションに追加してリダイレクト
        return redirect()->route('admin.index')->with('success', '店舗代表者を作成しました');
    }

    // メール送信フォームの表示
    public function showSendEmailForm()
    {
        return view('admin.send_email');
    }

    // メール送信処理
    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $email = $request->input('email');
        $subject = $request->input('subject');
        $message = $request->input('message');

        // メールの送信
        Mail::to($email)->send(new GeneralNotificationMail($subject, $message));

        return redirect()->route('admin.sendEmailForm')->with('success', 'メールが送信されました。');
    }

    public function deleteReview($id)
    {
        $review = StoreReview::find($id);
        
        if ($review) {
            $review->delete();
            return redirect()->back()->with('success', 'レビューを削除しました。');
        } else {
            return redirect()->back()->with('error', 'レビューが見つかりませんでした。');
        }
    }

    // インポートの表示
    public function showImportForm()
    {
        $regions = Region::all();
        $genres = Genre::all();
        $owners = Owner::all();
        $stores = Store::with('storeReviews')->get();
    
        return view('admin.index', compact('regions','genres','owners','stores'));
    }

    // CSVインポートを実行
    public function csvImport(Request $request)
    {
        // RegionとGenreのデータを取得
        $regions = Region::all();
        $genres = Genre::all();

        // CSVファイルのバリデーション
        $request->validate([
            'csvFile' => 'required|mimes:csv,txt|max:2048',
        ]);

        // ファイルが存在する場合
        if ($request->hasFile('csvFile')) {
            $file = $request->file('csvFile');
            $path = $file->getRealPath();
            $fp = fopen($path, 'r');

            // ヘッダー行をスキップ
            fgetcsv($fp);

            // 1行ずつ読み込み
            while (($csvData = fgetcsv($fp)) !== false) {
            
                // CSVデータをデータベースに挿入
                $this->InsertCsvData($csvData);
            }

            fclose($fp);

            // 成功メッセージとともにインポートフォームにリダイレクト
            return redirect()->route('csvImportForm')->with('success', 'CSVファイルのインポートが完了しました。');
        }

        // CSVファイルが選択されていない場合
        return back()->withErrors(['csvFile' => 'CSVファイルが必要です。']);
    }

    // CSVデータをデータベースに挿入
    public function InsertCsvData($csvData)
    {
        // RegionとGenreのIDをCSVデータから取得
        $regionId = DB::table('regions')->where('region', $csvData[1])->value('id');
        $genreId = DB::table('genres')->where('genre', $csvData[2])->value('id');


        // 新しいStoreエントリーを作成
        $store = new Store;
        $store->store = $csvData[0];
        $store->region_id = $regionId;
        $store->genre_id = $genreId;
        $store->overview = $csvData[3];
        $store->image = $csvData[4];
        $store->save();
    }
    
}
