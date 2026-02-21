<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StripeCheckoutController;

/*
|--------------------------------------------------------------------------
| Fortify routes (manual)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| 商品一覧・詳細（未ログインでもOK）
|--------------------------------------------------------------------------
*/

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

/*
|--------------------------------------------------------------------------
| 住所登録完了が必要なルート（ログイン + メール認証 + プロフィール必須）
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'profile.completed'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | いいね・コメント
    |--------------------------------------------------------------------------
    */

    Route::post('/item/{item_id}/like', [LikeController::class, 'store'])
        ->name('like.store');

    Route::delete('/item/{item_id}/like', [LikeController::class, 'destroy'])
        ->name('like.destroy');

    Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])
        ->name('comment.store');

    /*
    |--------------------------------------------------------------------------
    | 商品購入
    |--------------------------------------------------------------------------
    */

    // ★ これを {item_id} より先に置く（payment-method が item_id に食われるのを防ぐ）
    Route::post('/purchase/payment-method', [PurchaseController::class, 'savePaymentMethod'])
        ->name('purchase.payment_method');

    Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])
        ->name('purchase.create');

    Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])
        ->name('purchase.store');

    Route::post('/checkout/{item}', [StripeCheckoutController::class, 'create'])
        ->name('checkout.create');

    Route::get('/checkout/success', [StripeCheckoutController::class, 'success'])
        ->name('checkout.success');

    Route::get('/checkout/cancel', function (Request $request) {
        return redirect()->route('purchase.create', ['item_id' => $request->query('item')]);
    })->name('checkout.cancel');

    /*
    |--------------------------------------------------------------------------
    | 配送先変更
    |--------------------------------------------------------------------------
    */

    Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])
        ->name('purchase.address.edit');

    Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])
        ->name('purchase.address.update');

    /*
    |--------------------------------------------------------------------------
    | マイページ・プロフィール・出品
    |--------------------------------------------------------------------------
    */

    Route::get('/mypage', [MypageController::class, 'index']);
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);

    Route::get('/sell', [SellController::class, 'create']);

    Route::post('/sell/confirm', [SellController::class, 'confirm'])->name('sell.confirm');
    Route::post('/sell/back', [SellController::class, 'back'])->name('sell.back');

    Route::post('/sell', [SellController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| リダイレクト受け皿
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', fn() => redirect('/'));
Route::get('/home', fn() => redirect('/'));

/*
|--------------------------------------------------------------------------
| メール認証
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect(\App\Providers\RouteServiceProvider::HOME);
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});
