@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.burialAssistance.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.burial-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.burialAssistance.fields.id') }}
                        </th>
                        <td>
                            {{ $burialAssistance->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.burialAssistance.fields.burial_assitance') }}
                        </th>
                        <td>
                            {{ $burialAssistance->burial_assitance }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.burial-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection