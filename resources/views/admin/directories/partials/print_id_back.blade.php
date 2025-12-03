<div id="print-id-back-section" class="mt-3">
    @php
        use Carbon\Carbon;
        $sex = $directory->gender ?? null;
        if ($sex) { $sex = \App\Models\Directory::GENDER_SELECT[$sex] ?? $sex; }
        $sex = $sex ?: 'N/A';
        $blood = $directory->blood_type ?? 'N/A';
        $civil = $directory->civil_status ?? 'N/A';
        if ($civil) { $civil = \App\Models\Directory::CIVIL_STATUS_SELECT[$civil] ?? $civil; }
        $pob = $directory->place_of_birth ?? 'N/A';
        $doi = $directory->date_of_issue ? Carbon::parse($directory->date_of_issue)->format('d F Y') : 'N/A';
        $uidRaw = $directory->uid ?? '';
        $uidFmt = $uidRaw ? preg_replace('/(.{4})(?=.)/', '$1-', $uidRaw) : '';
        $qrUrl = null;
        try {
            if (method_exists($directory, 'getFirstMediaUrl')) {
                $qrUrl = $directory->getFirstMediaUrl('qr_code');
            }
        } catch (\Throwable $e) { $qrUrl = null; }
    @endphp

    <div class="id-card d-flex p-3" style="height:430px; display:flex; align-items:stretch; justify-content:center;">
        <div style="width:100%; display:grid; grid-template-columns: 1.2fr 1fr; grid-template-rows: auto 1fr auto; gap:12px; padding:24px; box-sizing:border-box;">
            <!-- Header: Date of Issue -->
            <div class="mt-4" style="grid-column: 1 / 2; grid-row: 1; color:#6c757d; font-size:12px; line-height:1.2;">
                <div style="font-size: 10px;">Araw ng pagkakaloob / Date of Issue</div>
                <div style="font-weight:700; font-size: 12px; color:#2c3e50; text-transform:uppercase;">{{ $doi }}</div>
            </div>

            <!-- Right: QR Code -->
            <div style="grid-column: 2 / 3; grid-row: 1 / 3; display:flex; align-items:center; justify-content:center;">
                <div style="width:260px; height:260px; background:#fff; display:flex; align-items:center; justify-content:center; border:1px solid #e9ecef;">
                    @if($qrUrl)
                        <img src="{{ $qrUrl }}" alt="QR Code" style="width:100%; height:100%; object-fit:contain;">
                    @else
                        @php
                            // Fallback: render QR inline (SVG) encoding UID as JSON
                            $payload = json_encode([ 'uid' => $directory->uid ]);
                        @endphp
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(240)->margin(0)->generate($payload) !!}
                    @endif
                </div>
            </div>

            <!-- Left: Details -->
            <div style="grid-column: 1 / 2; grid-row: 2; align-self:start;">
                <div style="margin-bottom:8px;">
                    <div style="font-size:12px; color:#6c757d;">Kasarian / <span style="color:#6c757d;">Sex</span></div>
                    <div style="font-size:16px; font-weight:700; color:#2c3e50; text-transform:uppercase;">{{ $sex }}</div>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-size:12px; color:#6c757d;">Uri ng Dugo / <span style="color:#6c757d;">Blood Type</span></div>
                    <div style="font-size:16px; font-weight:700; color:#2c3e50; text-transform:uppercase;">{{ $blood }}</div>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-size:12px; color:#6c757d;">Kalagayang Sibil / <span style="color:#6c757d;">Marital Status</span></div>
                    <div style="font-size:16px; font-weight:700; color:#2c3e50; text-transform:uppercase;">{{ $civil }}</div>
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-size:12px; color:#6c757d;">Lugar ng Kapanganakan / <span style="color:#6c757d;">Place of Birth</span></div>
                    <div style="font-size:16px; font-weight:700; color:#2c3e50; text-transform:uppercase;">{{ $pob }}</div>
                </div>
            </div>

            <!-- Footer row -->
            <div style="grid-column: 1 / 2; grid-row: 3; align-self:end; color:#6c757d; font-size:12px;">
                If found, please return to the nearest PSA Office.<br>
                <span style="color:#2c3e50;">www.psa.gov.ph</span>
            </div>
            <div style="grid-column: 2 / 3; grid-row: 3; align-self:end; justify-self:end; text-align:right;">
                <div style="width:260px; height:28px; background:repeating-linear-gradient(90deg, rgba(0,0,0,0.85) 0 2px, transparent 2px 4px); margin-left:auto;"></div>
                <div style="margin-top:6px; font-family:monospace; font-size:12px; color:#2c3e50;">{{ $uidFmt ?: (str_pad((string)($directory->id ?? 0), 12, '0', STR_PAD_LEFT)) }}</div>
            </div>
        </div>
    </div>
</div>
