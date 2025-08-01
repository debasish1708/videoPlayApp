@extends('layouts.layoutMaster')

@section('title', 'Videos')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss'
])
@vite([
  'resources/assets/vendor/libs/toastr/toastr.scss',
  'resources/assets/vendor/libs/animate-css/animate.scss'
])
 @vite([
  'resources/assets/vendor/libs/animate-css/animate.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/cleavejs/cleave.js',
  'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
 @vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('videos.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description' },
        { data: 'web_url', name: 'video_url' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false },
      ],
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10"' +
            '<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0"' +
              '<"me-2"f>B' +
            '>' +
          '>' +
        '>t' +
        '<"row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
        '>',
      buttons: [
        {
          text: '<i class="ti ti-plus me-1"></i> Add Video',
          className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
          action: function(){
            window.location.href = '{{ route('videos.create') }}'
          }
        }
      ],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
    });
    $('.dataTables_filter input').attr('placeholder', 'Search Videos');
  });
</script>

<script>
  function handleDeleteVideo(url){
    Swal.fire({
      text: 'Are you sure to Delete this video?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          method: 'DELETE',
          url: url,
          data: {
            _token: "{{ csrf_token() }}"
          },
          success: function (result) {
            Swal.fire({
              icon: 'success',
              title:'Success',
              text: result.message,
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
              },
              buttonsStyling: false
            });
            $('.datatables-users').DataTable().ajax.reload(null, false);
          },
          error: function (error) {
            Swal.fire({
              icon: 'error',
              title: error.responseJSON.message,
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
              },
              buttonsStyling: false
            });
          }
        })
      }
    })
  }
</script>

<script>
  function handlePublishVideo(checkbox, url){
    // Determine action based on checked state
    const isPublishing = checkbox.checked;
    const actionText = isPublishing ? 'publish' : 'unpublish';
    const confirmText = `Are you sure you want to ${actionText} this video?`;

    // Revert the checkbox immediately (will be set again on success)
    checkbox.checked = !isPublishing;

    Swal.fire({
      text: confirmText,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      cancelButtonText: 'Cancel',
      customClass: {
        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          method: 'POST',
          url: url,
          data: {
            _token: "{{ csrf_token() }}",
            status: isPublishing ? 1 : 0
          },
          success: function (result) {
            Swal.fire({
              icon: 'success',
              title:'Success',
              text: result.message,
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
              },
              buttonsStyling: false
            });
            $('.datatables-users').DataTable().ajax.reload(null, false);
          },
          error: function (error) {
            Swal.fire({
              icon: 'error',
              title: error.responseJSON?.message || 'Error',
              showConfirmButton: false,
              timer: 1500,
              customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
              },
              buttonsStyling: false
            });
          }
        });
      }
    });
  }
</script>
@endsection

@section('content')
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Description</th>
          <th>Video URL</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection
