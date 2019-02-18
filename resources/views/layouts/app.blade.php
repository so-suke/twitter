<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Laravel') }}</title>

  <!-- Scripts -->
  {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

  <!-- Styles -->
  @section('csses')
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/myapp.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
  @show
</head>

<body>
  <div id="app">
    <div class="ProfilePage" ref="profilePage">
      <nav class="navbar nav-pills navbar-expand-md navbar-light navbar-laravel">
        <div class="ProfilePage-editingOverlay"></div>
        <div class="container">
          @guest
          <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Twitter') }}
          </a>
          @endguest
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div id="action"></div>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="nav nav-pills mr-auto">
              <div class="nav-item">
                <a class="nav-link active" href="{{ route('home') }}">ホーム</a>
              </div>
              <div class="nav-item">
                <a class="nav-link" href="#">モーメント</a>
              </div>
              <div class="nav-item">
                <a class="nav-link" href="#">通知</a>
              </div>
              <div class="nav-item">
                <a class="nav-link" href="#">メッセージ</a>
              </div>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
              <!-- Authentication Links -->
              @guest
              <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
              </li>
              @if (Route::has('register'))
              <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
              </li>
              @endif
              @else
              <li class="nav-item mr-2">
                <div class="input-group border rounded">
                  <input type="text" class="form-control border-0 search-input" placeholder="キーワード検索" aria-label="Recipient's username" @input="inputedKeywordsSearch" aria-describedby="button-addon2">
                  <div class="dropdown-menu" id="js-keywordsSearch-dropdown">
                    <template v-for="searched_user in searched_users">
                      <a class="dropdown-item" :href="'/' + searched_user.screen_name" @click="redirectToProfile">@{{ '@' + searched_user.screen_name }}</a>
                    </template>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" :href="'/search_users/' + search_screen_name_kw" @click="redirectToProfile"><span>@</span>@{{ search_screen_name_kw }}ですべてのユーザーを検索</a>
                  </div>
                  <div class="input-group-append">
                    <button class="btn btn-outline-light" type="button" id="button-addon2">
                      <i class="fas fa-search text-primary"></i>
                    </button>
                  </div>
                </div>
              </li>
              <li class="nav-item dropdown mr-2">
                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                  <img class="nav-u-img rounded-circle" src="{{ asset('img/apple_u_img.jpeg') }}" alt="">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                  </a>

                  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                  </form>
                </div>
              </li>
              <li class="nav-item">
                <button type="button" class="btn btn-primary" @click="showTweetModal">ツイート</button>
              </li>
              @endguest
            </ul>
          </div>

          {{-- ツイートモーダル --}}
          <div class="modal" id="tweetModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="col-12 text-center modal-title font-weight-bold">ツイートする</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="d-flex">
                    <img class="Avatar--size32 user-avatar-img mr-2" src="{{ asset('img/apple_u_img.jpeg') }}" alt="">
                    <textarea class="form-control" id="tweetModalTextarea" rows="3" placeholder="いまどうしてる？"></textarea>
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-primary" v-on:click="insertNewTweet">ツイート</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- ツイート削除確認モーダル --}}
          <div class="modal" id="delete-tweet-dialog" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="col-12 text-center modal-title font-weight-bold">このツイートを本当に削除しますか？</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="d-flex">
                    <div class="d-flex flex-column">
                      <img class="avator rounded-circle mr-2" src="{{ asset('img/apple_u_img.jpeg') }}" alt="">
                    </div>
                    <div class="d-flex flex-column ml-2 w-100">
                      <div class="d-flex">
                        <span class="font-weight-bold">@{{ delete_modal.tweet_user_name }}</span>
                        <span class="text-muted">@{{ '@' + delete_modal.tweet_user_screen_name }}</span>
                        <span class="ml-2">??時間前</span>
                      </div>
                      <span>@{{ delete_modal.tweet_text }}</span>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">キャンセル</button>
                  <button type="button" class="btn btn-danger" v-on:click="deleteTweet">削除</button>
                </div>
              </div>
            </div>
          </div>

        </div>
      </nav>

      <main class="">
        @yield('content')
      </main>

      <div class="alert-messages" ref="messageDrawer" style="top: -63px;">
        <div class="message">
          <div class="message-inside">
            <span class="message-text">@{{ alert_message_text }}</span>
            <a role="button" class="Icon Icon--close Icon--medium dismiss" href="#" style="display: none;">
              <span class="visuallyhidden">非表示にする</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>{{-- .ProfilePage --}}

  @section('scripts')
  <script src="{{ asset('js/libs/vue.min.js') }}"></script>
  <script src="{{ asset('js/libs/axios.min.js') }}"></script>
  <script src="{{ asset('js/libs/jquery-3.3.1.min.js') }}"></script>
  {{-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  {{-- <script src="https://cdn.jsdelivr.net/npm/vue@2.5.17/dist/vue.js"></script> --}}
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script> --}}
  {{-- <script src="//unpkg.com/babel-polyfill@latest/dist/polyfill.min.js"></script>
  <script src="//unpkg.com/bootstrap-vue@latest/dist/bootstrap-vue.js"></script> --}}
  <script src="{{ asset('/js/contents/after_login.js') }}"></script>
  @show
</body>

</html>
