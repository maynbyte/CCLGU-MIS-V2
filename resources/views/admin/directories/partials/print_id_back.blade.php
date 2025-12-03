<div id="print-id-back-section" class="mt-3">

    <div class="id-card d-flex p-3" style="height:430px; display:flex; align-items:stretch; justify-content:center;">
        <div style="width:100%; display:flex; gap:24px; padding:24px; box-sizing:border-box;">
            <div style="flex:1; display:flex; flex-direction:column; justify-content:space-between;">
                <div>
                    <h5 style="margin:0 0 8px 0; font-weight:700;">Emergency Contact</h5>
                    <div style="color:#495057; font-size:14px;">Name: {{ $directory->emergency_contact_name ?? 'N/A' }}</div>
                    <div style="color:#495057; font-size:14px;">Phone: {{ $directory->emergency_contact_no ?? 'N/A' }}</div>
                    <div style="margin-top:12px; color:#6c757d; font-size:13px;">Blood Type: {{ $directory->blood_type ?? 'N/A' }}</div>
                </div>

                <div style="margin-top:8px;">
                    <div style="font-size:12px; color:#6c757d;">Authorized Signature</div>
                    <div style="height:48px; border-bottom:2px dashed rgba(0,0,0,0.1); width:240px;"></div>
                </div>
            </div>

            <div style="flex:0 0 240px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                @php
                    $qrUrl = null;
                    try {
                        if (method_exists($directory, 'getFirstMediaUrl')) {
                            $qrUrl = $directory->getFirstMediaUrl('qr_code');
                        }
                    } catch (\Throwable $e) { $qrUrl = null; }
                @endphp

                <div style="width:200px; height:200px; background:#fff; display:flex; align-items:center; justify-content:center; border:1px solid #e9ecef;">
                    @if($qrUrl)
                        <img src="{{ $qrUrl }}" alt="QR Code" style="width:100%; height:100%; object-fit:contain;">
                    @else
                        <div style="text-align:center; color:#6c757d;">QR CODE</div>
                    @endif
                </div>

                <div style="margin-top:12px; font-size:12px; color:#6c757d; text-align:center;">Scan for verification</div>
            </div>
        </div>
    </div>
</div>
