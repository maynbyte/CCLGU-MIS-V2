@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.familycomposition.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.familycompositions.update", [$familycomposition->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label for="family_name">{{ trans('cruds.familycomposition.fields.family_name') }}</label>
                <input class="form-control {{ $errors->has('family_name') ? 'is-invalid' : '' }}" type="text" name="family_name" id="family_name" value="{{ old('family_name', $familycomposition->family_name) }}">
                @if($errors->has('family_name'))
                    <span class="text-danger">{{ $errors->first('family_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.family_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="family_birthday">{{ trans('cruds.familycomposition.fields.family_birthday') }}</label>
                <input class="form-control date {{ $errors->has('family_birthday') ? 'is-invalid' : '' }}" type="text" name="family_birthday" id="family_birthday" value="{{ old('family_birthday', $familycomposition->family_birthday) }}">
                @if($errors->has('family_birthday'))
                    <span class="text-danger">{{ $errors->first('family_birthday') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.family_birthday_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.familycomposition.fields.family_relationship') }}</label>
                <select class="form-control {{ $errors->has('family_relationship') ? 'is-invalid' : '' }}" name="family_relationship" id="family_relationship">
                    <option value disabled {{ old('family_relationship', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('family_relationship', $familycomposition->family_relationship) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('family_relationship'))
                    <span class="text-danger">{{ $errors->first('family_relationship') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.family_relationship_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.familycomposition.fields.family_civil_status') }}</label>
                <select class="form-control {{ $errors->has('family_civil_status') ? 'is-invalid' : '' }}" name="family_civil_status" id="family_civil_status">
                    <option value disabled {{ old('family_civil_status', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('family_civil_status', $familycomposition->family_civil_status) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('family_civil_status'))
                    <span class="text-danger">{{ $errors->first('family_civil_status') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.family_civil_status_helper') }}</span>
            </div>
            <div class="form-group">
                <label>{{ trans('cruds.familycomposition.fields.family_highest_edu') }}</label>
                <select class="form-control {{ $errors->has('family_highest_edu') ? 'is-invalid' : '' }}" name="family_highest_edu" id="family_highest_edu">
                    <option value disabled {{ old('family_highest_edu', null) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                        <option value="{{ $key }}" {{ old('family_highest_edu', $familycomposition->family_highest_edu) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @if($errors->has('family_highest_edu'))
                    <span class="text-danger">{{ $errors->first('family_highest_edu') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.family_highest_edu_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="occupation">{{ trans('cruds.familycomposition.fields.occupation') }}</label>
                <input class="form-control {{ $errors->has('occupation') ? 'is-invalid' : '' }}" type="text" name="occupation" id="occupation" value="{{ old('occupation', $familycomposition->occupation) }}">
                @if($errors->has('occupation'))
                    <span class="text-danger">{{ $errors->first('occupation') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.occupation_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="remarks">{{ trans('cruds.familycomposition.fields.remarks') }}</label>
                <input class="form-control {{ $errors->has('remarks') ? 'is-invalid' : '' }}" type="text" name="remarks" id="remarks" value="{{ old('remarks', $familycomposition->remarks) }}">
                @if($errors->has('remarks'))
                    <span class="text-danger">{{ $errors->first('remarks') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.remarks_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="others">{{ trans('cruds.familycomposition.fields.others') }}</label>
                <input class="form-control {{ $errors->has('others') ? 'is-invalid' : '' }}" type="text" name="others" id="others" value="{{ old('others', $familycomposition->others) }}">
                @if($errors->has('others'))
                    <span class="text-danger">{{ $errors->first('others') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.familycomposition.fields.others_helper') }}</span>
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