<?php

namespace App\Http\Requests;

use App\Models\Barangay;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateBarangayRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('barangay_edit');
    }

    public function rules()
    {
        return [
            'barangay_name' => [
                'string',
                'required',
            ],
            'barangay' => [
                'string',
                'nullable',
            ],
            'barangay_chairman' => [
                'string',
                'nullable',
            ],
            'sk_chairman' => [
                'string',
                'nullable',
            ],
            'total_no_of_voters' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
