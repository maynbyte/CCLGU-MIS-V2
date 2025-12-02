@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <div>
            <h3 class="card-title mb-2"><i class="fas fa-users text-primary mr-2"></i>{{ trans('cruds.directory.title_singular') }} {{ trans('global.list') }}</h3>
        </div>

        <div class="card-tools ml-auto">
            @can('directory_create')
            <div class="btn btn-sm" role="group" aria-label="Directory actions">
                <a class="btn btn-success" href="{{ route('admin.directories.create') }}" data-toggle="tooltip" title="Add new directory">
                    <i class="fas fa-plus"></i>
                    <span class="d-none d-sm-inline ml-1">{{ trans('global.add') }} {{trans('cruds.directory.title_singular')}}</span>
                </a>
                <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal" data-toggle="tooltip" title="Import from CSV">
                    <i class="fas fa-file-csv"></i>
                    <span class="d-none d-sm-inline ml-1">{{ trans('global.app_csvImport') }}</span>
                </button>
            </div>
            @include('csvImport.modal', ['model' => 'Directory', 'route' => 'admin.directories.parseCsvImport'])
            @endcan
        </div>
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Directory">
            <thead>
                <tr>
                    <th width="10">
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.id') }}
                    </th> 
                      <th>
                        {{ trans('cruds.directory.fields.profile_picture') }}
                    </th>  
                    <th>
                        {{ trans('cruds.directory.fields.last_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.first_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.middle_name') }}
                    </th>
                   
                    <th>
                        {{ trans('cruds.directory.fields.suffix') }}
                    </th>

<!--Other
                    <th>
                        {{ trans('cruds.directory.fields.email') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.contact_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.birthday') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.place_of_birth') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.nationality') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.gender') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.highest_edu') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.civil_status') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.religion') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.street_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.street') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.city') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.province') }}
                    </th>
                   
                    <th>
                        {{ trans('cruds.directory.fields.ngo') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.sector') }}
                    </th>       
                    <th>
                        {{ trans('cruds.directory.fields.description') }}
                    </th>
                  
                    <th>
                        {{ trans('cruds.directory.fields.remarks') }}
                    </th>
                      -->
                     <th>
                        {{ trans('cruds.directory.fields.barangay') }}
                    </th>
                        <th>
                        {{ trans('cruds.directory.fields.comelec_status') }}
                    </th>
                    <th>
                        {{ trans('cruds.directory.fields.life_status') }}
                    </th>
                    <th>
                        Action Buttons
                    </th>
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
    // Arrange buttons: Select all, Deselect all, then Delete Selected (beside Deselect)
    let dtButtons = []
    dtButtons.push({ extend: 'selectAll',  text: '<i class="fas fa-check-double"></i> Select all',   className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'selectNone', text: '<i class="fas fa-ban"></i> Deselect all',         className: 'btn btn-outline-secondary btn-sm' })
    @can('directory_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id })
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.directories.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
                    .done(function () { location.reload() })
            }
        }
    })
    @endcan

    // Additional actions (restored)
    dtButtons.push({ extend: 'copy',  text: '<i class="fas fa-copy"></i> Copy',   className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'csv',   text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-outline-secondary btn-sm' })
    dtButtons.push({ extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-outline-secondary btn-sm' })

    let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
        select: { style: 'multi', selector: 'td:not(:last-child)' },
    ajax: "{{ route('admin.directories.index') }}",
    columns: [
{ data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'profile_picture', name: 'profile_picture', sortable: false, searchable: false },
{ data: 'last_name', name: 'last_name' },
{ data: 'first_name', name: 'first_name' },
{ data: 'middle_name', name: 'middle_name' },
{ data: 'suffix', name: 'suffix' },
//{ data: 'email', name: 'email' },
//{ data: 'contact_no', name: 'contact_no' },
//{ data: 'birthday', name: 'birthday' },
//{ data: 'place_of_birth', name: 'place_of_birth' },
//{ data: 'nationality', name: 'nationality' },
//{ data: 'gender', name: 'gender' },
//{ data: 'highest_edu', name: 'highest_edu' },
//{ data: 'civil_status', name: 'civil_status' },
//{ data: 'religion', name: 'religion' },
//{ data: 'street_no', name: 'street_no' },
//{ data: 'street', name: 'street' },
//{ data: 'city', name: 'city' },
//{ data: 'province', name: 'province' },
//{ data: 'ngo', name: 'ngos.name' },
//{ data: 'sector', name: 'sectors.name' },
//{ data: 'description', name: 'description' },
//{ data: 'remarks', name: 'remarks' },
{ data: 'barangay_barangay_name', name: 'barangay.barangay_name' },
{ data: 'comelec_status', name: 'comelec_status' },
{ data: 'life_status', name: 'life_status' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-Directory').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});
    // Enable Bootstrap tooltips for header actions
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection