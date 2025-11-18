<?php

namespace App\Http\Requests;

use App\Models\GuaranteeLetter;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateGuaranteeLetterRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('guarantee_letter_edit');
    }

    public function rules()
    {
        return [
            'directory' => [
                'string',
                'nullable',
            ],
        ];
    }
}
