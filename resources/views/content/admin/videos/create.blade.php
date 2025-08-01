@extends('layouts.layoutMaster')

@section('title', 'Video Creation - Forms')

<!-- Vendor Styles -->
@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/tagify/tagify.scss',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/libs/dropzone/dropzone.scss',
  'resources/assets/vendor/libs/spinkit/spinkit.scss'
])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/tagify/tagify.js',
  'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/libs/bloodhound/bloodhound.js',
  'resources/assets/vendor/libs/toastr/toastr.js',
  'resources/assets/vendor/libs/dropzone/dropzone.js',
  'resources/assets/vendor/libs/block-ui/block-ui.js'
])
@endsection

@section('page-style')
<style>
/* Minimal CSS - Only essential dropzone styling */
.dropzone {
  min-height: 200px;
  cursor: pointer !important;
}
.dropzone .dz-message {
  margin: 0;
  width: 100%;
}
.dropzone .dz-preview {
  margin: 10px;
}
</style>
@endsection

<!-- Page Scripts -->
@section('page-script')
@vite([
    'resources/assets/js/forms-selects.js',
    'resources/assets/js/forms-pickers.js',
    'resources/assets/js/forms-tagify.js',
    'resources/assets/js/forms-typeahead.js'
])
<script>
    const previewTemplate = `<div class="dz-preview dz-file-preview">
                            <div class="dz-details">
                            <div class="dz-thumbnail">
                                <video data-dz-thumbnail controls style="max-width:100%;max-height:120px;border-radius:8px;"></video>
                                <span class="dz-nopreview">No preview</span>
                                <div class="dz-success-mark">
                                    <svg width="54" height="54" viewBox="0 0 54 54"><path d="m9,20 9,9 18,-18" stroke="white" stroke-width="3" fill="none"/></svg>
                                </div>
                                <div class="dz-error-mark">
                                    <svg width="54" height="54" viewBox="0 0 54 54"><path d="m9,9 18,18 18,-18" stroke="white" stroke-width="3" fill="none"/></svg>
                                </div>
                                <div class="dz-error-message"><span data-dz-errormessage></span></div>
                                <div class="progress">
                                <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                                </div>
                            </div>
                            <div class="dz-filename" data-dz-name></div>
                            <div class="dz-size" data-dz-size></div>
                            </div>
                            </div>`;

    let myDropzone;

    function showWarning(message) {
      const warningBox = document.getElementById("upload-warning");
      warningBox.textContent = message;
      warningBox.classList.remove("d-none");
      setTimeout(() => warningBox.classList.add("d-none"), 3000);
    }

    function hideWarning() {
      document.getElementById("upload-warning").classList.add("d-none");
    }

    document.addEventListener('DOMContentLoaded', function () {
      const dropzoneBasic = document.querySelector('#dropzone-basic');

      if (dropzoneBasic) {
        myDropzone = new Dropzone(dropzoneBasic, {
          url: '#',
          autoProcessQueue: false,
          uploadMultiple: false,
          paramName: 'post_video',
          acceptedFiles: 'video/mp4',
          maxFilesize: 200,
          maxFiles: 1,
          addRemoveLinks: true,
          previewTemplate: previewTemplate,
          clickable: true,
          init: function () {
            const dz = this;

            dz.on("addedfile", function (file) {
              if (file.size > 200 * 1024 * 1024) {
                dz.removeFile(file);
                showWarning("Upload failed: File size exceeds 200MB.");
                return;
              }

              if (file.type !== "video/mp4") {
                dz.removeFile(file);
                showWarning("Upload failed: Only MP4 video files are allowed.");
                return;
              }

              hideWarning();

              const form = document.getElementById('video-create-form');
              let hiddenInput = form.querySelector('input[name="post_video"]');

              if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = 'post_video';
                hiddenInput.style.display = 'none';
                form.appendChild(hiddenInput);
              }

              const dataTransfer = new DataTransfer();
              dataTransfer.items.add(file);
              hiddenInput.files = dataTransfer.files;

              setTimeout(() => {
                file.status = Dropzone.SUCCESS;
                file.previewElement.classList.add('dz-success');
                dz.emit("success", file);
                dz.emit("complete", file);
              }, 500);
            });

            dz.on("removedfile", function (file) {
              hideWarning();
              const form = document.getElementById('video-create-form');
              const hiddenInput = form.querySelector('input[name="post_video"]');
              if (hiddenInput) {
                hiddenInput.remove();
              }
            });

            dz.on("error", function (file, message) {
              console.error("Dropzone error:", message);
              showWarning("Upload error: " + message);
            });
          }
        });
      }

      const form = document.getElementById('video-create-form');
      if (form) {
        form.addEventListener('submit', function (e) {
          // Clear all validation errors
          document.querySelectorAll('.validation-error').forEach(function(el) {
            el.textContent = '';
          });

          let valid = true;
          let firstInvalidField = null;
          const title = form.querySelector('input[name="title"]');
          const description = form.querySelector('textarea[name="description"]');
          const access_type = form.querySelector('select[name="access_type_id"]');
          const video = form.querySelector('input[name="post_video"]');

          if (!title.value.trim()) {
            valid = false;
            document.getElementById('title-error').textContent = 'Title is required.';
            firstInvalidField = title;
          }
          if (!description.value.trim()) {
            valid = false;
            document.getElementById('description-error').textContent = 'Description is required.';
            if (!firstInvalidField) firstInvalidField = description;
          }
          if (!access_type.value.trim()) {
            valid = false;
            document.getElementById('access_type-error').textContent = 'access_type is required.';
            if (!firstInvalidField) firstInvalidField = access_type;
          }
          if (!video || !video.files || video.files.length === 0) {
            valid = false;
            document.getElementById('video-error').textContent = 'Please upload a video file.';
            // Dropzone handles focus, so don't set firstInvalidField here
          }

          if (!valid) {
            e.preventDefault();
            if (firstInvalidField) {
              firstInvalidField.focus();
            }
            return false;
          }

          // Show BlockUI when all required fields are filled
          if (window.$ && window.$.blockUI) {
            $.blockUI({ message: '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
              css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
              },
              overlayCSS: {
                opacity: 0.5
              }
            });
            // $('#card-block').block({
            //   message:
            //     '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
            //   css: {
            //     backgroundColor: 'transparent',
            //     color: '#fff',
            //     border: '0'
            //   },
            //   overlayCSS: {
            //     opacity: 0.5
            //   }
            // });

          }
        });
      }
    });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const createVideoBtn = document.querySelector('#submit-video');
    const form = document.getElementById('video-create-form');
    if (form) {
      form.addEventListener('submit', function (e) {
        // Let the form submit naturally, BlockUI will be triggered by the submit event
      });
    }
    if (createVideoBtn) {
      createVideoBtn.onclick = function (e) {
        // Let the form submit naturally, BlockUI will be triggered by the submit event
      };
    }
  });
</script>
@endsection

@section('content')
<form id="video-create-form" action="{{ route('videos.store') }}" method="POST" enctype="multipart/form-data">
  @csrf
  <div class="row" id="card-block">
    <input type="hidden" name="type" value="post">
    <!-- Left Card: Create Post -->
    <div class="col-md-9">
      <div class="card h-100">
        <h5 class="card-header">Video</h5>
        <div class="card-body d-flex flex-column">
          <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
              <span class="text-danger">*</span>
              <input
                type="text"
                name="title"
                value="{{ old('title') }}"
                class="form-control"
                id="title"
                placeholder="Enter Title"
              />
              <div class="text-danger validation-error" id="title-error"></div>
              @error('title')
                <p class="text-danger">{{ $errors->first('title') }}</p>
              @enderror
          </div>
          <div class="mb-3 flex-grow-1">
            <label for="description" class="form-label">Enter Description:</label>
            <span class="text-danger">*</span>
            <textarea class="form-control" name="description" id="description" style="min-height: 150px">{{ old('description') }}</textarea>
            <div class="text-danger validation-error" id="description-error"></div>
            @error('description')
              <p class="text-danger">{{ $errors->first('description') }}</p>
            @enderror
          </div>
          <div class="row mb-3">
            
            <div class="col-md-6">
              <label for="access_type_id" class="form-label">Access Type:</label>
              <span class="text-danger">*</span>
              <select name="access_type_id" id="access_type_id" class="select2 form-select">
                <option value="">Select Access Type</option>
                @foreach($access_types as $access_type)
                  <option value="{{ $access_type->id }}" {{ old('access_type_id') == $access_type->id ? 'selected' : '' }}>{{ $access_type->name }}</option>
                @endforeach
              </select>
              <div class="text-danger validation-error" id="access_type-error"></div>
              @error('access_type_id')
                <p class="text-danger">{{ $errors->first('access_type_id') }}</p>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="genre_id" class="form-label">Genre Type:</label>
              <span class="text-danger">*</span>
              <select name="genre_id" id="genre_id" class="select2 form-select">
                <option value="">Select Genre Type</option>
                @foreach($genres as $genre)
                  <option value="{{ $genre->id }}" {{ old('genre_id') == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                @endforeach
              </select>
              <div class="text-danger validation-error" id="genre-error"></div>
              @error('genre_id')
                <p class="text-danger">{{ $errors->first('genre_id') }}</p>
              @enderror
            </div>

            <div class="col-md-6 mb-2 mt-2">
              <label for="TagifyBasic" class="form-label">Tags:</label>
              <input id="TagifyBasic"
                class="form-control add-field"
                type="text"
                name="tags"
                value="{{ old('tags') }}"
                placeholder="Enter Tags (Comma separated)"
                maxlength="150"
                minlength="3"
              />
              @error('tags')
                <p class="text-danger">{{ $errors->first('tags') }}</p>
              @enderror
            </div>
          </div>
          <div class="mt-auto">
            <button type="submit" id="submit-video" class="btn btn-card-block-custom btn-primary">Submit</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Right Card: Upload Video -->
    <div class="col-md-3">
      <div class="card h-100">
        <h5 class="card-header text-center">Upload Video <span class="text-danger">*</span></h5>
        <div class="card-body d-flex flex-column justify-content-center">
          <div class="dropzone border rounded p-3 text-center" id="dropzone-basic">
            <div id="upload-warning" class="d-none text-danger text-center small mt-2" role="alert"></div>
            <div class="dz-message text-center py-3">
              <i class="bi bi-cloud-arrow-up fs-1 mb-2"></i>
              <strong class="fs-6 d-block">Click to upload</strong>
              <p class="text-muted mb-0 small">
                Only video files (MP4). Max size 200MB.
              </p>
            </div>
          </div>
          <div class="text-danger validation-error" id="video-error"></div>
          @error('post_video')
            <p class="text-danger">{{ $errors->first('post_video') }}</p>
          @enderror
          <div class="text-center mt-3">
            <small class="text-muted">
              <span class="text-danger">*</span> Required dimensions: Video must between (510×210 to 1100×400) pixels.
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
@endsection
