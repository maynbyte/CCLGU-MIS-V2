@extends('layouts.admin')

@section('styles')
<style>
    .profile-user-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
    }

    .label-badge {
        font-size: 11px;
        font-weight: 600;
        padding: .25rem .5rem;
        border-radius: 999px;
    }

    .small-label {
        color: #6c757d;
        font-size: .85rem;
        margin-bottom: .25rem;
        display: block;
    }
</style>

{{-- Dropzone assets - install via npm or use CDN if needed --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cb = document.getElementById('claimant_is_patient');
        const patient = document.getElementById('patient_name');
        const claimant = document.getElementById('claimant_name');
        const claimantContact = document.getElementById('claimant_contact_no');

        if (!cb || !patient || !claimant) return;

        function sync() {
            const isChecked = cb.checked;
            patient.readOnly = isChecked;
            claimant.readOnly = isChecked;
            if (claimantContact) claimantContact.readOnly = isChecked;
            
            // Optional: add visual feedback
            if (isChecked) {
                patient.classList.add('bg-light');
                claimant.classList.add('bg-light');
                if (claimantContact) claimantContact.classList.add('bg-light');
            } else {
                patient.classList.remove('bg-light');
                claimant.classList.remove('bg-light');
                if (claimantContact) claimantContact.classList.remove('bg-light');
            }
        }

        cb.addEventListener('change', sync);
        sync(); // set initial state on load
    });
</script>
@endpush
@endsection

@section('content')
@php
use Carbon\Carbon;
use App\Models\Familycomposition;

/** Side/profile helpers (same as your create page) **/
$lastName = $directory->last_name;
$firstName = $directory->first_name;
$middleName = $directory->middle_name ?? null;
$suffix = $directory->suffix ?? null;

$fullName = trim(
($firstName ?: '') . ' ' .
($middleName ? mb_substr($middleName,0,1).'. ' : '') .
($lastName ?: '') .
($suffix ? ' '.$suffix : '')
);
if (!$directory->first_name && !$directory->last_name) {
$fullName = 'Peter Viscaal';
}

$photoUrl = null;
try {
if (method_exists($directory, 'getAttribute') && $directory->profile_picture) {
if (method_exists($directory->profile_picture, 'getUrl')) {
$photoUrl = $directory->profile_picture->getUrl();
} else {
$photoUrl = $directory->profile_picture;
}
}
} catch (\Throwable $e) { $photoUrl = null; }

$templateAvatar = asset('upload/free-user-icon.png');

$gender = '';
if (!empty($directory->gender)) {
$gender = \App\Models\Directory::GENDER_SELECT[$directory->gender] ?? $directory->gender;
}
$gender = $gender ?: 'Male';

$age = '';
if (!empty($directory->birthday)) {
try { $age = Carbon::parse($directory->birthday)->age; } catch (\Throwable $e) { $age=''; }
}

$nationality = $directory->nationality ?? 'Filipino';
$occupation = $directory->occupation ?? 'Not specified';

$lifeStatusRaw = $directory->life_status ?? null;
$lifeStatus = $lifeStatusRaw ? (\App\Models\Directory::LIFE_STATUS_SELECT[$lifeStatusRaw] ?? $lifeStatusRaw) : null;
switch ($lifeStatus) {
case 'Alive': $lifeStatusColor = 'success'; break;
case 'Deceased': $lifeStatusColor = 'danger'; break;
default: $lifeStatusColor = 'secondary'; $lifeStatus = 'N/A'; break;
}

$streetNo = $directory->street_no ?? null;
$street = $directory->street ?? null;
$city = $directory->city ?? null;
$province = $directory->province ?? null;
$addrParts = array_filter([$streetNo, $street, $city, $province]);
$address = $addrParts ? implode(', ', $addrParts) : '83 Mile Drive, Los Angeles, CA';

$phone = $directory->contact_no ?? 'N/A';
$email = $directory->email ?? 'N/A';

$barangayName = optional($directory->barangay)->barangay_name ?? '';

$ngos = [];
if ($directory->ngos && count($directory->ngos)) {
foreach ($directory->ngos as $ngo) { $ngos[] = trim($ngo->name); }
}

$comelecStatusRaw = $directory->comelec_status ?? null;
$comelecStatus = $comelecStatusRaw
? (\App\Models\Directory::COMELEC_STATUS_SELECT[$comelecStatusRaw] ?? $comelecStatusRaw)
: null;
switch (strtolower((string) $comelecStatus)) {
case 'registered': $comelecStatusColor = 'success'; break;
case 'unregistered': $comelecStatusColor = 'danger'; break;
default: $comelecStatusColor = 'secondary'; $comelecStatus = 'N/A'; break;
}

$sectors = [];
if ($directory->sectors && count($directory->sectors)) {
foreach ($directory->sectors as $sector) { $sectors[] = trim($sector->name); }
}

$fmt = function ($raw) {
if (empty($raw)) return '';
try {
if ($raw instanceof \DateTimeInterface) return $raw->format('d/m/Y');
return Carbon::parse($raw)->format('d/m/Y');
} catch (\Throwable $e) { return ''; }
};

$notes = trim(($directory->description ?? '').' '.($directory->remarks ?? ''));
$notes = $notes ?: 'Knee pain, Headache, Last time he looked sick';

/** Options/constants pulled from model (same as create) **/
$types = \App\Models\FinancialAssistance::TYPE_OF_ASSISTANCE;
$statuses = \App\Models\FinancialAssistance::STATUS_OPTIONS;
$ppOptions = \App\Models\FinancialAssistance::PROBLEM_PRESENTED_OPTIONS;
$swoNames = \App\Models\FinancialAssistance::SWO_NAMES;
$swoDesigs = \App\Models\FinancialAssistance::SWO_DESIGS;

/** Pre-fill JSON-ish fields for edit **/
$req = old('requirement_checklist', $fa->requirement_checklist ?? []);
$ppv = old('problem_presented_value', $fa->problem_presented_value ?? []);
$ppvValues = is_array($ppv) ? ($ppv['values'] ?? (is_array($ppv) ? $ppv : [])) : [];
$ppvOther = is_array($ppv) ? ($ppv['other'] ?? '') : '';

$reqPatient = is_array($req) ? ($req['patient'] ?? []) : [];
$reqPatientOther = is_array($req) ? ($req['patient_other'] ?? '') : '';
$reqClaimant = is_array($req) ? ($req['claimant'] ?? []) : [];
$reqClaimantOther = is_array($req) ? ($req['claimant_other'] ?? '') : '';

// Determine claimantIsPatient: prefer explicit old input, otherwise treat DB value
// as true only when it equals 1 (avoid checking by default unintentionally).
if (old('claimant_is_patient') !== null) {
  // old() returns string values from the request ('1' when checked)
  $claimantIsPatient = (string) old('claimant_is_patient') === '1';
} else {
  // Only consider DB value true when exactly integer 1
  $claimantIsPatient = isset($fa->claimant_is_patient) && ((int) $fa->claimant_is_patient === 1);
}

/** Defaults (only if nothing saved/posted) **/
if (!old('problem_presented_value') && empty($ppvValues)) {
$ppvValues = ['Medical Assistance'];
}
if (!old('req_patient') && empty($reqPatient)) {
$reqPatient = ['Medical Certificate / Medical Abstract', 'Lab Request / Reseta'];
}
if (!old('req_claimant') && empty($reqClaimant)) {
$reqClaimant = ['Photocopy of Valid ID', 'Original Barangay Certificate', 'Original Barangay Indigency'];
}
@endphp

<div class="mb-3">
  <a class="btn btn-default" href="{{ route('admin.financial-assistances.index') }}">
    {{ trans('global.back_to_list') }}
  </a>
</div>

<div class="container-fluid">
    <div class="row">
        {{-- LEFT COLUMN --}}
        <div class="col-lg-3">
            {{-- Profile --}}
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        <img class="profile-user-img img-fluid img-circle"
                            src="{{ $photoUrl ?: $templateAvatar }}"
                            alt="Profile picture">
                    </div>
                    <h3 class="profile-username text-center mt-3 mb-1">{{ $fullName }}</h3>
                    <p class="text-center">
                        <span class="badge bg-{{ $comelecStatusColor }}">{{ $comelecStatus }}</span>
                    </p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Gender</span>
                            <span class="text-dark font-weight-medium">{{ $gender }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Age</span>
                            <span class="text-dark font-weight-medium">{{ $age }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">City / Municipality</span>
                            <span class="text-dark font-weight-medium">{{ $city }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Occupation</span>
                            <span class="text-dark font-weight-medium">{{ $occupation }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted">Life Status</span>
                            <span class="text-dark font-weight-medium">{{ $lifeStatus }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Sector --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sector</h3>
                </div>
                <div class="card-body">
                    @if(count($sectors))
                    @foreach($sectors as $sector)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark">{{ $sector }}</span>
                        <span class="badge badge-success label-badge">Active</span>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">N/A</p>
                    @endif
                </div>
            </div>

            {{-- NGO --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Non-Government Organization</h3>
                </div>
                <div class="card-body">
                    @if(count($ngos))
                    @foreach($ngos as $ngo)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark">{{ $ngo }}</span>
                        <span class="badge badge-success label-badge">Active</span>
                    </div>
                    @endforeach
                    @else
                    <p class="text-muted">N/A</p>
                    @endif
                </div>
            </div>

            {{-- Notes --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Notes</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0 text-muted">{{ $notes }}</p>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-9">
            <div class="card">
              <div class="card-header p-2 d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Edit Financial Assistance</h5>
                @can('directory_edit')
                  <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> {{ trans('global.edit') }} Directory
                  </a>
                @endcan
              </div>
                <div class="card-body">
                    <form id="fa-form" method="POST" action="{{ route('admin.financial-assistances.update', $fa->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Keep the directory context --}}
                <input type="hidden" name="directory_id" value="{{ $directory->id }}">

                <div class="row">
                  {{-- Type of Assistance --}}
                  <div class="col-md-6">
                    @php $defaultType = 'Financial Assistance'; @endphp
                    <div class="form-group">
                      <label>Type of Assistance</label>
                      <select name="type_of_assistance" class="form-control">
                        @foreach ($types as $opt)
                          <option value="{{ $opt }}"
                            {{ old('type_of_assistance', $fa->type_of_assistance ?? $defaultType) === $opt ? 'selected' : '' }}
                            {{ $opt !== 'Financial Assistance' ? 'disabled' : '' }}>
                            {{ $opt }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  {{-- Patient Name --}}
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Patient Name</label>
                       @php
                         $patientValue = old('patient_name', $fa->patient_name ?? '');
                         if ($patientValue === '') { $patientValue = $fullName; }
                       @endphp
                       <input type="text" name="patient_name" id="patient_name" class="form-control"
                         value="{{ $patientValue }}" placeholder="{{ $fullName ?: 'Full name' }}"
                         {{ $claimantIsPatient ? 'readonly' : '' }}>
                    </div>
                  </div>
                </div>

                <div class="row">
                  {{-- Claimant Name --}}
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Claimant Name</label>
                      @php
                        $claimantValue = old('claimant_name', $fa->claimant_name ?? '');
                        // Only sync with patient name if checkbox is actually checked in DB
                        if ($claimantValue === '' && $claimantIsPatient && $patientValue) { 
                          $claimantValue = $patientValue; 
                        }
                      @endphp
                      <input type="text" name="claimant_name" id="claimant_name" class="form-control"
                        value="{{ $claimantValue }}" placeholder="{{ $fullName ?: 'Full name' }}"
                        {{ $claimantIsPatient ? 'readonly' : '' }}>
                    </div>
                  </div>

                  {{-- Claimant Contact No. --}}
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Claimant Contact No.</label>
                      @php
                        $claimantContactValue = old('claimant_contact_no', $fa->claimant_contact_no ?? '');
                        if ($claimantContactValue === '' && $claimantIsPatient) { 
                          $claimantContactValue = $phone; 
                        }
                      @endphp
                      <input type="text" name="claimant_contact_no" id="claimant_contact_no" class="form-control"
                        value="{{ $claimantContactValue }}" placeholder="{{ $phone }}">
                    </div>
                  </div>
                </div>

                {{-- Checkbox below the fields --}}
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-check mb-3">
                      {{-- Hidden input ensures claimant_is_patient always submits (0 when unchecked) --}}
                      <input type="hidden" name="claimant_is_patient" value="0">
                      <input class="form-check-input" type="checkbox" id="claimant_is_patient" name="claimant_is_patient" value="1"
                             {{ $claimantIsPatient ? 'checked' : '' }}>
                      <label class="form-check-label" for="claimant_is_patient">
                        Claimant and Patient are the same
                      </label>
                    </div>
                  </div>
                </div>

                {{-- Requirement Checklist: PATIENT --}}
                <div class="form-group">
                  <label>Requirements – Patient / Beneficiary</label>
                  @php
                    $patientChoices = [
                      'Medical Certificate / Medical Abstract',
                      'Lab Request / Reseta',
                      'Solicitation Letter',
                      'Death Certificate',
                      'Funeral contract',
                      'Birth Certificate',
                      'Certificate of Enrollment or Registration Form',
                      'Others',
                    ];
                  @endphp
                  <div class="row">
                    @foreach($patientChoices as $opt)
                      <div class="col-md-4">
                        <div class="form-check">
                          <input class="form-check-input req-patient" type="checkbox" name="req_patient[]"
                                 id="req_patient_{{ md5($opt) }}" value="{{ $opt }}"
                                 {{ in_array($opt, $reqPatient) ? 'checked' : '' }}>
                          <label class="form-check-label" for="req_patient_{{ md5($opt) }}">{{ $opt }}</label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <div class="mt-2" id="req_patient_other_wrap" style="{{ in_array('Others', $reqPatient) ? '' : 'display:none' }}">
                    <input type="text" name="req_patient_other" class="form-control" placeholder="Specify other requirement"
                           value="{{ old('req_patient_other', $reqPatientOther) }}">
                  </div>
                </div>

                {{-- Requirement Checklist: CLAIMANT --}}
                <div class="form-group">
                  <label>Requirements – Claimant</label>
                  @php
                    $claimantChoices = [
                      'Photocopy of Valid ID',
                      'Birth Certificate',
                      'Original Barangay Certificate',
                      'Original Barangay Indigency',
                      'Death Certificate',
                      'Others',
                    ];
                  @endphp
                  <div class="row">
                    @foreach($claimantChoices as $opt)
                      <div class="col-md-4">
                        <div class="form-check">
                          <input class="form-check-input req-claimant" type="checkbox" name="req_claimant[]"
                                 id="req_claimant_{{ md5($opt) }}" value="{{ $opt }}"
                                 {{ in_array($opt, $reqClaimant) ? 'checked' : '' }}>
                          <label class="form-check-label" for="req_claimant_{{ md5($opt) }}">{{ $opt }}</label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <div class="mt-2" id="req_claimant_other_wrap" style="{{ in_array('Others', $reqClaimant) ? '' : 'display:none' }}">
                    <input type="text" name="req_claimant_other" class="form-control" placeholder="Specify other requirement"
                           value="{{ old('req_claimant_other', $reqClaimantOther) }}">
                  </div>
                </div>

                {{-- Problem Presented (multi) --}}
                <div class="form-group">
                  <label>Problem Presented</label>
                  <div class="row">
                    @foreach($ppOptions as $opt)
                      <div class="col-md-4">
                        <div class="form-check">
                          <input class="form-check-input ppv" type="checkbox" name="problem_presented_value[]"
                                 id="ppv_{{ md5($opt) }}" value="{{ $opt }}"
                                 {{ in_array($opt, $ppvValues) ? 'checked' : '' }}>
                          <label class="form-check-label" for="ppv_{{ md5($opt) }}">{{ $opt }}</label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                  <div class="mt-2" id="ppv_other_wrap" style="{{ in_array('Others', $ppvValues) ? '' : 'display:none' }}">
                    <input type="text" name="problem_presented_other" class="form-control" placeholder="Specify other problem"
                           value="{{ old('problem_presented_other', $ppvOther) }}">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="date_interviewed">{{ trans('cruds.financialAssistance.fields.date_interviewed') }}</label>
                      <input class="form-control datetime {{ $errors->has('date_interviewed') ? 'is-invalid' : '' }}" type="text"
                             name="date_interviewed" id="date_interviewed"
                             value="{{ old('date_interviewed',
                               $fa->date_interviewed
                                 ? (function($raw){ try { return Carbon::parse($raw)->format(config('panel.date_format').' '.config('panel.time_format')); } catch(\Throwable $e){ return ''; } })($fa->date_interviewed)
                                 : now('Asia/Manila')->format(config('panel.date_format').' '.config('panel.time_format'))
                             ) }}">
                      @if($errors->has('date_interviewed')) <span class="text-danger">{{ $errors->first('date_interviewed') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_interviewed_helper') }}</span>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="amount">{{ trans('cruds.financialAssistance.fields.amount') }}</label>
                      <input type="number" step="0.01" name="amount" id="amount"
                             class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                             value="{{ old('amount', $fa->amount ?? '') }}">
                      @if($errors->has('amount')) <span class="text-danger">{{ $errors->first('amount') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.amount_helper') }}</span>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="scheduled_fa">{{ trans('cruds.financialAssistance.fields.scheduled_fa') }}</label>
                      <input class="form-control {{ $errors->has('scheduled_fa') ? 'is-invalid' : '' }}" type="date"
                             name="scheduled_fa" id="scheduled_fa"
                             value="{{ old('scheduled_fa', $fa->scheduled_fa ? (function($raw){ try { return Carbon::parse($raw)->format('Y-m-d'); } catch(\Throwable $e){ return ''; } })($fa->scheduled_fa) : '') }}">
                      @if($errors->has('scheduled_fa')) <span class="text-danger">{{ $errors->first('scheduled_fa') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.scheduled_fa_helper') }}</span>
                    </div>
                  </div>

                  <div class="col-md-3">
                    @php
                      $statuses = ['Ongoing','Pending','Claimed','Cancelled'];
                      $currentStatus = old('status', $fa->status ?? 'Ongoing');
                    @endphp
                    <label for="status">{{ trans('cruds.financialAssistance.fields.status') }}</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                      @foreach ($statuses as $s)
                        <option value="{{ $s }}" {{ $currentStatus === $s ? 'selected' : '' }}>{{ $s }}</option>
                      @endforeach
                    </select>
                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    <span class="help-block">{{ trans('cruds.financialAssistance.fields.status_helper') }}</span>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    @php
                      $defaultAssessment = "Client is assessed to be in dire need of financial assistance due to insufficient family's income to defray the cost of";
                    @endphp
                    <div class="form-group">
                      <label for="assessment">{{ trans('cruds.financialAssistance.fields.assessment') }}</label>
                      <input class="form-control {{ $errors->has('assessment') ? 'is-invalid' : '' }}" type="text"
                             name="assessment" id="assessment"
                             value="{{ old('assessment', $fa->assessment ?? $defaultAssessment) }}">
                      @if($errors->has('assessment')) <span class="text-danger">{{ $errors->first('assessment') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.assessment_helper') }}</span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    @php
                      $defaultRecommendation = 'In view of the above information, client is recommended for any possible Financial Assistance.';
                    @endphp
                    <div class="form-group">
                      <label for="recommendation">{{ trans('cruds.financialAssistance.fields.recommendation') }}</label>
                      <input class="form-control {{ $errors->has('recommendation') ? 'is-invalid' : '' }}" type="text"
                             name="recommendation" id="recommendation"
                             value="{{ old('recommendation', $fa->recommendation ?? $defaultRecommendation) }}">
                      @if($errors->has('recommendation')) <span class="text-danger">{{ $errors->first('recommendation') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.recommendation_helper') }}</span>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="date_claimed">{{ trans('cruds.financialAssistance.fields.date_claimed') }}</label>
                  <input type="datetime-local" name="date_claimed" class="form-control"
                         value="{{ old('date_claimed',
                            isset($fa->date_claimed_for_input) && $fa->date_claimed_for_input
                              ? $fa->date_claimed_for_input
                              : ($fa->date_claimed ? (function($raw){ try { return Carbon::parse($raw)->format('Y-m-d\TH:i'); } catch(\Throwable $e){ return ''; } })($fa->date_claimed) : '')
                         ) }}">
                  @if($errors->has('date_claimed')) <span class="text-danger">{{ $errors->first('date_claimed') }}</span> @endif
                  <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_claimed_helper') }}</span>
                </div>

                <div class="row">
                  {{-- Social Worker Name --}}
                  <div class="col-md-6">
                    @php
                      $swDefault = old('social_welfare_name', $fa->social_welfare_name ?? (is_array($swoNames) ? (array_values($swoNames)[0] ?? '') : ''));
                    @endphp
                    <div class="form-group">
                      <label>Social Worker Name</label>
                      <select name="social_welfare_name" class="form-control">
                        <option value="">{{ trans('global.pleaseSelect') }}</option>
                        @foreach($swoNames as $opt)
                          <option value="{{ $opt }}" {{ $swDefault === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  {{-- Social Worker Designation --}}
                  <div class="col-md-6">
                    @php
                      $desigDefault = old('social_welfare_desig', $fa->social_welfare_desig ?? (is_array($swoDesigs) ? (array_values($swoDesigs)[0] ?? '') : ''));
                    @endphp
                    <div class="form-group">
                      <label>Social Worker Designation</label>
                      <select name="social_welfare_desig" class="form-control">
                        <option value="">{{ trans('global.pleaseSelect') }}</option>
                        @foreach($swoDesigs as $opt)
                          <option value="{{ $opt }}" {{ $desigDefault === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="note">{{ trans('cruds.financialAssistance.fields.note') }}</label>
                  <input class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" type="text" name="note" id="note"
                         value="{{ old('note', $fa->note ?? '') }}">
                  @if($errors->has('note')) <span class="text-danger">{{ $errors->first('note') }}</span> @endif
                  <span class="help-block">{{ trans('cruds.financialAssistance.fields.note_helper') }}</span>
                </div>

                {{-- Requirements Dropzone --}}
                <div class="form-group">
                  <label for="requirements">{{ trans('cruds.financialAssistance.fields.requirements') }}</label>
                  <div class="needsclick dropzone {{ $errors->has('requirements') ? 'is-invalid' : '' }}" id="requirements-dropzone"></div>
                  @if($errors->has('requirements')) <span class="text-danger">{{ $errors->first('requirements') }}</span> @endif
                  <span class="help-block">{{ trans('cruds.financialAssistance.fields.requirements_helper') }}</span>
                </div>

                <div class="form-group">
                  <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                  </button>
                </div>
                  </form>
                </div>
              </div>
            </div> {{-- /RIGHT --}}

    </div>
</div>

<div class="mt-3">
  <a class="btn btn-default" href="{{ route('admin.financial-assistances.index') }}">
    {{ trans('global.back_to_list') }}
  </a>
</div>
@endsection

@section('scripts')
<script>
    // Prevent Dropzone from auto-discovering
    Dropzone.autoDiscover = false;
    
    var uploadedRequirementsMap = {}
Dropzone.options.requirementsDropzone = {
    url: '{{ route('admin.financial-assistances.storeMedia') }}',
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    },
    params: {
      size: 10
    },
    success: function (file, response) {
      $('form').append('<input type="hidden" name="requirements[]" value="' + response.name + '">')
      uploadedRequirementsMap[file.name] = response.name
    },
    removedfile: function (file) {
      file.previewElement.remove()
      var name = ''
      if (typeof file.file_name !== 'undefined') {
        name = file.file_name
      } else {
        name = uploadedRequirementsMap[file.name]
      }
      $('form').find('input[name="requirements[]"][value="' + name + '"]').remove()
    },
    init: function () {
@if(isset($financialAssistance) && $financialAssistance->requirements)
          var files =
            {!! json_encode($financialAssistance->requirements) !!}
              for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="requirements[]" value="' + file.file_name + '">')
            }
@endif
    },
     error: function (file, response) {
         if ($.type(response) === 'string') {
             var message = response //dropzone sends its own error messages in string
         } else {
             var message = response.errors.file
         }
         file.previewElement.classList.add('dz-error')
         _ref = file.previewElement.querySelectorAll('[data-dz-errormessage]')
         _results = []
         for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i]
             _results.push(node.textContent = message)
         }

         return _results
     }
}
</script>

<script>
    (function() {
        function toggleWrap(selector, inputSelector, triggerValue) {
            const anyChecked = Array.from(document.querySelectorAll(inputSelector))
                .some(cb => cb.checked && cb.value === triggerValue);
            var el = document.querySelector(selector);
            if (el) el.style.display = anyChecked ? '' : 'none';
        }

        function wire(groupClass, wrapSelector) {
            document.querySelectorAll(groupClass).forEach(cb => {
                cb.addEventListener('change', function() {
                    toggleWrap(wrapSelector, groupClass, 'Others');
                });
            });
            toggleWrap(wrapSelector, groupClass, 'Others'); // initial
        }
        wire('.req-patient', '#req_patient_other_wrap');
        wire('.req-claimant', '#req_claimant_other_wrap');
        wire('.ppv', '#ppv_other_wrap');
    })();
</script>



<script>
  // Keep single Others toggle script (DataTables removed with previous FA list)
  (function() {
    function toggleWrap(selector, inputSelector, triggerValue) {
      const anyChecked = Array.from(document.querySelectorAll(inputSelector))
        .some(cb => cb.checked && cb.value === triggerValue);
      var el = document.querySelector(selector);
      if (el) el.style.display = anyChecked ? '' : 'none';
    }
    function wire(groupClass, wrapSelector) {
      document.querySelectorAll(groupClass).forEach(cb => {
        cb.addEventListener('change', function() {
          toggleWrap(wrapSelector, groupClass, 'Others');
        });
      });
      toggleWrap(wrapSelector, groupClass, 'Others');
    }
    wire('.req-patient', '#req_patient_other_wrap');
    wire('.req-claimant', '#req_claimant_other_wrap');
    wire('.ppv', '#ppv_other_wrap');
  })();
</script>
<script>
  // Defensive handler: ensure claimant/patient readonly sync works even if other scripts ran earlier
  (function(){
    function initClaimantPatientSync(){
      var cb = document.getElementById('claimant_is_patient');
      var patient = document.getElementById('patient_name');
      var claimant = document.getElementById('claimant_name');
      var claimantContact = document.getElementById('claimant_contact_no');
      if (!cb || !patient || !claimant) return;

      // Store the directory contact number from PHP
      var directoryContactNo = '{{ $directory->contact_no ?? "" }}';

      function applyState(){
        var checked = !!cb.checked;
        patient.readOnly = checked;
        claimant.readOnly = checked;
        if (claimantContact) claimantContact.readOnly = checked;
        
        patient.classList.toggle('bg-light', checked);
        claimant.classList.toggle('bg-light', checked);
        if (claimantContact) claimantContact.classList.toggle('bg-light', checked);
        
        // Sync claimant name and contact when checkbox is checked
        if (checked) {
          if (patient.value) {
            claimant.value = patient.value;
          }
          if (claimantContact && directoryContactNo && directoryContactNo !== 'N/A') {
            claimantContact.value = directoryContactNo;
          }
        }
      }

      cb.addEventListener('change', function(){
        applyState();
      });

      // Keep claimant in sync while checked (only when checkbox is checked)
      patient.addEventListener('input', function(){ 
        if (cb.checked) claimant.value = patient.value; 
      });

      // initial
      applyState();
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initClaimantPatientSync);
    } else {
      initClaimantPatientSync();
    }
  })();
</script>
@endsection