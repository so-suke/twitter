{{-- $follow_classの値の切替により、フォローボタンの見た目も切り替えている。 --}}
<div class="user-actions {{ $follow_class }}" data-user-id="{{ $data_user_id }}" data-auth-user-id="{{ $data_auth_user_id }}">
  <span class="follow-button">
    <button type="button" class="btn btn-outline-info EdgeButton--medium button-text follow-text" v-on:click="follow">フォローする</button>
    <button type="button" class="btn btn-primary EdgeButton--medium button-text following-text" v-on:mouseleave="unCancelHoverStyleIfNeeded">フォロー中</button>
    <button type="button" class="btn btn-danger EdgeButton--medium button-text unfollow-text" v-on:click="unfollow">解除</button>
  </span>
</div>
