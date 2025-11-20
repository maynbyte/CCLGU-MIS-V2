<?php

namespace App\Http\Requests;

use App\Models\Directory;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDirectoryRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('directory_edit');
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
            'remarks' => [
                'string',
                'nullable',
            ],

            'barangay_id'    => ['nullable', 'integer', 'exists:barangays,id'],
            'barangay_other' => ['required_without:barangay_id', 'nullable', 'string', 'max:191'],
        ];
    }
}
