@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.guaranteeLetter.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.guarantee-letters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.guaranteeLetter.fields.id') }}
                        </th>
                        <td>
                            {{ $guaranteeLetter->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.guaranteeLetter.fields.directory') }}
                        </th>
                        <td>
                            {{ $guaranteeLetter->directory }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.guarantee-letters.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection