<?php

use Illuminate\Support\Facades\Route;

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

// トップページ
Route::get('/', 'App\Http\Controllers\PostsController@index')->name('top');

// 投稿
// resourceはcrud処理を自動登録してくれるがそれ以外はできない。
Route::resource('posts', 'App\Http\Controllers\PostsController', ['only' => ['create', 'store', 'show', 'edit', 'update', 'destroy']]);

// コメント
Route::resource('comments', 'App\Http\Controllers\CommentsController', ['only' => ['store', 'edit', 'update', 'destroy']]);

// 認証
Route::get('/logout', 'App\Http\Controllers\UsersController@logout');
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return redirect()->route('top');
})->name('dashboard');

// admin
Route::get('/admin', 'App\Http\Controllers\AdminController@index')->name('admin_top');
Route::post('/admin', 'App\Http\Controllers\AdminController@destroy');
