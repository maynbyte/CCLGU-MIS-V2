@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.solicitation.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.solicitations.update", [$solicitation->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="solicitation">{{ trans('cruds.solicitation.fields.solicitation') }}</label>
                <input class="form-control {{ $errors->has('solicitation') ? 'is-invalid' : '' }}" type="text" name="solicitation" id="solicitation" value="{{ old('solicitation', $solicitation->solicitation) }}">
                @if($errors->has('solicitation'))
                    <span class="text-danger">{{ $errors->first('solicitation') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.solicitation.fields.solicitation_helper') }}</span>
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