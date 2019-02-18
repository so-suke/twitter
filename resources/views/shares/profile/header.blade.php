<input type="file" class="d-none" ref="profileHeaderImgUploadInput" @change="uploadProfileHeaderCropImg">
<input type="file" class="d-none" ref="profileAvatarImgUploadInput" @change="uploadProfileAvatarCropImg">
<div class="ProfileCanopy">
  <div class="border-bottom ProfileCanopy-inner">
    <div class="ProfileCanopy-header">
      <div class="ProfileCanopy-headerBg">
        <img class="profileHeaderCanopyImg" src="{{ $profile->header_img_path }}" ref="profileHeaderCanopyImg">
      </div>

      <div class="profileHeaderEditing">
        <div class="profileHeaderEditing-overlay"></div>
        <button @click="toggleProfileHeaderEditingDropdown" class="ProfileHeaderEditing-button u-boxShadowInsetUserColorHover">
          <div class="profileHeaderEditing-changeHeaderHelp">
            <i class="fas fa-camera-retro"></i>
            <p>ヘッダー画像を変更する</p>
          </div>
        </button>
        <div id="choose-header-container">
          <div id="choose-header" ref="chooseHeader" class="mydropdown center">
            <div class="mydropdown-menu">
              <div class="dropdown-caret">
                <span class="caret-outer"></span>
                <span class="caret-inner"></span>
              </div>
              <ul>
                <li>
                  <button class="dropdown-link" v-on:click="showProfileHeaderImgUploadDialog">画像をアップロード</button>
                </li>
                <li>
                  <button class="dropdown-link">削除</button>
                </li>
                <li class="dropdown-divider" role="presentation"></li>
                <li>
                  <button class="dropdown-link" v-on:click="hideProfileHeaderEditingDropdown">キャンセル</button>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div id="header_image_upload_dialog" class="ProfileHeaderUploadDialog d-none" ref="ProfileHeaderUploadDialog">
        <div class="ProfileHeaderUploadDialog-body">
          <div class="ProfileHeaderUploadDialog-cropZone position-relative" style="width: 1160px; height: 320px;">
            <div class="ProfileHeaderUploadDialog-cropMask">
              <img ref="profileHeaderUploadDialogCropImage" src="" alt="" class="ProfileHeaderUploadDialog-cropImage" style="width: 1160px; height: 320px; position: relative; top: 0px; left: 0px;">
            </div>
          </div>
        </div>
        <div class="ProfileHeaderUploadDialog-footer">
          <div class="ProfileHeaderUploadDialog-footerInner Grid">
            <div class="Grid-cell">
              <div class="ProfileHeaderUploadDialog-footerContent Arrange">
                <div class="ProfileHeaderUploadDialog-cropperHelp Arrange-sizeFit">
                  <div class="ProfileHeaderUploadDialog-cropperHelpTitle">
                    ヘッダーの位置とサイズを変更
                  </div>
                  <div class="ProfileHeaderUploadDialog-cropperHelpSubtitle">
                    大きな画面では画像が部分的にトリミングされる場合があります
                  </div>
                </div>
                <div class="ProfileHeaderUploadDialog-cropperSlider Arrange-sizeFill">
                  <input type="range" min="0" max="100" value="0" class="slider" ref="uploadImageSliderRange" @input="adjustSizeHeaderCropImg">
                </div>
                <div class="ProfileHeaderUploadDialog-buttons Arrange-sizeFit">
                  <button type="button" class="btn btn-light profile-image-cancel js-close" @click="releaseEditingHeader">キャンセル</button>
                  <button type="button" class="btn btn-primary profile-image-save" @click="applyEditUploadedProfileHeaderImage">適用</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>{{-- ProfileHeaderUploadDialog --}}
      <div class="AppContainer">
        <div class="ProfileCanopy-avatar">

          <div class="ProfileAvatar">
            <img ref="profileAvatarImg" class="ProfileAvatar-img rounded-circle mr-2" src="{{ $profile->avatar_img_path }}" alt="">
          </div>

          <div class="ProfileAvatarEditing is-withAvatar">

            <div class="ProfileAvatarEditing-container">
              <img ref="ProfileAvatarEditingImg" class="ProfileAvatarEditing-image avatar" src="https://pbs.twimg.com/profile_images/1089481245208104961/PlfgSm8g_400x400.jpg" alt="palapalaravel2">
            </div>
            <div class="ProfileAvatarEditing-overlay"></div>
            <div class="ProfileAvatarEditing-buttonContainer">
              <button @click="toggleProfileAvatarEditingDropdown" class="ProfileAvatarEditing-button u-boxShadowInsetUserColorHover">
                <div class="ProfileAvatarEditing-changeAvatarHelp">
                  <i class="fas fa-camera-retro mb-1"></i>
                  <p class="mb-0">プロフィール画像を変更</p>
                </div>
              </button>
            </div>
            <div id="choose-photo" ref="choosePhoto" class="mydropdown center">
              <div class="mydropdown-menu">
                <div class="dropdown-caret">
                  <span class="caret-outer"></span>
                  <span class="caret-inner"></span>
                </div>
                <ul>
                  <li>
                    <button class="dropdown-link" v-on:click="showProfileAvatarImgUploadDialog">画像をアップロード</button>
                  </li>
                  <li>
                    <button class="dropdown-link">削除</button>
                  </li>
                  <li class="dropdown-divider" role="presentation"></li>
                  <li>
                    <button class="dropdown-link" v-on:click="hideProfileAvatarEditingDropdown">キャンセル</button>
                  </li>
                </ul>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>{{-- .ProfileCanopy-header --}}

    <div class="d-flex pt-2 bg-white ProfileCanopy-navBar">
      <div class="ProfilePage-editingOverlay"></div>
      <ul class="nav nav-tabs mx-auto">
        <li class="nav-item">
          <a class="ProfileNav-stat nav-link text-center {{ ((strpos(url()->current(), 'following') === false) && (strpos(url()->current(), 'followers') === false)) ? 'active':'' }}" href="{{ route('to_profile', ['screen_name' => $profile->u_screen_name]) }}">
            <span class="d-block fz-12">ツイート</span>
            <span class="d-block fz-18 font-weight-bold mt-1">1,103</span>
          </a>
        </li>
        <li class="nav-item">
          @php
          if($profile->user_id == Auth::id()) {
          $following_href = route('to_my_following');
          } else {
          $following_href = route('to_following', ['screen_name' => $profile->u_screen_name]);
          }
          @endphp
          <a class="ProfileNav-stat nav-link text-center {{ strpos(url()->current(), 'following') !== false ? 'active':'' }}" href="{{ $following_href }}">
            <span class="d-block fz-12">フォロー</span>
            <span class="d-block fz-18 font-weight-bold mt-1">60</span>
          </a>
        </li>
        <li class="nav-item">
          @php
          if($profile->user_id == Auth::id()) {
          $follower_href = route('to_my_followers');
          } else {
          $follower_href = route('to_followers', ['screen_name' => $profile->u_screen_name]);
          }
          @endphp
          <a class="ProfileNav-stat nav-link text-center {{ strpos(url()->current(), 'followers') !== false ? 'active':'' }}" href="{{ $follower_href }}">
            <span class="d-block fz-12">フォロワー</span>
            <span class="d-block fz-18 font-weight-bold mt-1">74</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="ProfileNav-stat nav-link" href="#">いいね</a>
        </li>
        <li class="nav-item">
          <a class="ProfileNav-stat nav-link disabled" href="#">リスト</a>
        </li>
        <li class="nav-item">
          <a class="ProfileNav-stat nav-link disabled" href="#">モーメント</a>
        </li>
      </ul>

      {{-- プロフィールに関する主要アクションボタンのグループ --}}
      <div class="">
        @if ($profile->user_id == Auth::id())
        <button class="btn btn-primary UserActions-editButton edit-button" ref="profile_edit_btn" @click="to_edit_profile">プロフィールを編集</button>
        <div class="ProfilePage-editingButtons" ref="profile_editing_btns">
          <button class="btn btn-primary" @click="cancelProfileEdit">キャンセル</button>
          <button class="btn btn-primary" @click="saveProfileEdit">変更を保存</button>
        </div>
        @else

        @php
        $follow_class = '';
        if($is_follow_auth_to_profile) {
        $follow_class = 'following';
        } else {
        $follow_class = 'not-following';
        }
        @endphp

        {{-- $follow_classの値の切替により、フォローボタンの見た目も切り替えている。 --}}
        @include('shares.user_actions', ['data_user_id' => $profile->user_id, 'data_auth_user_id' => Auth::id()])
        @endif
      </div>

    </div>{{-- .ProfileCanopy-navBar --}}
  </div>{{-- .ProfileCanopy-inner --}}


</div>{{-- .ProfileCanopy --}}
