@extends('layouts.admin')

@section('styles')
<style>
    .profile-user-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .label-badge {
        font-size: 11px;
        font-weight: 600;
        padding: .35rem .65rem;
        border-radius: 999px;
    }

    .small-label {
        color: #6c757d;
        font-size: .8rem;
        font-weight: 600;
        margin-bottom: .4rem;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .small-label i {
        margin-right: 0.4rem;
        opacity: 0.7;
    }

    .font-weight-medium {
        font-weight: 500;
        color: #2c3e50;
        font-size: 0.95rem;
    }

    .card {
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        padding: 1rem 1.25rem;
    }

    .card-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .card-header h3 i {
        margin-right: 0.5rem;
        color: #007bff;
    }

    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 0.85rem 1.25rem;
    }

    .list-group-item:first-child {
        border-top: none;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .profile-username {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .section-header {
        background: #f8f9fa;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.25rem;
        border-left: 4px solid #007bff;
    }

    .section-header h6 {
        margin: 0;
        font-size: 0.9rem;
        font-weight: 700;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-header h6 i {
        margin-right: 0.5rem;
        color: #007bff;
    }

    .nav-pills .nav-link {
        border-radius: 0.375rem;
        padding: 0.6rem 1.2rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.2s;
    }

    .nav-pills .nav-link:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }

    .nav-pills .nav-link.active {
        background-color: #007bff;
        color: #fff;
    }

    .table-striped tbody tr:hover {
        background-color: #f1f3f5;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.8rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        background-color: #e9ecef;
        color: #495057;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .info-badge i {
        margin-right: 0.4rem;
    }

    .data-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f3f5;
    }

    .data-row:last-child {
        border-bottom: none;
    }
</style>

{{-- Usually in your layout or this blade --}}
<link rel="stylesheet" href="{{ asset('plugins/dropzone/min/dropzone.min.css') }}">
<script src="{{ asset('plugins/dropzone/min/dropzone.min.js') }}"></script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cb = document.getElementById('claimant_is_patient');
        const patient = document.getElementById('patient_name');
        const claimant = document.getElementById('claimant_name');
        const claimantContact = document.getElementById('claimant_contact_no');

        if (!cb || !patient || !claimant) return;

        // Store the directory contact number from PHP
        const directoryContactNo = '{{ $directory->contact_no ?? "" }}';

        function sync() {
            const isChecked = cb.checked;
            patient.readOnly = isChecked;
            claimant.readOnly = isChecked;
            if (claimantContact) claimantContact.readOnly = isChecked;
            
            // Add visual feedback and sync values
            if (isChecked) {
                patient.classList.add('bg-light');
                claimant.classList.add('bg-light');
                if (claimantContact) claimantContact.classList.add('bg-light');
                
                // Sync claimant name with patient name
                if (patient.value) {
                    claimant.value = patient.value;
                }
                
                // Sync claimant contact with directory contact
                if (claimantContact && directoryContactNo && directoryContactNo !== 'N/A') {
                    claimantContact.value = directoryContactNo;
                }
            } else {
                patient.classList.remove('bg-light');
                claimant.classList.remove('bg-light');
                if (claimantContact) claimantContact.classList.remove('bg-light');
            }
        }

        cb.addEventListener('change', sync);
        
        // Keep claimant name in sync with patient name while checked
        patient.addEventListener('input', function() {
            if (cb.checked && patient.value) {
                claimant.value = patient.value;
            }
        });
        
        sync(); // set initial state on load
    });
</script>
@endpush

@section('content')
@php
use Carbon\Carbon;
use App\Models\Familycomposition;

// Convenience helpers with graceful fallbacks to template defaults
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

// Profile photo (Spatie Media or stored path) – keep template avatar if none
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

// Gender (mapped if using selects), fallback 'Male'
$gender = '';
if (!empty($directory->gender)) {
$gender = \App\Models\Directory::GENDER_SELECT[$directory->gender] ?? $directory->gender;
}
$gender = $gender ?: 'Male';

// Age (from birthday), empty if missing/invalid
$age = '';
if (!empty($directory->birthday)) {
try { $age = Carbon::parse($directory->birthday)->age; } catch (\Throwable $e) { $age=''; }
}

// Nationality – fallback to 'Filipino'
$nationality = $directory->nationality ?? 'Filipino';

// Occupation – fallback to "Not specified"
$occupation = $directory->occupation ?? 'Not specified';

// Life Status mapping
$lifeStatusRaw = $directory->life_status ?? null;
$lifeStatus = $lifeStatusRaw ? (\App\Models\Directory::LIFE_STATUS_SELECT[$lifeStatusRaw] ?? $lifeStatusRaw) : null;
switch ($lifeStatus) {
case 'Alive': $lifeStatusColor = 'success'; break;
case 'Deceased': $lifeStatusColor = 'danger'; break;
default: $lifeStatusColor = 'secondary'; $lifeStatus = 'N/A'; break;
}

// Address
$streetNo = $directory->street_no ?? null;
$street = $directory->street ?? null;
$city = $directory->city ?? null;
$province = $directory->province ?? null;
$addrParts = array_filter([$streetNo, $street, $city, $province]);
$address = $addrParts ? implode(', ', $addrParts) : '83 Mile Drive, Los Angeles, CA';

// Contact + Email
$phone = $directory->contact_no ?? 'N/A';
$email = $directory->email ?? 'N/A';

// Barangay
$barangayName = optional($directory->barangay)->barangay_name ?? '';

// NGOs
$ngos = [];
if ($directory->ngos && count($directory->ngos)) {
foreach ($directory->ngos as $ngo) { $ngos[] = trim($ngo->name); }
}

// COMELEC Status mapping
$comelecStatusRaw = $directory->comelec_status ?? null;
$comelecStatus = $comelecStatusRaw
? (\App\Models\Directory::COMELEC_STATUS_SELECT[$comelecStatusRaw] ?? $comelecStatusRaw)
: null;
switch (strtolower((string) $comelecStatus)) {
case 'registered': $comelecStatusColor = 'success'; break;
case 'unregistered': $comelecStatusColor = 'danger'; break;
default: $comelecStatusColor = 'secondary'; $comelecStatus = 'N/A'; break;
}

// Sectors
$sectors = [];
if ($directory->sectors && count($directory->sectors)) {
foreach ($directory->sectors as $sector) { $sectors[] = trim($sector->name); }
}

// helper to show dd/mm/YYYY while accepting strings or Carbon
$fmt = function ($raw) {
if (empty($raw)) return '';
try {
if ($raw instanceof \DateTimeInterface) return $raw->format('d/m/Y');
return Carbon::parse($raw)->format('d/m/Y');
} catch (\Throwable $e) { return ''; }
};

// Notes – description + remarks, else template string
$notes = trim(($directory->description ?? '').' '.($directory->remarks ?? ''));
$notes = $notes ?: 'Knee pain, Headache, Last time he looked sick';


$types = \App\Models\FinancialAssistance::TYPE_OF_ASSISTANCE;
$statuses = \App\Models\FinancialAssistance::STATUS_OPTIONS;
$ppOptions = \App\Models\FinancialAssistance::PROBLEM_PRESENTED_OPTIONS;
$swoNames = \App\Models\FinancialAssistance::SWO_NAMES;
$swoDesigs = \App\Models\FinancialAssistance::SWO_DESIGS;

// For edit: $fa is the loaded model; for create: $fa = null
$req = old('requirement_checklist', $fa->requirement_checklist ?? []);
$ppv = old('problem_presented_value', $fa->problem_presented_value ?? []);
$ppvValues = is_array($ppv) ? ($ppv['values'] ?? []) : [];
$ppvOther = is_array($ppv) ? ($ppv['other'] ?? '') : '';

$reqPatient = is_array($req) ? ($req['patient'] ?? []) : [];
$reqPatientOther = is_array($req) ? ($req['patient_other'] ?? '') : '';
$reqClaimant = is_array($req) ? ($req['claimant'] ?? []) : [];
$reqClaimantOther = is_array($req) ? ($req['claimant_other'] ?? '') : '';

$claimantIsPatient = old('claimant_is_patient', $fa->claimant_is_patient ?? true);
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
                    <h3 class="profile-username text-center mt-3 mb-2">{{ $fullName }}</h3>
                    <p class="text-center mb-3">
                        <span class="badge badge-lg bg-{{ $comelecStatusColor }}">
                            <i class="fas fa-vote-yea mr-1"></i>{{ $comelecStatus }}
                        </span>
                    </p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-venus-mars"></i> Gender</span>
                            <span class="text-dark font-weight-medium">{{ $gender }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-birthday-cake"></i> Age</span>
                            <span class="text-dark font-weight-medium">{{ $age ?: 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-map-marker-alt"></i> City / Municipality</span>
                            <span class="text-dark font-weight-medium">{{ $city ?: 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-briefcase"></i> Occupation</span>
                            <span class="text-dark font-weight-medium">{{ $occupation }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="fas fa-heartbeat"></i> Life Status</span>
                            <span class="badge bg-{{ $lifeStatusColor }}">{{ $lifeStatus }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Sector --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-layer-group text-primary"></i> Sectors</h5>
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
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-users text-primary"></i> Non-Government Organizations</h5>
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
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-sticky-note text-primary"></i> Notes & Remarks</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="color: #495057; line-height: 1.6;">{{ $notes }}</p>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header p-2 d-flex align-items-center">
                    <ul class="nav nav-pills flex-grow-1">
                        <li class="nav-item"><a class="nav-link" href="#tab-family" data-toggle="tab"><i class="fas fa-user mr-1"></i> General Information</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-general" data-toggle="tab"><i class="fas fa-plus-circle mr-1"></i> Add Financial Assistance</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#tab-orders" data-toggle="tab"><i class="fas fa-history mr-1"></i> Previous FA</a></li>
                    </ul>
                    @can('directory_edit')
                    <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm ml-2">
                        <i class="fas fa-edit mr-1"></i> {{ trans('global.edit') }} Directory
                    </a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- GENERAL --}}
                        <div class="tab-pane" id="tab-general">
                            <div class="tab-pane" id="tab-general">
                                <div class="card">
                                    <div class="card-header">
                                        <b>{{ trans('global.create') }} {{ trans('cruds.financialAssistance.title_singular') }}</b>
                                    </div>
                                    <div class="card-body">
                                        <form id="fa-form" method="POST" action="{{ route('admin.financial-assistances.store') }}" enctype="multipart/form-data">
                                            @csrf

                                            @php
                                            $forDirectory = isset($directory) && $directory instanceof \App\Models\Directory;
                                            @endphp

                                            @if($forDirectory)
                                            <input type="hidden" name="directory_id" value="{{ $directory->id }}">

                                            @else
                                            <div class="form-group">
                                                <label for="directory_id">Directory</label>
                                                <input type="number" class="form-control" name="directory_id" id="directory_id"
                                                    value="{{ old('directory_id') }}" required placeholder="Enter Directory ID">
                                                @error('directory_id') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                            @endif

                                            @php
                                            $selectedType = old('type_of_assistance', $assistance->type_of_assistance ?? 'Medical Assistance');
                                            @endphp

                                            <div class="row">
                                                {{-- Type of Assistance --}}
                                                <div class="col-md-6">
                                                    @php
                                                    $defaultType = 'Medical Assistance';
                                                    @endphp

                                                    <div class="form-group">
                                                        <label>Type of Assistance</label>
                                                        <select name="type_of_assistance" class="form-control">
                                                            @foreach ($types as $opt)
                                                            <option value="{{ $opt }}" {{ $opt === $selectedType ? 'selected' : '' }}>
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
                                                        <input
                                                            type="text"
                                                            name="patient_name"
                                                            id="patient_name"
                                                            class="form-control"
                                                            value="{{ $patientValue }}"
                                                            placeholder="{{ $fullName ?: 'Full name' }}"
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
                                                            if ($claimantValue === '' && $claimantIsPatient) { $claimantValue = $fullName; }
                                                        @endphp
                                                        <input
                                                            type="text"
                                                            name="claimant_name"
                                                            id="claimant_name"
                                                            class="form-control"
                                                            value="{{ $claimantValue }}"
                                                            placeholder="{{ $fullName ?: 'Full name' }}"
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
                                                                $claimantContactValue = $phone !== 'N/A' ? $phone : ''; 
                                                            }
                                                        @endphp
                                                        <input
                                                            type="text"
                                                            name="claimant_contact_no"
                                                            id="claimant_contact_no"
                                                            class="form-control"
                                                            value="{{ $claimantContactValue }}"
                                                            placeholder="09XX XXX XXXX"
                                                            {{ $claimantIsPatient ? 'readonly' : '' }}>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Checkbox below the fields --}}
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input"
                                                            type="checkbox"
                                                            id="claimant_is_patient"
                                                            name="claimant_is_patient"
                                                            value="1"
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
                                                // Defaults you want checked on first load
                                                $patientDefaults = [
                                                'Medical Certificate / Medical Abstract',
                                                'Lab Request / Reseta',
                                                ];

                                                // If there's NO prior user input (no validation error repost)
                                                // and we're on CREATE (no $fa) OR there's no saved selections,
                                                // seed the defaults so the boxes render as checked.
                                                if (!old('req_patient') && (!isset($fa) || empty($reqPatient))) {
                                                $reqPatient = $patientDefaults;
                                                }
                                                @endphp

                                                <div class="row">
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

                                                @php
                                                // Defaults to auto-check for Claimant requirements
                                                $claimantDefaults = [
                                                'Photocopy of Valid ID',
                                                'Original Barangay Certificate',
                                                'Original Barangay Indigency',
                                                ];

                                                // Seed defaults only if there's no prior user input AND no saved selection
                                                if (!old('req_claimant') && (!isset($fa) || empty($reqClaimant))) {
                                                $reqClaimant = $claimantDefaults;
                                                }
                                                @endphp

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
                                                @php
                                                // Auto-check "Medical Assistance" by default (first load only)
                                                if (!old('problem_presented_value') && (empty($ppvValues) || !is_array($ppvValues))) {
                                                $ppvValues = ['Medical Assistance'];
                                                }
                                                @endphp

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
                                                        <input
                                                            class="form-control datetime {{ $errors->has('date_interviewed') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="date_interviewed"
                                                            id="date_interviewed"
                                                            value="{{ old('date_interviewed', now('Asia/Manila')->format(config('panel.date_format').' '.config('panel.time_format'))) }}">
                                                        @if($errors->has('date_interviewed'))
                                                        <span class="text-danger">{{ $errors->first('date_interviewed') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_interviewed_helper') }}</span>

                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="amount">{{ trans('cruds.financialAssistance.fields.amount') }}</label>
                                                        <input type="number" step="0.01" name="amount" id="amount"
                                                            class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                                            value="{{ old('amount', $financialAssistance->amount ?? '') }}">
                                                        @if($errors->has('amount'))
                                                        <span class="text-danger">{{ $errors->first('amount') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.financialAssistance.fields.amount_helper') }}</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="scheduled_fa">{{ trans('cruds.financialAssistance.fields.scheduled_fa') }}</label>
                                                        <input class="form-control {{ $errors->has('scheduled_fa') ? 'is-invalid' : '' }}" type="date" name="scheduled_fa" id="scheduled_fa" value="{{ old('scheduled_fa', '') }}">
                                                        @if($errors->has('scheduled_fa')) <span class="text-danger">{{ $errors->first('scheduled_fa') }}</span> @endif
                                                        <span class="help-block">{{ trans('cruds.financialAssistance.fields.scheduled_fa_helper') }}</span>
                                                    </div>

                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="payout_location">Payout Location</label>
                                                        <input class="form-control {{ $errors->has('payout_location') ? 'is-invalid' : '' }}" 
                                                               type="text" 
                                                               name="payout_location" 
                                                               id="payout_location"
                                                               placeholder="Enter Payout Location"
                                                               value="{{ old('payout_location', '') }}">
                                                        @if($errors->has('payout_location')) <span class="text-danger">{{ $errors->first('payout_location') }}</span> @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-3">
                                                    @php
                                                    $statuses = ['Ongoing','Pending','Claimed','Cancelled'];
                                                    // Default to Ongoing unless there's old() or an existing $fa->status
                                                    $currentStatus = old('status', $fa->status ?? 'Ongoing');
                                                    @endphp

                                                    <label for="status">{{ trans('cruds.financialAssistance.fields.status') }}</label>
                                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                                        @foreach ($statuses as $s)
                                                        <option value="{{ $s }}" {{ $currentStatus === $s ? 'selected' : '' }}>
                                                            {{ $s }}
                                                        </option>
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
                                                        <input
                                                            class="form-control {{ $errors->has('assessment') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="assessment"
                                                            id="assessment"
                                                            value="{{ old('assessment', isset($fa) ? ($fa->assessment ?? $defaultAssessment) : $defaultAssessment) }}">
                                                        @if($errors->has('assessment'))
                                                        <span class="text-danger">{{ $errors->first('assessment') }}</span>
                                                        @endif
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
                                                        <input
                                                            class="form-control {{ $errors->has('recommendation') ? 'is-invalid' : '' }}"
                                                            type="text"
                                                            name="recommendation"
                                                            id="recommendation"
                                                            value="{{ old('recommendation', $fa->recommendation ?? $defaultRecommendation) }}">
                                                        @if($errors->has('recommendation'))
                                                        <span class="text-danger">{{ $errors->first('recommendation') }}</span>
                                                        @endif
                                                        <span class="help-block">{{ trans('cruds.financialAssistance.fields.recommendation_helper') }}</span>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="date_claimed">{{ trans('cruds.financialAssistance.fields.date_claimed') }}</label>
                                                <input type="datetime-local"
                                                    name="date_claimed"
                                                    class="form-control"
                                                    value="{{ old('date_claimed', $fa->date_claimed_for_input ?? '') }}">
                                                @if($errors->has('date_claimed')) <span class="text-danger">{{ $errors->first('date_claimed') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_claimed_helper') }}</span>
                                            </div>


                                            <div class="row">
                                                {{-- Social Worker Name --}}
                                                <div class="col-md-6">
                                                    @php
                                                    // Pick the value in this order: old() -> existing ($fa) -> first option
                                                    $swDefault = old('social_welfare_name', $fa->social_welfare_name ?? (is_array($swoNames) ? (array_values($swoNames)[0] ?? '') : ''));
                                                    @endphp

                                                    <div class="form-group">
                                                        <label>Social Worker Name</label>
                                                        <select name="social_welfare_name" class="form-control">
                                                            {{-- Keep the placeholder, but don't select it since we auto-pick the first SW --}}
                                                            <option value="">{{ trans('global.pleaseSelect') }}</option>

                                                            @foreach($swoNames as $opt)
                                                            <option value="{{ $opt }}" {{ $swDefault === $opt ? 'selected' : '' }}>
                                                                {{ $opt }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>

                                                {{-- Social Worker Designation --}}
                                                <div class="col-md-6">
                                                    @php
                                                    // Value priority: old() -> existing ($fa) -> first option in $swoDesigs
                                                    $desigDefault = old(
                                                    'social_welfare_desig',
                                                    $fa->social_welfare_desig ?? (is_array($swoDesigs) ? (array_values($swoDesigs)[0] ?? '') : '')
                                                    );
                                                    @endphp

                                                    <div class="form-group">
                                                        <label>Social Worker Designation</label>
                                                        <select name="social_welfare_desig" class="form-control">
                                                            <option value="">{{ trans('global.pleaseSelect') }}</option>
                                                            @foreach($swoDesigs as $opt)
                                                            <option value="{{ $opt }}" {{ $desigDefault === $opt ? 'selected' : '' }}>
                                                                {{ $opt }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="note">{{ trans('cruds.financialAssistance.fields.note') }}</label>
                                                <input class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" type="text" name="note" id="note" value="{{ old('note', '') }}">
                                                @if($errors->has('note')) <span class="text-danger">{{ $errors->first('note') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.note_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="requirements">{{ trans('cruds.financialAssistance.fields.requirements') }}</label>
                                                <div class="needsclick dropzone {{ $errors->has('requirements') ? 'is-invalid' : '' }}" id="requirements-dropzone">
                                                </div>
                                                @if($errors->has('requirements'))
                                                <span class="text-danger">{{ $errors->first('requirements') }}</span>
                                                @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.requirements_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <button class="btn btn-danger" type="submit">
                                                    Add Financial Assistance
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- FINANCIAL ASSISTANCE CREATE --}}

                        <div class="active tab-pane" id="tab-orders">
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
                                                    $schedDisplay = '—';
                                                    if (!empty($fa->scheduled_fa)) {
                                                        try {
                                                            $schedDisplay = \Carbon\Carbon::parse($fa->scheduled_fa)->format('F j (l), Y');
                                                        } catch (\Throwable $e) {
                                                            // Keep em dash; optionally could expose raw value for debugging
                                                            // $schedDisplay = e($fa->scheduled_fa); // Uncomment to see raw invalid string
                                                        }
                                                    }

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
                                                            <div class="d-flex flex-wrap gap-1" style="max-width: 200px;">
                                                                <a href="{{ route('admin.financial-assistances.show', $fa->id) }}" class="btn btn-sm btn-success mb-1" title="View" style="flex: 1 0 45%;">View</a>
                                                                <a href="{{ route('admin.financial-assistances.edit', $fa->id) }}" class="btn btn-sm btn-primary mb-1" style="flex: 1 0 45%;">Edit</a>
                                                                <a href="{{ route('admin.financial-assistances.print', $fa->id) }}" target="_blank" class="btn btn-sm btn-secondary mb-1" style="flex: 1 0 45%;">Print</a>
                                                                @can('financial_assistance_delete')
                                                                <form action="{{ route('admin.financial-assistances.destroy', $fa->id) }}" method="POST" style="flex: 1 0 45%;">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger w-100"
                                                                        onclick="return confirm('Delete this financial assistance record? This cannot be undone.')">
                                                                        Delete
                                                                    </button>
                                                                </form>
                                                                @endcan
                                                            </div>
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
                        <div class="tab-pane" id="tab-family">
                            {{-- PERSONAL DETAILS --}}
                            <div class="mb-4 pb-3">
                                <div class="section-header">
                                    <h6><i class="fas fa-id-card"></i> Personal Details</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-user"></i> Last name</span>
                                        <div class="font-weight-medium">{{ $directory->last_name}} {{ $directory->suffix ?: '' }}</div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-user"></i> First name</span>
                                        <div class="font-weight-medium">{{ $directory->first_name}}</div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-user"></i> Middle Name</span>
                                        <div class="font-weight-medium">{{ $directory->middle_name ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-birthday-cake"></i> Birthdate</span>
                                        <div class="font-weight-medium">{{ $directory->birthday ?: '-' }}</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <span class="small-label"><i class="fas fa-map-marker-alt"></i> Address</span>
                                        <div class="font-weight-medium">
                                            {{ $address }}
                                            @if($barangayName)
                                            <br><span class="badge badge-info mt-1"><i class="fas fa-home mr-1"></i>{{ $barangayName }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-phone"></i> Phone number</span>
                                        <div class="font-weight-medium">{{ $phone }}</div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <span class="small-label"><i class="fas fa-envelope"></i> Email</span>
                                        <div class="font-weight-medium">{{ $email }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- BACKGROUND INFORMATION --}}
                            <div class="mb-4 pb-3">
                                <div class="section-header">
                                    <h6><i class="fas fa-info-circle"></i> Background Information</h6>
                                </div>
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <span class="small-label"><i class="fas fa-graduation-cap"></i> Educational Attainment</span>
                                        <div class="font-weight-medium">{{ $directory->highest_edu ?: 'N/A' }}</div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <span class="small-label"><i class="fas fa-praying-hands"></i> Religion</span>
                                        <div class="font-weight-medium">{{ $directory->religion ?: 'N/A' }}</div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <span class="small-label"><i class="fas fa-heart"></i> Civil Status</span>
                                        <div class="font-weight-medium">{{ $directory->civil_status ?: 'N/A' }}</div>
                                    </div>

                                    <div class="col-lg-3 col-md-6 mb-3">
                                        <span class="small-label"><i class="fas fa-map-marker-alt"></i> Place of Birth</span>
                                        <div class="font-weight-medium">{{ $directory->place_of_birth ?: 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Family Composition --}}
                            <div>
                                <div class="section-header">
                                    <h6><i class="fas fa-user-friends"></i> Family Composition</h6>
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
                                            <tr>
                                                <td colspan="7" class="text-muted">No family members added.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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
        success: function(file, response) {
            $('form').append('<input type="hidden" name="requirements[]" value="' + response.name + '">')
            uploadedRequirementsMap[file.name] = response.name
        },
        removedfile: function(file) {
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedRequirementsMap[file.name]
            }
            $('form').find('input[name="requirements[]"][value="' + name + '"]').remove()
        },
        init: function() {
            @if(isset($financialAssistance) && $financialAssistance -> requirements)
            var files = {
                !!json_encode($financialAssistance -> requirements) !!
            }
            for (var i in files) {
                var file = files[i]
                this.options.addedfile.call(this, file)
                file.previewElement.classList.add('dz-complete')
                $('form').append('<input type="hidden" name="requirements[]" value="' + file.file_name + '">')
            }
            @endif
        },
        error: function(file, response) {
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
    dom: 'lfrtip',       // keeps built-in search (the 'f'), still no Buttons bar
    buttons: [],         // ensure no DT Buttons
    select: false,
    ordering: true,
    order: [[4,'desc']],
    paging: true,
    pageLength: 10,
    lengthChange: false,
    info: true,
    autoWidth: false,
    columnDefs: [{ orderable: false, targets: [0, 9] }],
    drawCallback: function() {
      var api = this.api();
      api.column(0, { search:'applied', order:'applied' })
         .nodes().each(function(cell, i){ cell.innerHTML = i + 1; });
    }
  });

  // Your custom search still works
  $('#fa-search-btn').on('click', function(){
    table.search($('#fa-search').val()).draw();
  });
  $('#fa-clear-btn').on('click', function(){
    $('#fa-search').val('');
    table.search('').draw();
  });
  $('#fa-search').on('keypress', function(e){
    if (e.which === 13) $('#fa-search-btn').click();
  });
});



</script>

@endsection