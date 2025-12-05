@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <div>
            <h3 class="card-title mb-2"><i class="fas fa-hand-holding-usd text-primary mr-2"></i>{{ trans('cruds.directory.title_singular') }} {{ trans('global.list') }}</h3>
        </div>

        <div class="card-tools ml-auto">
            @can('directory_create')
            <div class="btn btn-sm" role="group" aria-label="Directory actions">
              <a class="btn btn-success" href="{{ route('admin.directories.create') }}" data-toggle="tooltip" title="Add new directory">
                <i class="fas fa-plus"></i>
                <span class="d-none d-sm-inline ml-1">{{ trans('global.add') }} {{ trans('cruds.directory.title_singular') }}</span>
              </a>
            </div>
            @endcan
        </div>
    </div>

    <div class="card-body">
      <style>
        /* Default DataTables filter alignment (right).
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
                    <th>Patient Name</th>
                    <th>{{ trans('cruds.directory.fields.comelec_status') }}</th>
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
@include('admin.financialAssistances.partials.bulk_edit_modal')

@include('admin.financialAssistances.partials.print_payout_modal')

@include('admin.financialAssistances.partials.advanced_search_modal')

@include('admin.financialAssistances.partials.send_sms_modal')
@endsection

@section('scripts')
@parent
<script>
$(function () {
  // Show DataTables errors in the browser console for debugging
  $.fn.dataTable.ext.errMode = 'console';
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
  // Text Selected (right beside Delete Selected)
  dtButtons.push({
    extend: 'selected',
    text: '<i class="fas fa-sms"></i> Text Selected',
    className: 'btn btn-info btn-sm',
    action: function (e, dt) {
      var selectedRows = dt.rows({ selected: true }).data();
      if (selectedRows.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}');
        return;
      }
      
      // Count valid phone numbers
      var validCount = 0;
      var firstScheduledDate = null;
      selectedRows.each(function(row) {
        if (row.contact_no && row.contact_no !== 'N/A' && row.contact_no.trim() !== '') {
          validCount++;
          // Get the first scheduled date for the template
          if (!firstScheduledDate && row.latest_fa_scheduled_fa) {
            firstScheduledDate = row.latest_fa_scheduled_fa;
          }
        }
      });
      
      if (validCount === 0) {
        alert('No valid phone numbers found in selected records.');
        return;
      }
      
      // Update modal info and show
      $('#smsRecipientCount').text(validCount);
      $('#smsTotalSelected').text(selectedRows.length);
      $('#smsMessageText').val('');
      $('#smsCharCount').text('0/160');
      
      // Store the scheduled date in modal for template use
      $('#sendSmsModal').data('scheduled-date', firstScheduledDate);
      
      $('#sendSmsModal').modal('show');
    }
  });
  // Remaining utilities
  dtButtons.push({
    text: '<i class="fas fa-print"></i> Print Payout',
    className: 'btn btn-outline-secondary btn-sm',
    action: function (e, dt) { $('#printPayoutModal').modal('show'); }
  });
  dtButtons.push({ extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-outline-secondary btn-sm' });

  // Advanced Search
  dtButtons.push({
    text: '<i class="fas fa-search"></i> Advanced Search',
    className: 'btn btn-outline-secondary btn-sm',
    action: function () { 
      console.log('Advanced Search button clicked');
      initAdvancedSearch();
      $('#advancedSearchModal').modal('show'); 
    }
  });


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

      {
        data: 'latest_fa_patient_name',
        name: 'fa_last.patient_name',
        render: function (v, type) {
          if (type === 'display' || type === 'filter') {
            return v ? toTitleCase(v) : '<span class="text-muted">—</span>';
          }
          return v || '';
        },
        defaultContent: ''
      },
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

    {
      data: 'latest_fa_scheduled_fa',
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
          return '<a class="btn btn-sm btn-primary px-3" style="min-width:86px;" href="' + url + '">View</a>';
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

  // Print Payout handler
  $('#printPayoutBtn').on('click', function(){
      var payoutDate = $('#print_payout_date').val();
      if (!payoutDate) {
        alert('Please select a payout date');
        return;
      }

      // Get selected assistance types
      var selectedTypes = [];
      $('.assistance-type-checkbox:checked').each(function(){
        selectedTypes.push($(this).val());
      });

      if (selectedTypes.length === 0) {
        alert('Please select at least one type of assistance');
        return;
      }

      // Build URL with date and types
      var printUrl = "{{ route('admin.financial-assistances.printPayout') }}" + "?date=" + payoutDate;
      selectedTypes.forEach(function(type){
        printUrl += "&types[]=" + encodeURIComponent(type);
      });

      window.open(printUrl, '_blank');

      $('#printPayoutModal').modal('hide');
      $('#print_payout_date').val('');
      // Reset checkboxes to unchecked by default
      $('.assistance-type-checkbox').prop('checked', false);
  });

  // Ensure modal opens with all types unchecked (default)
  $('#printPayoutModal').on('show.bs.modal', function () {
    $('.assistance-type-checkbox').prop('checked', false);
  });

  // SMS character counter
  $('#smsMessageText').on('input', function() {
    var length = $(this).val().length;
    $('#smsCharCount').text(length + '/160');
  });

  // SMS template buttons
  $('.sms-template').on('click', function() {
    var template = $(this).data('template');
    var message = $(this).data('message');
    
    // Handle Payout Reminder template with dynamic date
    if (template === 'payout-reminder') {
      var scheduledDate = $('#sendSmsModal').data('scheduled-date');
      
      if (!scheduledDate) {
        alert('No payout schedule found for selected records.');
        return;
      }
      
      // Format date to "Dec. 13, 2025" format
      var date = new Date(scheduledDate.replace(' ', 'T'));
      var months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 
                    'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
      var formattedDate = months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
      
      message = "Buenas dias!\nMula sa Mayor's office. Ang payout ng inyong financial assistance ay sa " + formattedDate + ", 8:00 AM, sa Floating Pavilion, City Hall.\nHuwag pong mag-reply sa mensaheng ito.";
    }
    
    $('#smsMessageText').val(message).trigger('input');
  });

  // Send SMS handler
  $('#sendSmsBtn').on('click', function() {
    var message = $('#smsMessageText').val().trim();
    
    if (!message) {
      alert('Please enter a message');
      return;
    }

    var ids = $.map(table.rows({ selected: true }).data(), function (entry) { return entry.id });
    if (ids.length === 0) {
      alert('{{ trans('global.datatables.zero_selected') }}');
      return;
    }

    // Disable button and show loading
    var $btn = $(this);
    var originalText = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

    $.ajax({
      headers: {'x-csrf-token': _token},
      method: 'POST',
      url: "{{ route('admin.directories.sendBulkSms') }}",
      data: {
        ids: ids,
        message: message
      }
    }).done(function(response) {
      $('#sendSmsModal').modal('hide');
      $('#smsMessageText').val('');
      
      var alertMessage = response.message;
      if (response.warning) {
        alertMessage += '\n\n' + response.warning;
      }
      
      alert(alertMessage);
    }).fail(function(xhr) {
      var errorMessage = 'Failed to send SMS.';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
      }
      alert(errorMessage);
    }).always(function() {
      $btn.prop('disabled', false).html(originalText);
    });
  });

  // Enable Bootstrap tooltips for header actions
  $('[data-toggle="tooltip"]').tooltip();

  // Initialize Advanced Search DataTable (guarded)
  var advInit = false;
  function initAdvancedSearch() {
    if (advInit) { console.log('Advanced Search already initialized'); return; }
    advInit = true;
    console.log('Initializing Advanced Search DataTable');
    try {
        // Ensure table header matches expected columns to avoid aDataSort errors
        var thCount = $('#advancedSearchTable thead th').length;
        console.log('Advanced Search table thead th count:', thCount);
        if (thCount === 0) {
          console.error('Advanced Search table has no header cells, aborting init');
          advInit = false;
          return;
        }

        // If table already initialized, destroy it first
        if ($.fn.dataTable.isDataTable('#advancedSearchTable')) {
          console.log('Destroying existing Advanced Search DataTable instance');
          $('#advancedSearchTable').DataTable().clear().destroy();
        }

        // Define preferred column keys in order and trim to the actual header count
        // We add a client-side 'settings' column right after 'id' to render a View button
        var preferredKeys = ['id','settings','claimant_name','patient_name','type_of_assistance','date_claimed','status'];
        var useKeys = preferredKeys.slice(0, thCount);
        var viewPrefix = "{{ url('admin/financial-assistances') }}";
        var cols = useKeys.map(function(k){
          if (k === 'settings') {
            return {
              data: null,
              orderable: false,
              searchable: false,
              render: function(data, type, row) {
                var id = row.id || '';
                var href = viewPrefix + '/' + id;
                return '<a class="btn btn-sm btn-primary px-3 py-1" href="' + href + '" target="_blank" rel="noopener noreferrer">View</a>';
              }
            };
          }
          // Render the 'id' column as a 1-based row number for display, but keep raw id for ordering
          if (k === 'id') {
            return {
              data: 'id',
              name: 'id',
              defaultContent: '',
              render: function(data, type, row, meta) {
                if (type === 'display' || type === 'filter') {
                  return meta.row + meta.settings._iDisplayStart + 1;
                }
                return data;
              }
            };
          }

          // Render date_claimed as date-only for display
          if (k === 'date_claimed') {
            return {
              data: 'date_claimed',
              name: 'date_claimed',
              defaultContent: '',
              render: function(data, type, row, meta) {
                if (type === 'display' || type === 'filter') {
                  return formatDateOnly(data);
                }
                return data || '';
              }
            };
          }

          return { data: k, name: k, defaultContent: '' };
        });

        $('#advancedSearchTable').DataTable({
          dom: 'Bfrtip',
          buttons: [
            {
                text: '<input type="checkbox" id="adv_chk_claimant" style="pointer-events:none;margin-right:6px;"> Claimant Name',
                className: 'btn btn-sm btn-outline-secondary',
                action: function (e, dt, node, config) {
                  var $chk = $(node).find('input');
                  $chk.prop('checked', !$chk.prop('checked'));
                  // compute column index by header text to stay resilient to column order
                  var claimantIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('claimant') !== -1; }).first().index();
                  var patientIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('patient') !== -1; }).first().index();
                  if (claimantIdx === -1) { console.warn('Claimant column not found'); return; }
                  // get current global search value
                  var searchVal = dt.search().trim();
                  function esc(v){ return v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
                  var otherChecked = $('#adv_chk_patient').prop('checked');
                  if ($chk.prop('checked')) {
                    if (!searchVal) { alert('Please enter a search term in the Search box'); $chk.prop('checked', false); return; }
                    // if both checked, require both columns equal the search value
                    if (otherChecked && patientIdx !== -1) {
                      // case-insensitive substring match using PCRE inline modifier
                      dt.column(claimantIdx).search('(?i).*' + esc(searchVal) + '.*', true, false);
                      dt.column(patientIdx).search('(?i).*' + esc(searchVal) + '.*', true, false);
                      dt.draw();
                    } else {
                      // only claimant filter
                      dt.column(claimantIdx).search('(?i).*' + esc(searchVal) + '.*', true, false).draw();
                    }
                  } else {
                    // unchecked: clear claimant filter
                    dt.column(claimantIdx).search('');
                    // if other is checked, reapply its exact filter if searchVal present
                    if (otherChecked && patientIdx !== -1) {
                      if (!searchVal) { dt.column(patientIdx).search(''); dt.draw(); return; }
                      dt.column(patientIdx).search('(?i).*' + esc(searchVal) + '.*', true, false).draw();
                    } else {
                      dt.draw();
                    }
                  }
                }
              },
            {
              text: '<input type="checkbox" id="adv_chk_patient" style="pointer-events:none;margin-right:6px;"> Patient Name',
              className: 'btn btn-sm btn-outline-secondary',
              action: function (e, dt, node, config) {
                var $chk = $(node).find('input');
                $chk.prop('checked', !$chk.prop('checked'));
                var claimantIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('claimant') !== -1; }).first().index();
                var patientIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('patient') !== -1; }).first().index();
                if (patientIdx === -1) { console.warn('Patient column not found'); return; }
                var searchVal = dt.search().trim();
                function esc(v){ return v.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
                var otherChecked = $('#adv_chk_claimant').prop('checked');
                if ($chk.prop('checked')) {
                  if (!searchVal) { alert('Please enter a search term in the Search box'); $chk.prop('checked', false); return; }
                  if (otherChecked && claimantIdx !== -1) {
                    // case-insensitive substring match using PCRE inline modifier
                    dt.column(claimantIdx).search('(?i).*' + esc(searchVal) + '.*', true, false);
                    dt.column(patientIdx).search('(?i).*' + esc(searchVal) + '.*', true, false);
                    dt.draw();
                  } else {
                    dt.column(patientIdx).search('(?i).*' + esc(searchVal) + '.*', true, false).draw();
                  }
                } else {
                  dt.column(patientIdx).search('');
                  if (otherChecked && claimantIdx !== -1) {
                    if (!searchVal) { dt.column(claimantIdx).search(''); dt.draw(); return; }
                    dt.column(claimantIdx).search('(?i).*' + esc(searchVal) + '.*', true, false).draw();
                  } else {
                    dt.draw();
                  }
                }
              }
            }
            ,{
              text: '<i class="fas fa-sync-alt"></i> Clear',
              className: 'btn btn-sm btn-outline-secondary',
              action: function(e, dt, node, config) {
                // uncheck both checkboxes and clear their column searches
                try {
                  $('#adv_chk_claimant').prop('checked', false);
                  $('#adv_chk_patient').prop('checked', false);
                  var claimantIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('claimant') !== -1; }).first().index();
                  var patientIdx = $('#advancedSearchTable thead th').filter(function(i,el){ return $(el).text().trim().toLowerCase().indexOf('patient') !== -1; }).first().index();
                  if (claimantIdx !== -1) dt.column(claimantIdx).search('');
                  if (patientIdx !== -1) dt.column(patientIdx).search('');
                  dt.draw();
                } catch (err) {
                  console.error('Failed to clear advanced search filters', err);
                }
              }
            }
          ],
          processing: true,
          serverSide: true,
          ajax: "{{ route('admin.financial-assistances.index') }}?ajax=1",
          columns: cols,
          pageLength: 10,
          order: [[ 0, 'desc' ]]
        });
    } catch (e) {
      console.error('Failed to initialize Advanced Search DataTable', e);
      advInit = false;
    }
  }

  // Also attempt init when modal shown (redundant guard)
  $('#advancedSearchModal').on('shown.bs.modal', function(){ initAdvancedSearch(); });
});
</script>
@endsection
