<?php

namespace App\Http\Requests;

use App\Models\SectorGroup;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateSectorGroupRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('sector_group_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
