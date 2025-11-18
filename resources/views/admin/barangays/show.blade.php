@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.barangay.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.barangays.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.id') }}
                        </th>
                        <td>
                            {{ $barangay->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.barangay_name') }}
                        </th>
                        <td>
                            {{ $barangay->barangay_name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.barangay') }}
                        </th>
                        <td>
                            {{ $barangay->barangay }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.barangay_chairman') }}
                        </th>
                        <td>
                            {{ $barangay->barangay_chairman }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.sk_chairman') }}
                        </th>
                        <td>
                            {{ $barangay->sk_chairman }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.barangay.fields.total_no_of_voters') }}
                        </th>
                        <td>
                            {{ $barangay->total_no_of_voters }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.barangays.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection