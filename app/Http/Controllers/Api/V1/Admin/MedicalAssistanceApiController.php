<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalAssistanceRequest;
use App\Http\Requests\UpdateMedicalAssistanceRequest;
use App\Http\Resources\Admin\MedicalAssistanceResource;
use App\Models\MedicalAssistance;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MedicalAssistanceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('medical_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MedicalAssistanceResource(MedicalAssistance::all());
    }

    public function store(StoreMedicalAssistanceRequest $request)
    {
        $medicalAssistance = MedicalAssistance::create($request->all());

        return (new MedicalAssistanceResource($medicalAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(MedicalAssistance $medicalAssistance)
    {
        abort_if(Gate::denies('medical_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MedicalAssistanceResource($medicalAssistance);
    }

    public function update(UpdateMedicalAssistanceRequest $request, MedicalAssistance $medicalAssistance)
    {
        $medicalAssistance->update($request->all());

        return (new MedicalAssistanceResource($medicalAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(MedicalAssistance $medicalAssistance)
    {
        abort_if(Gate::denies('medical_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicalAssistance->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
