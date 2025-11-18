<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyFamilycompositionRequest;
use App\Http\Requests\StoreFamilycompositionRequest;
use App\Http\Requests\UpdateFamilycompositionRequest;
use App\Models\Familycomposition;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class FamilycompositionController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('familycomposition_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Familycomposition::query()->select(sprintf('%s.*', (new Familycomposition)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'familycomposition_show';
                $editGate      = 'familycomposition_edit';
                $deleteGate    = 'familycomposition_delete';
                $crudRoutePart = 'familycompositions';

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
            $table->editColumn('family_name', function ($row) {
                return $row->family_name ? $row->family_name : '';
            });

            $table->editColumn('family_relationship', function ($row) {
                return $row->family_relationship ? Familycomposition::FAMILY_RELATIONSHIP_SELECT[$row->family_relationship] : '';
            });
            $table->editColumn('family_civil_status', function ($row) {
                return $row->family_civil_status ? Familycomposition::FAMILY_CIVIL_STATUS_SELECT[$row->family_civil_status] : '';
            });
            $table->editColumn('family_highest_edu', function ($row) {
                return $row->family_highest_edu ? Familycomposition::FAMILY_HIGHEST_EDU_SELECT[$row->family_highest_edu] : '';
            });
            $table->editColumn('occupation', function ($row) {
                return $row->occupation ? $row->occupation : '';
            });
            $table->editColumn('remarks', function ($row) {
                return $row->remarks ? $row->remarks : '';
            });
            $table->editColumn('others', function ($row) {
                return $row->others ? $row->others : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.familycompositions.index');
    }

    public function create()
    {
        abort_if(Gate::denies('familycomposition_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familycompositions.create');
    }

    public function store(StoreFamilycompositionRequest $request)
    {
        $familycomposition = Familycomposition::create($request->all());

        return redirect()->route('admin.familycompositions.index');
    }

    public function edit(Familycomposition $familycomposition)
    {
        abort_if(Gate::denies('familycomposition_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familycompositions.edit', compact('familycomposition'));
    }

    public function update(UpdateFamilycompositionRequest $request, Familycomposition $familycomposition)
    {
        $familycomposition->update($request->all());

        return redirect()->route('admin.familycompositions.index');
    }

    public function show(Familycomposition $familycomposition)
    {
        abort_if(Gate::denies('familycomposition_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.familycompositions.show', compact('familycomposition'));
    }

    public function destroy(Familycomposition $familycomposition)
    {
        abort_if(Gate::denies('familycomposition_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $familycomposition->delete();

        return back();
    }

    public function massDestroy(MassDestroyFamilycompositionRequest $request)
    {
        $familycompositions = Familycomposition::find(request('ids'));

        foreach ($familycompositions as $familycomposition) {
            $familycomposition->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
