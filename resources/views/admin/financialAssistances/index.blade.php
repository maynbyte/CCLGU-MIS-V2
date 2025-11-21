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
      <style>
        /* Default DataTables filter alignment (right).
           Removed custom left float so the search/filter returns to the native right side.
           Keep buttons spacing tight. */
        .dataTables_wrapper .dt-buttons { margin-left: 10px; }
      </style>
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
<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" role="dialog" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkEditModalLabel">Edit Selected – Latest Financial Assistance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="bulk_scheduled_fa">Payout Schedule</label>
          <input type="date" id="bulk_scheduled_fa" class="form-control">
          <small class="form-text text-muted">Leave blank to keep existing schedule. If Status = Claimed, schedule will be cleared.</small>
        </div>
        <div class="form-group">
          <label for="bulk_status">Status</label>
          <select id="bulk_status" class="form-control">
            <option value="">— No change —</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Pending">Pending</option>
            <option value="Claimed">Claimed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" id="bulkApplyBtn" class="btn btn-primary">Apply Changes</button>
      </div>
    </div>
  </div>
  </div>
@endsection

@section('scripts')
@parent
<script>
$(function () {
  // Rebuild buttons so Edit/Delete are beside Deselect all
  let dtButtons = []

  // Select all
  dtButtons.push({
    extend: 'selectAll',
    text: '<i class="fas fa-check-double"></i> Select all',
    className: 'btn btn-outline-secondary btn-sm'
  });
  // Deselect all
  dtButtons.push({
    extend: 'selectNone',
    text: '<i class="fas fa-ban"></i> Deselect all',
    className: 'btn btn-outline-secondary btn-sm'
  });
  // Edit Selected (right beside Deselect all)
  dtButtons.push({
    extend: 'selected',
    text: '<i class="fas fa-edit"></i> Edit Selected',
    className: 'btn btn-primary btn-sm',
    action: function (e, dt) { $('#bulkEditModal').modal('show'); }
  });
  // Delete Selected (right beside Edit Selected)
  @can('directory_delete')
  dtButtons.push({
    extend: 'selected',
    text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
    className: 'btn btn-danger btn-sm',
    action: function (e, dt) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
      if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.directories.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
          .done(function(){ location.reload() })
      }
    }
  });
  @endcan
  // Remaining utilities
  dtButtons.push({ extend: 'copy',  text: '<i class="fas fa-copy"></i> Copy',  className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'csv',   text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-outline-secondary btn-sm' });


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

function formatDateOnly(value) {
  if (!value) return '<span class="text-muted">—</span>';

  let d;
  if (value instanceof Date) {
    d = value;
  } else if (typeof value === 'string') {
    const s = value.trim();
    if (/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}(:\d{2})?$/.test(s)) {
      d = new Date(s.replace(' ', 'T'));
    } else {
      d = new Date(s);
    }
  } else {
    return '<span class="text-muted">—</span>';
  }

  if (isNaN(d)) return '<span class="text-muted">—</span>';

  return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
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
    select: { style: 'multi', selector: 'td:not(:last-child)' },
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

    // Latest FA (created_at from latest FA record)
{
  data: 'latest_fa_created_at',
  name: 'fa_last.created_at',
  render: function (data, type, row) {
    if (type === 'display' || type === 'filter') {
      return formatDateOnly(data);
    }
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
  // Apply bulk changes
  $('#bulkApplyBtn').on('click', function(){
      var ids = $.map(table.rows({ selected: true }).data(), function (entry) { return entry.id });
      if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return; }

      var payload = {
        ids: ids,
        scheduled_fa: $('#bulk_scheduled_fa').val() || null,
        status: $('#bulk_status').val() || null
      };

      $.ajax({
        headers: {'x-csrf-token': _token},
        method: 'POST',
        url: "{{ route('admin.financial-assistances.bulkLatestUpdate') }}",
        data: payload
      }).done(function(){
        $('#bulkEditModal').modal('hide');
        $('#bulk_scheduled_fa').val('');
        $('#bulk_status').val('');
        table.ajax.reload(null, false);
      }).fail(function(xhr){
        alert((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed');
      });
  });
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(){
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
});
</script>
@endsection
