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
@endsection

@section('content')
@php
use Carbon\Carbon;

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
                    <ul class="nav nav-pills flex-grow-1">
                        <li class="nav-item"><a class="nav-link active" href="#tab-general" data-toggle="tab">Add Financial Assistance</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-orders" data-toggle="tab">Previous FA</a></li>
                        <li class="nav-item"><a class="nav-link" href="#tab-family" data-toggle="tab">Family Composition</a></li>
                    </ul>
                    @can('directory_edit')
                    <a href="{{ route('admin.directories.edit', $directory->id) }}" class="btn btn-primary btn-sm ml-2">
                        <i class="fas fa-edit"></i> {{ trans('global.edit') }} Directory
                    </a>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        {{-- GENERAL --}}
                        <div class="active tab-pane" id="tab-general">
                            <div class="tab-pane" id="tab-general">
                                <div class="card">
                                    <div class="card-header">
                                        {{ trans('global.create') }} {{ trans('cruds.financialAssistance.title_singular') }}
                                    </div>      
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('admin.financial-assistances.store') }}" enctype="multipart/form-data">
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

                                            <div class="form-group">
                                                <label for="problem_presented">{{ trans('cruds.financialAssistance.fields.problem_presented') }}</label>
                                                <input class="form-control {{ $errors->has('problem_presented') ? 'is-invalid' : '' }}" type="text" name="problem_presented" id="problem_presented" value="{{ old('problem_presented', '') }}">
                                                @if($errors->has('problem_presented')) <span class="text-danger">{{ $errors->first('problem_presented') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.problem_presented_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_interviewed">{{ trans('cruds.financialAssistance.fields.date_interviewed') }}</label>
                                                <input class="form-control datetime {{ $errors->has('date_interviewed') ? 'is-invalid' : '' }}" type="text" name="date_interviewed" id="date_interviewed" value="{{ old('date_interviewed') }}">
                                                @if($errors->has('date_interviewed')) <span class="text-danger">{{ $errors->first('date_interviewed') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_interviewed_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="assessment">{{ trans('cruds.financialAssistance.fields.assessment') }}</label>
                                                <input class="form-control {{ $errors->has('assessment') ? 'is-invalid' : '' }}" type="text" name="assessment" id="assessment" value="{{ old('assessment', '') }}">
                                                @if($errors->has('assessment')) <span class="text-danger">{{ $errors->first('assessment') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.assessment_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="recommendation">{{ trans('cruds.financialAssistance.fields.recommendation') }}</label>
                                                <input class="form-control {{ $errors->has('recommendation') ? 'is-invalid' : '' }}" type="text" name="recommendation" id="recommendation" value="{{ old('recommendation', '') }}">
                                                @if($errors->has('recommendation')) <span class="text-danger">{{ $errors->first('recommendation') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.recommendation_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="amount">{{ trans('cruds.financialAssistance.fields.amount') }}</label>
                                                <input class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}" type="text" name="amount" id="amount" value="{{ old('amount', '') }}">
                                                @if($errors->has('amount')) <span class="text-danger">{{ $errors->first('amount') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.amount_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="scheduled_fa">{{ trans('cruds.financialAssistance.fields.scheduled_fa') }}</label>
                                                <input class="form-control {{ $errors->has('scheduled_fa') ? 'is-invalid' : '' }}" type="text" name="scheduled_fa" id="scheduled_fa" value="{{ old('scheduled_fa', '') }}">
                                                @if($errors->has('scheduled_fa')) <span class="text-danger">{{ $errors->first('scheduled_fa') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.scheduled_fa_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="status">{{ trans('cruds.financialAssistance.fields.status') }}</label>
                                                <input class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" type="text" name="status" id="status" value="{{ old('status', '') }}">
                                                @if($errors->has('status')) <span class="text-danger">{{ $errors->first('status') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.status_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="date_claimed">{{ trans('cruds.financialAssistance.fields.date_claimed') }}</label>
                                                <input class="form-control {{ $errors->has('date_claimed') ? 'is-invalid' : '' }}" type="text" name="date_claimed" id="date_claimed" value="{{ old('date_claimed', '') }}">
                                                @if($errors->has('date_claimed')) <span class="text-danger">{{ $errors->first('date_claimed') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.date_claimed_helper') }}</span>
                                            </div>
                                            <div class="form-group">
                                                <label for="note">{{ trans('cruds.financialAssistance.fields.note') }}</label>
                                                <input class="form-control {{ $errors->has('note') ? 'is-invalid' : '' }}" type="text" name="note" id="note" value="{{ old('note', '') }}">
                                                @if($errors->has('note')) <span class="text-danger">{{ $errors->first('note') }}</span> @endif
                                                <span class="help-block">{{ trans('cruds.financialAssistance.fields.note_helper') }}</span>
                                            </div>
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
                        </div>


                        {{-- ORDERS → FINANCIAL ASSISTANCE CREATE (unchanged fields/values) --}}
                        <div class="tab-pane" id="tab-orders">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="text-muted font-weight-bold mb-0">Financial Assistance List</h6>
                                </div>
                                <div class="table-responsive">
                                 @php $fas = $directory->financialAssistances ?? collect(); @endphp

                                    <div class="card mt-3">
                                    
                                        <div class="card-body p-0">
                                            @if($fas->isEmpty())
                                            <p class="p-3 mb-0 text-muted">No records yet.</p>
                                            @else
                                            <div class="table-responsive">
                                                <table class="table table-striped table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Date Interviewed</th>
                                                            <th>Problem Presented</th>
                                                            <th>Assessment</th>
                                                            <th>Recommendation</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th>Date Claimed</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($fas as $fa)
                                                        <tr>
                                                            <td>{{ optional($fa->date_interviewed)->format('Y-m-d') ?? $fa->date_interviewed }}</td>
                                                            <td>{{ $fa->problem_presented }}</td>
                                                            <td>{{ $fa->assessment }}</td>
                                                            <td>{{ $fa->recommendation }}</td>
                                                            <td>{{ $fa->amount }}</td>
                                                            <td>{{ $fa->status }}</td>
                                                            <td>{{ optional($fa->date_claimed)->format('Y-m-d') ?? $fa->date_claimed }}</td>
                                                            <td class="text-nowrap">
                                                                <a href="{{ route('admin.financial-assistances.edit', $fa->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                                {{-- add show/delete if needed --}}
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif
                                        </div>
                                    </div>


                                  

                                </div>
                            </div>
                        </div>

                        {{-- FAMILY (read-only table) --}}
                        <div class="tab-pane" id="tab-family">
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
                                                <td>{{ \App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT[$fam->family_relationship] ?? ($fam->family_relationship ?? '—') }}</td>
                                                <td>{{ \App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT[$fam->family_civil_status] ?? ($fam->family_civil_status ?? '—') }}</td>
                                                <td>{{ \App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT[$fam->family_highest_edu] ?? ($fam->family_highest_edu ?? '—') }}</td>
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
    <a class="btn btn-default" href="{{ route('admin.directories.index') }}">
        {{ trans('global.back_to_list') }}
    </a>
</div>
@endsection

@push('scripts')
<script>
    // Dropzone for Financial Assistance requirements (unchanged behavior)
    var uploadedRequirementsMap = {};
    if (window.Dropzone) {
        Dropzone.options = Dropzone.options || {};
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
                $('form').append('<input type="hidden" name="requirements[]" value="' + response.name + '">');
                uploadedRequirementsMap[file.name] = response.name;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                var name = '';
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name;
                } else {
                    name = uploadedRequirementsMap[file.name];
                }
                $('form').find('input[name="requirements[]"][value="' + name + '"]').remove();
            },
            init: function() {
                @if(isset($financialAssistance) && $financialAssistance -> requirements)
                var files = {
                    !!json_encode($financialAssistance - > requirements) !!
                };
                for (var i in files) {
                    var file = files[i];
                    this.options.addedfile.call(this, file);
                    file.previewElement.classList.add('dz-complete');
                    $('form').append('<input type="hidden" name="requirements[]" value="' + file.file_name + '">');
                }
                @endif
            },
            error: function(file, response) {
                var message = $.type(response) === 'string' ? response : response.errors.file;
                file.previewElement.classList.add('dz-error');
                var nodes = file.previewElement.querySelectorAll('[data-dz-errormessage]');
                nodes.forEach(function(n) {
                    n.textContent = message;
                });
            }
        };
    }
</script>
@endpush