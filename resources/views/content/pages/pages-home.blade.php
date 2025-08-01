@php
$configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

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
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@vite(['resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

@section('title', 'Home')

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var table = $('.datatables-users').DataTable({
      processing: true,
      serverSide: true,
      ajax: '{!! route('dashboard') !!}',
      columns: [
        { data: 'DT_RowIndex', orderable: false, searchable: false, title: 'ID' },
        { data: 'title', name: 'title' },
        { data: 'web_url', name: 'video_url',searchable: false, orderable: false  },
        { data: 'views', name: 'views' },
        {
          data: 'access_type',
          name: 'Access Type',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            let typeMap = {
              'free': { title: 'Free', class: 'bg-label-success' },
              'paid': { title: 'Paid', class: 'bg-label-danger' },
              'ad supported': { title: 'Ad Supported', class: 'bg-label-warning' }
            };

            let key = (data || '').toString().toLowerCase().trim();
            let typeObj = typeMap[key];

            return typeObj
              ? `<span class="badge ${typeObj.class}">${typeObj.title}</span>`
              : `<span class="badge bg-label-secondary">${data ?? 'Unknown'}</span>`;
          }
        },
        { data: 'created_at', name: 'created_at' },
      ],
      dom:
        '<"row"' +
          '<"col-md-2"<"ms-n2"l>>' +
          '<"col-md-10 d-flex justify-content-end align-items-center flex-wrap gap-2"' +
            '<"me-2"f>' + // search box
            '<"video_status">' + // status filter
            'B' +
          '>' +
        '>t' +
        '<"row"' +
          '<"col-sm-12 col-md-6"i>' +
          '<"col-sm-12 col-md-6"p>' +
        '>',
      buttons: [],
      drawCallback: () => $('[data-bs-toggle="tooltip"]').tooltip(),

      initComplete: function () {
        this.api()
          .columns(4) // Change from 3 to 4 because 'Access Type' is column index 4
          .every(function () {
            var column = this;
            var select = $(
              '<select id="accessTypeFilter" class="form-select"><option value="">Filter by Access Type</option></select>'
            )
              .appendTo('.video_status')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '' + val + '' : '', true, false).draw();
              });

            $.get('/access-types', function (data) {
              data.forEach(function (d) {
                select.append('<option value="' + d + '">' + d + '</option>');
              });
            });
          });
      }

    });

    $('.dataTables_filter input').attr('placeholder', 'Search Videos');
  });
</script>
@endsection

@section('content')
  <div class="row g-4">
    @php
      $metrics = [
        ['title' => 'Total Views', 'subtitle' => 'Over 30 Days', 'value' => $videoDetails['totalViewCount'] . ' Views', 'bg' => 'success'],
        ['title' => 'Total Watchtime', 'subtitle' => 'Over 30 Days', 'value' => $videoDetails['totalWatchTime'] . ' Seconds', 'bg' => 'success'],
        ['title' => 'Total Views', 'subtitle' => 'Over 7 Days', 'value' => $videoDetailsOver7Days['totalViewCount'] . ' Views', 'bg' => 'info'],
        ['title' => 'Total Watchtime', 'subtitle' => 'Over 7 Days', 'value' => $videoDetailsOver7Days['totalWatchTime'] . ' Seconds', 'bg' => 'info'],
      ];
    @endphp

    @foreach($metrics as $metric)
    <div class="col-xl-3 col-md-6 col-12">
      <div class="card h-100">
        <div class="card-body">
          <div class="badge p-2 bg-label-{{ $metric['bg'] }} mb-3 rounded">
            @if ($metric['title'] === 'Total Views')
              <i class="ti ti-eye ti-28px"></i>
            @else
              <i class="ti ti-chart-bar ti-28px"></i>
            @endif
          </div>
          <h5 class="card-title mb-1">{{ $metric['title'] }}</h5>
          <p class="card-subtitle">{{ $metric['subtitle'] }}</p>
          <p class="text-heading mb-0 mt-1">{{ $metric['value'] }}</p>
        </div>
      </div>
    </div>
    @endforeach
  </div>

<div class="row g-4  py-5">
  <!-- Sales By Country -->
  <div class="col-xxl-4 col-md-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <div class="card-title mb-0">
          <h5 class="mb-1">Views By Country</h5>
          <p class="card-subtitle">Monthly Views Overview</p>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
            @foreach($totalViewCountWithCountry as $countryName => $views)
                @php
                    $code = $countryFlagCodes[$countryName] ?? 'xx';
                    $watchTime = $totalWatchTimeWithCountry[$countryName] ?? 0;
                @endphp

                <li class="d-flex align-items-center mb-4">
                    <div class="avatar flex-shrink-0 me-4">
                        <i class="fis fi fi-{{ $code }} rounded-circle fs-2"></i>
                    </div>
                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                        <div class="me-2">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-1">{{ number_format($views) }} Views</h6>
                            </div>
                            <small class="text-body">{{ $countryName }}</small>
                        </div>
                        <div class="user-progress">
                            <p class="text-primary fw-medium mb-0 d-flex align-items-center gap-1">
                                <i class='ti ti-clock'></i>
                                {{ $watchTime }}s Watch Time
                            </p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
      </div>
    </div>
  </div>
   <!-- Invoice table -->
  <div class="col-xxl-8">
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="table-sm datatables-users table border-top">
          <thead>
            <tr>
              <th>ID</th>
              <th>Title</th>
              <th>Video URL</th>
              <th>Views</th>
              <th>Type</th>
              <th>Created At</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection
