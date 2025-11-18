<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuaranteeLetterRequest;
use App\Http\Requests\UpdateGuaranteeLetterRequest;
use App\Http\Resources\Admin\GuaranteeLetterResource;
use App\Models\GuaranteeLetter;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuaranteeLetterApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('guarantee_letter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GuaranteeLetterResource(GuaranteeLetter::all());
    }

    public function store(StoreGuaranteeLetterRequest $request)
    {
        $guaranteeLetter = GuaranteeLetter::create($request->all());

        return (new GuaranteeLetterResource($guaranteeLetter))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(GuaranteeLetter $guaranteeLetter)
    {
        abort_if(Gate::denies('guarantee_letter_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new GuaranteeLetterResource($guaranteeLetter);
    }

    public function update(UpdateGuaranteeLetterRequest $request, GuaranteeLetter $guaranteeLetter)
    {
        $guaranteeLetter->update($request->all());

        return (new GuaranteeLetterResource($guaranteeLetter))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(GuaranteeLetter $guaranteeLetter)
    {
        abort_if(Gate::denies('guarantee_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $guaranteeLetter->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
