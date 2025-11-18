<?php

namespace App\Http\Requests;

use App\Models\Barangay;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyBarangayRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('barangay_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:barangays,id',
        ];
    }
}
