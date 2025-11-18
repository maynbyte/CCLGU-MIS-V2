@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.ngo.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.ngos.update", [$ngo->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="name">{{ trans('cruds.ngo.fields.name') }}</label>
                <input class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" type="text" name="name" id="name" value="{{ old('name', $ngo->name) }}" required>
                @if($errors->has('name'))
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.ngo.fields.name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="contact_person">{{ trans('cruds.ngo.fields.contact_person') }}</label>
                <input class="form-control {{ $errors->has('contact_person') ? 'is-invalid' : '' }}" type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $ngo->contact_person) }}">
                @if($errors->has('contact_person'))
                    <span class="text-danger">{{ $errors->first('contact_person') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.ngo.fields.contact_person_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="contact_no">{{ trans('cruds.ngo.fields.contact_no') }}</label>
                <input class="form-control {{ $errors->has('contact_no') ? 'is-invalid' : '' }}" type="text" name="contact_no" id="contact_no" value="{{ old('contact_no', $ngo->contact_no) }}">
                @if($errors->has('contact_no'))
                    <span class="text-danger">{{ $errors->first('contact_no') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.ngo.fields.contact_no_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.ngo.fields.description') }}</label>
                <input class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" type="text" name="description" id="description" value="{{ old('description', $ngo->description) }}">
                @if($errors->has('description'))
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.ngo.fields.description_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="total_members">{{ trans('cruds.ngo.fields.total_members') }}</label>
                <input class="form-control {{ $errors->has('total_members') ? 'is-invalid' : '' }}" type="text" name="total_members" id="total_members" value="{{ old('total_members', $ngo->total_members) }}">
                @if($errors->has('total_members'))
                    <span class="text-danger">{{ $errors->first('total_members') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.ngo.fields.total_members_helper') }}</span>
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