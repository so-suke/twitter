@extends('layouts.app')

@section('csses')
@parent
<link href="{{ asset('css/contents/profile.css') }}" rel="stylesheet">
<link href="{{ asset('css/contents/profile/follow.css') }}" rel="stylesheet">
<link href="{{ asset('css/contents/search.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="SearchNavigation-canopy">
  <div class="container">
    <span class="h3 text-white font-weight-bold"><span>@</span>{{ $search_screen_name_kw }}</span>
  </div>
</div>
<div class="container">
  <div class="d-flex mt-3">
    <div class="w-25">
      @include('shares.recommended_users')
      @include('shares.trends')
    </div>

    <div class="GridTimeline-items ml-3">
      @foreach ($users as $user)
      <div class="d-flex flex-column bg-white">
        <img class="ProfileCard-bg" src="{{ asset('img/apple_top_img.jpeg') }}" alt="">
        <div class="ProfileCard-content d-flex flex-column">
          <div class="d-flex justify-content-between">
            <a class="ProfileCard-avatarLink" href="">
              <img class="ProfileCard-avatarImage" src="{{ asset('img/apple_u_img.jpeg') }}" alt="">
            </a>
            <div class="ProfileCard-actions">
              @php
              $follow_class = '';
              if($user->is_auth_following_ifnotnull !== null) {
              $follow_class = 'following';
              } else {
              $follow_class = 'not-following';
              }
              @endphp

              @if ((int)$user->user_id !== Auth::id())
              @include('shares.user_actions', ['data_user_id' => $user->user_id, 'data_auth_user_id' => Auth::id()])
              @endif
            </div>
          </div>

          <div class="mt-2">
            <span class="d-block fz-18 font-weight-bold">{{ $user->u_name }}</span>
            <div class="d-flex">
              <span class="fz-14 text-muted">@ {{ $user->u_screen_name }}</span>
              @if ($user->is_auth_follower_ifnotnull !== null)
              <span class="FollowStatus ml-3">フォローされています</span>
              @endif
            </div>
          </div>

          <p class="ProfileCard-bio mb-0">{{ $user->p_text }}</p>
        </div>
      </div>
      @endforeach

      <div></div>
      <div></div>
    </div>

  </div>
</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/contents/profile.js') }}"></script>
@endsection
