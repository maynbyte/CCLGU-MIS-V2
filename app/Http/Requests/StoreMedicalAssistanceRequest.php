<?php

namespace App\Http\Requests;

use App\Models\MedicalAssistance;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreMedicalAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('medical_assistance_create');
    }

    public function rules()
    {
        return [
            'medical_assistance' => [
                'string',
                'nullable',
            ],
        ];
    }
}
