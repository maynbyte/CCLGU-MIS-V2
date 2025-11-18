<?php

namespace App\Http\Requests;

use App\Models\GuaranteeLetter;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyGuaranteeLetterRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('guarantee_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:guarantee_letters,id',
        ];
    }
}
