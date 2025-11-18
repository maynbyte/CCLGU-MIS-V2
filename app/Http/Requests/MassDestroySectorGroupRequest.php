<?php

namespace App\Http\Requests;

use App\Models\SectorGroup;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroySectorGroupRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('sector_group_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:sector_groups,id',
        ];
    }
}
