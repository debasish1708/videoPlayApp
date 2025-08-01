@extends('layouts.layoutMaster')

@section('title', 'Banner Details')

<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/toastr/toastr.scss',
    'resources/assets/vendor/libs/animate-css/animate.scss',
    'resources/assets/vendor/libs/tagify/tagify.scss',
    'resources/assets/vendor/libs/bootstrap-select/bootstrap-select.scss',
    'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  ])
@endsection

@section('content')
  <div class="col-md-9">
    <div class="card h-100">
    <h5 class="card-header">Banner</h5>
    <div class="card-body d-flex flex-column">
      <div class="mb-3">
      <label for="title" class="form-label">Title:</label>
      <span class="text-danger">*</span>
      <input type="text" name="title" value="{{ $banner->title }}" class="form-control" id="title"
        placeholder="Enter Title" readonly />
      @error('title')
        <p class="text-danger">{{ $errors->first('title') }}</p>
      @enderror
      </div>
      <div class="mb-3 flex-grow-1">
      <label for="description" class="form-label">Description:</label>
      <span class="text-danger">*</span>
      <textarea class="form-control" name="description" id="description" style="min-height: 150px"
        readonly>{{ $banner->description }}</textarea>
      @error('description')
        <p class="text-danger">{{ $errors->first('description') }}</p>
      @enderror
      </div>
      <div class="mb-3">
        <div class="mb-3">
           <label for="url" class="form-label">URL:</label>
           <input type="text" name="url" value="{{ $banner->url }}" class="form-control" id="url" readonly />
        </div>
        <img src="{{ $banner->image }}" alt="Banner Image" class="img-fluid rounded shadow"
            style="max-height: 300px;" />
        </div>
      <div class="mt-auto">
      <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-primary">Edit</a>
      </div>
    </div>
    </div>
  </div>
@endsection
