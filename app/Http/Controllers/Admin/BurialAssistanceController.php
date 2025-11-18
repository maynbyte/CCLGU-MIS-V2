<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyBurialAssistanceRequest;
use App\Http\Requests\StoreBurialAssistanceRequest;
use App\Http\Requests\UpdateBurialAssistanceRequest;
use App\Models\BurialAssistance;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BurialAssistanceController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('burial_assistance_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = BurialAssistance::query()->select(sprintf('%s.*', (new BurialAssistance)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'burial_assistance_show';
                $editGate      = 'burial_assistance_edit';
                $deleteGate    = 'burial_assistance_delete';
                $crudRoutePart = 'burial-assistances';

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
            $table->editColumn('burial_assitance', function ($row) {
                return $row->burial_assitance ? $row->burial_assitance : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.burialAssistances.index');
    }

    public function create()
    {
        abort_if(Gate::denies('burial_assistance_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.burialAssistances.create');
    }

    public function store(StoreBurialAssistanceRequest $request)
    {
        $burialAssistance = BurialAssistance::create($request->all());

        return redirect()->route('admin.burial-assistances.index');
    }

    public function edit(BurialAssistance $burialAssistance)
    {
        abort_if(Gate::denies('burial_assistance_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.burialAssistances.edit', compact('burialAssistance'));
    }

    public function update(UpdateBurialAssistanceRequest $request, BurialAssistance $burialAssistance)
    {
        $burialAssistance->update($request->all());

        return redirect()->route('admin.burial-assistances.index');
    }

    public function show(BurialAssistance $burialAssistance)
    {
        abort_if(Gate::denies('burial_assistance_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.burialAssistances.show', compact('burialAssistance'));
    }

    public function destroy(BurialAssistance $burialAssistance)
    {
        abort_if(Gate::denies('burial_assistance_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $burialAssistance->delete();

        return back();
    }

    public function massDestroy(MassDestroyBurialAssistanceRequest $request)
    {
        $burialAssistances = BurialAssistance::find(request('ids'));

        foreach ($burialAssistances as $burialAssistance) {
            $burialAssistance->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
