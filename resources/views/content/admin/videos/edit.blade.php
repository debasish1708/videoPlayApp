@extends('layouts.layoutMaster')

@section('title', 'Edit Video - Forms')

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

              const form = document.getElementById('video-edit-form');
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
              const form = document.getElementById('video-edit-form');
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

      const form = document.getElementById('video-edit-form');
      if (form) {
        form.addEventListener('submit', function (e) {
          // Clear all validation errors
          document.querySelectorAll('.validation-error').forEach(function (el) {
            el.textContent = '';
          });

          let valid = true;
          let firstInvalidField = null;
          const title = form.querySelector('input[name="title"]');
          const description = form.querySelector('textarea[name="description"]');
          const accessType = form.querySelector('select[name="access_type_id"]');
          const genre = form.querySelector('select[name="genre_id"]');

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
          if (!accessType.value.trim()) {
            valid = false;
            document.getElementById('access_type-error').textContent = 'Access Type is required.';
            if (!firstInvalidField) firstInvalidField = accessType;
          }
          if (!genre.value.trim()) {
            valid = false;
            document.getElementById('genre-error').textContent = 'Genre is required.';
            if (!firstInvalidField) firstInvalidField = genre;
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
            $.blockUI({
            message: '<div class="d-flex justify-content-center"><p class="mb-0">Please wait...</p> <div class="sk-wave m-0"><div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div> <div class="sk-rect sk-wave-rect"></div></div> </div>',
              css: {
                backgroundColor: 'transparent',
                color: '#fff',
                border: '0'
              },
              overlayCSS: {
                opacity: 0.5
              }
            });
          }
        });
      }
    });
</script>
@endsection

@section('content')
<form id="video-edit-form" action="{{ route('videos.update', $video->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  <input type="hidden" name="type" value="post">

  <div class="row g-4" id="card-block">
    <!-- First Div: Video Preview -->
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-play-circle me-2"></i>Video Preview
          </h5>
        </div>
        <div class="card-body p-0">
          @if($video->iframe_url)
            <div class="position-relative">
              <div class="ratio ratio-16x9">
                <iframe
                  src="{{ $video->iframe_url }}"
                  loading="lazy"
                  class="rounded-top"
                  allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                  allowfullscreen="true">
                </iframe>
              </div>
            </div>
            <div class="p-3 bg-light">
              <h6 class="fw-bold text-dark mb-2">{{ $video->title }}</h6>
              <p class="text-muted small mb-2">{{ $video->description }}</p>
              <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-secondary">
                  <i class="bi bi-calendar3 me-1"></i>{{ $video->created_at->format('M d, Y') }}
                </span>
                <span class="badge bg-info">
                  <i class="bi bi-tag me-1"></i>{{ $video->accessType->name ?? 'Uncategorized' }}
                </span>
              </div>
            </div>
          @else
            <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
              <div class="text-center text-muted">
                <i class="bi bi-video-slash fs-1 mb-3"></i>
                <p class="mb-0">No video available</p>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Second Div: Edit Form -->
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-pencil-square me-2"></i>Edit Video Details
          </h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <!-- Title Field -->
            <div class="col-12">
              <label for="title" class="form-label fw-bold">
                Title <span class="text-danger">*</span>
              </label>
              <input type="text" name="title" value="{{ old('title', $video->title) }}"
              class="form-control form-control-lg" id="title" placeholder="Enter video title..." />
              <div class="text-danger validation-error small" id="title-error"></div>
              @error('title')
                <p class="text-danger small">{{ $errors->first('title') }}</p>
              @enderror
            </div>

            <!-- Description Field -->
            <div class="col-12">
              <label for="description" class="form-label fw-bold">
                Description <span class="text-danger">*</span>
              </label>
              <textarea class="form-control" name="description" id="description" rows="4"
              placeholder="Enter video description...">{{ old('description', $video->description) }}</textarea>
              <div class="text-danger validation-error small" id="description-error"></div>
              @error('description')
                <p class="text-danger small">{{ $errors->first('description') }}</p>
              @enderror
            </div>

            <!-- Category Field -->
            <div class="col-md-6">
              <label for="category_id" class="form-label fw-bold">
                AccessType <span class="text-danger">*</span>
              </label>
              <select name="access_type_id" id="access_type_id" class="select2 form-select">
                <option value="">Select AccessType</option>
                @foreach($access_types as $access_type)
                  <option value="{{ $access_type->id }}" {{ old('access_type_id', $video->access_type_id) == $access_type->id ? 'selected' : '' }}>
                    {{ $access_type->name }}
                  </option>
                @endforeach
              </select>
              <div class="text-danger validation-error small" id="access_type-error"></div>
              @error('access_type_id')
                <p class="text-danger small">{{ $errors->first('access_type_id') }}</p>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="category_id" class="form-label fw-bold">
                Genre <span class="text-danger">*</span>
              </label>
              <select name="genre_id" id="genre_id" class="select2 form-select">
                <option value="">Select Genre</option>
                @foreach($genres as $genre)
                  <option value="{{ $genre->id }}" {{ old('genre_id', $video->genre_id) == $genre->id ? 'selected' : '' }}>
                    {{ $genre->name }}
                  </option>
                @endforeach
              </select>
              <div class="text-danger validation-error small" id="access_type-error"></div>
              @error('genre_id')
                <p class="text-danger small">{{ $errors->first('genre_id') }}</p>
              @enderror
            </div>

            <!-- Tags Field -->
            <div class="col-md-6">
              <label for="TagifyBasic" class="form-label fw-bold">Tags</label>
              <input id="TagifyBasic" class="form-control" type="text" name="tags"
              value="{{ old('tags', $video->tags) }}" placeholder="Enter tags..." maxlength="150" minlength="3" />
              @error('tags')
                <p class="text-danger small">{{ $errors->first('tags') }}</p>
              @enderror
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Third Div: Replace Video -->
    <div class="col-lg-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header">
          <h5 class="card-title mb-0">
            <i class="bi bi-cloud-arrow-up me-2"></i>Replace Video
          </h5>
        </div>
        <div class="card-body">
          <div class="text-center mb-3">
            <i class="bi bi-info-circle text-primary fs-1 mb-3"></i>
            <p class="text-muted small">Upload a new video to replace the current one</p>
          </div>

          <div class="dropzone border rounded p-3 text-center" id="dropzone-basic">
            <div id="upload-warning" class="d-none text-danger text-center small mt-2" role="alert"></div>
            <div class="dz-message text-center py-3">
              <i class="bi bi-cloud-arrow-up fs-1 mb-2 text-primary"></i>
              <strong class="fs-6 d-block">Click to upload</strong>
              <p class="text-muted mb-0 small">
                Only MP4 files. Max size 200MB.
              </p>
            </div>
          </div>

          <div class="text-danger validation-error small" id="video-error"></div>
          @error('post_video')
            <p class="text-danger small">{{ $errors->first('post_video') }}</p>
          @enderror

          <div class="text-center mt-3">
            <small class="text-muted">
              <i class="bi bi-lightbulb me-1"></i>
              Leave empty to keep current video
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons Row -->
    <div class="col-12">
      <div class="d-flex justify-content-end">
        <button type="submit" id="submit-video" class="btn btn-card-block-custom btn-primary">Update</button>
      </div>
    </div>
  </div>
</form>
@endsection
