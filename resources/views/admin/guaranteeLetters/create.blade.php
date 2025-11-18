@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.guaranteeLetter.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.guarantee-letters.store") }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="directory">{{ trans('cruds.guaranteeLetter.fields.directory') }}</label>
                <input class="form-control {{ $errors->has('directory') ? 'is-invalid' : '' }}" type="text" name="directory" id="directory" value="{{ old('directory', '') }}">
                @if($errors->has('directory'))
                    <span class="text-danger">{{ $errors->first('directory') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.guaranteeLetter.fields.directory_helper') }}</span>
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