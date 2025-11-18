@extends('layouts.admin')

@section('styles')
{{-- Optional tiny tweaks (AdminLTE already brings Bootstrap & Font Awesome) --}}
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

$templateAvatar = 'https://lh3.googleusercontent.com/aida-public/AB6AXuCpU3fnq0AodONDFbe63OCRcl74XtWfXZenSLGxnDN33o0nNN0fgZIpyC2RFkw6tOa_TTRUnok8WXiswhLXqu5df1jGbGCqSwONnsRTovZGpjRnSK9S4PmEy2gEwDOQ_4ojHi8XRxFci-A8BJmhaaTIzB8-F1nClIFc89wRLsQKHH5J46S0iYGS62AIBZpoAGzwGD27EYMSY5UWz3BRM7f_ey1aONQXyQ_z7YRcGxXgOvezMdvjruvnxGAjsM0wZM323TxTG-ECT60_';

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
                    <h3 class="profile-username text-center mt-3 mb-1">
                        {{ $fullName }}
                    </h3>
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
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#tab-general" data-toggle="tab">General</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-orders" data-toggle="tab">Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-family" data-toggle="tab">Family</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-copays" data-toggle="tab">Copays</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-messages" data-toggle="tab">Messages</a></li>
                      
                        <li class="nav-item">
@can('directory_edit')
    <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-edit"></i> {{ trans('global.edit') }}
    </a>
@endcan</li>
                    </ul>
                </div><!-- /.card-header -->

                <div class="card-body">
                    <div class="tab-content">
                        {{-- GENERAL TAB --}}
                        <div class="active tab-pane" id="tab-general">
                            {{-- PERSONAL DETAILS --}}
                            <div class="mb-4 pb-3 border-bottom">
                                <h6 class="text-muted font-weight-bold mb-3">PERSONAL DETAILS</h6>
                                <div class="row">
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
                                        <div class="font-weight-medium">{{ $birthdate }}</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <span class="small-label">Address</span>
                                        <div class="font-weight-medium">
                                            {{ $address }}
                                            @if($barangayName)
                                            <span class="text-muted"> • </span><span>{{ $barangayName }}</span>
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

                            {{-- BACKGROUND INFORMATION (template content retained) --}}
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

                            {{-- Family Composition--}}
                        

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
                                            <tr>
                                                <td colspan="7" class="text-muted">No family members added.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                        </div> {{-- /GENERAL --}}

                        {{-- Stub tabs to match your template; keep content empty for now --}}
                        <div class="tab-pane" id="tab-orders"></div>
                        <div class="tab-pane" id="tab-family"></div>
                        <div class="tab-pane" id="tab-copays"></div>
                        <div class="tab-pane" id="tab-messages"></div>
                        <div class="tab-pane" id="tab-logs"></div>
                    </div>
                </div><!-- /.card-body -->
            </div><!-- /.card -->
        </div>
    </div>
</div>

<div class="mt-3">
    <a class="btn btn-default" href="{{ route('admin.directories.index') }}">
        {{ trans('global.back_to_list') }}
    </a>
</div>
@endsection