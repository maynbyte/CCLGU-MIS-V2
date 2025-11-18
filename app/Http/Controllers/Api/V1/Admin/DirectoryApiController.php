<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreDirectoryRequest;
use App\Http\Requests\UpdateDirectoryRequest;
use App\Http\Resources\Admin\DirectoryResource;
use App\Models\Directory;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectoryApiController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('directory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DirectoryResource(Directory::with(['barangay', 'ngos', 'sectors'])->get());
    }

    public function store(StoreDirectoryRequest $request)
    {
        $directory = Directory::create($request->all());
        $directory->ngos()->sync($request->input('ngos', []));
        $directory->sectors()->sync($request->input('sectors', []));
        if ($request->input('profile_picture', false)) {
            $directory->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))->toMediaCollection('profile_picture');
        }

        return (new DirectoryResource($directory))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Directory $directory)
    {
        abort_if(Gate::denies('directory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DirectoryResource($directory->load(['barangay', 'ngos', 'sectors']));
    }

    public function update(UpdateDirectoryRequest $request, Directory $directory)
    {
        $directory->update($request->all());
        $directory->ngos()->sync($request->input('ngos', []));
        $directory->sectors()->sync($request->input('sectors', []));
        if ($request->input('profile_picture', false)) {
            if (! $directory->profile_picture || $request->input('profile_picture') !== $directory->profile_picture->file_name) {
                if ($directory->profile_picture) {
                    $directory->profile_picture->delete();
                }
                $directory->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))->toMediaCollection('profile_picture');
            }
        } elseif ($directory->profile_picture) {
            $directory->profile_picture->delete();
        }

        return (new DirectoryResource($directory))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Directory $directory)
    {
        abort_if(Gate::denies('directory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $directory->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
