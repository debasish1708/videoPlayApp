<div class="d-flex align-items-center gap-2">
  <!-- View Icon -->
  @if (\App\Enums\CustomerSupportStatus::PENDING->value == $query->status)
    <button type="button" class="p-0 border-0 bg-transparent flex-shrink-0" onclick="handleStatusButtons('{{ route('customer-support.reply', $query->id) }}', 'reply')"
      title="Reply">
      <i class="fas fa-reply text-primary"></i>
    </button>
  @endif

  <!-- Delete Icon -->
  <button type="button" class="p-1 border-0 bg-transparent flex-shrink-0"
    onclick="handleDeleteQuery('{{ route('customer-support.destroy', $query->id) }}')" title="DELETE">
    <i class="fas fa-trash-alt text-danger"></i>
  </button>

</div>
