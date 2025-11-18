<?php

namespace App\Http\Requests;

use App\Models\Familycomposition;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFamilycompositionRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('familycomposition_create');
    }

    public function rules()
    {
        return [
            'family_name'           => ['array', 'max:6'],
            'family_name.*'         => ['nullable', 'string', 'max:255'],

            'family_birthday'       => ['array', 'max:6'],
            // Choose ONE of these lines for date validation:
            // EITHER allow any parsable date:
            'family_birthday.*'     => ['nullable', 'date'],
            // OR strict HTML5 date input (yyyy-mm-dd):
            // 'family_birthday.*'  => ['nullable','date_format:Y-m-d'],

            'family_relationship'   => ['array', 'max:6'],
            'family_relationship.*' => ['nullable', 'string', 'max:50'],

            'family_civil_status'   => ['array', 'max:6'],
            'family_civil_status.*' => ['nullable', 'string', 'max:50'],

            'family_highest_edu'    => ['array', 'max:6'],
            'family_highest_edu.*'  => ['nullable', 'string', 'max:100'],

            'family_occupation'      => ['array', 'max:6'],
            'family_occupation.*'    => ['nullable', 'string', 'max:255'],

            'family_remarks'         => ['array', 'max:6'],
            'family_remarks.*'       => ['nullable', 'string', 'max:255'],

            'family_others'          => ['array', 'max:6'],
            'family_others.*'        => ['nullable', 'string', 'max:255'],

        ];
    }
}
