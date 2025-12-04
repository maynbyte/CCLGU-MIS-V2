<div class="d-flex flex-column" style="min-width:90px;">
    @can($viewGate)
        <a class="btn btn-sm btn-success mb-1 text-white w-100 text-center" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}" title="{{ trans('global.view') }}">
            <span class="d-none d-md-inline">{{ trans('global.view') }}</span>
        </a>
    @endcan

    @can($editGate)
        <a class="btn btn-sm btn-primary mb-1 text-white w-100 text-center" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}" title="{{ trans('global.edit') }}">
            <span class="d-none d-md-inline">{{ trans('global.edit') }}</span>
        </a>
    @endcan

    @can($deleteGate)
        <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: block;">
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="submit" class="btn btn-sm btn-danger w-100" value="{{ trans('global.delete') }}">
        </form>
    @endcan
</div>