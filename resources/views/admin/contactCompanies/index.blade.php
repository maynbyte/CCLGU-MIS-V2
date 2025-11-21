@extends('layouts.admin')
@section('content')
@can('contact_company_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.contact-companies.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.contactCompany.title_singular') }}
            </a>
            <button class="btn btn-warning" data-toggle="modal" data-target="#csvImportModal">
                {{ trans('global.app_csvImport') }}
            </button>
            @include('csvImport.modal', ['model' => 'ContactCompany', 'route' => 'admin.contact-companies.parseCsvImport'])
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.contactCompany.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <style>
          .dataTables_wrapper .dt-buttons { margin-left: 10px; }
        </style>
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-ContactCompany">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.contactCompany.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.contactCompany.fields.company_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.contactCompany.fields.company_address') }}
                    </th>
                    <th>
                        {{ trans('cruds.contactCompany.fields.company_website') }}
                    </th>
                    <th>
                        {{ trans('cruds.contactCompany.fields.company_email') }}
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
                        <input class="search" type="text" placeholder="{{ trans('global.search') }}">
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
    let dtButtons = [];
    dtButtons.push({ extend: 'selectAll', text: '<i class="fas fa-check-double"></i> Select all', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'selectNone', text: '<i class="fas fa-ban"></i> Deselect all', className: 'btn btn-outline-secondary btn-sm' });
    @can('contact_company_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.contact-companies.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
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
        ajax: "{{ route('admin.contact-companies.index') }}",
        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'id', name: 'id' },
            { data: 'company_name', name: 'company_name' },
            { data: 'company_address', name: 'company_address' },
            { data: 'company_website', name: 'company_website' },
            { data: 'company_email', name: 'company_email' },
            { data: 'actions', name: '{{ trans('global.actions') }}' }
        ],
        orderCellsTop: true,
        order: [[ 2, 'desc' ]],
        pageLength: 50,
    };
  let table = $('.datatable-ContactCompany').DataTable(dtOverrideGlobals);
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