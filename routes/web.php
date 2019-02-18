<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/following', 'ActionController@toMyFollowing')->name('to_my_following');
Route::get('/followers', 'ActionController@toMyFollowers')->name('to_my_followers');
Route::post('/insert_new_tweet', 'ActionController@insertNewTweet')->name('insert_new_tweet');
Route::get('/search_users/{q}', 'ActionController@toSearchUsers')->name('search_users');

Route::get('/twitter/public/ajax_q/get_init_profile', 'ActionController@ajaxGetInitProfile')->name('get_init_profile');
Route::post('/twitter/public/ajax_q/update_profile', 'ActionController@ajaxUpdateProfile')->name('update_profile');
Route::post('/twitter/public/ajax_q/insert_new_tweet', 'ActionController@ajaxInsertNewTweet');
Route::post('/twitter/public/ajax_q/delete_tweet', 'ActionController@ajaxDeleteTweet');
Route::post('/twitter/public/ajax_q/get_new_tweets_by_min_id', 'HomeController@ajaxGetNewTweetsByMinId')->name('get_new_tweets_by_min_id');
//ユーザ検索機能で使用。検索キーワードに該当するユーザー群を取得。
Route::post('/twitter/public/ajax_q/get_searched_users', 'ActionController@ajaxGetSearchedUsers');
//フォロー
Route::post('/twitter/public/ajax_q/follow', 'ActionController@ajaxFollow');
//フォロー解除
Route::post('/twitter/public/ajax_q/unfollow', 'ActionController@ajaxUnfollow');
//プロフィール画面:ヘッダー画像アップロード機能
Route::post('/twitter/public/ajax_q/uploadProfileHeaderImg', 'ActionController@ajaxUploadProfileHeaderImg');
//プロフィール画面:avatar画像アップロード機能
Route::post('/twitter/public/ajax_q/uploadProfileAvatarImg', 'ActionController@ajaxUploadProfileAvatarImg');
//プロフィール画面:画像編集機能:画像の拡大機能
Route::post('/twitter/public/ajax_q/zoom_image', 'ActionController@ajaxZoomImage');
Route::post('/twitter/public/ajax_q/zoom_avatar_crop_image', 'ActionController@ajaxZoomAvatarCropImage');

Route::get('/{screen_name}', 'ActionController@toProfile')->name('to_profile');
Route::get('/{screen_name}/following', 'ActionController@toFollowing')->name('to_following');
Route::get('/{screen_name}/followers', 'ActionController@toFollowers')->name('to_followers');