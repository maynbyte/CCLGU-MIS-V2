<div id="print-id-section" class="mt-3">
    <style>
        /* Scoped to this partial: display all id-value text in uppercase */
        #print-id-section .id-value { text-transform: uppercase; }
    </style>
    <div class="id-card d-flex p-3">
        <div class="id-header">
            <div class="id-left ml-5">
                    <img src="{{ asset('/cavite-city-seal.png') }}" alt="Cavite City Seal" style="width:72px; height:auto;">
                </div>
            <div class="gov-title">
                <div style="font-size:14px; font-weight:700;">REPUBLIKA NG PILIPINAS</div>
                <div style="font-size:12px; font-weight:500;">Republic of the Philippines</div>
                <div style="font-size:14px; font-weight:700;">PAMBANSANG PAGKAKAKILANLAN</div>
                <div style="font-size:12px; font-weight:500;">Cavite City Identification Card</div>
            </div>
            <div class="id-right mr-5">
                <img src="{{ asset('/upload/C1.png') }}" style="width:80px; height:auto;" alt="Cavite City Logo">
            </div>
        </div>
        {{-- UID placed below left logo outside the header layout --}}
        @php
            $uidRaw = $directory->uid ?? '';
            $uidFmt = $uidRaw ? preg_replace('/(.{4})(?=.)/', '$1-', $uidRaw) : '';
        @endphp
        <div class="id-uid-below-left">{{ $uidFmt ?: '1234-5678-1234-5678' }}</div>
        <div style="width:100%; display:flex; justify-content:center; align-items:flex-start;">
            <div class="id-body ml-5" style="display:grid; grid-template-columns: 1fr 2fr; grid-template-rows: auto auto; gap:12px 24px; max-width:720px; width:100%; gap:80px;">
            <!-- Left large colored profile photo -->
            <div class="grid-left-photo" style="grid-row: 1 / span 2; grid-column: 1; display:flex; align-items:flex-start; gap:10px;">
                <div class="id-photo" style="flex:1 1 auto;">
                    <img src="{{ $photoUrl ?: $templateAvatar }}" alt="Photo" style="display:block; width:100%; height:auto;">
                </div>
                <div class="bw-photo" style="flex:0 0 60px; width:60px; height:80px; overflow:hidden; border:1px solid rgba(0,0,0,0.08); background:#fff; padding:1px; filter:grayscale(100%); box-shadow:0 1px 2px rgba(0,0,0,0.08); align-self:flex-start;">
                    <img src="{{ $photoUrl ?: $templateAvatar }}" alt="Photo BW" style="display:block; width:100%; height:100%; object-fit:cover;">
                </div>
            </div>

            <div class="grid-main-details" style="grid-row: 1 / span 2; grid-column: 2;">
                <div class="id-details">
                <div class="id-row" style="display:block; margin-bottom:8px;">
                    <div class="id-label">Apelyido / Last Name</div>
                    <div class="id-value">{{ $directory->last_name ?: 'DELA CRUZ' }}</div>
                </div>
                <div class="id-row" style="display:block; margin-bottom:8px;">
                    <div class="id-label">Mga Pangalang / Given Names</div>
                    <div class="id-value">{{ $directory->first_name ?: 'JUAN' }}</div>
                </div>
                <div class="id-row" style="display:block; margin-bottom:8px;">
                    <div class="id-label">Gitnang Apelyido / Middle Name</div>
                    <div class="id-value">{{ $directory->middle_name ?: 'MARTINEZ' }}</div>
                </div>
                <div class="id-row" style="display:block; margin-bottom:8px;">
                    <div class="id-label">Petsa ng Kapanganakan / Date of Birth</div>
                    <div class="id-value">{{ $fmt($directory->birthday) ?: 'JANUARY 01, 1990' }}</div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="id-footer ml-5 mb-2">
            <div class="id-label">Tirahan/Address</div>
            <div class="id-value">{{ $address }}</div>
            
        </div>
    </div>
</div>
