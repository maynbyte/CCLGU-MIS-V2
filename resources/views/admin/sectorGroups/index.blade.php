@extends('layouts.admin')
@section('content')
@can('sector_group_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.sector-groups.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.sectorGroup.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.sectorGroup.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-SectorGroup">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.sectorGroup.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.sectorGroup.fields.name') }}
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
@can('sector_group_delete')
  dtButtons.push({
    extend: 'selected',
    text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
    className: 'btn btn-danger btn-sm',
    action: function (e, dt) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
      if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.sector-groups.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
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
    ajax: "{{ route('admin.sector-groups.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'name', name: 'name' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-SectorGroup').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection