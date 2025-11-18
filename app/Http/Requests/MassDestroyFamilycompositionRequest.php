<?php

namespace App\Http\Requests;

use App\Models\Familycomposition;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyFamilycompositionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('familycomposition_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:familycompositions,id',
        ];
    }
}
