@extends('layouts.admin')
@section('content')
@can('solicitation_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.solicitations.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.solicitation.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'Solicitation', 'route' => 'admin.solicitations.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.solicitation.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
      <style>
        /* Universal toolbar spacing */
        .dataTables_wrapper .dt-buttons { margin-left: 10px; }
      </style>
      <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Solicitation">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.solicitation.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.solicitation.fields.solicitation') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
                    </td>
                    <td>
                    </td>
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
  // Universal button set: Select all, Deselect all, Delete selected, then export utilities
  let dtButtons = [];
  dtButtons.push({ extend: 'selectAll', text: '<i class="fas fa-check-double"></i> Select all', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'selectNone', text: '<i class="fas fa-ban"></i> Deselect all', className: 'btn btn-outline-secondary btn-sm' });
  @can('solicitation_delete')
  dtButtons.push({
    extend: 'selected',
    text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
    className: 'btn btn-danger btn-sm',
    action: function (e, dt) {
      var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
      if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.solicitations.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
          .done(function(){ location.reload() })
      }
    }
  });
  @endcan
  dtButtons.push({ extend: 'copy',  text: '<i class="fas fa-copy"></i> Copy',  className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'csv',   text: '<i class="fas fa-file-csv"></i> CSV',  className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'pdf',   text: '<i class="fas fa-file-pdf"></i> PDF',    className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'print', text: '<i class="fas fa-print"></i> Print',    className: 'btn btn-outline-secondary btn-sm' });
  dtButtons.push({ extend: 'colvis', text: '<i class="fas fa-columns"></i> Columns', className: 'btn btn-outline-secondary btn-sm' });

  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    select: { style: 'multi', selector: 'td:not(:last-child)' },
    ajax: "{{ route('admin.solicitations.index') }}",
    columns: [
      { data: 'placeholder', name: 'placeholder' },
      { data: 'id', name: 'id' },
      { data: 'solicitation', name: 'solicitation' },
      { data: 'actions', name: '{{ trans('global.actions') }}' }
    ],
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 50,
  };
  let table = $('.datatable-Solicitation').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
let visibleColumnsIndexes = null;
$('.datatable thead').on('input', '.search', function () {
      let strict = $(this).attr('strict') || false
      let value = strict && this.value ? "^" + this.value + "$" : this.value

      let index = $(this).parent().index()
      if (visibleColumnsIndexes !== null) {
        index = visibleColumnsIndexes[index]
      }

      table
        .column(index)
        .search(value, strict)
        .draw()
  });
table.on('column-visibility.dt', function(e, settings, column, state) {
      visibleColumnsIndexes = []
      table.columns(":visible").every(function(colIdx) {
          visibleColumnsIndexes.push(colIdx);
      });
  })
});

</script>
@endsection