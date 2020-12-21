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
Route::get('/search', 'App\Http\Controllers\PostsController@search')->name('search');

// 投稿
Route::resource('posts', 'App\Http\Controllers\PostsController', ['only' => ['create', 'store', 'show', 'edit', 'update', 'destroy']]);

// コメント
Route::resource('comments', 'App\Http\Controllers\CommentsController', ['only' => ['store', 'edit', 'update', 'destroy']]);

// 認証
Route::get('/logout', 'App\Http\Controllers\UsersController@logout');
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return redirect()->route('top');
})->name('dashboard');

// 管理者ページ
Route::get('/admin', 'App\Http\Controllers\AdminController@index')->name('admin_top');
Route::get('/admin_comment_list', 'App\Http\Controllers\AdminController@comment')->name('admin_comment_list');
Route::get('/admin/search/', 'App\Http\Controllers\AdminController@search');
Route::get('/admin/comment_search/', 'App\Http\Controllers\AdminController@comment_search');
Route::get('/admin_comment/{post_id?}', 'App\Http\Controllers\AdminController@showComments');

Route::post('/admin_delete', 'App\Http\Controllers\AdminController@destroy');
Route::post('/admin_mult_delete', 'App\Http\Controllers\AdminController@multDestroy');
Route::post('/admin_comment_delete', 'App\Http\Controllers\AdminController@commentDestroy');
Route::post('/admin_mult_comment_delete', 'App\Http\Controllers\AdminController@commentMultDestroy');
