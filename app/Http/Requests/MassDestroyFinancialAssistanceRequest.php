<?php

namespace App\Http\Requests;

use App\Models\FinancialAssistance;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFinancialAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('financial_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:financial_assistances,id',
        ];
    }
}
