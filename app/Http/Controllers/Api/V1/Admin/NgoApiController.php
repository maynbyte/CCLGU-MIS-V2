<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNgoRequest;
use App\Http\Requests\UpdateNgoRequest;
use App\Http\Resources\Admin\NgoResource;
use App\Models\Ngo;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NgoApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('ngo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new NgoResource(Ngo::all());
    }

    public function store(StoreNgoRequest $request)
    {
        $ngo = Ngo::create($request->all());

        return (new NgoResource($ngo))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Ngo $ngo)
    {
        abort_if(Gate::denies('ngo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new NgoResource($ngo);
    }

    public function update(UpdateNgoRequest $request, Ngo $ngo)
    {
        $ngo->update($request->all());

        return (new NgoResource($ngo))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Ngo $ngo)
    {
        abort_if(Gate::denies('ngo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ngo->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
