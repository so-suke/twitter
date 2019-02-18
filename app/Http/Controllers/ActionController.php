<?php

namespace App\Http\Controllers;
use App\Follow;
use App\Profilese;
use App\Retweet;
use App\Tweet;
use App\User;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Image;
use Log;

class ActionController extends Controller {

  public function __construct() {
    $this->middleware('auth');
  }

  //検索されたユーザを表示するページへ遷移させる。
  public function toSearchUsers(Request $request, $q) {
    $search_screen_name_kw = $q;
    $auth_user_id = Auth::id();
    //検索スクリーン名キーワードを元にユーザを取得し、ページへ返す。
    $users = User::select(DB::raw("u.id as user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
					select 1
					from follows as f2
					where f2.to_user_id = u.id
					and f2.from_user_id = $auth_user_id
				) is_auth_following_ifnotnull, (
					select 1
					from follows as f3
					where f3.from_user_id = u.id
					and f3.to_user_id = $auth_user_id
				) as is_auth_follower_ifnotnull"))
      ->from('users as u')
      ->join('profilese as p', function ($join) use ($search_screen_name_kw) {
        $join->on('u.id', '=', 'p.user_id')
          ->where('u.screen_name', 'like', "%$search_screen_name_kw%");
      })
      ->get();
    return view('contents.search.users', [
      'users' => $users,
      'search_screen_name_kw' => $search_screen_name_kw,
    ]);
  }

  public function insertNewTweet(Request $request) {
    $tweet_text = $request->tweet_text;
    $tweet = new Tweet;
    $tweet->user_id = Auth::id();
    $tweet->text = $tweet_text;
    $tweet->save();

    return redirect()->route('home');
  }

  //from after_login.js, ユーザ検索機能で使用。検索キーワードに該当するユーザー群を取得。
  public function ajaxGetSearchedUsers(Request $request) {
    $search_screen_name_kw = $request->search_screen_name_kw;
    $users = User::where('screen_name', 'like', "%$search_screen_name_kw%")
      ->limit(5)
      ->get();
    return response()->json([
      'users' => $users,
    ]);
  }

  //from after_login.js
  public function ajaxInsertNewTweet(Request $request) {
    $tweet_text = $request->tweet_text;
    $tweet = new Tweet;
    $tweet->user_id = Auth::id();
    $tweet->text = $tweet_text;
    $tweet->save();

    return response()->json([
      'tweet_id' => $tweet->id,
    ]);
  }

  //from after_login.js
  public function ajaxDeleteTweet(Request $request) {
    $will_delete_tweet_id = $request->will_delete_tweet_id;
    $tweet = Tweet::find($will_delete_tweet_id);
    $tweet->delete();

    return response()->json([
      'result' => 'tweet delete success.',
    ]);
  }

  //authユーザがプロフィールユーザを既にフォローしているかどうか？
  public function _getIsFollowAuthToProfile($profile_user_id) {
    $is_follow_auth_to_profile = false;
    if ($profile_user_id !== Auth::id()) {
      $is_follow_auth_to_profile = Follow::where('from_user_id', Auth::id())
        ->where('to_user_id', $profile_user_id)
        ->exists();
    }
    return $is_follow_auth_to_profile;
  }

  //フォローしているユーザの取得。自分のものか他人のものかで、処理を振り分けている。
  public function _getFollowings($is_of_auth_user, $profile_user_id) {
    $followings = null;
    $auth_user_id = Auth::id();
    if ($is_of_auth_user === true) {
      $is_auth_following_select = '1';
    } else {
      $is_auth_following_select = "(
				select 1
				from follows as f3
				where f3.from_user_id = $auth_user_id
				and f3.to_user_id = f.to_user_id
			)";
    }

    $followings = Follow::select(DB::raw("f.to_user_id as user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
				select 1
				from follows as f2
				where f2.from_user_id = f.to_user_id
				and f2.to_user_id = $auth_user_id
			) is_auth_follower_ifnotnull, $is_auth_following_select as is_auth_following_ifnotnull"))
      ->from('follows as f')
      ->join('users as u', function ($join) use ($profile_user_id) {
        $join->on('f.to_user_id', '=', 'u.id')
          ->where('f.from_user_id', $profile_user_id);
      })
      ->join('profilese as p', 'u.id', '=', 'p.user_id')
      ->get();

    return $followings;
  }

  //フォローしているユーザの取得。自分のものか他人のものかで、処理を振り分けている。
  public function _getFollowers($is_of_auth_user, $profile_user_id) {
    $followers = null;
    $auth_user_id = Auth::id();

    if ($is_of_auth_user === true) {
      $is_auth_follower_select = '1';
    } else {
      $is_auth_follower_select = "(
				select 1
				from follows as f3
				where f3.from_user_id = f.from_user_id
				and f3.to_user_id = $auth_user_id
			)";
    }

    $followers = Follow::select(DB::raw("f.from_user_id as user_id, u.name as u_name, u.screen_name as u_screen_name, p.text as p_text, (
				select 1
				from follows as f2
				where f2.to_user_id = f.from_user_id
				and f2.from_user_id = $auth_user_id
			) is_auth_following_ifnotnull, $is_auth_follower_select as is_auth_follower_ifnotnull"))
      ->from('follows as f')
      ->join('users as u', function ($join) use ($profile_user_id) {
        $join->on('f.from_user_id', '=', 'u.id')
          ->where('f.to_user_id', $profile_user_id);
      })
      ->join('profilese as p', 'u.id', '=', 'p.user_id')
      ->get();

    return $followers;
  }

  //プロフィール情報取得関数
  public function _getProfile($screen_name) {
    $profile = Profilese::select(DB::raw('p.id, p.user_id, p.text, p.header_img_path, p.avatar_img_path, u.name as u_name, u.screen_name as u_screen_name'))
      ->from('profilese as p')
      ->join('users as u', function ($join) use ($screen_name) {
        $join->on('p.user_id', '=', 'u.id')
          ->where('u.screen_name', $screen_name);
      })
      ->first();

    return $profile;
  }

  //(フォロー表示画面)へ遷移
  public function toFollowing(Request $request, $screen_name) {
    $profile = $this->_getProfile($screen_name);
    $followings = $this->_getFollowings(false, $profile->user_id);
    $is_follow_auth_to_profile = $this->_getIsFollowAuthToProfile($profile->user_id);

    return view('contents.profile.following', [
      'profile' => $profile,
      'followings' => $followings,
      'is_follow_auth_to_profile' => $is_follow_auth_to_profile,
    ]);
  }

  //ログインユーザの(フォローしているユーザ表示画面)へ遷移
  public function toMyFollowing(Request $request) {
    $profile = $this->_getProfile(Auth::user()->screen_name);
    $followings = $this->_getFollowings(true, $profile->user_id);
    $is_follow_auth_to_profile = $this->_getIsFollowAuthToProfile($profile->user_id);

    return view('contents.profile.following', [
      'profile' => $profile,
      'followings' => $followings,
      'is_follow_auth_to_profile' => $is_follow_auth_to_profile,
    ]);
  }

  //(フォロワー表示画面)へ遷移
  public function toFollowers(Request $request, $screen_name) {
    $profile = $this->_getProfile($screen_name);
    $auth_user_id = Auth::id();
    $profile_user_id = $profile->user_id;
    $followers = $this->_getFollowers(false, $profile_user_id);

    $is_follow_auth_to_profile = $this->_getIsFollowAuthToProfile($profile->user_id);
    return view('contents.profile.followers', [
      'profile' => $profile,
      'followers' => $followers,
      'is_follow_auth_to_profile' => $is_follow_auth_to_profile,
    ]);
  }

  //ログインユーザの(フォロワー表示画面)へ遷移
  public function toMyFollowers(Request $request) {
    $profile = $this->_getProfile(Auth::user()->screen_name);
    $auth_user_id = Auth::id();
    $profile_user_id = Auth::id();
    $followers = $this->_getFollowers(true, $profile_user_id);

    $is_follow_auth_to_profile = $this->_getIsFollowAuthToProfile($profile->user_id);

    return view('contents.profile.followers', [
      'profile' => $profile,
      'followers' => $followers,
      'is_follow_auth_to_profile' => $is_follow_auth_to_profile,
    ]);
  }

  //フォロー処理
  public function ajaxFollow(Request $request) {
    $from_user_id = $request->from_user_id;
    $to_user_id = $request->to_user_id;
    $follow = new Follow;
    $follow->from_user_id = $from_user_id;
    $follow->to_user_id = $to_user_id;
    $follow->save();

    return response()->json([
      'result' => 'success',
    ]);
  }

  //フォロー解除、処理
  public function ajaxUnfollow(Request $request) {
    $from_user_id = $request->from_user_id;
    $to_user_id = $request->to_user_id;

    Log::debug($from_user_id);
    Log::debug($to_user_id);

    Follow::where('from_user_id', $from_user_id)
      ->where('to_user_id', $to_user_id)
      ->delete();

    return response()->json([
      'result' => 'success',
    ]);
  }

  //プロフィールページへ遷移する。
  public function toProfile(Request $request, $screen_name) {
    $user = Auth::user();

    $profile = $this->_getProfile($screen_name);

    include app_path('includes/cmn_tweet_sql_select.php');

    //プロフィールユーザのリツイートを取得
    $retweets = Retweet::select(DB::raw($retweet_sql_select))
      ->from('retweets as r')
      ->join('tweets as t', 'r.tweet_id', '=', 't.id')
      ->join('users as tu', 't.user_id', '=', 'tu.id')
      ->join('users as ru', function ($join) use ($profile) {
        $join->on('r.user_id', '=', 'ru.id')
          ->where('r.user_id', $profile->user_id);
      })
      ->join('profilese as p', 'r.user_id', '=', 'p.user_id');

    //プロフィールユーザのツイートを取得。ユーザidがプロフィールユーザのものだけ抽出。上記リツイートとユニオン。
    $tweets = Tweet::select(DB::raw($tweet_sql_select))
      ->from('tweets as t')
      ->join('users as u', function ($join) use ($profile) {
        $join->on('t.user_id', '=', 'u.id')
          ->where('t.user_id', $profile->user_id);
      })
      ->join('profilese as p', 't.user_id', '=', 'p.user_id')
      ->union($retweets)
      ->orderBy('at_for_sort', 'desc')
      ->get();

    //(ログインユーザ)が(プロフィールユーザ)を既にフォロー済みかどうか。
    $is_follow_auth_to_profile = false;
    if ($profile->user_id !== Auth::id()) {
      $is_follow_auth_to_profile = Follow::where('from_user_id', Auth::id())
        ->where('to_user_id', $profile->user_id)
        ->exists();
    }
    return view('contents.profile.index', [
      'profile' => $profile,
      'tweets' => $tweets,
      'is_follow_auth_to_profile' => $is_follow_auth_to_profile,
    ]);
  }

  public function ajaxUpdateProfile(Request $request) {
    $name = $request->input('name');
    $text = $request->input('text');

    $user = Auth::user();
    $user->name = $name;

    $profile = Profilese::where('user_id', Auth::id())->first();
    $profile->text = $text;

    $user->save();
    $profile->save();
    return response()->json([
      'user' => $user,
      'profile' => $profile,
    ]);
  }

  //プロフィール画面:ヘッダー画像アップロード機能
  public function ajaxUploadProfileHeaderImg(Request $request) {
    $request->validate([
      'image' => 'required',
      'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $orig_width = $request->orig_width;
    $orig_height = $request->orig_height;

    if ($request->image) {
      //setting flag for condition
      $is_make_upload_dir = true;
      // create new directory for uploading image if doesn't exist
      $upload_dir_path = 'profile_header_crop_imgs/';
      if (!File::exists($upload_dir_path)) {
        $is_make_upload_dir = File::makeDirectory($upload_dir_path, 0777, true);
      }

      $img = $request->image;
      $filename = rand(1111, 9999) . time() . $img->getClientOriginalName(); //ファイル名を取得します。
      $upload_path = $upload_dir_path . $filename;
      if ($is_make_upload_dir === true) {
        Image::make($img)->fit($orig_width, $orig_height, function ($constraint) {
          $constraint->upsize();
        })->save($upload_path);
      }
    }
    return response()->json([
      'result' => 'upload_success',
      'uploaded_path' => $upload_path,
    ]);
  }

  //プロフィール画面:avatar画像アップロード機能
  public function ajaxUploadProfileAvatarImg(Request $request) {
    $request->validate([
      'image' => 'required',
      'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $orig_width = $request->orig_width;
    $orig_height = $request->orig_height;

    if ($request->image) {
      //setting flag for condition
      $is_make_upload_dir = true;
      // create new directory for uploading image if doesn't exist
      $upload_dir_path = 'profile_avatar_crop_imgs/';
      if (!File::exists($upload_dir_path)) {
        $is_make_upload_dir = File::makeDirectory($upload_dir_path, 0777, true);
      }

      $img = $request->image;
      $filename = rand(1111, 9999) . time() . $img->getClientOriginalName(); //ファイル名を取得します。
      $upload_path = $upload_dir_path . $filename;
      if ($is_make_upload_dir === true) {
        Image::make($img)->fit($orig_width, $orig_height, function ($constraint) {
          $constraint->upsize();
        })->save($upload_path);
      }
    }
    return response()->json([
      'result' => 'upload_success',
      'uploaded_path' => $upload_path,
    ]);
  }

  public function ajaxZoomImage(Request $request) {
    $orig_width = $request->orig_width;
    $orig_height = $request->orig_height;
    $client_width = $request->client_width;
    $client_height = $request->client_height;
    //Image::crop関数のため、int型に変換。
    $position_left = $request->position_left;
    $position_top = $request->position_top;
    // $magnification = $request->magnification;
    //切り取りサイズは、編集倍率により調整。例えば、倍率が2倍の場合は、元の画像の半分に切り取りする。
    //Image::crop関数のため、int型に変換。
    // $upload_img_crop_width = round($orig_width / $magnification);
    // $upload_img_crop_height = round($orig_height / $magnification);
    $tmp_uploaded_path = $request->tmp_uploaded_path;
    $file = File::get($tmp_uploaded_path);

    $is_make_upload_dir = true;
    // create new directory for uploading image if doesn't exist
    $upload_dir_path = 'profile_header_imgs/';
    if (!File::exists($upload_dir_path)) {
      $is_make_upload_dir = File::makeDirectory($upload_dir_path, 0777, true);
    }
    $filename = rand(1111, 9999) . time() . basename($tmp_uploaded_path);
    $upload_path = $upload_dir_path . $filename;
    //フロント側と同じ手順を繰り返す(画像リサイズ→中心に合わせる)。それから、引き延ばす(リサイズ)
    $img = Image::make($file);
    //画像劣化を防ぐため、画像編集後に元のサイズに戻す。そのためのサイズ取得。
    $prev_img_width = $img->width();
    $prev_img_height = $img->height();
    $img
      ->resize($client_width, $client_height)
      ->crop($orig_width, $orig_height, $position_left, $position_top)
      ->resize($prev_img_width, $prev_img_height)
      ->save($upload_path);

    //DBに画像パスの保存
    $profile = Profilese::where('user_id', Auth::id())
      ->first();
    $profile->header_img_path = $upload_path;
    $profile->save();
    return response()->json([
      'result' => 'success',
      'uploaded_path' => $upload_path,
    ]);
  }

  public function ajaxZoomAvatarCropImage(Request $request) {
    $crop_width = $request->crop_width;
    $crop_height = $request->crop_height;
    $client_width = $request->client_width;
    $client_height = $request->client_height;
    //Image::crop関数のため、int型に変換。
    $position_left = $request->position_left;
    $position_top = $request->position_top;
    $tmp_uploaded_path = $request->tmp_uploaded_path;
    $file = File::get($tmp_uploaded_path);

    $is_make_upload_dir = true;
    // create new directory for uploading image if doesn't exist
    $upload_dir_path = 'profile_avatar_imgs/';
    if (!File::exists($upload_dir_path)) {
      $is_make_upload_dir = File::makeDirectory($upload_dir_path, 0777, true);
    }
    $filename = rand(1111, 9999) . time() . basename($tmp_uploaded_path);
    $upload_path = $upload_dir_path . $filename;
    // フロント側と同じ手順を繰り返す(画像リサイズ→中心に合わせる)。それから、引き延ばす(リサイズ)
    $img = Image::make($file);
    // 画像劣化を防ぐため、画像編集後に元のサイズに戻す。そのためのサイズ取得。
    $prev_img_width = $img->width();
    $prev_img_height = $img->height();
    $img
      ->resize($client_width, $client_height)
      ->crop($crop_width, $crop_height, $position_left, $position_top)
      ->resize($prev_img_width, $prev_img_height)
      ->save($upload_path);

    // DBに画像パスの保存
    $profile = Profilese::where('user_id', Auth::id())
      ->first();
    $profile->avatar_img_path = $upload_path;
    $profile->save();
    return response()->json([
      'result' => 'success',
      'uploaded_path' => $upload_path,
    ]);
  }
}
