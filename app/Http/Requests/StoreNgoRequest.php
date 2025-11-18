<?php

namespace App\Http\Requests;

use App\Models\Ngo;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreNgoRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('ngo_create');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
            'contact_person' => [
                'string',
                'nullable',
            ],
            'contact_no' => [
                'string',
                'nullable',
            ],
            'description' => [
                'string',
                'nullable',
            ],
            'total_members' => [
                'string',
                'nullable',
            ],
        ];
    }
}
