<?php

namespace App\Http\Requests;

use App\Models\FinancialAssistance;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFinancialAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('financial_assistance_create');
    }

    public function rules()
    {
        return [
            'problem_presented' => [
                'string',
                'nullable',
            ],
            'date_interviewed' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'assessment' => [
                'string',
                'nullable',
            ],
            'recommendation' => [
                'string',
                'nullable',
            ],
            'amount' => [
                'string',
                'nullable',
            ],
            'scheduled_fa' => [
                'string',
                'nullable',
            ],
            'status' => [
                'string',
                'nullable',
            ],
            'date_claimed' => [
                'string',
                'nullable',
            ],
            'note' => [
                'string',
                'nullable',
            ],
            'requirements' => [
                'array',
            ],
            'directory_id' => [
                'required', 'exists:directories,id'],
        ];
    }
}
