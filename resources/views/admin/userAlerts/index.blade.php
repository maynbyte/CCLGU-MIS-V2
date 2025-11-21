@extends('layouts.admin')
@section('content')
@can('user_alert_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.user-alerts.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.userAlert.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.userAlert.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-UserAlert">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.alert_text') }}
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.alert_link') }}
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.user') }}
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.created_at') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($userAlerts as $key => $userAlert)
                        <tr data-entry-id="{{ $userAlert->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $userAlert->id ?? '' }}
                            </td>
                            <td>
                                {{ $userAlert->alert_text ?? '' }}
                            </td>
                            <td>
                                {{ $userAlert->alert_link ?? '' }}
                            </td>
                            <td>
                                @foreach($userAlert->users as $key => $item)
                                    <span class="badge badge-info">{{ $item->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                {{ $userAlert->created_at ?? '' }}
                            </td>
                            <td>
                                @can('user_alert_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.user-alerts.show', $userAlert->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan


                                @can('user_alert_delete')
                                    <form action="{{ route('admin.user-alerts.destroy', $userAlert->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
    @can('user_alert_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) { return $(entry).data('entry-id') });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.user-alerts.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
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

    let table = $('.datatable-UserAlert:not(.ajaxTable)').DataTable({
        buttons: dtButtons,
        orderCellsTop: true,
        order: [[ 1, 'desc' ]],
        pageLength: 100,
        select: { style: 'multi', selector: 'td:not(:last-child)' }
    });
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection