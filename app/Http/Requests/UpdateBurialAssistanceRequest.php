<?php

namespace App\Http\Requests;

use App\Models\BurialAssistance;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBurialAssistanceRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('burial_assistance_edit');
    }

    public function rules()
    {
        return [
            'burial_assitance' => [
                'string',
                'nullable',
            ],
        ];
    }
}
