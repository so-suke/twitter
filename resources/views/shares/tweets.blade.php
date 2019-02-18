{{-- ホーム画面およびプロフィール画面のツイート表示リスト --}}
<ul class="bg-white">
  <li class="d-flex flex-column border-bottom p-2" v-for="new_tweet in new_tweets">
    <div class="d-flex align-items-center ml-5" v-if="new_tweet.tweet_kind === 'retweet'">
      <i class="fas fa-retweet text-muted"></i>
      <span class="fz-12 text-muted ml-1">@{{ new_tweet.retweet_user_name }}さんがリツイート</span>
    </div>
    <div class="d-flex">
      <div class="d-flex flex-column">
        <img class="avator rounded-circle mr-2" :src="new_tweet.avatar_img_path" alt="">
      </div>
      <div class="d-flex flex-column ml-2">
        <div>
          <span class="font-weight-bold">@{{ new_tweet.user_name }}</span>
          <span class="text-muted">@{{ new_tweet.user_screen_name }}</span>
          <span class="ml-2">??時間前</span>
        </div>
        <span>@{{ new_tweet.text }}</span>
        <div class="d-flex mt-2">
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionComment"><i class="far fa-comment"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionRetweet"><i class="fas fa-retweet"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionHeart"><i class="fas fa-heart"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionInfo"><i class="fas fa-info-circle"></i></button>
        </div>
      </div>
    </div>
  </li>

  @foreach ($tweets as $tweet)
  <li class="d-flex flex-column border-bottom p-2 js-stream-item">
    @if ($tweet->tweet_kind === 'retweet')
    <div class="d-flex align-items-center ml-5">
      <i class="fas fa-retweet text-muted"></i>
      <span class="fz-12 text-muted ml-1">{{ $tweet->retweet_user_name }}さんがリツイート</span>
    </div>
    @endif
    <div class="d-flex">
      <div class="d-flex flex-column">
        <img class="avator rounded-circle mr-2" src="{{ $tweet->avatar_img_path }}" alt="">
      </div>
      <div class="d-flex flex-column ml-2 w-100 js-tweet-content" data-tweet-id="{{ $tweet->id }}">
        <div class="d-flex">
          <span class="font-weight-bold jsTweetUserName">{{ $tweet->user_name }}</span>
          <div class="text-muted">
            <span>@</span>
            <span class="jsTweetUserScreenName">{{ $tweet->user_screen_name }}</span>
          </div>
          <span class="ml-2">??時間前</span>
          <div class="dropdown ml-auto">
            <button class="btn btn-link text-muted dropdown-toggle px-2 py-0" type="button" id="tweetOptionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
            <div class="dropdown-menu" aria-labelledby="tweetOptionDropdown">
              @if ($tweet->tweet_kind === 'retweet' || $tweet->user_id !== Auth::id())
              <a class="dropdown-item">まだ何もありません</a>
              @else
              <a class="dropdown-item" href="#" v-on:click.prevent="showTweetDeleteModal">ツイート削除</a>
              @endif
            </div>
          </div>
        </div>
        <span class="jsTweetText">{{ $tweet->text }}</span>
        <div class="d-flex mt-2">
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionComment"><i class="far fa-comment"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionRetweet"><i class="fas fa-retweet"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionHeart"><i class="fas fa-heart"></i></button>
          <button class="btn btn-sm btn-link text-muted col-3 text-left fz-16 py-0 tweetOptionInfo"><i class="fas fa-info-circle"></i></button>
        </div>
      </div>
    </div>
  </li>
  @endforeach
</ul>
