@extends('layouts.admin')
@section('content')
@can('ngo_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.ngos.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.ngo.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.ngo.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Ngo">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.name') }}
                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.contact_person') }}
                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.contact_no') }}
                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.description') }}
                    </th>
                    <th>
                        {{ trans('cruds.ngo.fields.total_members') }}
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
@can('ngo_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.ngos.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
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
    ajax: "{{ route('admin.ngos.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
{ data: 'id', name: 'id' },
{ data: 'name', name: 'name' },
{ data: 'contact_person', name: 'contact_person' },
{ data: 'contact_no', name: 'contact_no' },
{ data: 'description', name: 'description' },
{ data: 'total_members', name: 'total_members' },
{ data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 2, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Ngo').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection