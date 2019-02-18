const substrWithPX = (str_with_px) => {
  return str_with_px.substr(0, str_with_px.length - 2)
}
const getSubstrPX_Int = (str_with_px) => {
  return parseInt(substrWithPX(str_with_px))
}

// Search for a specific character string from the back and obtain what I cut out from where it appears Two times
const getCutoutAppearSpecificStrTwotimesFromLast = ({ target_str, search_str }) => {
  const from_last_first_idx = target_str.lastIndexOf(search_str);
  const from_last_second_idx = target_str.lastIndexOf(search_str, from_last_first_idx - 1);
  return target_str.substring(from_last_second_idx + 1);
}

// Circle to be the formwork frame for clipping avatar image.
const avatar_cropimg_circle_diameter = 240;

const avatar_cropimg_minleft = 40;
const avatar_cropimg_mintop = -280;

const getAvatarImgMaxLeft = ({ curr_width }) => {
  return avatar_cropimg_minleft + avatar_cropimg_circle_diameter - curr_width;
}
const getAvatarImgMaxTop = ({ curr_height }) => {
  return avatar_cropimg_mintop + avatar_cropimg_circle_diameter - curr_height;
}

var app = new Vue({
  el: '#app',
  mixins: [mix_after_login],
  data: {
    tweets: [],
    new_tweets: [],
    profile_edit_name: '',
    profile_edit_screen_name: '',
    profile_edit_text: '',
    header_img: {
      orig_width: 0,
      orig_height: 0,
      max_left: null,
      max_top: null,
    },
    avatar_img: {
      orig_width: 0,
      orig_height: 0,
      orig_left: 0,
      orig_top: 0,
      curr_left: 0,
      curr_top: 0,
      min_left: avatar_cropimg_minleft,
      min_top: avatar_cropimg_mintop,
      max_left: null,
      max_top: null,
      ref_width: 0,
      ref_height: 0,
      ref_slider_val: 0,
      ref_left: 0,
      ref_top: 0,
      is_slider_minus_direction: false, // For the slider, it is used to judge the return from minus to plus direction.
      prev_slider_val: null, // The same purpose as 'avatar_img.is_slider_minus_direction'
    },
    header_cropimg: {
      uploaded_path: null,
    }
  },
  mounted() {
    // HeaderCropImageの元々の大きさを保存
    this.header_img.orig_width = getSubstrPX_Int(this.$refs.profileHeaderUploadDialogCropImage.style.width);
    this.header_img.orig_height = getSubstrPX_Int(this.$refs.profileHeaderUploadDialogCropImage.style.height);
    //画像のサイズ拡大に伴うleft値とtop値。
    this.header_img.max_left = -1 * parseInt(this.header_img.orig_width * .5);
    this.header_img.max_top = -1 * parseInt(this.header_img.orig_height * .5);
    // AvatarCropImageについても同様に。
    this.avatar_img.orig_width = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.width);
    this.avatar_img.orig_height = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.height);
    this.avatar_img.orig_left = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.left);
    this.avatar_img.orig_top = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.top);
    this.avatar_img.curr_left = this.avatar_img.orig_left;
    this.avatar_img.curr_top = this.avatar_img.orig_top;
    this.avatar_img.max_left = getAvatarImgMaxLeft({ curr_width: this.avatar_img.orig_width });
    this.avatar_img.max_top = getAvatarImgMaxTop({ curr_height: this.avatar_img.orig_height });
  },
  methods: {
    _updateProfile() {
      const params = new URLSearchParams();
      params.append('name', this.profile_edit_name);
      params.append('text', this.profile_edit_text);
      axios.post('/twitter/public/ajax_q/update_profile', params)
        .then((response) => {
          // console.log(response.data);
          const user = response.data.user;
          const profile = response.data.profile;
          this.$refs.profileName.innerHTML = user.name;
          this.$refs.profileText.innerHTML = profile.text;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    follow(e) {
      //フォローボタン押下時の処理
      $e_target = e.target;
      //ボタンの見た目切替のため
      $user_actions = $e_target.closest('.user-actions');

      $user_actions.classList.remove('not-following')
      $user_actions.classList.add('following');

      $follow_btn = $e_target.closest('.follow-button');
      $follow_btn.classList.add('cancel-hover-style');

      const params = new URLSearchParams();
      const auth_user_id = $user_actions.dataset.authUserId;
      const to_user_id = $user_actions.dataset.userId;
      params.append('from_user_id', auth_user_id);
      params.append('to_user_id', to_user_id);
      axios.post('/twitter/public/ajax_q/follow', params)
        .then((response) => {
          console.log(response.data);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    unfollow(e) {
      //フォロー解除ボタン押下時の処理
      $e_target = e.target;
      //ボタンの見た目切替のため
      $user_actions = $e_target.closest('.user-actions');
      $user_actions.classList.add('not-following');
      $user_actions.classList.remove('following')

      $follow_btn = $e_target.closest('.follow-button');
      $follow_btn.classList.add('cancel-hover-style');

      const params = new URLSearchParams();
      const auth_user_id = $user_actions.dataset.authUserId;
      const to_user_id = $user_actions.dataset.userId;

      params.append('from_user_id', auth_user_id);
      params.append('to_user_id', to_user_id);
      axios.post('/twitter/public/ajax_q/unfollow', params)
        .then((response) => {
          console.log(response.data);
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    unCancelHoverStyleIfNeeded(e) {
      $e_target = e.target;
      $follow_btn = $e_target.closest('.follow-button');
      $follow_btn.classList.remove('cancel-hover-style');
    },
    //
    to_edit_profile(event) {
      this.profile_edit_name = this.$refs.profileName.innerHTML;
      this.profile_edit_screen_name = this.$refs.profileScreenName.innerHTML;
      this.profile_edit_text = this.$refs.profileText.innerHTML;
      this.$refs.ProfileAvatarEditingImg.src = this.$refs.profileAvatarImg.src;

      this.$refs.profilePage.classList.add('is-editing');
    },
    //プロフィール編集状態の解除。
    _returnFromProfileEditing() {
      this.$refs.profilePage.classList.remove('is-editingHeader');
      this.$refs.profilePage.classList.remove('is-editing');
    },
    //プロフィール編集の取消。
    cancelProfileEdit(event) {
      this._returnFromProfileEditing();
    },
    //プロフィール編集の保存。
    saveProfileEdit(event) {
      this._updateProfile()
      //描画が若干もたつくので少し遅らせています。
      setTimeout(() => {
        this._returnFromProfileEditing();
      }, 100)
    },
    //スライダーによってHeaderCropImgの大きさを調整する。
    adjustSizeHeaderCropImg(e) {
      const slider_val = e.target.value;
      const orig_width = this.header_img.orig_width; //元々のwidth値
      const orig_height = this.header_img.orig_height; //元々のheight値
      const width = parseInt(orig_width + (orig_width * 0.01 * slider_val));
      const height = parseInt(orig_height + (orig_height * 0.01 * slider_val));

      this.$refs.profileHeaderUploadDialogCropImage.style.width = width + 'px';
      this.$refs.profileHeaderUploadDialogCropImage.style.height = height + 'px';
      this.$refs.profileHeaderUploadDialogCropImage.style.left = parseInt(this.header_img.max_left * 0.01 * slider_val) + 'px';
      this.$refs.profileHeaderUploadDialogCropImage.style.top = parseInt(this.header_img.max_top * 0.01 * slider_val) + 'px';
    },
    //スライダーによってAvatarCropImgの大きさを調整する。
    adjustSizeAvatarCropImg(e) {
      const slider_val = e.target.value;
      if (this.avatar_img.is_slider_minus_direction === true && slider_val > this.avatar_img.prev_slider_val) {
        this.tmpSaveAvatarCropImgRefPosition();
        this.avatar_img.is_slider_minus_direction = false;
      }
      const orig_width = this.avatar_img.orig_width; //元々のwidth値
      const orig_height = this.avatar_img.orig_height; //元々のheight値
      const curr_width = parseInt(orig_width + (orig_width * 0.01 * slider_val));
      const curr_height = parseInt(orig_height + (orig_height * 0.01 * slider_val));

      this.$refs.profileAvatarUploadDialogCropImage.style.width = curr_width + 'px';
      this.$refs.profileAvatarUploadDialogCropImage.style.height = curr_height + 'px';
      // Calculating max value of position
      this.avatar_img.max_left = getAvatarImgMaxLeft({ curr_width });
      this.avatar_img.max_top = getAvatarImgMaxTop({ curr_height });

      // We subtract the whenClickSlider_Reference(width, height) from the current (width, height) of cropimg and halve it. It is the value that position moves.  
      const move_left = parseInt((curr_width - this.avatar_img.ref_width) / 2);
      const move_top = parseInt((curr_height - this.avatar_img.ref_height) / 2);
      if (slider_val < this.avatar_img.ref_slider_val) {
        // When the slider advances in the minus direction.

        // For the slider, it is used to judge the return from minus to plus direction.
        this.avatar_img.is_slider_minus_direction = true;
        this.avatar_img.prev_slider_val = slider_val;

        if (this.avatar_img.curr_left > (this.avatar_img.min_left - 1)) {
          this.avatar_img.curr_left = this.avatar_img.min_left;
        } else if (this.avatar_img.curr_left < this.avatar_img.max_left) {
          this.avatar_img.curr_left = this.avatar_img.max_left;
        } else {
          // Since position is a negative value, subtraction is performed.
          this.avatar_img.curr_left = this.avatar_img.ref_left - move_left;
        }
        if (this.avatar_img.curr_top > (this.avatar_img.min_top - 1)) {
          this.avatar_img.curr_top = this.avatar_img.min_top;
        } else if (this.avatar_img.curr_top < this.avatar_img.max_top) {
          this.avatar_img.curr_top = this.avatar_img.max_top;
        } else {
          // Since position is a negative value, subtraction is performed.
          this.avatar_img.curr_top = this.avatar_img.ref_top - move_top;
        }
      } else {
        //When the slider advances in the plus direction.
        this.avatar_img.is_slider_minus_direction = false;
        this.avatar_img.curr_left = this.avatar_img.ref_left - move_left;
        this.avatar_img.curr_top = this.avatar_img.ref_top - move_top;
      }
      this.$refs.profileAvatarUploadDialogCropImage.style.left = this.avatar_img.curr_left + 'px';
      this.$refs.profileAvatarUploadDialogCropImage.style.top = this.avatar_img.curr_top + 'px';
    },
    tmpSaveAvatarCropImgRefPosition() {
      this.avatar_img.ref_slider_val = parseInt(this.$refs.uploadImageSliderRange.value);
      this.avatar_img.ref_width = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.width);
      this.avatar_img.ref_height = getSubstrPX_Int(this.$refs.profileAvatarUploadDialogCropImage.style.height);
      this.avatar_img.ref_left = this.avatar_img.curr_left;
      this.avatar_img.ref_top = this.avatar_img.curr_top;
    },
    //ヘッダー画像のアップロードに関するドロップダウンの開閉。
    toggleProfileHeaderEditingDropdown() {
      this.$refs.chooseHeader.classList.toggle('open');
    },
    //アバター画像のアップロードに関するドロップダウンの開閉。
    toggleProfileAvatarEditingDropdown() {
      this.$refs.choosePhoto.classList.toggle('open');
    },
    //ヘッダー画像のアップロードダイアログを開く。
    showProfileHeaderImgUploadDialog() {
      //「ファイルを開く」ダイアログの「キャンセル」押下時にインプットのchangeイベント発火を防ぐ。
      this.$refs.profileHeaderImgUploadInput.value = null;
      //画像アップロードインプットを開く。
      this.$refs.profileHeaderImgUploadInput.click();
    },
    //ヘッダー画像のアップロードに関するドロップダウンを閉じる。
    hideProfileHeaderEditingDropdown() {
      this.$refs.chooseHeader.classList.remove('open');
    },
    //アバター画像のアップロードダイアログを開く。
    showProfileAvatarImgUploadDialog() {
      //「ファイルを開く」ダイアログの「キャンセル」押下時にインプットのchangeイベント発火を防ぐ。
      this.$refs.profileAvatarImgUploadInput.value = null;
      //画像アップロードインプットを開く。
      this.$refs.profileAvatarImgUploadInput.click();
    },
    //アバター画像のアップロードに関するドロップダウンを閉じる。
    hideProfileAvatarEditingDropdown() {
      this.$refs.choosePhoto.classList.remove('open');
    },
    //ヘッダー編集用画像のアップロードダイアログの画像選択完了時に発火させる。
    uploadProfileHeaderCropImg() {
      var formData = new FormData();
      formData.append("image", this.$refs.profileHeaderImgUploadInput.files[0]);
      formData.append('orig_width', this.header_img.orig_width);
      formData.append('orig_height', this.header_img.orig_height);
      axios.post('/twitter/public/ajax_q/uploadProfileHeaderImg', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then((response) => {
          console.log(response.data);
          this.$refs.profileHeaderUploadDialogCropImage.src = response.data.uploaded_path;
          //編集用画像のアップロードパス。
          this.header_cropimg.uploaded_path = response.data.uploaded_path;
          this.$refs.ProfileHeaderUploadDialog.classList.remove('d-none');
          this.$refs.profilePage.classList.add('is-editingHeader');
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //アバター編集用画像のアップロードダイアログの画像選択完了時に発火させる。
    uploadProfileAvatarCropImg() {
      var formData = new FormData();
      formData.append("image", this.$refs.profileAvatarImgUploadInput.files[0]);
      formData.append('orig_width', this.avatar_img.orig_width);
      formData.append('orig_height', this.avatar_img.orig_height);
      axios.post('/twitter/public/ajax_q/uploadProfileAvatarImg', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then((response) => {
          console.log(response.data);
          this.$refs.profileAvatarUploadDialogCropImage.src = response.data.uploaded_path;
          this.$refs.profileImgUploadDialog.style.display = 'block';
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    // Release editingHeader state
    releaseEditingHeader() {
      this.$refs.profilePage.classList.remove('is-editingHeader');
      //ヘッダー画像編集ダイアログ系を閉じる。
      this.$refs.ProfileHeaderUploadDialog.classList.add('d-none');
      this.$refs.profileHeaderUploadDialogCropImage.src = '';
      this.hideProfileHeaderEditingDropdown();
    },
    // Release editingAvatar state
    releaseEditingAvatar() {
      //avatar画像編集ダイアログ系を閉じる。
      this.$refs.profileImgUploadDialog.style.display = 'none';
      this.$refs.profileAvatarUploadDialogCropImage.src = '';
      this.hideProfileAvatarEditingDropdown();
    },
    //ヘッダー画像の拡大率の編集処理を適用。
    applyEditUploadedProfileHeaderImage() {
      const client_width = this.$refs.profileHeaderUploadDialogCropImage.clientWidth;
      const client_height = this.$refs.profileHeaderUploadDialogCropImage.clientHeight;
      const style_top_with_px = this.$refs.profileHeaderUploadDialogCropImage.style.top;
      const style_left_with_px = this.$refs.profileHeaderUploadDialogCropImage.style.left;
      //style要素のtop, left値に'px'が付いているので削除、数値化。
      const position_top = parseFloat(style_top_with_px.substr(0, style_top_with_px.length - 2) * -1);
      const position_left = parseFloat(style_left_with_px.substr(0, style_left_with_px.length - 2) * -1);
      //アップロード画像の倍率
      const magnification = 1 + (0.01 * this.$refs.uploadImageSliderRange.value);
      const params = new URLSearchParams();
      params.append('orig_width', this.header_img.orig_width);
      params.append('orig_height', this.header_img.orig_height);
      params.append('client_width', client_width);
      params.append('client_height', client_height);
      params.append('position_left', position_left);
      params.append('position_top', position_top);
      params.append('tmp_uploaded_path', this.header_cropimg.uploaded_path);
      axios.post('/twitter/public/ajax_q/zoom_image', params)
        .then((response) => {
          console.log(response.data);
          this.releaseEditingHeader();
          //ヘッダー画像を差し替える。
          this.$refs.profileHeaderCanopyImg.src = response.data.uploaded_path;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    //Apply avatar image (while changing position and size)。
    applyEditProfileAvatarCropImg() {
      const client_width = this.$refs.profileAvatarUploadDialogCropImage.clientWidth;
      const client_height = this.$refs.profileAvatarUploadDialogCropImage.clientHeight;
      const style_top_with_px = this.$refs.profileAvatarUploadDialogCropImage.style.top;
      const style_left_with_px = this.$refs.profileAvatarUploadDialogCropImage.style.left;
      // Since top and left values ​​of style element have 'px' attached, delete and digitize. We also calculate the moving distance of the image.
      const position_top = avatar_cropimg_mintop - parseInt(style_top_with_px.substr(0, style_top_with_px.length - 2));
      const position_left = avatar_cropimg_minleft - parseInt(style_left_with_px.substr(0, style_left_with_px.length - 2));
      const params = new URLSearchParams();
      params.append('crop_width', avatar_cropimg_circle_diameter);
      params.append('crop_height', avatar_cropimg_circle_diameter);
      params.append('client_width', client_width);
      params.append('client_height', client_height);
      params.append('position_left', position_left);
      params.append('position_top', position_top);
      params.append('tmp_uploaded_path', getCutoutAppearSpecificStrTwotimesFromLast({
        target_str: this.$refs.profileAvatarUploadDialogCropImage.src,
        search_str: '/',
      }));
      axios.post('/twitter/public/ajax_q/zoom_avatar_crop_image', params)
        .then((response) => {
          console.log(response.data);
					this.releaseEditingAvatar();
          //avatar画像の通常版と編集版の双方を新しい画像に差し替える。
          this.$refs.ProfileAvatarEditingImg.src = response.data.uploaded_path;
          this.$refs.profileAvatarImg.src = response.data.uploaded_path;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
  }
});

// Process concerning the drag of avatar crop image.
const ui_draggables = document.getElementsByClassName("ui-draggable");
const $jsAvatarCropImg = document.getElementById("jsAvatarCropImg");
let x = 0;
let y = 0;
let drag = null;
let when_mdown_avatar_cropimg_left = 0;
let when_mdown_avatar_cropimg_top = 0;

//マウスが要素内で押されたとき、又はタッチされたとき発火
for (var i = 0; i < ui_draggables.length; i++) {
  ui_draggables[i].addEventListener("mousedown", mdown, false);
}
//マウスがs押された際の関数
function mdown(event) {
  // console.log('mdown');
  drag = this;
  this.classList.add("ui-draggable-dragging");
  //要素内の相対座標を取得
  x = event.pageX - this.offsetLeft;
  y = event.pageY - this.offsetTop;
  when_mdown_avatar_cropimg_left = app.avatar_img.curr_left;
  when_mdown_avatar_cropimg_top = app.avatar_img.curr_top;

  //ムーブイベントにコールバック
  window.addEventListener("mousemove", mmove, false);
}

//マウスカーソルが動いたときに発火
function mmove(event) {
  // console.log('mmove');

  app.avatar_img.curr_left = when_mdown_avatar_cropimg_left + (event.pageX - x);
  if (app.avatar_img.curr_left > app.avatar_img.min_left) {
    app.avatar_img.curr_left = app.avatar_img.min_left;
  } else if (app.avatar_img.curr_left < app.avatar_img.max_left) {
    app.avatar_img.curr_left = app.avatar_img.max_left;
  }
  $jsAvatarCropImg.style.left = `${app.avatar_img.curr_left}px`;

  app.avatar_img.curr_top = when_mdown_avatar_cropimg_top + (event.pageY - y);
  if (app.avatar_img.curr_top > app.avatar_img.min_top) {
    app.avatar_img.curr_top = app.avatar_img.min_top;
  } else if (app.avatar_img.curr_top < app.avatar_img.max_top) {
    app.avatar_img.curr_top = app.avatar_img.max_top;
  }
  $jsAvatarCropImg.style.top = `${app.avatar_img.curr_top}px`;

  //マウスボタンが離されたとき発火
  window.addEventListener("mouseup", mup, false);
}

//マウスボタンが上がったら発火
function mup(e) {
  // console.log('mup');
  //ムーブベントハンドラの消去
  window.removeEventListener("mousemove", mmove, false);
  window.removeEventListener("mouseup", mup, false);

  //クラス名 .drag も消す
  drag.classList.remove("ui-draggable-dragging");
}
