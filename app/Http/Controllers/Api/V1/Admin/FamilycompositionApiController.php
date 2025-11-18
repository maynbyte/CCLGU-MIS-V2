<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFamilycompositionRequest;
use App\Http\Requests\UpdateFamilycompositionRequest;
use App\Http\Resources\Admin\FamilycompositionResource;
use App\Models\Familycomposition;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FamilycompositionApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('familycomposition_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FamilycompositionResource(Familycomposition::all());
    }

    public function store(StoreFamilycompositionRequest $request)
    {
        $familycomposition = Familycomposition::create($request->all());

        return (new FamilycompositionResource($familycomposition))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Familycomposition $familycomposition)
    {
        abort_if(Gate::denies('familycomposition_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FamilycompositionResource($familycomposition);
    }

    public function update(UpdateFamilycompositionRequest $request, Familycomposition $familycomposition)
    {
        $familycomposition->update($request->all());

        return (new FamilycompositionResource($familycomposition))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Familycomposition $familycomposition)
    {
        abort_if(Gate::denies('familycomposition_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $familycomposition->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
