<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payroll - Financial Assistance</title>
    <style>
        @media print {
            @page { 
                size: landscape;
                margin: 0.3in 0.3in 0.3in 0.3in;
            }
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: left;
            margin-bottom: 10px;
        }
        .header .title {
            font-weight: bold;
            font-size: 11px;
            margin: 2px 0;
        }
        .header .subtitle {
            font-size: 10px;
            margin: 1px 0;
        }
        .header-labels {
            position: absolute;
            top: 10px;
            right: 10px;
            text-align: right;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 8px;
            vertical-align: middle;
        }
        th {
            background-color: #fff;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
        }
        td {
            text-align: center;
        }
        td.text-left {
            text-align: left;
        }
        .col-no { width: 25px; }
        .col-date { width: 50px; }
        .col-name { width: 140px; }
        .col-sex { width: 30px; }
        .col-age { width: 25px; }
        .col-claimant { width: 140px; }
        /* relationship column removed */
        .col-barangay { width: 100px; }
        .col-kind { width: 80px; }
        .col-address { width: 120px; }
        .col-amount { width: 60px; }
        .col-signature { width: 80px; }
        .footer-section {
            margin-top: 15px;
            font-size: 8px;
        }
        .footer-text {
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .signature-box {
            text-align: center;
            flex: 1;
        }
        .signature-box .name {
            font-weight: bold;
            margin-top: 30px;
            text-decoration: underline;
        }
        .signature-box .position {
            font-size: 8px;
        }
        .no-print {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: flex-end;
        }
        .no-print button {
            margin: 0;
            padding: 5px 12px;
            cursor: pointer;
        }
        .subtotal-row td {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print</button>
        <button onclick="window.close()">✖ Close</button>
    </div>

    <div class="header">
        <div class="title">LIST OF BENEFICIARIES</div>
        <div class="subtitle">CITY OF CAVITE</div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">NO.</th>
                <th rowspan="2" class="col-date">DATE</th>
                <th rowspan="2" class="col-name">NAME OF BENEFICIARY</th>
                <th rowspan="2" class="col-sex">SEX</th>
                <th rowspan="2" class="col-age">AGE</th>
                <th rowspan="2" class="col-claimant">NAME OF CLAIMANT</th>
                <!-- RELATIONSHIP column removed -->
                <th rowspan="2" class="col-barangay">BARANGAY</th>
                <th rowspan="2" class="col-kind">TYPE OF<br>ASSISTANCE</th>
                <th rowspan="2" class="col-address">ADDRESS</th>
                <th rowspan="2" class="col-amount">CONTACT NUMBER</th>
                <th rowspan="2" class="col-signature">SIGNATURE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($directories as $index => $directory)
            @php
                $fa = $directory->latestFinancialAssistance;
                $age = $directory->birthday ? \Carbon\Carbon::parse($directory->birthday)->age : '';
                $fullName = trim(implode(' ', array_filter([
                    $directory->first_name,
                    $directory->middle_name,
                    $directory->last_name,
                    $directory->suffix
                ])));
                $address = trim(implode(', ', array_filter([
                    $directory->street_no,
                    $directory->street,
                    $directory->barangay->barangay_name ?? $directory->barangay_other
                ])));
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $fa && $fa->scheduled_fa ? \Carbon\Carbon::parse($fa->scheduled_fa)->format('m/d/Y') : '' }}</td>
                <td class="text-left">{{ strtoupper($fullName) }}</td>
                <td>{{ $directory->gender ? substr($directory->gender, 0, 1) : '' }}</td>
                <td>{{ $age }}</td>
                <td class="text-left">{{ $fa ? $fa->claimant_name : '' }}</td>
                <td class="text-left">{{ $directory->barangay->barangay_name ?? $directory->barangay_other ?? '' }}</td>
                <td class="text-left">{{ $fa ? $fa->type_of_assistance : '' }}</td>
                <td class="text-left">{{ $address }}</td>
                <td>{{ $directory->contact_no ?? '' }}</td>
                <td style="background-color: #f9f9f9;">&nbsp;</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align: center; padding: 20px;">No recipients scheduled for {{ \Carbon\Carbon::parse($payoutDate)->format('F d, Y') }}</td>
            </tr>
            @endforelse
            
            @if($directories->count() > 0)
            @for($i = $directories->count(); $i < 20; $i++)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="background-color: #f9f9f9;">&nbsp;</td>
            </tr>
            @endfor
            @endif
        </tbody>
    </table>

    {{-- Footer/signature blocks removed — showing only the full list for printing --}}

    <script>
        // Auto-print on load
        window.onload = function() { 
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
