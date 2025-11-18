<?php

namespace App\Http\Requests;

use App\Models\MedicalAssistance;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateMedicalAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('medical_assistance_edit');
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
