<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\OwnerAuthController;
use App\Http\Controllers\StoreReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test-error', function () {
    abort(500, 'Intentional Error for Testing');
});
// ユーザーがメールアドレスの確認を行うためのページを表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// ユーザーがメールアドレスの確認を行うためのページを表示
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/login');
})->middleware(['auth', 'signed'])->name('verification.verify');

// ユーザーが認証メールを再送信するためのリクエスト
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 確認済みのユーザーのみがこのルートにアクセス可能
Route::get('/profile', function () {
    
})->middleware('verified');



// ログイン中
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/', [AuthController::class,'index']);
    Route::get('/thanks', [AuthController::class, 'thanks']);
    Route::get('/detail', [AuthController::class,'detail'])->name('detail');
    Route::get('/done', [AuthController::class,'done'])->name('done');

    // 店舗詳細
    Route::post('/store/{id}', [StoreController::class, 'create']);
    Route::get('/search',[StoreController::class, 'search']);
    Route::post('/search',[StoreController::class, 'search'])->name('search');
    Route::get('/store/{id}', [StoreController::class, 'detail'])->name('store.detail');

    // マイページ
    Route::delete('reservations/{reservation}', [MypageController::class, 'destroy'])->name('reservation.destroy');
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage');
    Route::post('/reservations', [MypageController::class, 'store'])->name('reservation.store');
    Route::get('/edit/{id}',[MypageController::class,'edit'])->name('edit');
    Route::post('/update/{id}',[MypageController::class,'update'])->name('update');
    Route::get('/checkin/{reservation_id}', [StoreController::class, 'checkin'])->name('checkin');

    // お気に入り機能
    Route::post('store/{store}/favorite',[FavoriteController::class, 'toggleFavorite'])->name('store.favorite');

    // レビュー
    Route::post('/review/{id}', [ReviewController::class, 'review'])->name('store.review');
    Route::delete('/review/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');
    Route::patch('/review/{id}', [ReviewController::class, 'update'])->name('review.update');
    

    // 決済
    Route::post('/payment', [UserController::class, 'payment'])->name('payment');
    Route::get('/payment/{reservation}', [UserController::class, 'paymentPage'])->name('payment.page');
    Route::get('/complete', [UserController::class, 'complete'])->name('complete');
    Route::post('/payment/process', [UserController::class, 'payment'])->name('payment.process');


    // 口コミ
    Route::post('/review', [StoreReviewController::class, 'store'])->name('store_reviews.store'); 
    Route::get('/store_reviews/{store_id}', [StoreReviewController::class, 'index'])->name('store_reviews.index');
    Route::get('/review/{id}/edit', [StoreReviewController::class, 'edit'])->name('store_reviews.edit');
    Route::patch('/review/{id}', [StoreReviewController::class, 'update'])->name('store_reviews.update');
    Route::delete('/review/{id}', [StoreReviewController::class, 'destroy'])->name('store_reviews.destroy');



});

// ゲスト
Route::get('/', [StoreController::class, 'index']);
Route::get('/store/{id}', [StoreController::class, 'detail'])->name('store.detail');
Route::get('/search',[StoreController::class, 'search']);
Route::post('/search',[StoreController::class, 'search'])->name('search');
Route::get('/stores', [StoreController::class, 'index'])->name('store.index');


// 口コミ画面の表示
Route::get('/review/{id}', [StoreReviewController::class, 'review_index'])->name('review.index');

// 店舗の口コミ一覧表示
Route::get('/store_reviews/{store_id}', [StoreReviewController::class, 'index'])->name('store_reviews.index');





// 管理者
Route::group(['middleware' => ['auth:admin', 'role:admin']], function () {
    Route::get('/admin/index', [AdminAuthController::class, 'index'])->name('admin.index');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/send-email', [AdminAuthController::class, 'showSendEmailForm'])->name('admin.sendEmailForm');
    Route::post('/admin/send-email', [AdminAuthController::class, 'sendEmail'])->name('admin.sendEmail');
    Route::post('/admin/owner/register', [AdminAuthController::class, 'registerOwner'])->name('admin.owner.register.submit');
    Route::delete('/admin/reviews/{id}', [AdminAuthController::class, 'deleteReview'])->name('admin.deleteReview');


    Route::get('/csv-import', [AdminAuthController::class, 'csvImport']);
    Route::post('/csv-import', [AdminAuthController::class, 'csvImport'])->name('csvImport');
    Route::get('/csv-import', [AdminAuthController::class, 'showImportForm'])->name('csvImportForm');


});


Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('/admin/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');

// 店舗管理者
Route::group(['middleware' => ['auth:owner', 'role:owner']], function () {
    Route::get('/owner/index', [OwnerAuthController::class, 'index'])->name('owner.index');
    Route::post('/owner/logout', [OwnerAuthController::class, 'logout'])->name('owner.logout');
});

Route::get('/owner/login', [OwnerAuthController::class, 'showLoginForm'])->name('owner.login');
Route::post('/owner/login', [OwnerAuthController::class, 'login'])->name('owner.login.submit');
Route::post('/owner/logout', [OwnerAuthController::class, 'logout'])->name('owner.logout');

Route::post('/owner/store', [OwnerAuthController::class, 'create'])->name('owner.store');
Route::delete('/owner/store/delete/{id}', [OwnerAuthController::class, 'delete'])->name('owner.delete');
Route::get('/owner/store/edit/{id}', [OwnerAuthController::class, 'edit'])->name('owner.edit');
Route::put('/owner/store/update/{id}', [OwnerAuthController::class, 'update'])->name('owner.update');