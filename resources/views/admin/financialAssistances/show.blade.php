@extends('layouts.admin')
@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-eye"></i> {{ trans('global.show') }} {{ trans('cruds.financialAssistance.title') }}
        </h5>
    </div>

    <div class="card-body">
        <!-- Action Buttons -->
        <div class="mb-4">
            <a class="btn btn-secondary" href="{{ route('admin.financial-assistances.index') }}">
                <i class="fas fa-arrow-left"></i> {{ trans('global.back_to_list') }}
            </a>
            @can('financial_assistance_edit')
            <a class="btn btn-info" href="{{ route('admin.financial-assistances.edit', $financialAssistance->id) }}">
                <i class="fas fa-edit"></i> Edit
            </a>
            @endcan
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Basic Information Card -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                @if($financialAssistance->reference_no)
                                <tr>
                                    <th><i class="fas fa-barcode text-muted"></i> Reference Number</th>
                                    <td><strong class="text-primary">{{ $financialAssistance->reference_no }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <th style="width: 200px;"><i class="fas fa-user text-muted"></i> Claimant Name</th>
                                    <td><strong>{{ $financialAssistance->claimant_name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th style="width: 200px;"><i class="fas fa-phone text-muted"></i> Claimant Contact No</th>
                                    <td><strong>{{ $financialAssistance->claimant_contact_no ?? 'N/A' }}</strong></td>
                                </tr>

                                <tr>
                                    <th><i class="fas fa-calendar text-muted"></i> {{ trans('cruds.financialAssistance.fields.date_interviewed') }}</th>
                                    <td>{{ $financialAssistance->date_interviewed ? \Carbon\Carbon::parse($financialAssistance->date_interviewed)->format('F d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-money-bill-wave text-muted"></i> {{ trans('cruds.financialAssistance.fields.amount') }}</th>
                                    <td><strong class="text-success">₱{{ number_format($financialAssistance->amount ?? 0, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-clock text-muted"></i> {{ trans('cruds.financialAssistance.fields.scheduled_fa') }}</th>
                                    <td>{{ $financialAssistance->scheduled_fa ? \Carbon\Carbon::parse($financialAssistance->scheduled_fa)->format('F d, Y g:i A') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="fas fa-flag text-muted"></i> {{ trans('cruds.financialAssistance.fields.status') }}</th>
                                    <td>
                                        @php
                                        $statusColors = [
                                        'Ongoing' => 'warning',
                                        'Pending' => 'info',
                                        'Claimed' => 'success',
                                        'Cancelled' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$financialAssistance->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-{{ $statusColor }} px-3 py-2">{{ $financialAssistance->status }}</span>
                                    </td>
                                </tr>
                                @if($financialAssistance->date_claimed)
                                <tr>
                                    <th><i class="fas fa-check-circle text-muted"></i> {{ trans('cruds.financialAssistance.fields.date_claimed') }}</th>
                                    <td class="text-success">
                                        <strong>{{ \Carbon\Carbon::parse($financialAssistance->date_claimed)->format('F d, Y g:i A') }}</strong>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Details Card -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-file-alt text-primary"></i> Assistance Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-exclamation-triangle"></i> {{ trans('cruds.financialAssistance.fields.problem_presented') }}
                            </label>
                            <p class="border-left border-warning pl-3">{{ $financialAssistance->problem_presented ?: 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-clipboard-check"></i> {{ trans('cruds.financialAssistance.fields.assessment') }}
                            </label>
                            <p class="border-left border-info pl-3">{{ $financialAssistance->assessment ?: 'N/A' }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-lightbulb"></i> {{ trans('cruds.financialAssistance.fields.recommendation') }}
                            </label>
                            <p class="border-left border-success pl-3">{{ $financialAssistance->recommendation ?: 'N/A' }}</p>
                        </div>

                        @if($financialAssistance->note)
                        <div class="mb-0">
                            <label class="font-weight-bold text-muted">
                                <i class="fas fa-sticky-note"></i> {{ trans('cruds.financialAssistance.fields.note') }}
                            </label>
                            <p class="border-left border-secondary pl-3">{{ $financialAssistance->note }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Requirements Card -->
                @if($financialAssistance->requirements->count() > 0)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-paperclip text-primary"></i> {{ trans('cruds.financialAssistance.fields.requirements') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($financialAssistance->requirements as $key => $media)
                            <a href="{{ $media->getUrl() }}" target="_blank" class="list-group-item list-group-item-action">
                                <i class="fas fa-file-download text-primary"></i>
                                {{ trans('global.view_file') }} {{ $key + 1 }}
                                <i class="fas fa-external-link-alt text-muted float-right mt-1"></i>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-md-4">
                @if($financialAssistance->qr_code)
                <!-- QR Code Card -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-qrcode text-primary"></i> QR Code</h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="p-3 bg-white border rounded mb-3" style="display: inline-block;">
                            <img src="{{ $financialAssistance->getFirstMediaUrl('qr_code') }}"
                                alt="QR Code"
                                class="img-fluid"
                                style="max-width: 250px;">
                        </div>

                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle"></i>
                            Scan this QR code to view payout details
                        </p>

                        @if($financialAssistance->qr_token)
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.financial-assistances.printQrCode', $financialAssistance->id) }}"
                                target="_blank"
                                class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-print"></i> Print QR Code
                            </a>
                            <a href="{{ route('payout.verify', ['qr_token' => $financialAssistance->qr_token]) }}"
                                target="_blank"
                                class="btn btn-info btn-block">
                                <i class="fas fa-external-link-alt"></i> Preview Verification Page
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 8px;
        border: none;
    }

    .card-header {
        border-radius: 8px 8px 0 0 !important;
        border-bottom: 2px solid rgba(0, 0, 0, .125);
    }

    .table-borderless th {
        font-weight: 600;
        color: #495057;
        padding: 0.75rem 0.5rem;
    }

    .table-borderless td {
        padding: 0.75rem 0.5rem;
    }

    .table-borderless tr {
        border-bottom: 1px solid #f0f0f0;
    }

    .table-borderless tr:last-child {
        border-bottom: none;
    }

    .badge {
        font-size: 0.875rem;
        font-weight: 500;
    }

    .border-left {
        border-left-width: 3px !important;
    }

    .list-group-item {
        border-radius: 6px !important;
        margin-bottom: 0.5rem;
    }

    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    }
</style>

@endsection