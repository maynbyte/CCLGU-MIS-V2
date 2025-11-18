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

<div class="card">
    <div class="card-header">
        {{ trans('cruds.directory.title_singular') }} {{ trans('global.list') }}
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
                   Financial Assistance
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

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    ajax: "{{ route('admin.directories.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
      { data: 'id', name: 'id' },
      { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false },
      { data: 'last_name', name: 'last_name' },
      { data: 'first_name', name: 'first_name' },
      { data: 'middle_name', name: 'middle_name' },
      { data: 'suffix', name: 'suffix' },
      { data: 'barangay_barangay_name', name: 'barangay.barangay_name' },
      { data: 'comelec_status', name: 'comelec_status' },
      { data: 'life_status', name: 'life_status' },
       {
    data: 'id', // use the Directory ID from server
    name: 'fa_link',
    orderable: false,
    searchable: false,
    render: function (data, type, row, meta) {
      let url = "{{ route('admin.financial-assistances.create') }}?directory_id=" + data;
      return '<a class="btn btn-sm btn-primary" href="' + url + '">Add</a>';
    }
  }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]], 
    pageLength: 100,
  };

  let table = $('.datatable-Directory').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(){
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
  });
});
</script>
@endsection
