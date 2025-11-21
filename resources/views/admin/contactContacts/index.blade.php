@extends('layouts.admin')
@section('content')
@can('contact_contact_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.contact-contacts.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.contactContact.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.contactContact.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-ContactContact">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.company') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_first_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_last_name') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_phone_1') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_phone_2') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_email') }}
                        </th>
                        <th>
                            {{ trans('cruds.contactContact.fields.contact_address') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contactContacts as $key => $contactContact)
                        <tr data-entry-id="{{ $contactContact->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $contactContact->id ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->company->company_name ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_first_name ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_last_name ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_phone_1 ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_phone_2 ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_email ?? '' }}
                            </td>
                            <td>
                                {{ $contactContact->contact_address ?? '' }}
                            </td>
                            <td>
                                @can('contact_contact_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.contact-contacts.show', $contactContact->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('contact_contact_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.contact-contacts.edit', $contactContact->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('contact_contact_delete')
                                    <form action="{{ route('admin.contact-contacts.destroy', $contactContact->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    // Universal button suite for static table
    let dtButtons = [];
    dtButtons.push({ extend: 'selectAll', text: '<i class="fas fa-check-double"></i> Select all', className: 'btn btn-outline-secondary btn-sm' });
    dtButtons.push({ extend: 'selectNone', text: '<i class="fas fa-ban"></i> Deselect all', className: 'btn btn-outline-secondary btn-sm' });
    @can('contact_contact_delete')
    dtButtons.push({
        extend: 'selected',
        text: '<i class="fas fa-trash-alt"></i> {{ trans('global.datatables.delete') }}',
        className: 'btn btn-danger btn-sm',
        action: function (e, dt) {
            var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) { return $(entry).data('entry-id'); });
            if (ids.length === 0) { alert('{{ trans('global.datatables.zero_selected') }}'); return }
            if (confirm('{{ trans('global.areYouSure') }}')) {
                $.ajax({ headers: {'x-csrf-token': _token}, method: 'POST', url: "{{ route('admin.contact-contacts.massDestroy') }}", data: { ids: ids, _method: 'DELETE' } })
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

    let table = $('.datatable-ContactContact:not(.ajaxTable)').DataTable({
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