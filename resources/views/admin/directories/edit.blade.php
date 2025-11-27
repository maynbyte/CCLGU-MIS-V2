@extends('layouts.admin')

@php
use Illuminate\Support\Carbon;

$birthdayIso = '';
$oldBday = old('birthday');

if ($oldBday) {
    try {
        // Accept dd/mm/yyyy, mm-dd-yyyy, etc.
        $birthdayIso = Carbon::parse(str_replace('/', '-', $oldBday))->format('Y-m-d');
    } catch (\Throwable $e) {
        $birthdayIso = '';
    }
} elseif (!empty($directory->birthday)) {
    try {
        $birthdayIso = Carbon::parse($directory->birthday)->format('Y-m-d');
    } catch (\Throwable $e) {
        $birthdayIso = '';
    }
}
@endphp

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-user-edit"></i> {{ trans('global.edit') }} {{ trans('cruds.directory.title_singular') }}</h4>
    </div>

    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.directories.update', $directory->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ===== Personal Information ===== --}}
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">

                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="fas fa-user text-primary"></i> Personal Information</h5>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body" style="display:block;">
                                    <div class="form-group">
                                        <div class="row">
                                                                                        {{-- Picture --}}
                                                                                        <div class="col-md-2 picture-container">
    <div class="picture">
        <img
            src="{{ isset($directory) && $directory->profile_picture ? $directory->profile_picture->getUrl() : asset('upload/free-user-icon.png') }}"
            class="picture-src"
            id="wizardPicturePreview"
            alt="Profile preview"
            style="width:100%;height:auto;display:block;"
        >
        <input type="file" name="profile_picture" id="wizard-picture" accept="image/*" style="display: none;">
    </div>
    <button type="button" class="btn btn-sm btn-primary mt-2" id="upload-btn" onclick="document.getElementById('wizard-picture').click()">
        <i class="fas fa-upload mr-1"></i>Upload Picture
    </button>
    <button type="button" class="btn btn-sm btn-danger mt-2" id="delete-btn" style="{{ isset($directory) && $directory->profile_picture ? 'display:inline-block;' : 'display:none;' }}">
        <i class="fas fa-trash mr-1"></i>Delete Picture
    </button>
</div>

                                            <div class="col-md-10">
                                                <div class="row mb-3">
                                                    {{-- First Name --}}
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label font-weight-bold required" for="first_name"><i class="fas fa-user text-muted mr-1"></i>{{ trans('cruds.directory.fields.first_name') }}</label>
                                                        <input class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" type="text" name="first_name" id="first_name" value="{{ old('first_name', $directory->first_name) }}" required>
                                                        @if($errors->has('first_name'))
                                                        <span class="text-danger">{{ $errors->first('first_name') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.first_name_helper') }}</span>
                                                    </div>
                                                    {{-- Middle Name --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="middle_name"><i class="fas fa-user text-muted mr-1"></i>{{ trans('cruds.directory.fields.middle_name') }}</label>
                                                        <input class="form-control {{ $errors->has('middle_name') ? 'is-invalid' : '' }}" type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $directory->middle_name) }}">
                                                        @if($errors->has('middle_name'))
                                                        <span class="text-danger">{{ $errors->first('middle_name') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.middle_name_helper') }}</span>
                                                    </div>
                                                    {{-- Last Name --}}
                                                    <div class="col-md-4 mb-3">
                                                        <label class="form-label font-weight-bold required" for="last_name"><i class="fas fa-user text-muted mr-1"></i>{{ trans('cruds.directory.fields.last_name') }}</label>
                                                        <input class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" type="text" name="last_name" id="last_name" value="{{ old('last_name', $directory->last_name) }}" required>
                                                        @if($errors->has('last_name'))
                                                        <span class="text-danger">{{ $errors->first('last_name') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.last_name_helper') }}</span>
                                                    </div>
                                                    {{-- Suffix --}}
                                                    <div class="col-md-1 mb-3">
                                                        <label for="suffix">{{ trans('cruds.directory.fields.suffix') }}</label>
                                                        <input class="form-control {{ $errors->has('suffix') ? 'is-invalid' : '' }}" type="text" name="suffix" id="suffix" value="{{ old('suffix', $directory->suffix) }}">
                                                        @if($errors->has('suffix'))
                                                        <span class="text-danger">{{ $errors->first('suffix') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.suffix_helper') }}</span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    {{-- Email --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="email"><i class="fas fa-envelope text-muted mr-1"></i>{{ trans('cruds.directory.fields.email') }}</label>
                                                        <input class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" type="text" name="email" id="email" value="{{ old('email', $directory->email) }}">
                                                        @if($errors->has('email'))
                                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.email_helper') }}</span>
                                                    </div>
                                                    {{-- Contact No --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold" for="contact_no"><i class="fas fa-phone text-muted mr-1"></i>{{ trans('cruds.directory.fields.contact_no') }}</label>
                                                        <input class="form-control {{ $errors->has('contact_no') ? 'is-invalid' : '' }}" type="text" name="contact_no" id="contact_no" value="{{ old('contact_no', $directory->contact_no) }}">
                                                        @if($errors->has('contact_no'))
                                                        <span class="text-danger">{{ $errors->first('contact_no') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.contact_no_helper') }}</span>
                                                    </div>
                                                    {{-- Birthday (text + datepicker class in your theme) --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="birthday"><i class="fas fa-birthday-cake text-muted mr-1"></i>{{ trans('cruds.directory.fields.birthday') }}</label>
                                                       <input class="form-control {{ $errors->has('birthday') ? 'is-invalid' : '' }}" type="date" name="birthday" id="birthday" value="{{ $birthdayIso }}">

                                                        @if($errors->has('birthday'))
                                                        <span class="text-danger">{{ $errors->first('birthday') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.birthday_helper') }}</span>
                                                    </div>
                                                    {{-- Place of Birth --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold" for="place_of_birth"><i class="fas fa-map-marker-alt text-muted mr-1"></i>{{ trans('cruds.directory.fields.place_of_birth') }}</label>
                                                        <input class="form-control {{ $errors->has('place_of_birth') ? 'is-invalid' : '' }}" type="text" name="place_of_birth" id="place_of_birth" value="{{ old('place_of_birth', $directory->place_of_birth) }}">
                                                        @if($errors->has('place_of_birth'))
                                                        <span class="text-danger">{{ $errors->first('place_of_birth') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.place_of_birth_helper') }}</span>
                                                    </div>
                                                    {{-- Gender --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold"><i class="fas fa-venus-mars text-muted mr-1"></i>{{ trans('cruds.directory.fields.gender') }}</label>
                                                        <select class="form-control {{ $errors->has('gender') ? 'is-invalid' : '' }}" name="gender" id="gender">
                                                            <option value disabled {{ old('gender', $directory->gender) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                                            @foreach(App\Models\Directory::GENDER_SELECT as $key => $label)
                                                            <option value="{{ $key }}" {{ old('gender', $directory->gender) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('gender'))
                                                        <span class="text-danger">{{ $errors->first('gender') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.gender_helper') }}</span>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    {{-- Highest Education --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold"><i class="fas fa-graduation-cap text-muted mr-1"></i>{{ trans('cruds.directory.fields.highest_edu') }}</label>
                                                        <select class="form-control form-control-md {{ $errors->has('highest_edu') ? 'is-invalid' : '' }}" name="highest_edu" id="highest_edu">
                                                            <option value disabled {{ old('highest_edu', $directory->highest_edu) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                                            @foreach(App\Models\Directory::HIGHEST_EDU_SELECT as $key => $label)
                                                            <option value="{{ $key }}" {{ old('highest_edu', $directory->highest_edu) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('highest_edu'))
                                                        <span class="text-danger">{{ $errors->first('highest_edu') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.highest_edu_helper') }}</span>
                                                    </div>
                                                    {{-- Civil Status --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold"><i class="fas fa-heart text-muted mr-1"></i>{{ trans('cruds.directory.fields.civil_status') }}</label>
                                                        <select class="form-control form-control-md {{ $errors->has('civil_status') ? 'is-invalid' : '' }}" name="civil_status" id="civil_status">
                                                            <option value disabled {{ old('civil_status', $directory->civil_status) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                                            @foreach(App\Models\Directory::CIVIL_STATUS_SELECT as $key => $label)
                                                            <option value="{{ $key }}" {{ old('civil_status', $directory->civil_status) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('civil_status'))
                                                        <span class="text-danger">{{ $errors->first('civil_status') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.civil_status_helper') }}</span>
                                                    </div>
                                                    {{-- Religion --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold"><i class="fas fa-praying-hands text-muted mr-1"></i>{{ trans('cruds.directory.fields.religion') }}</label>
                                                        <select class="form-control form-control-md {{ $errors->has('religion') ? 'is-invalid' : '' }}" name="religion" id="religion">
                                                            <option value disabled {{ old('religion', $directory->religion) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                                            @foreach(App\Models\Directory::RELIGION_SELECT as $key => $label)
                                                            <option value="{{ $key }}" {{ old('religion', $directory->religion) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('religion'))
                                                        <span class="text-danger">{{ $errors->first('religion') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.religion_helper') }}</span>
                                                    </div>
                                                    {{-- Nationality --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold" for="nationality"><i class="fas fa-flag text-muted mr-1"></i>{{ trans('cruds.directory.fields.nationality') }}</label>
                                                        <input class="form-control form-control-md {{ $errors->has('nationality') ? 'is-invalid' : '' }}" type="text" name="nationality" id="nationality" value="{{ old('nationality', $directory->nationality) }}" placeholder="Filipino">
                                                        @if($errors->has('nationality'))
                                                        <span class="text-danger small">{{ $errors->first('nationality') }}</span>
                                                        @endif
                                                    </div>
                                                    {{-- Occupation --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold" for="occupation"><i class="fas fa-briefcase text-muted mr-1"></i>{{ trans('cruds.directory.fields.occupation') }}</label>
                                                        <input class="form-control form-control-md {{ $errors->has('occupation') ? 'is-invalid' : '' }}" type="text" name="occupation" id="occupation" value="{{ old('occupation', $directory->occupation) }}" placeholder="Job title">
                                                        @if($errors->has('occupation'))
                                                        <span class="text-danger small">{{ $errors->first('occupation') }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <h6 class="text-muted mb-3 mt-2 font-weight-bold"><i class="fas fa-home mr-1"></i>Address Information</h6>

                                                <div class="row mb-3">
                                                    {{-- House No --}}
                                                    <div class="col-md-1 mb-3 pr-1">
                                                        <label class="form-label font-weight-bold" for="street_no">House #</label>
                                                        <input class="form-control form-control-md {{ $errors->has('street_no') ? 'is-invalid' : '' }}" type="text" name="street_no" id="street_no" value="{{ old('street_no', $directory->street_no) }}">
                                                        @if($errors->has('street_no'))
                                                        <span class="text-danger">{{ $errors->first('street_no') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.street_no_helper') }}</span>
                                                    </div>
                                                    {{-- Street --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="street">{{ trans('cruds.directory.fields.street') }}</label>
                                                        <input class="form-control form-control-md {{ $errors->has('street') ? 'is-invalid' : '' }}" type="text" name="street" id="street" value="{{ old('street', $directory->street) }}" placeholder="Enter Street">
                                                        @if($errors->has('street'))
                                                        <span class="text-danger">{{ $errors->first('street') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.street_helper') }}</span>
                                                    </div>
                                                    {{-- City --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="city">{{ trans('cruds.directory.fields.city') }}</label>
                                                        <input class="form-control form-control-md {{ $errors->has('city') ? 'is-invalid' : '' }}" type="text" name="city" id="city" value="{{ old('city', $directory->city) }}" placeholder="Enter City">
                                                        @if($errors->has('city'))
                                                        <span class="text-danger">{{ $errors->first('city') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.city_helper') }}</span>
                                                    </div>
                                                    {{-- Province --}}
                                                    <div class="col-md-2 mb-3">
                                                        <label class="form-label font-weight-bold" for="province">{{ trans('cruds.directory.fields.province') }}</label>
                                                        <input class="form-control form-control-md {{ $errors->has('province') ? 'is-invalid' : '' }}" type="text" name="province" id="province" value="{{ old('province', $directory->province) }}" placeholder="Enter Province">
                                                        @if($errors->has('province'))
                                                        <span class="text-danger">{{ $errors->first('province') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.province_helper') }}</span>
                                                    </div>
                                                    {{-- Barangay --}}
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label font-weight-bold" for="barangay_id">{{ trans('cruds.directory.fields.barangay') }}</label>
                                                        <select class="form-control form-control-md select2 {{ $errors->has('barangay') ? 'is-invalid' : '' }}" name="barangay_id" id="barangay_id">
                                                            @foreach($barangays as $id => $entry)
                                                            <option value="{{ $id }}" {{ (string)old('barangay_id', $directory->barangay_id) === (string)$id ? 'selected' : '' }}>{{ $entry }}</option>
                                                            @endforeach
                                                        </select>
                                                        @if($errors->has('barangay'))
                                                        <span class="text-danger">{{ $errors->first('barangay') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.directory.fields.barangay_helper') }}</span>
                                                    </div>
                                                </div>
                                            </div>{{-- /.col-md-10 --}}
                                        </div>{{-- /.row --}}
                                    </div>{{-- /.form-group --}}
                                </div>{{-- /.card-body --}}
                            </div>{{-- /.card --}}

                        </div>{{-- /.col-12 --}}
                    </div>{{-- /.row --}}
                </div>{{-- /.container-fluid --}}
            </section>

            {{-- ===== Family Composition (inside the same form) ===== --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-friends text-primary"></i> {{ trans('cruds.familycomposition.title_singular') }}</h5>
                        <small class="badge badge-info">Up to 6 rows</small>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm" id="family-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Names</th>
                                        <th>Birthday</th>
                                        <th>Relationship</th>
                                        <th>Civil Status</th>
                                        <th>Highest Education</th>
                                        <th>Occupation / Remarks</th>
                                        <th style="width:70px;">Action</th>
                                    </tr>
                                </thead>
                                {{-- EDIT-ready tbody --}}
                                <tbody>
                                    @if (is_array(old('family_name')))
                                    @foreach (old('family_name') as $i => $ignore)
                                    <tr class="fam-row">
                                        <td><input type="text" name="family_name[]" class="form-control" value="{{ old('family_name.'.$i) }}" placeholder="Full name"></td>
                                        <td><td>
  <input type="date" name="family_birthday[]" class="form-control" value="{{ optional($row->family_birthday)->toDateString() }}">
</td>


                                        </td>
                                     
                                        <td>
                                            <select name="family_relationship[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ old('family_relationship.'.$i) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_civil_status[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ old('family_civil_status.'.$i) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_highest_edu[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ old('family_highest_edu.'.$i) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <input type="text" name="family_occupation[]" class="form-control mb-1" value="{{ old('family_occupation.'.$i) }}" placeholder="Occupation">
                                                <input type="text" name="family_remarks[]" class="form-control" value="{{ old('family_remarks.'.$i) }}" placeholder="Remarks">
                                                <input type="hidden" name="family_others[]" value="{{ old('family_others.'.$i) }}">
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <button type="button" class="btn btn-sm btn-success add-row" title="Add row">+</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">–</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @elseif($directory->familycompositions->count())
                                    @foreach ($directory->familycompositions as $row)
                                    <tr class="fam-row">
                                        <td><input type="text" name="family_name[]" class="form-control" value="{{ $row->family_name }}" placeholder="Full name"></td>
                                        <td><input type="date" name="family_birthday[]" class="form-control"value="{{ old('family_birthday.'.$loop->index, optional($row->family_birthday)->format('Y-m-d')) }}"></td>
                                
                                        <td>
                                            <select name="family_relationship[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ $row->family_relationship == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_civil_status[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ $row->family_civil_status == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_highest_edu[]" class="form-control">
                                                <option value="" disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                                                <option value="{{ $key }}" {{ $row->family_highest_edu == $key ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <input type="text" name="family_occupation[]" class="form-control mb-1" value="{{ $row->occupation }}" placeholder="Occupation">
                                                <input type="text" name="family_remarks[]" class="form-control" value="{{ $row->remarks }}" placeholder="Remarks">
                                                <input type="hidden" name="family_others[]" value="{{ $row->others }}">
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <button type="button" class="btn btn-sm btn-success add-row" title="Add row">+</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">–</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr class="fam-row">
                                        <td><input type="text" name="family_name[]" class="form-control" placeholder="Full name"></td>
                                        <td><input type="date" name="family_birthday[]" class="form-control"></td>
                                        <td>
                                            <select name="family_relationship[]" class="form-control">
                                                <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_civil_status[]" class="form-control">
                                                <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="family_highest_edu[]" class="form-control">
                                                <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                                @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <input type="text" name="family_occupation[]" class="form-control mb-1" placeholder="Occupation">
                                                <input type="text" name="family_remarks[]" class="form-control" placeholder="Remarks">
                                                <input type="hidden" name="family_others[]" value="">
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            <button type="button" class="btn btn-sm btn-success add-row" title="Add row">+</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">–</button>
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            <div class="font-weight-bold mb-1">Please fix the errors below:</div>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>{{-- /.card-body --}}
                </div>{{-- /.card --}}
            </div>{{-- /.col-12 --}}

            {{-- ===== Other Information ===== --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Other Information</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-4" style="display:block;">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 bg-white h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-bold"><i class="fas fa-users text-muted mr-1"></i>{{ trans('cruds.directory.fields.ngo') }}</span>
                                            <button type="button" class="btn btn-tool collapse-toggle" data-target="#ngoCollapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                        <div id="ngoCollapse" class="collapse show">
                                            <div class="mb-2">
                                                <span class="btn btn-sm btn-outline-primary select-all">{{ trans('global.select_all') }}</span>
                                                <span class="btn btn-sm btn-outline-secondary deselect-all">{{ trans('global.deselect_all') }}</span>
                                            </div>
                                            @php $selectedNgos = old('ngos', $directory->ngos->pluck('id')->toArray()); @endphp
                                            <select class="form-control select2 {{ $errors->has('ngos') ? 'is-invalid' : '' }}" name="ngos[]" id="ngos" multiple>
                                                @foreach($ngos as $id => $ngo)
                                                <option value="{{ $id }}" {{ in_array($id, $selectedNgos ?? []) ? 'selected' : '' }}>{{ $ngo }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('ngos'))
                                            <span class="text-danger">{{ $errors->first('ngos') }}</span>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.directory.fields.ngo_helper') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 bg-white h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="font-weight-bold"><i class="fas fa-layer-group text-muted mr-1"></i>{{ trans('cruds.directory.fields.sector') }}</span>
                                            <button type="button" class="btn btn-tool collapse-toggle" data-target="#sectorsCollapse" title="Collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                        <div id="sectorsCollapse" class="collapse show">
                                            <div class="mb-2">
                                                <span class="btn btn-sm btn-outline-primary select-all">{{ trans('global.select_all') }}</span>
                                                <span class="btn btn-sm btn-outline-secondary deselect-all">{{ trans('global.deselect_all') }}</span>
                                            </div>
                                            @php $selectedSectors = old('sectors', $directory->sectors->pluck('id')->toArray()); @endphp
                                            <select class="form-control select2 {{ $errors->has('sectors') ? 'is-invalid' : '' }}" name="sectors[]" id="sectors" multiple>
                                                @foreach($sectors as $id => $sector)
                                                <option value="{{ $id }}" {{ in_array($id, $selectedSectors ?? []) ? 'selected' : '' }}>{{ $sector }}</option>
                                                @endforeach
                                            </select>
                                            @if($errors->has('sectors'))
                                            <span class="text-danger">{{ $errors->first('sectors') }}</span>
                                            @endif
                                            <span class="help-block">{{ trans('cruds.directory.fields.sector_helper') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold"><i class="fas fa-vote-yea text-muted mr-1"></i>{{ trans('cruds.directory.fields.comelec_status') }}</label>
                            <select class="form-control {{ $errors->has('comelec_status') ? 'is-invalid' : '' }}" name="comelec_status" id="comelec_status">
                                <option value disabled {{ old('comelec_status', $directory->comelec_status) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Directory::COMELEC_STATUS_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('comelec_status', $directory->comelec_status) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('comelec_status'))
                            <span class="text-danger">{{ $errors->first('comelec_status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.directory.fields.comelec_status_helper') }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold"><i class="fas fa-heartbeat text-muted mr-1"></i>{{ trans('cruds.directory.fields.life_status') }}</label>
                            <select class="form-control {{ $errors->has('life_status') ? 'is-invalid' : '' }}" name="life_status" id="life_status">
                                <option value disabled {{ old('life_status', $directory->life_status) === null ? 'selected' : '' }}>{{ trans('global.pleaseSelect') }}</option>
                                @foreach(App\Models\Directory::LIFE_STATUS_SELECT as $key => $label)
                                <option value="{{ $key }}" {{ old('life_status', $directory->life_status) == (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @if($errors->has('life_status'))
                            <span class="text-danger">{{ $errors->first('life_status') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.directory.fields.life_status_helper') }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold" for="description"><i class="fas fa-file-alt text-muted mr-1"></i>{{ trans('cruds.directory.fields.description') }}</label>
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" id="description">{{ old('description', $directory->description) }}</textarea>
                            @if($errors->has('description'))
                            <span class="text-danger">{{ $errors->first('description') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.directory.fields.description_helper') }}</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label font-weight-bold" for="remarks"><i class="fas fa-sticky-note text-muted mr-1"></i>{{ trans('cruds.directory.fields.remarks') }}</label>
                            <input class="form-control {{ $errors->has('remarks') ? 'is-invalid' : '' }}" type="text" name="remarks" id="remarks" value="{{ old('remarks', $directory->remarks) }}">
                            @if($errors->has('remarks'))
                            <span class="text-danger">{{ $errors->first('remarks') }}</span>
                            @endif
                            <span class="help-block">{{ trans('cruds.directory.fields.remarks_helper') }}</span>
                        </div>

                        <div class="form-group mt-4">
                            <button class="btn btn-md btn-outline-primary px-5" type="submit">
                                <i class="fas fa-save mr-2"></i>{{ trans('global.save') }}
                            </button>
                            <a href="{{ route('admin.directories.index') }}" class="btn btn-md btn-outline-danger px-5 ml-2">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </div>{{-- /.card-body --}}
                </div>{{-- /.card --}}
            </div>{{-- /.col-12 --}}

        </form>
    </div>{{-- /.card-body --}}
</div>{{-- /.card --}}
@endsection

@push('styles')
<style>
    .gap-2>* {
        margin-right: .25rem;
    }

    .gap-2>*:last-child {
        margin-right: 0;
    }

    /* Enhanced form styling */
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .card {
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
        padding: 1rem 1.25rem;
    }

    .required::after {
        content: " *";
        color: #dc3545;
        font-weight: bold;
    }

    .picture-container {
        text-align: center;
    }

    .picture {
        width: 150px;
        height: 150px;
        border: 3px solid #dee2e6;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .picture:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .picture img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .picture input[type="file"] {
        display: none;
    }

    .picture-label {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 0.5rem;
        cursor: pointer;
    }

    .picture-label:hover {
        color: #007bff;
    }

    /* Table styling */
    #family-table thead {
        background-color: #f8f9fa;
    }

    #family-table th {
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    #family-table td {
        vertical-align: middle;
    }

    #family-table .form-control {
        font-size: 0.875rem;
    }

    /* Button improvements */
    .btn-sm {
        font-size: 0.875rem;
        padding: 0.25rem 0.75rem;
    }

    .text-muted {
        opacity: 0.7;
    }

    /* Uniform help-block styling */
    .help-block {
        display: block;
        font-size: 0.7rem;
        margin-top: 4px;
        color: #6c757d;
    }

    .collapse-toggle { cursor:pointer; }
</style>
@endpush

@section('scripts')
@parent
<script>
    (function($) {
        function initFamilyRows() {
            const $table = $('#family-table');
            if (!$table.length) return;

            const MAX_ROWS = 6;
            const $tbody = $table.find('tbody');
            const $proto = $tbody.find('tr.fam-row').first();
            if (!$proto.length) return;

            function rowCount() {
                return $tbody.find('tr.fam-row').length;
            }

            function resetRow($tr) {
                $tr.find('input').each(function() {
                    if (this.type === 'hidden') {
                        this.value = ''; // family_others[]
                    } else {
                        this.value = '';
                    }
                });
                $tr.find('select').each(function() {
                    this.selectedIndex = 0; // placeholder option
                });
            }

            function updateAddButtons() {
                const full = rowCount() >= MAX_ROWS;
                $tbody.find('.add-row').prop('disabled', full).toggleClass('disabled', full);
            }

            function addRow() {
                if (rowCount() >= MAX_ROWS) return;
                const $clone = $proto.clone(false, false);
                resetRow($clone);
                $tbody.append($clone);
                updateAddButtons();
            }

            function removeRow(btn) {
                if (rowCount() <= 1) return;
                $(btn).closest('tr.fam-row').remove();
                updateAddButtons();
            }

            // Avoid duplicate bindings
            $tbody.off('.familyRows');

            // Delegate
            $tbody.on('click.familyRows', '.add-row', function(e) {
                e.preventDefault();
                addRow();
            });

            $tbody.on('click.familyRows', '.remove-row', function(e) {
                e.preventDefault();
                removeRow(this);
            });

            updateAddButtons();
        }

        $(initFamilyRows);
        document.addEventListener('turbolinks:load', initFamilyRows);
        document.addEventListener('livewire:load', initFamilyRows);
    })(jQuery);
</script>
@endsection

{{-- Keep Dropzone init in a single stack to avoid duplicate @section("scripts") collisions --}}
@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
  const input       = document.getElementById('wizard-picture');
  const img         = document.getElementById('wizardPicturePreview');
  const uploadBtn   = document.getElementById('upload-btn');
  const deleteBtn   = document.getElementById('delete-btn');
  const placeholder = "{{ asset('upload/free-user-icon.png') }}";
  let objectUrl     = null;

  function showPreview(file) {
    if (!file) { resetPreview(); return; }
    if (!/^image\//i.test(file.type)) { 
      alert('Please select an image file (JPG, PNG, GIF).'); 
      input.value=''; 
      return; 
    }
    if (file.size > 5 * 1024 * 1024) { 
      alert('Maximum file size is 5MB. Please choose a smaller image.'); 
      input.value=''; 
      return; 
    }

    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    img.src = objectUrl;
    
    // Show delete button when file is uploaded
    if (deleteBtn) deleteBtn.style.display = 'inline-block';
  }

  function resetPreview() {
    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = null;
    img.src = placeholder;
    input.value = '';
    
    // Hide delete button when no file
    if (deleteBtn) deleteBtn.style.display = 'none';
  }

  if (input) {
    input.addEventListener('change', function () {
      const file = this.files && this.files[0];
      showPreview(file);
    });
  }

  // Delete button functionality
  if (deleteBtn) {
    deleteBtn.addEventListener('click', function() {
      resetPreview();
    });
  }

  // Nice UX: click the image to open the file picker
  img.addEventListener('click', () => input && input.click());
});
</script>

<script>
// Simple collapse toggles (independent of Bootstrap JS)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.collapse-toggle').forEach(function(btn){
        btn.addEventListener('click', function(){
            var target = document.querySelector(btn.getAttribute('data-target'));
            if (!target) return;
            var icon = btn.querySelector('i');
            var visible = target.classList.contains('show');
            if (visible) {
                target.classList.remove('show');
                target.style.display = 'none';
                if (icon) icon.classList.remove('fa-minus'), icon.classList.add('fa-plus');
            } else {
                target.classList.add('show');
                target.style.display = '';
                if (icon) icon.classList.remove('fa-plus'), icon.classList.add('fa-minus');
            }
        });
    });
});
</script>





<script>
document.addEventListener('DOMContentLoaded', function () {
  var el = document.getElementById('birthday');
  if (!el) return;

  // Nuke common plugins if they latched on
  if (window.jQuery) {
    var $ = window.jQuery;
    try { if ($.fn.inputmask && $(el).inputmask) $(el).inputmask('remove'); } catch (e) {}
    try { if ($.fn.datepicker && $(el).datepicker) $(el).datepicker('destroy'); } catch (e) {}
    try { if ($.fn.datetimepicker && $(el).datetimepicker) $(el).datetimepicker('destroy'); } catch (e) {}
  }
});
</script>



@section('scripts')
@parent
<script>
(function ready() {
  var input = document.getElementById('wizard-picture');
  var img   = document.getElementById('wizardPicturePreview');
  if (!input || !img) return;

  function show(file){
    if (!file) return;
    if (!/^image\//i.test(file.type)) { alert('Please select an image file.'); input.value=''; return; }
    if (file.size > 5 * 1024 * 1024)   { alert('Max file size is 5MB.');      input.value=''; return; }
    var url = URL.createObjectURL(file);
    img.src = url;
    img.onload = function(){ try { URL.revokeObjectURL(url); } catch(e){} };
  }

  input.addEventListener('change', function(){ show(this.files && this.files[0]); });
  img.addEventListener('click', function(){ input && input.click(); });
})();

document.addEventListener('turbolinks:load', function(){ try { ready(); } catch(_){} });
document.addEventListener('livewire:load',  function(){ try { ready(); } catch(_){} });
</script>
@endsection

<script>
(function ($) {
  // On submit, convert dd/mm/yyyy (or d/m/yyyy) -> yyyy-mm-dd
  $(document).on('submit', 'form', function () {
    $(this).find('input[name="family_birthday[]"]').each(function () {
      var v = (this.value || '').trim();
      if (!v) return;

      // Accept d/m/Y, dd/mm/YYYY, d-m-Y, dd-mm-YYYY
      var m = v.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
      if (m) {
        var d  = m[1].padStart(2, '0');
        var mo = m[2].padStart(2, '0');
        var y  = m[3];
        this.value = y + '-' + mo + '-' + d; // ISO for backend/DB
      }
    });
  });
})(jQuery);
</script>
@endpush
