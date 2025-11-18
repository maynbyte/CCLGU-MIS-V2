<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroySolicitationRequest;
use App\Http\Requests\StoreSolicitationRequest;
use App\Http\Requests\UpdateSolicitationRequest;
use App\Models\Solicitation;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SolicitationController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('solicitation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Solicitation::query()->select(sprintf('%s.*', (new Solicitation)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'solicitation_show';
                $editGate      = 'solicitation_edit';
                $deleteGate    = 'solicitation_delete';
                $crudRoutePart = 'solicitations';

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
            $table->editColumn('solicitation', function ($row) {
                return $row->solicitation ? $row->solicitation : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.solicitations.index');
    }

    public function create()
    {
        abort_if(Gate::denies('solicitation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.solicitations.create');
    }

    public function store(StoreSolicitationRequest $request)
    {
        $solicitation = Solicitation::create($request->all());

        return redirect()->route('admin.solicitations.index');
    }

    public function edit(Solicitation $solicitation)
    {
        abort_if(Gate::denies('solicitation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.solicitations.edit', compact('solicitation'));
    }

    public function update(UpdateSolicitationRequest $request, Solicitation $solicitation)
    {
        $solicitation->update($request->all());

        return redirect()->route('admin.solicitations.index');
    }

    public function show(Solicitation $solicitation)
    {
        abort_if(Gate::denies('solicitation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.solicitations.show', compact('solicitation'));
    }

    public function destroy(Solicitation $solicitation)
    {
        abort_if(Gate::denies('solicitation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $solicitation->delete();

        return back();
    }

    public function massDestroy(MassDestroySolicitationRequest $request)
    {
        $solicitations = Solicitation::find(request('ids'));

        foreach ($solicitations as $solicitation) {
            $solicitation->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
