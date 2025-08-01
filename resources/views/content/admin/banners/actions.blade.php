<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  <button type="button" class="p-0 border-0 bg-transparent flex-shrink-0" onclick="window.location.href='{{ route('banners.show', $banner->id) }}'"
    title="View">
    <i class="fas fa-eye text-info"></i>
  </button>

  <!-- Edit Icon -->
  <button type="button" class="p-0 border-0 bg-transparent flex-shrink-0"
    onclick="window.location.href='{{ route('banners.edit', $banner->id) }}'" title="Edit">
    <i class="fas fa-pencil-alt text-primary"></i>
  </button>

  <!-- Delete Icon -->
  <button type="button" class="p-0 border-0 bg-transparent flex-shrink-0"
    onclick="handleDeleteBanner('{{ route('banners.destroy', $banner->id) }}')" title="DELETE">
    <i class="fas fa-trash-alt text-danger"></i>
  </button>

  <!-- Switch (can be at start or end) -->
  {{-- <label class="switch switch-success flex-shrink-1" title="publish">
    <input type="checkbox" class="switch-input"
      {{ $banner->status == App\Enums\VideoStatus::PUBLISHED->value ? 'checked' : '' }}
      onchange="handlePublishBanner(this, '{{ route('banners.publish', $banner->id) }}')" />
    <span class="switch-toggle-slider">
      <span class="switch-on">
        <i class="ti ti-check"></i>
      </span>
      <span class="switch-off">
        <i class="ti ti-x"></i>
      </span>
    </span>
  </label> --}}

</div>
