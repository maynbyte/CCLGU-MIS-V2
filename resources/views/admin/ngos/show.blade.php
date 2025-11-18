@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.ngo.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.ngos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.id') }}
                        </th>
                        <td>
                            {{ $ngo->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.name') }}
                        </th>
                        <td>
                            {{ $ngo->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.contact_person') }}
                        </th>
                        <td>
                            {{ $ngo->contact_person }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.contact_no') }}
                        </th>
                        <td>
                            {{ $ngo->contact_no }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.description') }}
                        </th>
                        <td>
                            {{ $ngo->description }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.ngo.fields.total_members') }}
                        </th>
                        <td>
                            {{ $ngo->total_members }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.ngos.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection