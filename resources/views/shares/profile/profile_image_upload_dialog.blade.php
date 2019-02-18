<div id="profile_image_upload_dialog" ref="profileImgUploadDialog" class="modal-container image-upload-dialog" style="z-index: 4002; display: none;">
  <div class="mymodal profile-avatar-modal unselectable" id="profile_image_upload_dialog-dialog" style="top: 50px; left: 423px;">
    <div class="mymodal-content">
      <div class="mymodal-header">
        <h3 class="mymodal-title" id="profile_image_upload_dialog-header">画像の位置とサイズを変更</h3>
      </div>
      <div class="mymodal-body" id="profile_image_upload_dialog-body">
        <div class="upload-frame image-upload-crop">
          <div class="crop-zone cropper-avatar-size">
            <div class="cropper-mask">
              <div class="cropper-overlay ui-draggable ui-draggable-handle" style="position: relative; width: 320px; right: auto; height: 320px; bottom: auto; left: 0px; top: 0px;"></div>
              <img ref="profileAvatarUploadDialogCropImage" id="jsAvatarCropImg" class="crop-image" alt="palapalaravel2" src="" style="width: 361px; height: 240px; position: relative; top: -280px; left: -20.5px;">
            </div>
            <div class="cropper-slider-outer">
              <input type="range" min="0" max="100" value="0" class="slider" ref="uploadImageSliderRange" @input="adjustSizeAvatarCropImg" @mousedown="tmpSaveAvatarCropImgRefPosition">
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light profile-image-cancel js-close" @click="releaseEditingAvatar">キャンセル</button>
        <button type="button" class="btn btn-primary profile-image-save" @click="applyEditProfileAvatarCropImg">適用</button>
      </div>
    </div>
  </div>
</div>
