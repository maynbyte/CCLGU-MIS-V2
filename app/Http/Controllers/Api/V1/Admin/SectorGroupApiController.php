<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSectorGroupRequest;
use App\Http\Requests\UpdateSectorGroupRequest;
use App\Http\Resources\Admin\SectorGroupResource;
use App\Models\SectorGroup;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SectorGroupApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('sector_group_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SectorGroupResource(SectorGroup::all());
    }

    public function store(StoreSectorGroupRequest $request)
    {
        $sectorGroup = SectorGroup::create($request->all());

        return (new SectorGroupResource($sectorGroup))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SectorGroup $sectorGroup)
    {
        abort_if(Gate::denies('sector_group_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SectorGroupResource($sectorGroup);
    }

    public function update(UpdateSectorGroupRequest $request, SectorGroup $sectorGroup)
    {
        $sectorGroup->update($request->all());

        return (new SectorGroupResource($sectorGroup))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SectorGroup $sectorGroup)
    {
        abort_if(Gate::denies('sector_group_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sectorGroup->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
