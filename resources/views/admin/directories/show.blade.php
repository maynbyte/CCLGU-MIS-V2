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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

    /* Print styles for ID card */
    @media print {
        body * { visibility: hidden; }
        #print-id-section, #print-id-section * { visibility: visible; }
        #print-id-section { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }

    .id-card {
        width: 680px;
        height: 430px;
        border-radius: 16px;
        background: #fff url('/upload/ph-id-bg.png') center/cover no-repeat;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        position: relative;
        overflow: hidden;
        border: 1px solid #e3e6ea;
        margin: 0 auto;
    }
    .id-header {
        position: absolute; left: 20px; top: 16px; right: 20px;
        display: flex; align-items: flex-start; justify-content: space-between;
        color: #1f2d3d;
    }
    .id-header .gov-title {
        font-size: 16px; font-weight: 700; line-height: 1.2; flex: 1; text-align: center; margin-top: 2px;
    }
    .id-header .id-left img,
    .id-header .id-right img {
        width: 64px;
        height: auto;
        display: block;
    }
    .id-header .gov-title { flex: 1; text-align: center; }
    /* ID body 3x3 grid layout */
    .id-body { position: absolute; left: 20px; right: 20px; top: 80px; bottom: 20px; display: grid; grid-template-columns: 1fr 0.8fr 1fr; grid-template-rows: 1fr 0.8fr 1fr; gap: 10px; align-items: center; }

    /* Large color profile on left column, middle row */
    .grid-left-photo { grid-column: 1 / 2; grid-row: 2 / 3; display: flex; align-items: center; justify-content: center; }
    .grid-left-photo .id-photo { width: 140px; height: 180px; border-radius: 8px; overflow: hidden; border: 2px solid #ddd; background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
    .id-photo img { width: 100%; height: 100%; object-fit: cover; }

    /* Main details occupy right two columns, middle row */
    .grid-main-details { grid-column: 2 / 4; grid-row: 2 / 3; }
    .grid-main-details .id-details { margin: 0; display: grid; grid-template-columns: 1fr; row-gap: 8px; }

    /* Center cell: small B/W profile + small coat of arms */
    .grid-center-icons { grid-column: 2 / 3; grid-row: 2 / 3; display: flex; align-items: center; justify-content: center; gap: 10px; }
    .bw-photo { width: 48px; height: 60px; border: 1px solid #ccc; border-radius: 4px; overflow: hidden; filter: grayscale(100%); background: #f8f9fa; display: flex; align-items: center; justify-content: center; }
    .bw-photo img { width: 100%; height: 100%; object-fit: cover; }
    .mini-logo { width: 40px; height: 40px; display: block; }
    .id-row { display: flex; margin-bottom: 8px; }
    .id-label { width: 200px; color: #6c757d; font-weight: 600; font-size: 10px; text-transform: uppercase; }
    .id-value { color: #2c3e50; font-weight: 600; font-size: 16px; }
    .id-left { display: flex; flex-direction: column; align-items: center; }
    .id-left .id-uid-left { margin-top: 6px; }
    .id-left .id-uid-text { font-weight: 700; letter-spacing: 2px; }
    /* UID positioned below left logo but outside header grid/layout */
    .id-uid-below-left {
        position: absolute;
        top: 96px; /* adjust as needed to sit below header */
        left: 40px; /* align under left logo */
        font-weight: 700;
        letter-spacing: 2px;
        background: rgba(255,255,255,0.0);
        padding: 2px 6px;
    }
    .id-footer { position: absolute; left: 20px; right: 20px; bottom: 20px; color: #2c3e50; font-size: 13px; }
</style>
@endsection

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
try {
$age = Carbon::parse($directory->birthday)->age;
} catch (\Throwable $e) {
$age = ''; // keep empty on parse error
}
}

// Nationality – fallback to 'Filipino'
$nationality = $directory->nationality ?? 'Filipino';

// Occupation – fallback to "Not specified"
$occupation = $directory->occupation ?? 'Not specified';

// Life Status mapping
$lifeStatusRaw = $directory->life_status ?? null;
$lifeStatus = $lifeStatusRaw ? (\App\Models\Directory::LIFE_STATUS_SELECT[$lifeStatusRaw] ?? $lifeStatusRaw) : null;

// Bootstrap color mapping
switch ($lifeStatus) {
case 'Alive':
$lifeStatusColor = 'success'; // green
break;
case 'Deceased':
$lifeStatusColor = 'danger'; // red
break;
default:
$lifeStatusColor = 'secondary'; // gray if null or unknown
$lifeStatus = 'N/A'; // show N/A if nothing set
break;
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

// Birthdate display (keep template if not set)
$birthdate = $directory->birthday ;

// Barangay
$barangayName = optional($directory->barangay)->barangay_name ?? '';

// NGOs
$ngos = [];
if ($directory->ngos && count($directory->ngos)) {
foreach ($directory->ngos as $ngo) {
$ngos[] = trim($ngo->name);
}
}

// COMELEC Status mapping
$comelecStatusRaw = $directory->comelec_status ?? null;

// Resolve code -> label (fallback to raw if not found)
$comelecStatus = $comelecStatusRaw
? (\App\Models\Directory::COMELEC_STATUS_SELECT[$comelecStatusRaw] ?? $comelecStatusRaw)
: null;

// Bootstrap color mapping
switch (strtolower((string) $comelecStatus)) {
case 'registered':
$comelecStatusColor = 'success'; // green
break;
case 'unregistered':
$comelecStatusColor = 'danger'; // red
break;
default:
$comelecStatusColor = 'secondary'; // gray for null/unknown
$comelecStatus = 'N/A';
break;
}

// Sectors
$sectors = [];
if ($directory->sectors && count($directory->sectors)) {
foreach ($directory->sectors as $sector) {
$sectors[] = trim($sector->name);
}
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
@endphp

<div class="mb-4">
    <a class="btn btn-outline-secondary" href="{{ route('admin.directories.index') }}">
        <i class="fas fa-arrow-left mr-2"></i>{{ trans('global.back_to_list') }}
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
                    <h3 class="profile-username text-center mt-3 mb-2">
                        {{ $fullName }}
                    </h3>
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
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group"></i> Sectors</h3>
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
                    <h3 class="card-title"><i class="fas fa-users"></i> Non-Government Organizations</h3>
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
                    <h3 class="card-title"><i class="fas fa-sticky-note"></i> Notes & Remarks</h3>
                </div>
                <div class="card-body">
                    <p class="mb-0" style="color: #495057; line-height: 1.6;">{{ $notes }}</p>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#tab-general" data-toggle="tab"><i class="fas fa-user mr-1"></i> General Information</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-print-id" data-toggle="tab"><i class="fas fa-id-card mr-1"></i> Print ID</a></li>
                        <li class="nav-item ml-auto">
@can('directory_edit')
    <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit mr-1"></i> {{ trans('global.edit') }}
    </a>
@endcan</li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- GENERAL TAB --}}
                        <div class="active tab-pane" id="tab-general">
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
                                        <div class="font-weight-medium">{{ $birthdate ?: '-' }}</div>
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

                            {{-- Family Composition--}}
                        
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
                        {{-- PRINT ID TAB --}}
                        <div class="tab-pane" id="tab-print-id">
                            <div class="section-header d-flex align-items-center justify-content-between">
                                <h6 class="mb-0"><i class="fas fa-id-card"></i> Preview of Front Part ID</h6>
                                <div class="no-print">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print</button>
                                </div>
                            </div>

                                @include('admin.directories.partials.print_id_front', [
                                    'directory' => $directory,
                                    'photoUrl' => $photoUrl,
                                    'templateAvatar' => $templateAvatar,
                                    'address' => $address,
                                    'fmt' => $fmt,
                                ])
                                {{-- BACK PART PREVIEW (below front preview) --}}
                                <div class="mt-3">
                                    <div class="section-header d-flex align-items-center justify-content-between">
                                        <h6 class="mb-0"><i class="fas fa-id-card"></i> Preview of Back  Part ID</h6>
                                    <div class="no-print">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print mr-1"></i> Print</button>
                                    </div>
                                </div>

                                    @include('admin.directories.partials.print_id_back', [
                                        'directory' => $directory,
                                        'photoUrl' => $photoUrl,
                                        'templateAvatar' => $templateAvatar,
                                        'address' => $address,
                                        'fmt' => $fmt,
                                    ])
                                </div>
                        </div>
                        <div class="tab-pane" id="tab-logs"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a class="btn btn-outline-secondary" href="{{ route('admin.directories.index') }}">
        <i class="fas fa-arrow-left mr-2"></i>{{ trans('global.back_to_list') }}
    </a>
</div>
@endsection