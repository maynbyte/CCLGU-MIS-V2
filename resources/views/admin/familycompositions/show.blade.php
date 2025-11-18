@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.familycomposition.title') }}
    </div>

    <div class="card-body">
        <div class="form-group">
            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.familycompositions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        <table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Names</th>
            <th>Birthday</th>
            <th>Relationship</th>
            <th>Civil Status</th>
            <th>Highest Education</th>
            <th>Occupation / Remarks</th>
        </tr>
    </thead>
    <tbody>
        @php
            $rows = max(
                count($familycomposition->family_name ?? []),
                count($familycomposition->family_birthday ?? []),
                count($familycomposition->family_relationship ?? []),
                count($familycomposition->family_civil_status ?? []),
                count($familycomposition->family_highest_edu ?? []),
                count($familycomposition->occupation ?? []),
                count($familycomposition->remarks ?? [])
            );
        @endphp

        @for ($i = 0; $i < $rows; $i++)
            <tr>
                <td>{{ $familycomposition->family_name[$i] ?? '' }}</td>
                <td>{{ isset($familycomposition->family_birthday[$i]) ? \Carbon\Carbon::parse($familycomposition->family_birthday[$i])->format('m/d/Y') : '' }}</td>
                <td>{{ $familycomposition->family_relationship[$i] ?? '' }}</td>
                <td>{{ $familycomposition->family_civil_status[$i] ?? '' }}</td>
                <td>{{ $familycomposition->family_highest_edu[$i] ?? '' }}</td>
                <td>
                    {{ $familycomposition->occupation[$i] ?? '' }}
                    @php $rem = $familycomposition->remarks[$i] ?? null; @endphp
                    @if($rem) <small class="text-muted d-block">{{ $rem }}</small> @endif
                </td>
            </tr>
        @endfor
    </tbody>
</table>

            <div class="form-group">
                <a class="btn btn-default" href="{{ route('admin.familycompositions.index') }}">
                    {{ trans('global.back_to_list') }}
                </a>
            </div>
        </div>
    </div>
</div>



@endsection