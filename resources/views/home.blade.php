@extends('layouts.app')

@section('csses')
@parent
<link rel="stylesheet" href="{{ asset('css/contents/home.css') }}">
@endsection

@section('content')
<div class="container">
  <div class="d-flex">
    <div class="dashbordLeft">
      <div class="position-relative d-flex flex-column bg-white">
        <div>
          <img class="dashbordProfileCardImgTop" src="{{ asset('img/apple_top_img.jpeg') }}" alt="">
        </div>

        <div class="DashboardProfileCard-content">
          <div class="dashbordProfileCardCenter">
            <a href="" class="DashboardProfileCard-avatarLink d-inline-block">
              <a href="{{ route('to_profile', ['screen_name' => $user->screen_name]) }}">
                <img class="DashboardProfileCard-avatarImage" src="{{ $profile->avatar_img_path }}" alt="">
              </a>
            </a>
            <div class="dashbordProfileCard-userFields">
              <a class="d-block DashboardProfileCard-name text-body" href="{{ route('to_profile', ['screen_name' => $user->screen_name]) }}">{{ $user->name }}</a>
              <span class="d-block text-muted dashbordProfileCard-userFieldsSN">
                @<a class="text-muted" href="{{ route('to_profile', ['screen_name' => $user->screen_name]) }}">{{ $user->screen_name }}</a>
              </span>
            </div>
          </div>

          <div class="d-flex justify-content-between p-3">
            <div class="">
              <span class="d-block text-muted font-weight-bold fz-12">ツイート</span>
              <a class="font-weight-bold fz-18" href="{{ route('to_profile', ['screen_name' => $user->screen_name]) }}">1091</a>
            </div>
            <div class="">
              <span class="d-block text-muted font-weight-bold fz-12">フォロー</span>
              <a class="font-weight-bold fz-18" href="{{ route('to_my_following') }}">60</a>
            </div>
            <div class="">
              <span class="d-block text-muted font-weight-bold fz-12">フォロワー</span>
              <a class="font-weight-bold fz-18" href="{{ route('to_my_followers') }}">75</a>
            </div>
          </div>
        </div>

      </div>

      @include('shares.trends')
    </div>
    <div class="contentMain mx-2">

      <div class="d-flex home-tweet-box">
        <img class="nav-u-img rounded-circle mr-2" src="{{ $profile->avatar_img_path }}" alt="">
        <form class="tweet-form condensed" ref="tweetForm" id="jsTweetForm" action="{{ route('insert_new_tweet') }}" method="POST">
          @csrf
          <div class="input-group tweet-content">
            <input type="text" v-on:click="toggleToTweetBoxNoCondensed" class="form-control search-input tmp-tweet-box" placeholder="いまどうしてる？">
            <textarea id="jsTweetBox" class="form-control search-input tweet-box" name="tweet_text" rows="3" placeholder="いまどうしてる？"></textarea>
          </div>
          <div class="TweetBoxToolbar mt-2 jusfity-content-end">
            <button type="submit" class="btn btn-primary js-tweet-btn">ツイート</button>
          </div>
        </form>
      </div>

      <button type="button" v-if="new_tweets_cnt > 0" v-on:click="showNewTweets" class="new-tweets-bar btn btn-light btn-block d-flex align-items-center justify-content-center">
        <span class="text-primary">新しいツイート@{{ new_tweets_cnt }}件を見る</span>
      </button>

      @include('shares.tweets')
    </div>

    <div class="dashbordRight notouch">
      <div class="d-flex flex-column mt-2 p-2 bg-white">
        <div class="d-flex pb-2">
          <span class="fz-18 font-weight-bold mr-3">おすすめユーザー</span>
          <a class="fz-12 align-self-end mr-2" href="">更新</a>
          <a class="fz-12 align-self-end" href="">すべて見る</a>
        </div>
        <ul>
          <li class="d-flex flex-column border-bottom pb-2">
            <div class="d-flex justify-content-end">
              <div class="fz-12">
                <a href="">aaaa</a>さんと<a href="">他のユーザー</a>
                にフォローされています
              </div>
            </div>
            <div class="d-flex">
              <img class="avator rounded-circle mr-2" src="{{ asset('img/default.jpeg') }}" alt="">
              <div class="d-flex flex-column">
                <div>
                  <span class="font-weight-bold">Ricardo Nunes</span>
                  <span class="text-muted">@ricardonu</span>
                </div>
                <button class="btn btn-sm btn-outline-primary w-50">フォローする</button>

              </div>
            </div>
          </li>
          <li class="d-flex flex-column border-bottom pb-2">
            <div class="d-flex justify-content-end">
              <div class="fz-12">
                <a href="">aaaa</a>さんと<a href="">他のユーザー</a>
                にフォローされています
              </div>
            </div>
            <div class="d-flex">
              <img class="avator rounded-circle mr-2" src="{{ asset('img/default.jpeg') }}" alt="">
              <div class="d-flex flex-column">
                <div>
                  <span class="font-weight-bold">Ricardo Nunes</span>
                  <span class="text-muted">@ricardonu</span>
                </div>
                <button class="btn btn-sm btn-outline-primary w-50">フォローする</button>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('/js/contents/home.js') }}"></script>
@endsection
