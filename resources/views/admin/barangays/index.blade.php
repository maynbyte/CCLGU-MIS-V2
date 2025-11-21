@extends('layouts.admin')
@section('content')
@can('barangay_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.barangays.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.barangay.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Barangay', 'route' => 'admin.barangays.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.barangay.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Barangay">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.barangay_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.barangay') }}
                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.barangay_chairman') }}
                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.sk_chairman') }}
                    </th>
                    <th>
                        {{ trans('cruds.barangay.fields.total_no_of_voters') }}
                    </th>
                    <th>
                        &nbsp;
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
    let dtButtons = [];
    dtButtons.push({ extend: 'selectAll', text: '<i class="fas fa-check-double"></i> Select all', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'selectNone', text: '<i class="fas fa-ban"></i> Deselect all', className: 'btn btn-outline-secondary btn-sm' });
@can('barangay_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.barangays.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
                    .done(function(){ location.reload() })
            }
        }
    })
@endcan
    dtButtons.push({ extend: 'copy', text: '<i class="fas fa-copy"></i> Copy', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-outline-secondary btn-sm' });

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    select: { style: 'multi', selector: 'td:not(:last-child)' },
    ajax: "{{ route('admin.barangays.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'barangay_name', name: 'barangay_name' },
{ data: 'barangay', name: 'barangay' },
{ data: 'barangay_chairman', name: 'barangay_chairman' },
{ data: 'sk_chairman', name: 'sk_chairman' },
{ data: 'total_no_of_voters', name: 'total_no_of_voters' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 100,
  };
  let table = $('.datatable-Barangay').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection