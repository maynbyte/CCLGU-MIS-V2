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