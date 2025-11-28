<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QR Code - {{ $financialAssistance->reference_no }}</title>
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
  @media print {
    .no-print { display: none; }
    body { margin: 0; padding: 0; }
  }

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
  .auth-bg{
    position: fixed;
    inset: 0; /* top:0; right:0; bottom:0; left:0 */
    background:
      linear-gradient(rgba(255,255,255,0.85), rgba(255,255,255,0.85)),
      url('{{ asset('city_hall.jpg') }}') no-repeat center center;
    background-size: cover;
    background-attachment: fixed;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none; /* don't block clicks */
  }

  /* Bottom landmark strip (repeat-x) */
  .auth-landmark{
    position: fixed;
    left: 0; right: 0; bottom: 0;
    height: 50px;            /* adjust strip height if needed */
    background: url('{{ asset('landmark.png') }}') repeat-x center bottom;
    background-size: auto 50px; /* scale height to 50px */
    z-index: 1;
    pointer-events: none;
  }

  /* Ensure the login card sits above the background */
  .login-box, .login-box .card { position: relative; z-index: 2; }

  /* Login box to match login form size and center it */
  .login-box {
    width: 400px !important;
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    margin: 0 !important;
  }

  /* QR Code specific styles */
  .qr-content {
    text-align: center;
    padding: 30px 20px;
  }

  .qr-code-wrapper {
    display: inline-block;
    padding: 15px;
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 25px;
  }

  .qr-code-wrapper img {
    display: block;
    width: 200px;
    height: 200px;
  }

  .reference-section {
    margin-top: 20px;
  }

  .reference-label {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
  }

  .reference-number {
    font-family: 'Courier New', monospace;
    font-weight: 700;
    font-size: 18px;
    color: #212529;
    padding: 10px 20px;
    background: #f8f9fa;
    border-radius: 5px;
    display: inline-block;
    border: 1px solid #dee2e6;
  }

  .btn-group {
    margin-top: 20px;
  }
</style>

<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <div class="qr-content">
                <!-- QR Code Section -->
                <div class="qr-section">
                    @if($financialAssistance->qr_code)
                        <div class="qr-code-wrapper">
                            <img src="{{ $financialAssistance->getFirstMediaUrl('qr_code') }}" 
                                 alt="QR Code for {{ $financialAssistance->reference_no }}">
                        </div>
                    @else
                        <div style="padding: 60px; border: 2px dashed #ccc; border-radius: 8px;">
                            <p style="color: #999; margin: 0;">QR Code not generated</p>
                        </div>
                    @endif
                </div>

                <!-- Reference Number Section -->
                <div class="reference-section">
                    <div class="reference-label">Reference Number</div>
                    <div class="reference-number">{{ $financialAssistance->reference_no }}</div>
                </div>

                <!-- Buttons -->
                <div class="btn-group no-print">
                    <button onclick="window.print()" class="btn btn-primary btn-sm">
                        <i class="fas fa-print"></i> Print
                    </button>
                    <a href="{{ route('admin.financial-assistances.show', $financialAssistance->id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
