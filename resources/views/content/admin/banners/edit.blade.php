@extends('layouts.layoutMaster')

@section('title', 'Banner Edit - Forms')

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
      <img data-dz-thumbnail style="max-width:100%;max-height:120px;border-radius:8px;" />
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
    setTimeout(() => warningBox.classList.add("d-none"), 4000);
    }

    function hideWarning() {
    document.getElementById("upload-warning").classList.add("d-none");
    }

    document.addEventListener('DOMContentLoaded', function () {
    const dropzoneBasic = document.querySelector('#dropzone-basic');

    if (dropzoneBasic) {
      const myDropzone = new Dropzone(dropzoneBasic, {
      url: '#',
      paramName: 'file',
      acceptedFiles: 'image/jpeg,image/jpg,image/png',
      maxFilesize: 1, // MB
      maxFiles: 1,
      parallelUploads: 1,
      autoProcessQueue: true,
      addRemoveLinks: true,
      previewTemplate: previewTemplate,
      init: function () {
        const dz = this;
        // Add previous image if it exists
        if (window.previousImageUrl) {
        const mockFile = { name: "Current Image", size: 12345 };
        dz.emit("addedfile", mockFile);
        dz.emit("thumbnail", mockFile, window.previousImageUrl);
        dz.emit("complete", mockFile);
        dz.files.push(mockFile);
        // Ensure the hidden input exists
        let existingInput = document.querySelector('input[name="existing_image"]');
        if (!existingInput) {
          existingInput = document.createElement('input');
          existingInput.type = 'hidden';
          existingInput.name = 'existing_image';
          existingInput.value = "{{$banner->image}}";
          dz.element.closest('form').appendChild(existingInput);
        }
        }
        dz.on("addedfile", function (file) {
        // Hide the upload message
        dz.element.querySelector('.dz-message').style.display = 'none';
        // Validate and assign file to hidden input
        if (file.size > 1 * 1024 * 1024) {
          dz.removeFile(file);
          const warningBox = document.getElementById("upload-warning");
          warningBox.textContent = "Upload failed: Image size exceeds 1MB.";
          warningBox.classList.remove("d-none");
          setTimeout(() => warningBox.classList.add("d-none"), 3000);
          return;
        }
        const reader = new FileReader();
        reader.onload = function (event) {
          const img = new Image();
          img.onload = function () {
          const aspect = img.width / img.height;
          if (Math.abs(aspect - 16 / 9) > 0.02) {
            dz.removeFile(file);
            const warningBox = document.getElementById("upload-warning");
            warningBox.textContent = "Upload failed: Image must have a 16:9 aspect ratio.";
            warningBox.classList.remove("d-none");
            setTimeout(() => warningBox.classList.add("d-none"), 3000);
            return;
          }
          // Assign file to hidden input
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          document.querySelector("input[name='post_image']").files = dataTransfer.files;
          };
          img.src = event.target.result;
        };
        reader.readAsDataURL(file);
        });
        dz.on("removedfile", function (file) {
        // Clear hidden input
        const fileInput = document.querySelector("input[name='post_image']");
        if (fileInput) fileInput.value = '';
        // Show the upload message if no files remain
        if (dz.files.length === 0) {
          dz.element.querySelector('.dz-message').style.display = '';
        }
        // Remove the hidden input if the mock file is removed
        let existingInput = document.querySelector('input[name="existing_image"]');
        if (existingInput) {
          existingInput.remove();
        }
        });
        dz.on("error", function (file, message) {
        const warningBox = document.getElementById("upload-warning");
        warningBox.textContent = "Upload error: " + message;
        warningBox.classList.remove("d-none");
        setTimeout(() => warningBox.classList.add("d-none"), 3000);
        });
      }
      });
    }

    const form = document.getElementById('banner-edit-form');
    if (form) {
      form.addEventListener('submit', function (e) {
      // Clear all validation errors
      document.querySelectorAll('.validation-error').forEach(function (el) {
        el.textContent = '';
      });

      let valid = true;
      let firstInvalidField = null;
      const url = form.querySelector('input[name="url"]');
      const image = form.querySelector('input[name="post_image"]');

      if (!url.value.trim()) {
        valid = false;
        document.getElementById('url-error').textContent = 'URL is required.';
        firstInvalidField = url;
      }
    //   if (!image || !image.files || image.files.length === 0) {
    //     valid = false;
    //     document.getElementById('image-error').textContent = 'Please upload an image file.';
    //     // Dropzone handles focus, so don't set firstInvalidField here
    //   }

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

  <script>
    document.addEventListener('DOMContentLoaded', function () {
    const editBannerBtn = document.querySelector('#edit-banner');
    const form = document.getElementById('banner-edit-form');
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
  <form id="banner-edit-form" action="{{ route('banners.update', $banner->id) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row" id="card-block">
    <input type="hidden" name="type" value="post">
    <!-- Left Card: Create Post -->
    <div class="col-md-9">
      <div class="card h-100">
      <h5 class="card-header">Banner</h5>
      <div class="card-body d-flex flex-column">
        <div class="mb-3">
        <label for="title" class="form-label">Title:</label>
        <input type="text" name="title" value="{{ old('title') ?? $banner->title ?? '' }}" class="form-control"
          id="title" placeholder="Enter Title" />
        <div class="text-danger validation-error" id="title-error"></div>
        @error('title')
      <p class="text-danger">{{ $errors->first('title') }}</p>
      @enderror
        </div>
        <div class="mb-3 flex-grow-1">
        <label for="description" class="form-label">Enter Description:</label>
        <textarea class="form-control" name="description" id="description"
          style="min-height: 150px">{{ old('description') ?? $banner->description ?? '' }}</textarea>
        <div class="text-danger validation-error" id="description-error"></div>
        @error('description')
      <p class="text-danger">{{ $errors->first('description') }}</p>
      @enderror
        </div>
        <div class="mb-3">
        <label for="url" class="form-label">url</label>
        <span class="text-danger">*</span>
        <input type="text" name="url" value="{{ old('url') ?? $banner->url ?? '' }}" class="form-control" id="url"
          placeholder="Enter URL" />
        <div class="text-danger validation-error" id="url-error"></div>
        @error('url')
      <p class="text-danger">{{ $errors->first('url') }}</p>
      @enderror
        </div>
        <div class="mt-auto">
        <button type="submit" id="edit-banner" class="btn btn-card-block-custom btn-primary">Update</button>
        </div>
      </div>
      </div>
    </div>
    <!-- Right Card: Upload Video -->
    <div class="col-md-3">
      <div class="card h-100">
      <h5 class="card-header text-center">Upload Image</h5>
      <div class="card-body d-flex flex-column justify-content-center">

        @if(isset($banner) && $banner->image)
      <input type="hidden" name="existing_image" value="{{ basename($banner->image) }}">
      <script>
      window.previousImageUrl = "{{ $banner->image }}";
      </script>
      @endif

        <div class="dropzone border rounded p-3 text-center" id="dropzone-basic">
        <input type="file" name="post_image" class="d-none" />
        <div id="upload-warning" class="d-none text-danger text-center small mt-2" role="alert"></div>
        <div class="dz-message text-center py-3">
          <i class="bi bi-cloud-arrow-up fs-1 mb-2"></i>
          <strong class="fs-6 d-block">Click to upload</strong>
          <p class="text-muted mb-0 small">
          Only JPG or PNG images. Max size 1MB. Required resolution: 16:9 (e.g. 1600x900, 1280x720, etc).
          </p>
        </div>
        </div>
        <div class="text-danger validation-error" id="image-error"></div>
        @error('post_image')
      <p class="text-danger">{{ $errors->first('post_image') }}</p>
      @enderror
      </div>
      </div>
    </div>
    </div>
  </form>
@endsection
