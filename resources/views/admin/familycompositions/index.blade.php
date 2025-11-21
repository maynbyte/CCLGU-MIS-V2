@extends('layouts.admin')
@section('content')
@can('familycomposition_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.familycompositions.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.familycomposition.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.familycomposition.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <table class=" table table-bordered table-striped table-hover ajaxTable datatable datatable-Familycomposition">
            <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.family_name') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.family_birthday') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.family_relationship') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.family_civil_status') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.family_highest_edu') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.occupation') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.remarks') }}
                    </th>
                    <th>
                        {{ trans('cruds.familycomposition.fields.others') }}
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
    @can('familycomposition_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).data(), function (entry) { return entry.id });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.familycompositions.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
                    .done(function(){ location.reload() })
            }
        }
    });
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
        ajax: "{{ route('admin.familycompositions.index') }}",
        columns: [
            { data: 'placeholder', name: 'placeholder' },
            { data: 'id', name: 'id' },
            { data: 'family_name', name: 'family_name' },
            { data: 'family_birthday', name: 'family_birthday' },
            { data: 'family_relationship', name: 'family_relationship' },
            { data: 'family_civil_status', name: 'family_civil_status' },
            { data: 'family_highest_edu', name: 'family_highest_edu' },
            { data: 'occupation', name: 'occupation' },
            { data: 'remarks', name: 'remarks' },
            { data: 'others', name: 'others' },
            { data: 'actions', name: '{{ trans('global.actions') }}' }
        ],
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 100,
    };
  let table = $('.datatable-Familycomposition').DataTable(dtOverrideGlobals);
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
});

</script>
@endsection