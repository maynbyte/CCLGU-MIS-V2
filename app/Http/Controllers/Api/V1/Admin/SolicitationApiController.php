<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSolicitationRequest;
use App\Http\Requests\UpdateSolicitationRequest;
use App\Http\Resources\Admin\SolicitationResource;
use App\Models\Solicitation;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SolicitationApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('solicitation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SolicitationResource(Solicitation::all());
    }

    public function store(StoreSolicitationRequest $request)
    {
        $solicitation = Solicitation::create($request->all());

        return (new SolicitationResource($solicitation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Solicitation $solicitation)
    {
        abort_if(Gate::denies('solicitation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SolicitationResource($solicitation);
    }

    public function update(UpdateSolicitationRequest $request, Solicitation $solicitation)
    {
        $solicitation->update($request->all());

        return (new SolicitationResource($solicitation))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Solicitation $solicitation)
    {
        abort_if(Gate::denies('solicitation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $solicitation->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
