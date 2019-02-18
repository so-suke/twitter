<div class="d-flex flex-column profileLeft px-2">
  {{-- <div ref="profile_card"> --}}
  <div class="ProfileHeaderCard">
    <div class="h4 font-weight-bold mb-0" ref="profileName">{{ $profile->u_name }}</div>
    <a class="text-muted screen_name" ref="profileScreenName" href="{{ route('to_profile', ['screen_name' => $profile->u_screen_name]) }}">@ {{ $profile->u_screen_name }}</a>
    <div class="my-2" ref="profileText">{{ $profile->text }}</div>
    <div class="mb-1 text-muted">サンプル住所</div>
    <div class="mb-1 text-muted">2014年2月に登録</div>
  </div>
  {{-- </div> --}}

  <div class="ProfileHeaderCardEditing" ref="profile_card_editing">
    <div class="d-flex flex-column">
      <input class="form-control" v-model:value="profile_edit_name">
      <span class="text-muted">@{{ profile_edit_screen_name }}</span>
      <textarea class="form-control" rows="3" v-model:value="profile_edit_text"></textarea>
    </div>
  </div>
</div>
