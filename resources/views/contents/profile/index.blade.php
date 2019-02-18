@extends('layouts.app')

@section('csses')
@parent
<link href="{{ asset('css/contents/profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container profilePage position-relative" ref="profilePage">

  @include('shares.profile.header')

  <div class="d-flex mt-3">

    @include('shares.profile.left')

    @include('shares.profile.center')

    @include('shares.profile.right')

  </div>

  @include('shares.profile.profile_image_upload_dialog')

</div>
@endsection

@section('scripts')
@parent
<script src="{{ asset('js/contents/profile.js') }}"></script>
@endsection
