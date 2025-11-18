<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBarangayRequest;
use App\Http\Requests\UpdateBarangayRequest;
use App\Http\Resources\Admin\BarangayResource;
use App\Models\Barangay;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarangayApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('barangay_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BarangayResource(Barangay::all());
    }

    public function store(StoreBarangayRequest $request)
    {
        $barangay = Barangay::create($request->all());

        return (new BarangayResource($barangay))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Barangay $barangay)
    {
        abort_if(Gate::denies('barangay_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new BarangayResource($barangay);
    }

    public function update(UpdateBarangayRequest $request, Barangay $barangay)
    {
        $barangay->update($request->all());

        return (new BarangayResource($barangay))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Barangay $barangay)
    {
        abort_if(Gate::denies('barangay_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barangay->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
