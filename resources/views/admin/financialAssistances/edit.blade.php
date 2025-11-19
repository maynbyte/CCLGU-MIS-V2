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

<link rel="stylesheet" href="{{ asset('plugins/dropzone/min/dropzone.min.css') }}">
<script src="{{ asset('plugins/dropzone/min/dropzone.min.js') }}"></script>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cb = document.getElementById('claimant_is_patient');
        const patient = document.getElementById('patient_name');
        if (!cb || !patient) return;

        function sync() {
            patient.disabled = cb.checked;
        }
        cb.addEventListener('change', sync);
        sync();
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

$templateAvatar = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCpU3fnq0AodONDFbe63OCRcl74XtWfXZenSLGxnDN33o0nNN0fgZIpyC2RFkw6tOa_TTRUnok8WXiswhLXqu5df1jGbGCqSwONnsRTovZGpjRnSK9S4PmEy2gEwDOQ_4ojHi8XRxFci-A8BJmhaaTIzB8-F1nClIFc89wRLsQKHH5J46S0iYGS62AIBZpoAGzwGD27EYMSY5UWz3BRM7f_ey1aONQXyQ_z7YRcGxXgOvezMdvjruvnxGAjsM0wZM323TxTG-ECT60_';

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

$claimantIsPatient = (bool) old('claimant_is_patient', $fa->claimant_is_patient ?? false);

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
    <a class="btn btn-default" href="{{ route('admin.directories.index') }}">
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
    <div class="card-header p-2 d-flex align-items-center">
      <ul class="nav nav-pills flex-grow-1" role="tablist">
        <li class="nav-item">
          <a class="nav-link" href="#tab-general-info" data-toggle="tab" role="tab">General Information</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="#tab-edit-FA" data-toggle="tab" role="tab">Edit Financial Assistance</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#tab-previous-FA" data-toggle="tab" role="tab">Previous FA</a>
        </li>
      </ul>

      @can('financial_assistance_create')
        {{-- Pass the current directory so the create form is pre-filled --}}
        <a href="{{ route('admin.financial-assistances.create', ['directory_id' => $directory->id]) }}"
            class="btn btn-success btn-sm ml-2">
            <i class="fas fa-plus-circle"></i> Add Financial Assistance
        </a>
        @endcan

      @can('directory_edit')
      <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm ml-2">
        <i class="fas fa-edit"></i> {{ trans('global.edit') }} Directory
      </a>
      @endcan
    </div>

    <div class="card-body">
      <div class="tab-content">

      {{-- GENERAL INFORMATION --}}
        <div class="tab-pane" id="tab-general-info" role="tabpanel">
          <div>
            {{-- PERSONAL DETAILS --}}
            <div class="mb-4 pb-3 border-bottom">
              <h6 class="text-muted font-weight-bold mb-3">PERSONAL DETAILS</h6>
              <div class="row">
                @if(!empty($directory->maiden_surname))
                  <div class="col-md-3 mb-3">
                    <span class="small-label">Maiden Surname</span>
                    <div class="font-weight-medium">{{ $directory->maiden_surname }}</div>
                  </div>
                @endif

                <div class="col-md-3 mb-3">
                  <span class="small-label">Last name</span>
                  <div class="font-weight-medium">{{ $directory->last_name}} {{ $directory->suffix }}</div>
                </div>
                <div class="col-md-3 mb-3">
                  <span class="small-label">First name</span>
                  <div class="font-weight-medium">{{ $directory->first_name}}</div>
                </div>
                <div class="col-md-3 mb-3">
                  <span class="small-label">Middle Name</span>
                  <div class="font-weight-medium">{{ $directory->middle_name ?? '-' }}</div>
                </div>

                <div class="col-md-3 mb-3">
                  <span class="small-label">Birthdate</span>
                  <div class="font-weight-medium">
                    {{ $directory->birthday ? \Carbon\Carbon::parse($directory->birthday)->format('F j, Y') : '-' }}
                  </div>
                </div>

                <div class="col-md-3 mb-3">
                  <span class="small-label">Address</span>
                  <div class="font-weight-medium">
                    @php
                      $displayBarangay = trim((string)($directory->barangay_other ?? ''));
                      if ($displayBarangay === '') {
                        $displayBarangay = $barangayName ?? optional($directory->barangay)->barangay_name;
                      }
                    @endphp
                    {{ $address }}
                    @if(!empty($displayBarangay))
                      <span class="text-muted"> • </span><span>{{ $displayBarangay }}</span>
                    @endif
                  </div>
                </div>

                <div class="col-md-3 mb-3">
                  <span class="small-label">Phone number</span>
                  <div class="font-weight-medium">{{ $phone }}</div>
                </div>
                <div class="col-md-3 mb-3">
                  <span class="small-label">Email</span>
                  <div class="font-weight-medium">{{ $email }}</div>
                </div>
              </div>
            </div>

            {{-- BACKGROUND INFORMATION --}}
            <div class="mb-4 pb-3 border-bottom">
              <h6 class="text-muted font-weight-bold mb-3">BACKGROUND INFORMATION</h6>
              <div class="row">
                <div class="col-lg-3 col-md-6 mb-3">
                  <span class="small-label">Highest Educational Attainment</span>
                  <span class="text-muted"> </span><span>{{ $directory->highest_edu }}</span>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <span class="small-label">Religion</span>
                  <span class="text-muted"> </span><span>{{ $directory->religion }}</span>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <span class="small-label">Civil Status</span>
                  <span class="text-muted"> </span><span>{{ $directory->civil_status }}</span>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                  <span class="small-label">Place of Birth</span>
                  <span class="text-muted"> </span><span>{{ $directory->place_of_birth }}</span>
                </div>
              </div>
            </div>

            {{-- FAMILY COMPOSITION --}}
            <div>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted font-weight-bold mb-0">Family Composition</h6>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead class="thead-light">
                    <tr>
                      <th>Names</th>
                      <th>Birthday</th>
                      <th>Relationship</th>
                      <th>Civil Status</th>
                      <th>Highest Education</th>
                      <th>Occupation</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse ($directory->familycompositions as $fam)
                      <tr>
                        <td>{{ $fam->family_name ?? '—' }}</td>
                        <td>{{ $fmt($fam->family_birthday) ?: '—' }}</td>
                        <td>{{ Familycomposition::FAMILY_RELATIONSHIP_SELECT[$fam->family_relationship] ?? ($fam->family_relationship ?? '—') }}</td>
                        <td>{{ Familycomposition::FAMILY_CIVIL_STATUS_SELECT[$fam->family_civil_status] ?? ($fam->family_civil_status ?? '—') }}</td>
                        <td>{{ Familycomposition::FAMILY_HIGHEST_EDU_SELECT[$fam->family_highest_edu] ?? ($fam->family_highest_edu ?? '—') }}</td>
                        <td>{{ $fam->occupation ?? '—' }}</td>
                        <td>{{ $fam->remarks ?? '—' }}</td>
                      </tr>
                    @empty
                      <tr><td colspan="7" class="text-muted">No family members added.</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>

        {{-- EDIT FINANCIAL ASSISTANCE (ACTIVE) --}}
        <div class="tab-pane active" id="tab-edit-FA" role="tabpanel">
          <div class="card">
            <div class="card-header">
              {{ trans('global.edit') }} {{ trans('cruds.financialAssistance.title_singular') }}
            </div>
            <div class="card-body">
              <form id="fa-form" method="POST" action="{{ route('admin.financial-assistances.update', $fa->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Keep the directory context --}}
                <input type="hidden" name="directory_id" value="{{ $directory->id }}">

                <div class="row">
                  {{-- Type of Assistance --}}
                  <div class="col-md-4">
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

                  {{-- Patient Name + claimant is patient --}}
                  <div class="col-md-8">
                    <div class="form-group mb-2">
                      <label>Patient Name</label>
                      <input type="text" name="patient_name" id="patient_name" class="form-control"
                             value="{{ old('patient_name', $fa->patient_name ?? '') }}" placeholder="Full name"
                             {{ $claimantIsPatient ? 'disabled' : '' }}>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="checkbox" id="claimant_is_patient" name="claimant_is_patient" value="1"
                             {{ old('claimant_is_patient', $fa->claimant_is_patient ?? false) ? 'checked' : '' }}>
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
                                 ? Carbon::parse($fa->date_interviewed)->format(config('panel.date_format').' '.config('panel.time_format'))
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
                             value="{{ old('scheduled_fa', $fa->scheduled_fa ? Carbon::parse($fa->scheduled_fa)->format('Y-m-d') : '') }}">
                      @if($errors->has('scheduled_fa')) <span class="text-danger">{{ $errors->first('scheduled_fa') }}</span> @endif
                      <span class="help-block">{{ trans('cruds.financialAssistance.fields.scheduled_fa_helper') }}</span>
                    </div>
                  </div>

                  <div class="col-md-3">
                    @php
                      $statuses = ['Pending','Claimed','Cancelled'];
                      $currentStatus = old('status', $fa->status ?? 'Pending');
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
                              : ($fa->date_claimed ? Carbon::parse($fa->date_claimed)->format('Y-m-d\TH:i') : '')
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
        </div>

        {{-- PREVIOUS FA (NOT ACTIVE BY DEFAULT) --}}
        <div class="tab-pane" id="tab-previous-FA" role="tabpanel">
         <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-muted font-weight-bold mb-0">Financial Assistance List</h6>
                                </div>

                                @php
                                $fmtLong = function ($raw) {
                                if (empty($raw)) return '—';
                                try {
                                $dt = $raw instanceof \DateTimeInterface
                                ? \Carbon\Carbon::instance($raw)
                                : \Carbon\Carbon::parse((string)$raw);
                                return $dt->format('F j (l), Y'); // e.g., September 29 (Monday), 2025
                                } catch (\Throwable $e) { return '—'; }
                                };
                                $list = $directory->financialAssistances ?? collect();
                                @endphp


                                <div class="table-responsive">

                                    <div class="card mt-3">
                                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
                                        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                                        <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

                                        @php
                                        // Pretty long-date formatter
                                        $fmtLong = function ($raw) {
                                        if (empty($raw)) return '—';
                                        try {
                                        $dt = $raw instanceof \DateTimeInterface
                                        ? \Carbon\Carbon::instance($raw)
                                        : \Carbon\Carbon::parse((string)$raw);
                                        return $dt->format('F j (l), Y'); // e.g., September 29 (Monday), 2025
                                        } catch (\Throwable $e) { return '—'; }
                                        };

                                        $list = ($list ?? collect());
                                        @endphp

                                    

                                        <div class="table-responsive">
                                            <table id="fa-table" class="table table-striped table-sm mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Reference No.</th>
                                                        <th>Type of Assistance</th>
                                                        <th>Patient Name</th>
                                                        <th>Date of Application</th>
                                                        <th>Payout Schedule</th>
                                                        <th>Date Claimed</th>
                                                        <th>Status</th>
                                                        <th>Added By</th>
                                                        <th>Settings</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($list as $fa)
                                                    @php
                                                    $isSame = (int)($fa->claimant_is_patient ?? 0) === 1;
                                                    $patientDisplay = $isSame ? ($fullName ?? '—') : ($fa->patient_name ?: '—');

                                                    // Pretty display for Payout Schedule
                                                    $schedDisplay = $fa->scheduled_fa
                                                    ? \Carbon\Carbon::parse($fa->scheduled_fa)->format('F j (l), Y')
                                                    : '—';

                                                    // Badge color
                                                    $status = $fa->status ?: '—';
                                                    $statusLower = strtolower($fa->status ?? '');
                                                    $badgeClass = 'secondary';
                                                    if ($statusLower === 'claimed') $badgeClass = 'success';
                                                    elseif ($statusLower === 'pending') $badgeClass = 'warning';
                                                    elseif (in_array($statusLower, ['cancelled','canceled'])) $badgeClass = 'danger';

                                                    // Application date (choose your real column, fallback to created_at)
                                                    $applicationRaw = $fa->application_date ?? $fa->date ?? $fa->created_at ?? null;

                                                    // Timestamps for correct sorting (DataTables uses data-order)
                                                    $appTs = 0;
                                                    if ($applicationRaw instanceof \DateTimeInterface) { $appTs = $applicationRaw->getTimestamp(); }
                                                    elseif (!empty($applicationRaw)) { try { $appTs = \Carbon\Carbon::parse((string)$applicationRaw)->timestamp; } catch (\Throwable $e) {} }

                                                    $schedTs = 0;
                                                    if ($fa->scheduled_fa instanceof \DateTimeInterface) { $schedTs = $fa->scheduled_fa->getTimestamp(); }
                                                    elseif (!empty($fa->scheduled_fa)) { try { $schedTs = \Carbon\Carbon::parse((string)$fa->scheduled_fa)->timestamp; } catch (\Throwable $e) {} }

                                                    $claimTs = 0;
                                                    if ($fa->date_claimed instanceof \DateTimeInterface) { $claimTs = $fa->date_claimed->getTimestamp(); }
                                                    elseif (!empty($fa->date_claimed)) { try { $claimTs = \Carbon\Carbon::parse((string)$fa->date_claimed)->timestamp; } catch (\Throwable $e) {} }
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $fa->reference_no ?? '—' }}</td>
                                                        <td>{{ $fa->type_of_assistance ?: '—' }}</td>
                                                        <td>{{ $patientDisplay }}</td>

                                                        {{-- Date of Application (pretty) with numeric sort key --}}
                                                        <td data-order="{{ $appTs }}">{{ $fmtLong($applicationRaw) }}</td>

                                                        {{-- Payout Schedule --}}
                                                        <td data-order="{{ $schedTs }}">{{ $schedDisplay }}</td>

                                                        {{-- Date Claimed --}}
                                                        <td data-order="{{ $claimTs }}">{{ $fmtLong($fa->date_claimed) }}</td>

                                                        <td><span class="badge bg-{{ $badgeClass }}">{{ $status }}</span></td>
                                                        <td>{{ optional($fa->addedBy)->name ?? '—' }}</td>
                                                        <td class="text-nowrap">
                                                            <a href="{{ route('admin.financial-assistances.edit', $fa->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                            <a href="{{ route('admin.financial-assistances.print', $fa->id) }}" target="_blank" class="btn btn-sm btn-secondary">Print</a>
                                                            @can('financial_assistance_delete')
                                                            <form action="{{ route('admin.financial-assistances.destroy', $fa->id) }}" method="POST" class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Delete this financial assistance record? This cannot be undone.')">
                                                                    Delete
                                                                </button>
                                                            </form>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="10" class="text-muted">No records yet.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>


                                    </div>

                                </div>
                            </div>
        </div>

        

      </div> {{-- /.tab-content --}}
    </div>
  </div>
</div> {{-- /RIGHT --}}

    </div>
</div>

<div class="mt-3">
    <a class="btn btn-default" href="{{ route('admin.directories.index') }}">
        {{ trans('global.back_to_list') }}
    </a>
</div>
@endsection

@section('scripts')
<script>
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
             var message = response //dropzone sends it's own error messages in string
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
    (function() {
        function toggleWrap(selector, inputSelector, triggerValue) {
            const anyChecked = Array.from(document.querySelectorAll(inputSelector))
                .some(cb => cb.checked && cb.value === triggerValue);
            document.querySelector(selector).style.display = anyChecked ? '' : 'none';
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

    $(function() {
        var table = $('#fa-table').DataTable({
            dom: 'lfrtip', // keeps built-in search (the 'f'), still no Buttons bar
            buttons: [], // ensure no DT Buttons
            select: false,
            ordering: true,
            order: [
                [4, 'desc']
            ],
            paging: true,
            pageLength: 10,
            lengthChange: false,
            info: true,
            autoWidth: false,
            columnDefs: [{
                orderable: false,
                targets: [0, 9]
            }],
            drawCallback: function() {
                var api = this.api();
                api.column(0, {
                        search: 'applied',
                        order: 'applied'
                    })
                    .nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
            }
        });

        // Your custom search still works
        $('#fa-search-btn').on('click', function() {
            table.search($('#fa-search').val()).draw();
        });
        $('#fa-clear-btn').on('click', function() {
            $('#fa-search').val('');
            table.search('').draw();
        });
        $('#fa-search').on('keypress', function(e) {
            if (e.which === 13) $('#fa-search-btn').click();
        });
    });
</script>
@endsection