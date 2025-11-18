<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyFinancialAssistanceRequest;
use App\Http\Requests\StoreFinancialAssistanceRequest;
use App\Http\Requests\UpdateFinancialAssistanceRequest;
use App\Models\FinancialAssistance;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Directory;



class FinancialAssistanceController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('financial_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = FinancialAssistance::query()->select(sprintf('%s.*', (new FinancialAssistance)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'financial_assistance_show';
                $editGate      = 'financial_assistance_edit';
                $deleteGate    = 'financial_assistance_delete';
                $crudRoutePart = 'financial-assistances';

                return view('partials.datatablesActions', compact(
                    'viewGate',
                    'editGate',
                    'deleteGate',
                    'crudRoutePart',
                    'row'
                ));
            });

            $table->editColumn('id', function ($row) {
                return $row->id ? $row->id : '';
            });
            $table->editColumn('directory', function ($row) {
                return $row->directory ? $row->directory : '';
            });
            $table->editColumn('problem_presented', function ($row) {
                return $row->problem_presented ? $row->problem_presented : '';
            });

            $table->editColumn('assessment', function ($row) {
                return $row->assessment ? $row->assessment : '';
            });
            $table->editColumn('recommendation', function ($row) {
                return $row->recommendation ? $row->recommendation : '';
            });
            $table->editColumn('amount', function ($row) {
                return $row->amount ? $row->amount : '';
            });
            $table->editColumn('scheduled_fa', function ($row) {
                return $row->scheduled_fa ? $row->scheduled_fa : '';
            });
            $table->editColumn('status', function ($row) {
                return $row->status ? $row->status : '';
            });
            $table->editColumn('date_claimed', function ($row) {
                return $row->date_claimed ? $row->date_claimed : '';
            });
            $table->editColumn('note', function ($row) {
                return $row->note ? $row->note : '';
            });
            $table->editColumn('requirements', function ($row) {
                if (! $row->requirements) {
                    return '';
                }
                $links = [];
                foreach ($row->requirements as $media) {
                    $links[] = '<a href="' . $media->getUrl() . '" target="_blank">' . trans('global.downloadFile') . '</a>';
                }

                return implode(', ', $links);
            });

            $table->rawColumns(['actions', 'placeholder', 'requirements']);

            return $table->make(true);
        }

        return view('admin.financialAssistances.index');
    }

  /*  public function create()
    {
        abort_if(Gate::denies('financial_assistance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.financialAssistances.create');
    }*/


    public function create(Request $request)
    {
        abort_if(Gate::denies('financial_assistance_create'), 403);

        $directory = null;

        // If you call /admin/financial-assistances/create?directory_id=123
        if ($request->filled('directory_id')) {
            $directory = Directory::find($request->input('directory_id'));
        }

        // If you later add a nested route like /admin/directories/{directory}/financial-assistances/create
        if (!$directory && $request->route('directory')) {
            $directory = $request->route('directory');
        }

        // OPTIONAL: If you want to show a dropdown when no directory is preselected:
        // $directories = Directory::orderBy('last_name')->get()
        //     ->mapWithKeys(fn($d) => [$d->id => trim(($d->first_name.' '.$d->last_name) ?: 'Directory #'.$d->id)]);

        return view('admin.financialAssistances.create', [
            'directory' => $directory,
            // 'directories' => $directories, // uncomment if you use the dropdown below
        ]);
    }


   public function store(StoreFinancialAssistanceRequest $request)
{
    abort_if(Gate::denies('financial_assistance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

    // Make sure the directory exists (your FormRequest should already validate this)
    $directory = Directory::findOrFail($request->input('directory_id'));

    // Create through the relationship so directory_id is set automatically
    $fa = $directory->financialAssistances()->create(
        $request->except(['directory_id', 'requirements', 'ck-media'])
    );

    // Requirements (Spatie Media Library)
    foreach ($request->input('requirements', []) as $file) {
        $fa->addMedia(storage_path('tmp/uploads/' . basename($file)))
           ->toMediaCollection('requirements');
    }

    // CKEditor media (if any)
    if ($media = $request->input('ck-media', false)) {
        Media::whereIn('id', $media)->update(['model_id' => $fa->id]);
    }

    // Redirect to the Directory show page
    return redirect()
        ->route('admin.directories.show', $directory->id)
        ->with('message', 'Financial assistance created and linked to the directory ✅');
}


    public function edit(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.financialAssistances.edit', compact('financialAssistance'));
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

        return redirect()->route('admin.financial-assistances.index');
    }

    public function show(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.financialAssistances.show', compact('financialAssistance'));
    }

    public function destroy(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $financialAssistance->delete();

        return back();
    }

    public function massDestroy(MassDestroyFinancialAssistanceRequest $request)
    {
        $financialAssistances = FinancialAssistance::find(request('ids'));

        foreach ($financialAssistances as $financialAssistance) {
            $financialAssistance->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('financial_assistance_create') && Gate::denies('financial_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new FinancialAssistance();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }

    
}
