<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Case Summary Report</title>

    {{-- Bootstrap (CDN or your local build) --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    {{-- Your print stylesheet (lives in /public) --}}
    <link rel="stylesheet" href="{{ asset( 'css/case-study-print.css') }}">
</head>


@php
use Carbon\Carbon;



// Get family rows from the related directory; guarantee it's a Collection
$fam = collect(optional($financialAssistance->directory)->familycompositions ?? [])->values();



$dir = $financialAssistance->directory;

// Safe name parts (support old/new column names)
$first = $dir->first_name ?? $dir->firstname ?? '';
$middle = $dir->middle_name ?? $dir->middlename ?? '';
$last = $dir->last_name ?? $dir->lastname ?? '';
$suffix = $dir->suffix ?? '';

$fullName = trim(implode(' ', array_filter([$first, $middle, $last, $suffix])));

// Age
$age = '';
if (!empty($dir->birthday)) {
try { $age = Carbon::parse($dir->birthday)->age; } catch (\Throwable $e) { $age = ''; }
}

// Header date (interviewed)
$headerDate = '';
if (!empty($financialAssistance->date_interviewed)) {
try { $headerDate = Carbon::parse($financialAssistance->date_interviewed)->format('F d, Y'); } catch (\Throwable $e) {}
}

// Address
$streetNo = $dir->street_no ?? '';
$street = $dir->street ?? '';
$city = $dir->city ?? '';
$address = trim(implode(' ', array_filter([$streetNo, $street, $city])));

// Barangay (relation or string fallback)
$barangayName = optional($dir->barangay)->barangay_name ?? ($dir->barangay ?? '');

// Civil status (normalize)
$civil = $dir->civil_status ?? '';

// Education / Contact / Occupation / Religion
$highestEduc = $dir->highest_educ ?? '';
$contactNo = $dir->contact_no ?? '';
$occupation = $dir->occupation ?? '';
$religion = $dir->religion ?? '';
$pob = $dir->place_of_birth ?? '';

// Sector checkboxes – support either a relation OR a comma string
$sectorList = [];
if (method_exists($dir, 'sectors') && $dir->relationLoaded('sectors') ? $dir->sectors : ($dir->sectors ?? null)) {
$sectorList = collect($dir->sectors)->pluck('name')->map(fn($n)=>trim($n))->all();
} elseif (!empty($dir->sector)) {
$sectorList = collect(explode(',', $dir->sector))->map(fn($n)=>trim($n))->all();
}
// helper for case-insensitive sector contains
$hasSector = function(string $needle) use ($sectorList) {
foreach ($sectorList as $s) {
if (mb_strtolower($s) === mb_strtolower($needle)) return true;
}
return false;
};

// Problem Presented (current schema supports multi via JSON)
// Accept either array ['values'=>[], 'other'=>string] OR flat array OR comma string
$ppValues = [];
$ppOther = '';

$rawPP = $financialAssistance->problem_presented_value ?? null;
if (is_array($rawPP)) {
$ppValues = $rawPP['values'] ?? (array) $rawPP;
$ppOther = $rawPP['other'] ?? '';
} elseif (is_string($rawPP) && str_starts_with($rawPP, '[')) {
try { $decoded = json_decode($rawPP, true, 512, JSON_THROW_ON_ERROR);
$ppValues = $decoded['values'] ?? (array) $decoded;
$ppOther = $decoded['other'] ?? '';
} catch (\Throwable $e) {}
} elseif (is_string($rawPP) && $rawPP !== '') {
$ppValues = array_map('trim', explode(',', $rawPP));
}

// Legacy single text fallback
if (empty($ppValues) && !empty($financialAssistance->problem_presented)) {
$ppValues = [$financialAssistance->problem_presented];
}

$ppHas = function(string $needle) use ($ppValues) {
foreach ((array) $ppValues as $v) {
if (mb_strtolower($v) === mb_strtolower($needle)) return true;
}
return false;
};

// Family composition – use first 4 of the directory’s familycompositions if present
$fam = $dir->familycompositions ?? collect();
$fam = collect($fam)->take(4)->values();

@endphp

<body>
    <div class="container my-4">
        <!-- Header Information -->
        <div class="container">
            <div class="row align-items-center">
                <!-- Left Column: Logo -->
                <div class="col-md-3" style="left: 80px;">
                    <img src="{{ asset('upload/Seal.jpg') }}" class="header-logo logo-left" alt="Left Logo">

                </div>
                <!-- Center Column: Header Text -->
                <div class="col-md-5 text-center">
                    <h5 class="mb-0">Republic of the Philippines</h5>
                    <h5 class="mb-0">City of Cavite</h5>
                    <p class="mb-0">Office of the City Social Welfare &amp; Development Office</p>
                    <p class="mb-0">Samonte Park, Cavite City</p>
                    <p class="mb-0">
                        Email: <a href="mailto:cswdo.cavitecity2020@gmail.com">cswdo.cavitecity2020@gmail.com</a>
                    </p>
                    <p class="mb-0">Tel. No.: (046)431-7859 / 887-9878</p>
                </div>
                <!-- Right Column: Logo -->
                <div class="col-md-3 text-left">
                    <img src="{{ asset('upload/DSWD-logo.png') }}" class="header-logo logo-right" alt="Right Logo">

                </div>
            </div>
        </div>

        <!-- Date and Report Title -->
        <div class="d-flex align-items-center mb-3 date-container">
            <div class="ml-auto text-right">
                <strong>Date:</strong>
                <span class="date-line">{{ $headerDate }}</span>
            </div>
        </div>
        <div class="text-center mb-2 header-text">
            <h3>CASE SUMMARY REPORT</h3>
        </div>

        <!-- Identifying Information Section -->
        <div class="mb-2">
            <h5>I. IDENTIFYING INFORMATION</h5>
        </div>
        <div class="identifying-info">
            <!-- Row 1: Name, Age, Sex -->
            <div class="form-group indent name-row">
                <div class="name-field">
                    <label for="name" class="field-label">Name:</label>
                    <span class="underline field-value">{{ $fullName }}</span>
                </div>
                <div class="age-field">
                    <label for="age" class="field-label">Age:</label>
                    <span class="underline field-value">{{ $age ? $age.' Years Old' : '' }}</span>
                </div>
                <div class="sex-field">
                    <label for="sex" class="field-label">Sex:</label>
                    <span class="underline field-value">{{ $dir->gender ?? '' }}</span>
                </div>
            </div>

            <!-- Row 2: Address and Barangay -->
            <div class="form-group indent address-row">
                <div class="address-field">
                    <label for="address" class="field-label">Address:</label>
                    <span class="underline field-value">{{ $address }}</span>
                </div>
                <div class="barangay-field">
                    <label for="barangay" class="field-label">Barangay:</label>
                    <span class="underline field-value">{{ $barangayName }}</span>
                </div>
            </div>

            <!-- Row 3: Date of Birth and Place of Birth -->
            <div class="form-group indent dob-row">
                <div class="dob-field">
                    <label for="dob" class="field-label">Date of Birth:</label>
                    <span class="underline field-value">
                        {{ !empty($dir->birthday) ? Carbon::parse($dir->birthday)->format('F d, Y') : '' }}
                    </span>
                </div>
                <div class="pob-field">
                    <label for="pob" class="field-label">Place of Birth:</label>
                    <span class="underline field-value">{{ $pob }}</span>
                </div>
            </div>

            <div class="form-group indent d-flex align-items-baseline mt-2">
                <label for="civilStatus" class="field-label">Civil Status:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="single" value="Single"
                        {{ $civil === 'Single' ? 'checked' : '' }}>
                    <label class="form-check-label" for="single">Single</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="married" value="Married"
                        {{ $civil === 'Married' ? 'checked' : '' }}>
                    <label class="form-check-label" for="married">Married</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="livein" value="Live-in/Common-Law"
                        {{ $civil === 'Live-in/Common-Law' ? 'checked' : '' }}>
                    <label class="form-check-label" for="livein">Live-in / Common-Law</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="widower" value="Widow/er"
                        {{ $civil === 'Widow/er' ? 'checked' : '' }}>
                    <label class="form-check-label" for="widower">Widow/er</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="separated" value="Separated"
                        {{ $civil === 'Separated' ? 'checked' : '' }}>
                    <label class="form-check-label" for="separated">Separated</label>
                </div>
            </div>

            <div class="form-group indent d-flex align-items-baseline mt-2">
                <div class="higheduc-field">
                    <label for="higheduc" class="field-label">Educational Attainment:</label>
                    <span class="underline field-value">{{ $highestEduc }}</span>
                </div>
                <div class="contact-field">
                    <label for="contact_no" class="field-label">Contact No.</label>
                    <span class="underline field-value">{{ $contactNo }}</span>
                </div>
            </div>

            <div class="form-group indent d-flex align-items-baseline mt-2">
                <div class="occupation-field">
                    <label for="occupation" class="field-label">Occupation</label>
                    <span class="underline field-value">{{ $occupation }}</span>
                </div>
                <div class="religion-field">
                    <label for="religion" class="field-label">Religion</label>
                    <span class="underline field-value">{{ $religion }}</span>
                </div>
            </div>

            <div class="form-group indent d-flex align-items-baseline mt-2">
                <label for="sector" class="field-label">Sector:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="senior" value="Senior Citizen"
                        {{ $hasSector('Senior Citizen') ? 'checked' : '' }}>
                    <label class="form-check-label" for="senior">Senior Citizen</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="PWD" value="PWD"
                        {{ $hasSector('PWD') ? 'checked' : '' }}>
                    <label class="form-check-label" for="PWD">PWD</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="4ps" value="4ps"
                        {{ $hasSector('4ps') ? 'checked' : '' }}>
                    <label class="form-check-label" for="4ps">4ps</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="solo_parent" value="solo_parent"
                        {{ $hasSector('solo_parent') ? 'checked' : '' }}>
                    <label class="form-check-label" for="solo_parent">Solo Parent</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="UPAO" value="UPAO"
                        {{ $hasSector('UPAO') ? 'checked' : '' }}>
                    <label class="form-check-label" for="UPAO">UPAO</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="Others" value="Others"
                        {{ $hasSector('Others') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Others">Others</label>
                </div>
            </div>
        </div>

        <!-- Family Composition -->
        <h5 class="section-title">II. FAMILY COMPOSITION</h5>
        <div class="table-responsive" style="font-size: 12px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>DATE OF BIRTH</th>
                        <th>RELATIONSHIP</th>
                        <th>CIVIL STATUS</th>
                        <th>EDUCATIONAL ATTAINMENT</th>
                        <th>OCCUPATION / REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < 4; $i++)
                        @php
                        $row=$fam->get($i); // may be null if fewer than 4 rows

                        $rel = $row?->family_relationship;
                        $civ = $row?->family_civil_status;
                        $edu = $row?->family_highest_edu;
                        $name = $row?->family_name ?? '';
                        $dob = $row?->family_birthday
                        ? Carbon::parse($row->family_birthday)->format('F d, Y')
                        : '';
                        $occ = trim(($row?->occupation ?? '').' '.($row?->remarks ?? ''));
                        @endphp
                        <tr>
                            <td class="table-col-name">{{ $name }}</td>
                            <td class="table-col-dob">{{ $dob }}</td>
                            <td class="table-col-relationship">
                                {{ $rel ? (\App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT[$rel] ?? $rel) : '' }}
                            </td>
                            <td class="table-col-civil">
                                {{ $civ ? (\App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT[$civ] ?? $civ) : '' }}
                            </td>
                            <td class="table-col-education">
                                {{ $edu ? (\App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT[$edu] ?? $edu) : '' }}
                            </td>
                            <td class="table-col-occupation">{{ $occ }}</td>
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>

        <!-- Problem Presented -->
        <h5 class="section-title">III. PROBLEM PRESENTED</h5>
        <div class="form-group">
            <div class="form-group indent">
                <label for="problempresented" class="field-label">
                    The client is seeking assistance to cope with his/her present situation for:
                </label>
                <div class="row">
                    <!-- Column 1 -->
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="medicalAssistance" value="Medical Assistance"
                                {{ $ppHas('Medical Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="medicalAssistance">Medical Assistance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="burialAssistance" value="Burial Assistance"
                                {{ $ppHas('Burial Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="burialAssistance">Burial Assistance</label>
                        </div>
                    </div>

                    <!-- Column 2 -->
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="foodAssistance" value="Food Assistance"
                                {{ $ppHas('Food Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="foodAssistance">Food Assistance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="shelterAssistance" value="Shelter Assistance"
                                {{ $ppHas('Shelter Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="shelterAssistance">Shelter Assistance</label>
                        </div>
                    </div>

                    <!-- Column 3 -->
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="educationalAssistance" value="Educational Assistance"
                                {{ $ppHas('Educational Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="educationalAssistance">Educational Assistance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="transportationAssistance" value="Transportation Assistance"
                                {{ $ppHas('Transportation Assistance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="transportationAssistance">Transportation Assistance</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="othersAssistance" value="Others"
                                {{ $ppHas('Others') ? 'checked' : '' }}>
                            <label class="form-check-label" for="othersAssistance">Others</label>
                            @if($ppHas('Others') && $ppOther)
                            <span class="underline field-value">{{ $ppOther }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment -->
        <h5 class="section-title">IV. ASSESSMENT</h5>
        <div class="form-group indent">
            <label for="assessment" class="field-label">
                Client is assessed to be in dire need of financial assistance due to insufficient family’s income to defray the cost of
            </label>
            <span class="assessment-text">
                {{ $financialAssistance->assessment }}
            </span>
        </div>

        <!-- Recommendation -->
        <h5 class="section-title">V. RECOMMENDATION</h5>
        <div class="form-group indent">
            <label for="recommendation" style="padding-bottom:5px;">{{ $financialAssistance->recommendation }}</label>
        </div>

        <!-- Signature -->
        <div class="signature">
            <div class="text-left">
                <p class="mb-4"><strong>INTERVIEWED AND ASSESSED BY:</strong></p>
                <p class="mb-0"><strong>{{ $financialAssistance->social_welfare_name ?? 'CRISTINE JOY G. MINGO, RSW' }}</strong></p>
                <p>{{ $financialAssistance->social_welfare_desig ?? 'Social Welfare Officer I' }}</p>
            </div>
            <div class="text-right">
                <p class="mb-4"><strong>REVIEWED AND APPROVED BY:</strong></p>
                <p class="mb-0"><strong>ROWENA C. ANDRADE, RSW</strong></p>
                <p>City Social Welfare &amp; Dev't Officer</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<script>
    window.onload = function() {
        window.print();
    };
</script>