@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.medicalAssistance.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.medical-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.medicalAssistance.fields.id') }}
                        </th>
                        <td>
                            {{ $medicalAssistance->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.medicalAssistance.fields.medical_assistance') }}
                        </th>
                        <td>
                            {{ $medicalAssistance->medical_assistance }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.medical-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection