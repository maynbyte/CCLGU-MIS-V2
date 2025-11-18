<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Http\Requests\UpdateBurialAssistanceRequest;
use App\Http\Resources\Admin\BurialAssistanceResource;
use App\Models\BurialAssistance;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BurialAssistanceApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('burial_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BurialAssistanceResource(BurialAssistance::all());
    }

    public function store(StoreBurialAssistanceRequest $request)
    {
        $burialAssistance = BurialAssistance::create($request->all());

        return (new BurialAssistanceResource($burialAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(BurialAssistance $burialAssistance)
    {
        abort_if(Gate::denies('burial_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BurialAssistanceResource($burialAssistance);
    }

    public function update(UpdateBurialAssistanceRequest $request, BurialAssistance $burialAssistance)
    {
        $burialAssistance->update($request->all());

        return (new BurialAssistanceResource($burialAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(BurialAssistance $burialAssistance)
    {
        abort_if(Gate::denies('burial_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $burialAssistance->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
