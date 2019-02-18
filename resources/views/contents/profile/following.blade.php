@extends('layouts.app')

@section('csses')
@parent
<link href="{{ asset('css/contents/profile.css') }}" rel="stylesheet">
<link href="{{ asset('css/contents/profile/follow.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container profilePage" ref="profilePage">

  @include('shares.profile.header')

  <div class="d-flex mt-3">

    @include('shares.profile.left')

    <div class="GridTimeline-items">
      @foreach ($followings as $following)
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
              if($following->is_auth_following_ifnotnull !== null) {
              $follow_class = 'following';
              } else {
              $follow_class = 'not-following';
              }
              @endphp
              @include('shares.user_actions', ['data_user_id' => $following->user_id, 'data_auth_user_id' => Auth::id()])
            </div>
          </div>

          <div class="mt-2">
            <span class="d-block fz-18 font-weight-bold">{{ $following->u_name }}</span>
            <div class="d-flex">
              <span class="fz-14 text-muted">@ {{ $following->u_screen_name }}</span>
              @if ($following->is_auth_follower_ifnotnull !== null)
              <span class="FollowStatus ml-3">フォローされています</span>
              @endif
            </div>
          </div>

          <p class="ProfileCard-bio mb-0">{{ $following->p_text }}</p>
        </div>
      </div>
      @endforeach

      <div></div>
      <div></div>
    </div>

  </div>

  @include('shares.profile.profile_image_upload_dialog')

</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/contents/profile.js') }}"></script>
@endsection
