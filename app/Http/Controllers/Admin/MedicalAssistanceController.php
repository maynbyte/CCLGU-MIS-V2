<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyMedicalAssistanceRequest;
use App\Http\Requests\StoreMedicalAssistanceRequest;
use App\Http\Requests\UpdateMedicalAssistanceRequest;
use App\Models\MedicalAssistance;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class MedicalAssistanceController extends Controller
{
    use CsvImportTrait;

    /**
     * Display a listing of medical assistance records. Handles DataTables AJAX requests.
     */
    public function index(Request $request)
    {
        abort_if(Gate::denies('medical_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = MedicalAssistance::query()->select(sprintf('%s.*', (new MedicalAssistance)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'medical_assistance_show';
                $editGate      = 'medical_assistance_edit';
                $deleteGate    = 'medical_assistance_delete';
                $crudRoutePart = 'medical-assistances';

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
            $table->editColumn('medical_assistance', function ($row) {
                return $row->medical_assistance ? $row->medical_assistance : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.medicalAssistances.index');
    }

    /**
     * Show form for creating a new medical assistance record.
     */
    public function create()
    {
        abort_if(Gate::denies('medical_assistance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.medicalAssistances.create');
    }

    /**
     * Store a newly created medical assistance record.
     */
    public function store(StoreMedicalAssistanceRequest $request)
    {
        $medicalAssistance = MedicalAssistance::create($request->all());

        return redirect()->route('admin.medical-assistances.index');
    }

    /**
     * Show form for editing an existing medical assistance record.
     */
    public function edit(MedicalAssistance $medicalAssistance)
    {
        abort_if(Gate::denies('medical_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.medicalAssistances.edit', compact('medicalAssistance'));
    }

    /**
     * Update the specified medical assistance record.
     */
    public function update(UpdateMedicalAssistanceRequest $request, MedicalAssistance $medicalAssistance)
    {
        $medicalAssistance->update($request->all());

        return redirect()->route('admin.medical-assistances.index');
    }

    /**
     * Display details of a specific medical assistance record.
     */
    public function show(MedicalAssistance $medicalAssistance)
    {
        abort_if(Gate::denies('medical_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.medicalAssistances.show', compact('medicalAssistance'));
    }

    /**
     * Delete a medical assistance record.
     */
    public function destroy(MedicalAssistance $medicalAssistance)
    {
        abort_if(Gate::denies('medical_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $medicalAssistance->delete();

        return back();
    }

    /**
     * Delete multiple medical assistance records at once.
     */
    public function massDestroy(MassDestroyMedicalAssistanceRequest $request)
    {
        $medicalAssistances = MedicalAssistance::find(request('ids'));

        foreach ($medicalAssistances as $medicalAssistance) {
            $medicalAssistance->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
