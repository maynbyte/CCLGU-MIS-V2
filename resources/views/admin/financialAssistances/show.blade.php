@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.financialAssistance.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.financial-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.id') }}
                        </th>
                        <td>
                            {{ $financialAssistance->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.problem_presented') }}
                        </th>
                        <td>
                            {{ $financialAssistance->problem_presented }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.date_interviewed') }}
                        </th>
                        <td>
                            {{ $financialAssistance->date_interviewed }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.assessment') }}
                        </th>
                        <td>
                            {{ $financialAssistance->assessment }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.recommendation') }}
                        </th>
                        <td>
                            {{ $financialAssistance->recommendation }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.amount') }}
                        </th>
                        <td>
                            {{ $financialAssistance->amount }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.scheduled_fa') }}
                        </th>
                        <td>
                            {{ $financialAssistance->scheduled_fa }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.status') }}
                        </th>
                        <td>
                            {{ $financialAssistance->status }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.date_claimed') }}
                        </th>
                        <td>
                            {{ $financialAssistance->date_claimed }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.note') }}
                        </th>
                        <td>
                            {{ $financialAssistance->note }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.financialAssistance.fields.requirements') }}
                        </th>
                        <td>
                            @foreach($financialAssistance->requirements as $key => $media)
                                <a href="{{ $media->getUrl() }}" target="_blank">
                                    {{ trans('global.view_file') }}
                                </a>
                            @endforeach
                        </td>
                    </tr>
                    @if($financialAssistance->reference_no)
                    <tr>
                        <th>
                            Reference Number
                        </th>
                        <td>
                            <strong>{{ $financialAssistance->reference_no }}</strong>
                        </td>
                    </tr>
                    @endif
                    @if($financialAssistance->qr_code)
                    <tr>
                        <th>
                            QR Code
                        </th>
                        <td>
                            <div class="mb-2">
                                <img src="{{ $financialAssistance->getFirstMediaUrl('qr_code') }}" 
                                     alt="QR Code" 
                                     style="max-width: 300px; border: 1px solid #ddd; padding: 10px; background: white;">
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Scan this QR code to view payout details
                            </small>
                            @if($financialAssistance->qr_token)
                            <div class="mt-2">
                                <a href="{{ route('admin.financial-assistances.printQrCode', $financialAssistance->id) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-warning mr-2">
                                    <i class="fas fa-qrcode"></i> Print QR Code
                                </a>
                                <a href="{{ route('payout.verify', ['qr_token' => $financialAssistance->qr_token]) }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-external-link-alt"></i> Preview Verification Page
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.financial-assistances.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection