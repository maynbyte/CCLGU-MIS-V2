<?php

namespace App\Http\Requests;

use App\Models\Solicitation;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreSolicitationRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('solicitation_create');
    }

    public function rules()
    {
        return [
            'solicitation' => [
                'string',
                'nullable',
            ],
        ];
    }
}
