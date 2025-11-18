<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreFinancialAssistanceRequest;
use App\Http\Requests\UpdateFinancialAssistanceRequest;
use App\Http\Resources\Admin\FinancialAssistanceResource;
use App\Models\FinancialAssistance;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinancialAssistanceApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('financial_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FinancialAssistanceResource(FinancialAssistance::all());
    }

    public function store(StoreFinancialAssistanceRequest $request)
    {
        $financialAssistance = FinancialAssistance::create($request->all());

        foreach ($request->input('requirements', []) as $file) {
            $financialAssistance->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('requirements');
        }

        return (new FinancialAssistanceResource($financialAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FinancialAssistanceResource($financialAssistance);
    }

    public function update(UpdateFinancialAssistanceRequest $request, FinancialAssistance $financialAssistance)
    {
        $financialAssistance->update($request->all());

        if (count($financialAssistance->requirements) > 0) {
            foreach ($financialAssistance->requirements as $media) {
                if (! in_array($media->file_name, $request->input('requirements', []))) {
                    $media->delete();
                }
            }
        }
        $media = $financialAssistance->requirements->pluck('file_name')->toArray();
        foreach ($request->input('requirements', []) as $file) {
            if (count($media) === 0 || ! in_array($file, $media)) {
                $financialAssistance->addMedia(storage_path('tmp/uploads/' . basename($file)))->toMediaCollection('requirements');
            }
        }

        return (new FinancialAssistanceResource($financialAssistance))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $financialAssistance->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
