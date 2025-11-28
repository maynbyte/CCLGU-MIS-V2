<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payout Verification - {{ $financialAssistance->reference_no }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">

{{-- Fixed background + overlay --}}
<div class="auth-bg" aria-hidden="true"></div>

{{-- Fixed repeating landmark strip at the bottom --}}
<div class="auth-landmark" aria-hidden="true"></div>

<style>
    html {
        height: 100%;
        min-height: 100vh;
    }

    body.login-page {
        height: 100% !important;
        min-height: 100vh !important;
        margin: 0 !important;
        padding: 0 !important;
        background: url('{{ asset('city_hall.jpg') }}') no-repeat center center fixed !important;
        background-size: cover !important;
        background-color: transparent !important;
    }

    body.login-page::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.85);
        z-index: 0;
        pointer-events: none;
    }
    /* Full-page background with white overlay */
    .auth-bg {
        position: fixed;
        inset: 0;
        background:
          linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
          url('{{ asset('city_hall.jpg') }}') no-repeat center center;
        background-size: cover;
        background-attachment: fixed;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
    }

    /* Bottom landmark strip (repeat-x) */
    .auth-landmark {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        height: 50px;
        background: url('{{ asset('landmark.png') }}') repeat-x center bottom;
        background-size: auto 50px;
        z-index: 1;
        pointer-events: none;
    }

    /* Login box styling */
    .details-box,
    .details-box .card {
        position: relative;
        z-index: 2;
    }

    .details-box {
        position: absolute !important;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
    }

    /* Mobile (≤576px) - Fit to screen */
    @media (max-width: 576px) {
        body.login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 350px 10px 360px 10px !important;
        }

        .details-box {
            width: 95% !important;
            max-width: 95% !important;
            padding: 0 10px !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
            transform: none !important;
            margin-bottom: 30px !important;
        }
        
        .card-body.login-card-body {
            padding: 1rem 0.75rem !important;
        }
        
        .login-box-msg {
            font-size: 0.95rem !important;
            margin-bottom: 0.75rem !important;
        }
        
        .info-section {
            margin-bottom: 1rem !important;
        }
        
        .info-section h2 {
            font-size: 0.85rem !important;
            margin-bottom: 0.5rem !important;
            padding-bottom: 0.3rem !important;
        }
        
        .info-row {
            flex-direction: column !important;
            padding: 0.35rem 0 !important;
            font-size: 0.8rem !important;
        }
        
        .info-label {
            min-width: auto !important;
            margin-bottom: 0.2rem !important;
            font-size: 0.8rem !important;
        }
        
        .info-label i {
            width: 16px !important;
            font-size: 0.75rem !important;
        }
        
        .info-value {
            padding-left: 1.25rem !important;
            font-size: 0.8rem !important;
        }
        
        .payout-highlight {
            padding: 0.5rem !important;
            font-size: 0.8rem !important;
            margin: 0.5rem 0 !important;
        }
        
        .status-badge {
            font-size: 0.65rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        
        .reference-number {
            font-size: 0.85rem !important;
        }
        
        .alert {
            font-size: 0.75rem !important;
            padding: 0.4rem !important;
        }

        .auth-landmark {
            position: fixed;
            bottom: 0;
            height: 40px;
            background-size: auto 40px;
            z-index: 999;
        }
        
    }

    /* Tablet to Laptop (577px-1024px) - Smooth transition */
    @media (min-width: 577px) and (max-width: 1024px) {
        body.login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(30px, 4vw, 40px) clamp(15px, 2vw, 20px) clamp(80px, 10vw, 100px) clamp(15px, 2vw, 20px) !important;
        }

        .details-box {
            width: clamp(90%, 95%, 98%) !important;
            max-width: clamp(700px, 85vw, 1000px) !important;
            padding: 0 clamp(15px, 2vw, 20px) !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
            transform: none !important;
            margin: clamp(20px, 3vw, 30px) auto !important;
        }
        
        .card-body.login-card-body {
            padding: clamp(1.5rem, 2.5vw, 2.5rem) !important;
        }
        
        .login-box-msg {
            font-size: clamp(1.05rem, 1.5vw, 1.2rem) !important;
            margin-bottom: clamp(1rem, 1.5vw, 1.5rem) !important;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(1.5rem, 2vw, 2rem);
            margin-bottom: clamp(1.5rem, 2vw, 2rem);
        }

        .info-section {
            margin-bottom: 0 !important;
        }
        
        .info-section h2 {
            font-size: clamp(0.9rem, 1.2vw, 1.1rem) !important;
            margin-bottom: clamp(0.75rem, 1vw, 1rem) !important;
            padding-bottom: clamp(0.4rem, 0.6vw, 0.6rem) !important;
        }
        
        .info-row {
            font-size: clamp(0.85rem, 1vw, 1rem) !important;
            padding: clamp(0.4rem, 0.6vw, 0.75rem) 0 !important;
        }
        
        .info-label {
            min-width: clamp(100px, 12vw, 140px) !important;
            font-size: clamp(0.85rem, 1vw, 1rem) !important;
        }
        
        .info-value {
            font-size: clamp(0.85rem, 1vw, 1rem) !important;
        }
        
        .payout-highlight {
            font-size: clamp(0.85rem, 1vw, 1rem) !important;
            padding: clamp(0.6rem, 0.8vw, 1rem) !important;
            margin: clamp(0.75rem, 1vw, 1rem) 0 !important;
        }
        
        .reference-number {
            font-size: clamp(0.9rem, 1.1vw, 1.1rem) !important;
        }

        .auth-landmark {
            position: fixed;
            bottom: 0;
            height: clamp(50px, 6vw, 60px);
            background-size: auto clamp(50px, 6vw, 60px);
            z-index: 999;
        }
    }

    /* Desktop (>1024px) - Wide form */
    @media (min-width: 1025px) {
        body.login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px 100px 20px !important;
        }

        .details-box {
            width: 95% !important;
            max-width: 1200px !important;
            padding: 0 20px !important;
            position: relative !important;
            top: auto !important;
            left: auto !important;
            transform: none !important;
            margin: 20px auto !important;
        }
        
        .card-body.login-card-body {
            padding: 2.5rem !important;
        }
        
        .login-box-msg {
            font-size: 1.2rem !important;
            margin-bottom: 1.5rem !important;
        }
        
        .info-section {
            margin-bottom: 2rem !important;
        }
        
        .info-section h2 {
            font-size: 1.1rem !important;
            margin-bottom: 1rem !important;
            padding-bottom: 0.6rem !important;
        }
        
        .info-row {
            font-size: 1rem !important;
            padding: 0.75rem 0 !important;
        }
        
        .info-label {
            min-width: 150px !important;
            font-size: 1rem !important;
        }
        
        .info-value {
            font-size: 1rem !important;
        }
        
        .payout-highlight {
            font-size: 1rem !important;
            padding: 1rem !important;
            margin: 1rem 0 !important;
        }
        
        .reference-number {
            font-size: 1.1rem !important;
        }

        .auth-landmark {
            position: fixed;
            bottom: 0;
            height: 60px;
            background-size: auto 60px;
            z-index: 999;
        }
    }

    .info-section {
        margin-bottom: 1.5rem;
    }

    .info-section h2 {
        font-size: 1rem;
        font-weight: 600;
        color: #007bff;
        margin-bottom: 0.75rem;
        border-bottom: 2px solid #007bff;
        padding-bottom: 0.5rem;
    }

    .info-row {
        display: flex;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 0.9rem;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #555;
        min-width: 120px;
        display: flex;
        align-items: center;
    }

    .info-label i {
        margin-right: 0.5rem;
        color: #007bff;
        width: 18px;
    }

    .info-value {
        color: #333;
        flex: 1;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .status-ongoing {
        background: #fff3cd;
        color: #856404;
    }

    .status-pending {
        background: #cfe2ff;
        color: #084298;
    }

    .status-claimed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #842029;
    }

    .payout-highlight {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 0.75rem;
        margin: 0.75rem 0;
        border-radius: 6px;
        font-size: 0.9rem;
    }

    .payout-highlight strong {
        color: #007bff;
    }

    .reference-number {
        font-family: 'Courier New', monospace;
        font-weight: 700;
        font-size: 1rem;
    }

    /* 2x2 Grid Layout for Desktop */
    @media (min-width: 769px) {
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-section {
            margin-bottom: 0 !important;
        }

        .info-section.full-width {
            grid-column: 1 / -1;
        }
    }
</style>

<div class="details-box">
    <div class="card">
        <div class="card-body login-card-body">
            <p class="details-box-msg mb-3">
                <strong>Financial Assistance Details</strong>
            </p>

            <div class="info-grid">
                <!-- Directory Information -->
                <div class="info-section">
                    <h2><i class="fas fa-user"></i> Beneficiary Information</h2>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-id-card"></i> Name:</span>
                        <span class="info-value">
                            {{ $directory->first_name }} 
                            {{ $directory->middle_name ? $directory->middle_name . ' ' : '' }}
                            {{ $directory->last_name }}
                            {{ $directory->suffix ? ' ' . $directory->suffix : '' }}
                        </span>
                    </div>
                </div>

                <!-- Reference Number -->
                <div class="info-section">
                    <h2><i class="fas fa-barcode"></i> Reference Number</h2>
                    <div class="info-row">
                        <span class="info-value reference-number">{{ $financialAssistance->reference_no }}</span>
                    </div>
                </div>

                <!-- Assistance Details -->
                <div class="info-section">
                    <h2><i class="fas fa-hands-helping"></i> Assistance Details</h2>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-tag"></i> Type:</span>
                        <span class="info-value">{{ $financialAssistance->type_of_assistance ?? 'Financial Assistance' }}</span>
                    </div>
                    @if($financialAssistance->amount)
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-money-bill-wave"></i> Amount:</span>
                        <span class="info-value"><strong>₱{{ number_format($financialAssistance->amount, 2) }}</strong></span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-info-circle"></i> Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ strtolower($financialAssistance->status ?? 'ongoing') }}">
                                {{ $financialAssistance->status ?? 'Ongoing' }}
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Payout Details -->
                <div class="info-section">
                <h2><i class="fas fa-calendar-check"></i> Payout Details</h2>
                
                <div class="payout-highlight">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-map-marked-alt"></i> Where:</span>
                        <span class="info-value"><strong>{{ $payoutLocation }}</strong></span>
                    </div>
                </div>

                <div class="payout-highlight">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-clock"></i> When:</span>
                        <span class="info-value"><strong>{{ $scheduledDate }}</strong></span>
                    </div>
                </div>

                    @if($financialAssistance->date_claimed)
                    <div class="alert alert-success mt-3" style="font-size: 0.85rem; padding: 0.5rem;">
                        <i class="fas fa-check-double"></i> 
                        <strong>Claimed on:</strong> {{ \Carbon\Carbon::parse($financialAssistance->date_claimed)->format('F d, Y g:i A') }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="mt-3 text-center text-muted" style="font-size: 0.85rem;">
                <i class="fas fa-shield-alt"></i> For inquiries, please contact the Cavite City Hall
            </div>
        </div>
    </div>
</div>

<!-- Background and landmark elements -->
<div class="auth-bg"></div>
<div class="auth-landmark"></div>

</body>
</html>
