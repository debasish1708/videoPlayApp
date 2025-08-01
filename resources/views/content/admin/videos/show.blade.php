@extends('layouts.layoutMaster')

@section('title', 'Video Details')

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

@section('content')
  <div class="row g-4 align-items-stretch">
    <!-- Video Preview -->
    <div class="col-lg-4">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-white border-bottom-0 pb-0">
      <h5 class="card-title mb-0">
        <i class="bi bi-play-circle me-2 text-primary"></i>Video Preview
      </h5>
      </div>
      <div class="card-body p-0">
      @if($video->iframe_url)
      <div class="position-relative">
      <div class="ratio ratio-16x9">
      <iframe src="{{ $video->iframe_url }}" loading="lazy" class="rounded-top"
        allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen="true">
      </iframe>
      </div>
      </div>
      <div class="p-3 bg-light rounded-bottom">
      <h6 class="fw-bold text-dark mb-2">{{ $video->title }}</h6>
      <p class="text-muted small mb-2">{{ $video->description }}</p>
      <div class="d-flex flex-wrap gap-2">
      <span class="badge bg-secondary">
        <i class="bi bi-calendar3 me-1"></i>{{ $video->created_at->format('M d, Y') }}
      </span>
      <span class="badge bg-info">
        <i class="bi bi-tag me-1"></i>{{ $access_type->name ?? 'Uncategorized' }}
      </span>
      <span class="badge bg-info">
        <i class="bi bi-tag me-1"></i>{{ $genre->name ?? 'UnGenre' }}
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

    <!-- Video Details -->
    <div class="col-lg-8">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-header bg-white border-bottom-0 d-flex align-items-center justify-content-between">
      <div>
        <h4 class="card-title mb-0 fw-bold">
        <i class="bi bi-info-circle me-2 text-success"></i>Video Details
        </h4>
      </div>
      <div>
        <div class="mt-auto">
           <button type="submit" id="submit-video" class="btn btn-card-block-custom btn-primary" onclick="window.location='{{ route('videos.edit', $video->id) }}'">Edit</button>
        </div>
      </div>
      </div>
      <div class="card-body">
      <dl class="row mb-0 fs-5">
        <dt class="col-sm-3 text-muted">Title</dt>
        <dd class="col-sm-9 fw-semibold">{{ $video->title }}</dd>

        <dt class="col-sm-3 text-muted">Description</dt>
        <dd class="col-sm-9">{{ $video->description }}</dd>

        <dt class="col-sm-3 text-muted">AccessType</dt>
        <dd class="col-sm-9">
        <span class="badge bg-info fs-6">{{ $access_type->name ?? 'Uncategorized' }}</span>
        </dd>

        <dt class="col-sm-3 text-muted">Genre</dt>
        <dd class="col-sm-9">
        <span class="badge bg-info fs-6">{{ $genre->name ?? 'UnGenre' }}</span>
        </dd>

        <dt class="col-sm-3 text-muted">Tags</dt>
        <dd class="col-sm-9">
        @if($video->tags)
        @foreach(json_decode($video->tags) as $tag)
        <span class="badge bg-primary me-1 mb-1">{{ is_array($tag) ? ($tag['value'] ?? $tag[0]) : $tag }}</span>
        @endforeach
      @else
      <span class="text-muted">No tags</span>
      @endif
        </dd>

        <dt class="col-sm-3 text-muted">Created At</dt>
        <dd class="col-sm-9">{{ $video->created_at->format('M d, Y') }}</dd>
      </dl>
      </div>
    </div>
    </div>
  </div>
@endsection
