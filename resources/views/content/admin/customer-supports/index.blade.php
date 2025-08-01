@extends('layouts.layoutMaster')

@section('title', 'Banners')

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
      ajax: '{!! route('customer-support.index') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'query', name: 'query' },
        {
          data: 'status',
          name: 'Status',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            let typeMap = {
              'pending': { title: 'Pending', class: 'bg-label-warning' },
              'resolved': { title: 'Resolved', class: 'bg-label-success' }
            };

            let key = (data || '').toString().toLowerCase().trim();
            let typeObj = typeMap[key];

            return typeObj
              ? `<span class="badge ${typeObj.class}">${typeObj.title}</span>`
              : `<span class="badge bg-label-secondary">${data ?? 'Unknown'}</span>`;
          }
        },
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
        // {
        //   text: '<i class="ti ti-plus me-1"></i> Add Banner',
        //   className: 'btn btn-primary create-post-btn bg-gradient-primary-custom',
        //   action: function(){
        //     window.location.href = '{{ route('banners.create') }}'
        //   }
        // }
      ],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),
    });
    $('.dataTables_filter input').attr('placeholder', 'Search Query');
  });
</script>

<script>
  function handleDeleteQuery(url){
    Swal.fire({
      text: 'Are you sure',
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

  function handleStatusButtons(url, type){
    // let url=$(this).data('url');
    let message=type=='reply' ? 'Please Resolve the Query' : 'N/A';

    //let url=$(this).data('url');
    let swalOptions = {
      text: message,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      customClass: {
        confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
        cancelButton: 'btn btn-label-secondary waves-effect waves-light'
      },
      buttonsStyling: false
    };

    if(type == 'reply'){
      swalOptions.title = 'Query';
      swalOptions.input = 'text';
      swalOptions.inputAttributes = { autocapitalize: 'off' };
      swalOptions.inputValidator = (value) => {
        if (!value) {
          return 'You need to provide the Solution!';
        }
      };
    }

    Swal.fire(swalOptions).then((result) => {
      if (result.isConfirmed) {
        let ajaxData = {
          _token: "{{ csrf_token() }}",
          response: result.value
        };

        $.ajax({
          method: 'PUT',
          url: url,
          data: ajaxData,
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
@endsection

@section('content')
<div class="card">
  <div class="card-datatable table-responsive">
    <table class="datatables-users table">
      <thead class="border-top">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Query</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection
