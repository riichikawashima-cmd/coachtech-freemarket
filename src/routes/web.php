<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

/*
|--------------------------------------------------------------------------
| Fortify routes (manual)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // login（表示）
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

    // login（送信）
    Route::post('/login', [LoginController::class, 'store'])->name('login');

    // register（表示）
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');

    // register（送信）
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
| 商品一覧・詳細
|--------------------------------------------------------------------------
*/

// 商品一覧（トップ）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/items', [ItemController::class, 'index']);

// 商品詳細
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// いいね
Route::post('/item/{item_id}/like', [LikeController::class, 'store'])
    ->middleware('auth')
    ->name('like.store');

Route::delete('/item/{item_id}/like', [LikeController::class, 'destroy'])
    ->middleware('auth')
    ->name('like.destroy');

// コメント
Route::post('/item/{item_id}/comment', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comment.store');

/*
|--------------------------------------------------------------------------
| 商品購入
|--------------------------------------------------------------------------
*/

// 購入画面表示
Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])
    ->middleware('auth')
    ->name('purchase.create');

// 購入処理
Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])
    ->middleware('auth')
    ->name('purchase.store');

/*
|--------------------------------------------------------------------------
| リダイレクト受け皿
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', fn() => redirect('/'));
Route::get('/home', fn() => redirect('/'));

// 配送先変更
Route::get('/purchase/address/{item_id}', [AddressController::class, 'edit'])
    ->middleware('auth')
    ->name('purchase.address.edit');

Route::post('/purchase/address/{item_id}', [AddressController::class, 'update'])
    ->middleware('auth')
    ->name('purchase.address.update');

/*
|--------------------------------------------------------------------------
| マイページ・プロフィール・出品
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // マイページ
    Route::get('/mypage', [MypageController::class, 'index']);

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit']);
    Route::post('/mypage/profile', [ProfileController::class, 'update']);

    // 商品出品
    Route::get('/sell', [SellController::class, 'create']);
    Route::post('/sell', [SellController::class, 'store']);
});
