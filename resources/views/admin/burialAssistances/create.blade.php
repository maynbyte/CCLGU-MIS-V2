@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.burialAssistance.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.burial-assistances.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="burial_assitance">{{ trans('cruds.burialAssistance.fields.burial_assitance') }}</label>
                <input class="form-control {{ $errors->has('burial_assitance') ? 'is-invalid' : '' }}" type="text" name="burial_assitance" id="burial_assitance" value="{{ old('burial_assitance', '') }}">
                @if($errors->has('burial_assitance'))
                    <span class="text-danger">{{ $errors->first('burial_assitance') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.burialAssistance.fields.burial_assitance_helper') }}</span>
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