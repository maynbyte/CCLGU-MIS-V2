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


        // If you're fetching per Directory:
        $list = \App\Models\FinancialAssistance::query()
            ->where('directory_id', $directory->id)
            ->orderByRaw('date_claimed IS NULL')   // puts NULLs last
            ->orderByDesc('date_claimed')          // newest first
            ->get();

        // OR via relationship:
        $directory->load(['financialAssistances' => function ($q) {
            $q->orderByRaw('date_claimed IS NULL')->orderByDesc('date_claimed');
        }]);
        $list = $directory->financialAssistances;
    }


    public function store(StoreFinancialAssistanceRequest $request)
    {
        abort_if(Gate::denies('financial_assistance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $directory = Directory::findOrFail($request->input('directory_id'));

        // Ensure default status 'Ongoing' if none supplied
        $payload = $request->except(['directory_id', 'requirements', 'ck-media']);
        if (empty($payload['status'])) {
            $payload['status'] = 'Ongoing';
        }
        // Create via parent relation (sets directory_id automatically)
        $fa = $directory->financialAssistances()->create(
            $payload + ['user' => auth()->id()]
        );

        // Attach uploaded requirement files (Dropzone tmp -> media library)
        foreach ($request->input('requirements', []) as $file) {
            $fa->addMedia(storage_path('tmp/uploads/' . basename($file)))
                ->toMediaCollection('requirements');
        }

        // CKEditor media, if any
        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $fa->id]);
        }

        $data = $request->validate([
            // your rules...
            'directory_id' => ['required', 'exists:directories,id'],
        ]);

        $fa = \App\Models\FinancialAssistance::create($data);

        return redirect()->to(
            route('admin.financial-assistances.create', ['directory_id' => $fa->directory_id]) . '#tab-general'
        )->with('message', 'Financial assistance created ✅');
    }

    public function update(UpdateFinancialAssistanceRequest $request, FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Update FA (keep directory_id if you don’t allow reassignment)
        $updateData = $request->except(['requirements', 'ck-media']);
        if (empty($updateData['status'])) {
            $updateData['status'] = 'Ongoing';
        }
        $financialAssistance->update($updateData);

        // --- Media sync (kept from your working code) ---
        if (count($financialAssistance->requirements) > 0) {
            foreach ($financialAssistance->requirements as $media) {
                if (! in_array($media->file_name, $request->input('requirements', []))) {
                    $media->delete();
                }
            }
        }
        $existing = $financialAssistance->requirements->pluck('file_name')->toArray();
        foreach ($request->input('requirements', []) as $file) {
            if (count($existing) === 0 || ! in_array($file, $existing)) {
                $financialAssistance
                    ->addMedia(storage_path('tmp/uploads/' . basename($file)))
                    ->toMediaCollection('requirements');
            }
        }
        // --- end media sync ---

        // After editing, redirect to create form (with same directory context) as requested
        return redirect()->to(
            route('admin.financial-assistances.create', ['directory_id' => $financialAssistance->directory_id])
        )->with('message', 'Financial assistance updated ✅ Redirected to create new record.');
    }




    public function edit(FinancialAssistance $financialAssistance)
    {
        abort_if(Gate::denies('financial_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Load relations the Blade uses (parent directory + media for Dropzone preload)
        $financialAssistance->load('directory', 'media');

        // The edit.blade expects $fa and $directory (and sometimes $financialAssistance)
        return view('admin.financialAssistances.edit', [
            'fa'                   => $financialAssistance,
            'directory'            => $financialAssistance->directory,
            'financialAssistance'  => $financialAssistance, // keep this too, in case the view references it
        ]);
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

    public function financialAssistances()
    {
        return $this->hasMany(\App\Models\FinancialAssistance::class, 'directory_id');
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

    public function printCaseSummary(\App\Models\FinancialAssistance $financialAssistance)
    {
        abort_if(\Gate::denies('financial_assistance_show'), \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Eager load what we need
        $financialAssistance->load([
            'directory.barangay',
            'directory.familycompositions',
            'directory.sectors',   // if you have a sectors relation
            'addedBy:id,name'      // if you show "added by" elsewhere
        ]);

        return view('admin.financialAssistances.print-case-summary', compact('financialAssistance'));
    }
}
