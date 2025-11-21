@extends('layouts.admin')
@section('content')
@can('directory_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.directories.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.directory.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Directory', 'route' => 'admin.directories.parseCsvImport'])
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.directory.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Directory">
            <thead>
                <tr>   
                    <th>No.</th> {{-- Count column --}}
                    <th>{{ trans('cruds.directory.fields.profile_picture') }}</th>
                    <th>Last Name + Suffix</th>
                    <th>{{ trans('cruds.directory.fields.first_name') }}</th>
                    <th>{{ trans('cruds.directory.fields.middle_name') }}</th>
                    <th>{{ trans('cruds.directory.fields.barangay') }}</th>
                    <th>{{ trans('cruds.directory.fields.comelec_status') }}</th>
                    {{-- NEW: Latest FA + Status --}}
                    <th>Latest FA Record</th>
                    <th>Payout Schedule</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Financial Assistance</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)

  @can('directory_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}';
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.directories.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) {
          return entry.id
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')
        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
  @endcan


  // Put this once (e.g., in your ccmis-directory.js)
function formatDateTime12h(value) {
  if (!value) return '<span class="text-muted">—</span>';

  let d;
  if (value instanceof Date) {
    d = value;
  } else if (typeof value === 'string') {
    const s = value.trim();
    // Handle "YYYY-MM-DD HH:mm:ss" (Safari-safe)
    if (/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}(:\d{2})?$/.test(s)) {
      d = new Date(s.replace(' ', 'T'));
    } else {
      d = new Date(s);
    }
  } else {
    return '<span class="text-muted">—</span>';
  }

  if (isNaN(d)) return '<span class="text-muted">—</span>';

  const datePart = d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  const timePart = d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
  return `${datePart} ${timePart}`; // e.g., "June 10, 1992 3:15 PM"
}


  function toTitleCase(str) {
    if (!str) return '';
    return str.toLowerCase().replace(/\b[\p{L}’']+/gu, function(w){
      return w.charAt(0).toUpperCase() + w.slice(1);
    });
  }

  function statusBadge(raw) {
    if (!raw) return '<span class="text-muted">—</span>';
    const val = String(raw).toLowerCase();
    if (val === 'claimed') {
      return '<span class="badge badge-success">Claimed</span>';
    }
    if (val === 'cancelled' || val === 'canceled') {
      return '<span class="badge badge-danger">Cancelled</span>';
    }
    // Treat everything else (e.g. Pending) as Ongoing
    return '<span class="badge badge-warning">Ongoing</span>';
  }

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.directories.index') }}",
    columns: [
  
      // Count / Row number (continuous per page)
      {
        data: null,
        name: '_row',
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
          return meta.row + meta.settings._iDisplayStart + 1;
        }
      },

      { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false },

    // 1) Last name + Suffix (display both, sort by last_name only)
{
  data: null,
  name: 'last_name',              // server-side sort/search by last_name
  render: function (data, type, row) {
    if (type === 'display' || type === 'filter') {
      var ln = (row.last_name || '').trim();
      var sx = (row.suffix || '').trim();
      var out = [ln, sx].filter(Boolean).join(' ').trim();  // e.g., "Dela Cruz Jr"
      return toTitleCase(out);
    }
    // for sort/type !== 'display', return raw last_name so ordering works
    return row.last_name || '';
  },
  defaultContent: ''
},

// 2) First name
{
  data: 'first_name',
  name: 'first_name',
  render: function (v, type) {
    if (type === 'display' || type === 'filter') return toTitleCase(v || '');
    return v || '';
  },
  defaultContent: ''
},

// 3) Middle name
{
  data: 'middle_name',
  name: 'middle_name',
  render: function (v, type) {
    if (type === 'display' || type === 'filter') return toTitleCase(v || '');
    return v || '';
  },
  defaultContent: ''
},

      { data: 'barangay_barangay_name', name: 'barangay.barangay_name' },
      { data: 'comelec_status', name: 'comelec_status' },

    // NEW: Latest FA (created_at)
{
  data: 'created_at',
  name: 'created_at',
  render: function (data, type, row) {
    if (type === 'display' || type === 'filter') {
      return formatDateTime12h(data);
    }
    // For sorting, return a numeric timestamp
    const ts = Date.parse(typeof data === 'string' ? data.replace(' ', 'T') : data);
    return isNaN(ts) ? '' : ts;
  }
},

    // NEW: Payout Schedule (scheduled_fa)
    {
      data: 'latest_fa_scheduled_fa',
      // Use actual joined column name so Yajra doesn't prefix with directories.
      // Prevents SQL: Unknown column directories.latest_fa_scheduled_fa in where clause
      name: 'fa_last.scheduled_fa',
      render: function (data, type, row) {
        if (type === 'display' || type === 'filter') {
          return data ? (new Date(String(data).replace(' ', 'T'))).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : '<span class="text-muted">—</span>';
        }
        const ts = Date.parse(typeof data === 'string' ? data.replace(' ', 'T') : data);
        return isNaN(ts) ? '' : ts;
      }
    },

  // Latest FA status as a colored badge
{
  data: 'latest_fa_status',
  name: 'latest_fa_status',
  orderable: false,       // set to true if you want to sort by raw status text
  searchable: false,      // set to true if you want to search by status text
  render: function (status, type) {
    if (type !== 'display') return status || '';

    var s = (status || '').toString().trim().toLowerCase();
    var map = {
      'pending':  { label: 'Pending',  cls: 'badge badge-warning',  // orange/yellow
                    // uncomment to force orange shade:
                    // style: 'style="background-color:#fd7e14;color:#fff;"'
                  },
      'cancelled':{ label: 'Cancelled',cls: 'badge badge-danger' }, // red
      'claimed':  { label: 'Claimed',  cls: 'badge badge-success' } // green
      // 'on-going': { label: 'On-going', cls: 'badge badge-warning' } // (optional)
    };

    var m = map[s] || { label: status || '—', cls: 'badge badge-secondary' };

    // If you enabled the custom orange style above, include it here:
    // return `<span class="${m.cls}" ${m.style || ''}>${m.label}</span>`;

    return `<span class="${m.cls}">${m.label}</span>`;
  }
},

 { data: 'remarks', name: 'remarks', orderable: true, searchable: true },
      // FA Add link
      {
        data: 'id',
        name: 'fa_link',
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
          let url = "{{ route('admin.financial-assistances.create') }}?directory_id=" + data;
          return '<a class="btn btn-sm btn-primary" href="' + url + '">View</a>';
        }
      }
    ],
    // Sort by Name (index 3) ascending
    orderCellsTop: true,
    order: [[ 7, 'desc' ]],
    pageLength: 10,
  };

  let table = $('.datatable-Directory').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(){
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
});
</script>
@endsection
