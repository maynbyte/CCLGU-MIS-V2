@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.barangay.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route("admin.barangays.update", [$barangay->id]) }}" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="form-group">
                <label class="required" for="barangay_name">{{ trans('cruds.barangay.fields.barangay_name') }}</label>
                <input class="form-control {{ $errors->has('barangay_name') ? 'is-invalid' : '' }}" type="text" name="barangay_name" id="barangay_name" value="{{ old('barangay_name', $barangay->barangay_name) }}" required>
                @if($errors->has('barangay_name'))
                    <span class="text-danger">{{ $errors->first('barangay_name') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.barangay.fields.barangay_name_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="barangay">{{ trans('cruds.barangay.fields.barangay') }}</label>
                <input class="form-control {{ $errors->has('barangay') ? 'is-invalid' : '' }}" type="text" name="barangay" id="barangay" value="{{ old('barangay', $barangay->barangay) }}">
                @if($errors->has('barangay'))
                    <span class="text-danger">{{ $errors->first('barangay') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.barangay.fields.barangay_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="barangay_chairman">{{ trans('cruds.barangay.fields.barangay_chairman') }}</label>
                <input class="form-control {{ $errors->has('barangay_chairman') ? 'is-invalid' : '' }}" type="text" name="barangay_chairman" id="barangay_chairman" value="{{ old('barangay_chairman', $barangay->barangay_chairman) }}">
                @if($errors->has('barangay_chairman'))
                    <span class="text-danger">{{ $errors->first('barangay_chairman') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.barangay.fields.barangay_chairman_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="sk_chairman">{{ trans('cruds.barangay.fields.sk_chairman') }}</label>
                <input class="form-control {{ $errors->has('sk_chairman') ? 'is-invalid' : '' }}" type="text" name="sk_chairman" id="sk_chairman" value="{{ old('sk_chairman', $barangay->sk_chairman) }}">
                @if($errors->has('sk_chairman'))
                    <span class="text-danger">{{ $errors->first('sk_chairman') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.barangay.fields.sk_chairman_helper') }}</span>
            </div>
            <div class="form-group">
                <label for="total_no_of_voters">{{ trans('cruds.barangay.fields.total_no_of_voters') }}</label>
                <input class="form-control {{ $errors->has('total_no_of_voters') ? 'is-invalid' : '' }}" type="number" name="total_no_of_voters" id="total_no_of_voters" value="{{ old('total_no_of_voters', $barangay->total_no_of_voters) }}" step="1">
                @if($errors->has('total_no_of_voters'))
                    <span class="text-danger">{{ $errors->first('total_no_of_voters') }}</span>
                @endif
                <span class="help-block">{{ trans('cruds.barangay.fields.total_no_of_voters_helper') }}</span>
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