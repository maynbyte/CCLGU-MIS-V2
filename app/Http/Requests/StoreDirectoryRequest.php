<?php

namespace App\Http\Requests;

use App\Models\Directory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDirectoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('directory_create');
    }

    // app/Http/Requests/StoreDirectoryRequest.php (and UpdateDirectoryRequest.php)
    public function prepareForValidation(): void
    {
        if ($this->input('barangay_id') === 'other') {
            $this->merge(['barangay_id' => null]);
        }
    }



    public function rules()
    {
        return [
            'last_name' => [
                'string',
                'required',
            ],
            'first_name' => [
                'string',
                'required',
            ],
            'middle_name' => [
                'string',
                'nullable',
            ],
            'suffix' => [
                'string',
                'nullable',
            ],
            'email' => [
                'string',
                'nullable',
            ],
            'contact_no' => [
                'string',
                'nullable',
            ],
            'birthday' => [
                'date_format:' . config('panel.date_format'),
                'nullable',
            ],
            'place_of_birth' => [
                'string',
                'nullable',
            ],
            'nationality' => [
                'string',
                'nullable',
            ],
            'street_no' => [
                'string',
                'nullable',
            ],
            'street' => [
                'string',
                'nullable',
            ],
            'city' => [
                'string',
                'nullable',
            ],
            'province' => [
                'string',
                'nullable',
            ],
            'ngos.*' => [
                'integer',
            ],
            'ngos' => [
                'array',
            ],
            'sectors.*' => [
                'integer',
            ],
            'sectors' => [
                'array',
            ],
            'comelec_status' => [
                'string',
                'nullable',
            ],
            'maiden_surname' =>
            ['nullable', 'string', 'max:191'],
            'remarks' => [
                'string',
                'nullable',
            ],
            'barangay_id'    => ['nullable', 'integer', 'exists:barangays,id'],
            'barangay_other' => ['required_without:barangay_id', 'nullable', 'string', 'max:191'],

            // child rows
            'family_name'           => ['array', 'max:6'],
            'family_name.*'         => ['nullable', 'string', 'max:255'],
            'family_birthday'       => ['array'],
            'family_birthday.*'     => ['nullable', 'date'],
            'family_relationship'   => ['array'],
            'family_relationship.*' => ['nullable', 'string', 'max:100'],
            'family_civil_status'   => ['array'],
            'family_civil_status.*' => ['nullable', 'string', 'max:100'],
            'family_highest_edu'    => ['array'],
            'family_highest_edu.*'  => ['nullable', 'string', 'max:100'],
            'family_occupation'       => ['array'],
            'family_occupation.*'     => ['nullable', 'string', 'max:255'],
            'family_remarks'          => ['array'],
            'family_remarks.*'        => ['nullable', 'string', 'max:255'],
            'family_others'           => ['array'],
            'family_others.*'         => ['nullable', 'string', 'max:255'],
        ];
    }
}
