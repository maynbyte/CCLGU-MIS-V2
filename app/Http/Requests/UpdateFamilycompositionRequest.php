<?php

namespace App\Http\Requests;

use App\Models\Familycomposition;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateFamilycompositionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('familycomposition_edit');
    }

    public function rules()
    {
        return [
            'family_name' => [
                'string',
                'nullable',
            ],
            'family_birthday' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'occupation' => [
                'string',
                'nullable',
            ],
            'remarks' => [
                'string',
                'nullable',
            ],
            'others' => [
                'string',
                'nullable',
            ],
        ];
    }
}
