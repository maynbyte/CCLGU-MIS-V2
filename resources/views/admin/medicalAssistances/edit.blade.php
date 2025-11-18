@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.medicalAssistance.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.medical-assistances.update", [$medicalAssistance->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="medical_assistance">{{ trans('cruds.medicalAssistance.fields.medical_assistance') }}</label>
                <input class="form-control {{ $errors->has('medical_assistance') ? 'is-invalid' : '' }}" type="text" name="medical_assistance" id="medical_assistance" value="{{ old('medical_assistance', $medicalAssistance->medical_assistance) }}">
                @if($errors->has('medical_assistance'))
                    <span class="text-danger">{{ $errors->first('medical_assistance') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.medicalAssistance.fields.medical_assistance_helper') }}</span>
            </div>
            <div class="form-group">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>



@endsection