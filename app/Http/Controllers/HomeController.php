<?php

namespace App\Http\Controllers;
use App\Retweet;
use App\Tweet;
use App\Profilese;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller {
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct() {
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index() {
    $user = Auth::user();
    $user_id = Auth::id();
		$profile = Profilese::where('user_id', $user_id)->first();

    include app_path('includes/cmn_tweet_sql_select.php');

    //自分がフォローしているユーザのリツイート(および、それに付随するツイート情報)を取得。※自分のものも取得。
    $retweets = Retweet::select(DB::raw($retweet_sql_select))
      ->from('retweets as r')
      ->join('tweets as t', function ($join) use ($user_id) {
        $join->on('r.tweet_id', '=', 't.id')
          ->whereRaw("(r.user_id in (
						select to_user_id
						from follows
						WHERE from_user_id = ?
					) or r.user_id = ?)", [$user_id, $user_id]);
      })
      ->join('users as tu', 't.user_id', '=', 'tu.id')
      ->join('users as ru', 'r.user_id', '=', 'ru.id')
      ->join('profilese as p', 'r.user_id', '=', 'p.user_id');

    //自分がフォローしているユーザのツイートを取得。※上記リツイート情報とユニオンするため、null取得あり。※自分のものも取得。
    $tweets = Tweet::select(DB::raw($tweet_sql_select))
      ->from('tweets as t')
      ->join('users as u', function ($join) use ($user_id) {
        $join->on('t.user_id', '=', 'u.id')
          ->whereRaw("(t.user_id in (
						select to_user_id
						from follows
						WHERE from_user_id = ?
					) or t.user_id = ?)", [$user_id, $user_id]);
      })
      ->join('profilese as p', 't.user_id', '=', 'p.user_id')
      ->union($retweets)
      ->orderBy('at_for_sort', 'desc')
      ->get();

    return view('home', [
      'user' => $user,
      'profile' => $profile,
      'tweets' => $tweets,
    ]);
  }

  //from home.js
  public function ajaxGetNewTweetsByMinId(Request $request) {
    $user = Auth::user();
    $user_id = Auth::id();
    $min_new_tweet_id = $request->min_new_tweet_id;

    //自分がフォローしているユーザのツイートを取得。※上記リツイート情報とユニオンするため、null取得あり。※自分のものも取得。
    $tweets = Tweet::select(DB::raw("t.user_id, t.text, u.name as user_name, u.screen_name as user_screen_name,
			NULL as retweet_user_name, NULL as retweet_user_screen_name, 'tweet' as tweet_kind, t.created_at AS 'at_for_sort'"))
      ->from('tweets as t')
      ->join('users as u', function ($join) use ($user_id, $min_new_tweet_id) {
        $join->on('t.user_id', '=', 'u.id')
          ->where('t.id', '>=', $min_new_tweet_id)
          ->whereRaw("(t.user_id in (
						select to_user_id
						from follows
						WHERE from_user_id = ?
					) or t.user_id = ?)", [$user_id, $user_id]);
      })
      ->orderBy('at_for_sort', 'desc')
      ->get();
    return response()->json([
      'tweets' => $tweets,
    ]);
  }
}
