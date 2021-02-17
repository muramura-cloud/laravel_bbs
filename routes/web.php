<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['checkAuth', 'setupSidebar']], function () {

    // トップページ
    Route::get('/', 'App\Http\Controllers\PostsController@index')->name('top');
    Route::get('/introduce', 'App\Http\Controllers\PostsController@introduce')->name('introduce');
    Route::get('/search', 'App\Http\Controllers\PostsController@search')->name('search');
    Route::post('/ajaxlike', 'App\Http\Controllers\PostsController@ajaxlike');
    Route::resource('posts', 'App\Http\Controllers\PostsController', ['only' => ['create', 'store', 'show', 'edit', 'update', 'destroy']]);

    // コメント
    Route::resource('comments', 'App\Http\Controllers\CommentsController', ['only' => ['store', 'edit', 'update', 'destroy']]);

    // ユーザーページと認証
    Route::get('/user', 'App\Http\Controllers\UsersController@index')->name('user_top');
    Route::get('/user/read', 'App\Http\Controllers\UsersController@read');
    Route::get('/logout', 'App\Http\Controllers\UsersController@logout');
    Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
        return redirect()->route('top');
    });

    //違反報告
    Route::get('/report/create', 'App\Http\Controllers\ReportController@create')->name('report_create');
    Route::get('/report/reported', 'App\Http\Controllers\ReportController@showReported')->name('report_reported');
    Route::post('/report', 'App\Http\Controllers\ReportController@store');
});

// 管理者ページ
Route::group(['middleware' => 'checkAdmin'], function () {
    Route::get('/admin', 'App\Http\Controllers\AdminController@index')->name('admin_top');
    Route::get('/admin/search/', 'App\Http\Controllers\AdminController@search');
    Route::get('/admin/comment_search/', 'App\Http\Controllers\AdminController@commentSearch');
    Route::get('/admin/reported/', 'App\Http\Controllers\AdminController@showReported');
    Route::get('/admin_comment/{post_id?}', 'App\Http\Controllers\AdminController@showComments');
    Route::get('/admin_comment_list', 'App\Http\Controllers\AdminController@comment')->name('admin_comment_list');
    Route::post('/admin_delete', 'App\Http\Controllers\AdminController@destroy');
    Route::post('/admin_mult_delete', 'App\Http\Controllers\AdminController@multDestroy');
    Route::post('/admin_comment_delete', 'App\Http\Controllers\AdminController@commentDestroy');
    Route::post('/admin_mult_comment_delete', 'App\Http\Controllers\AdminController@commentMultDestroy');
});
