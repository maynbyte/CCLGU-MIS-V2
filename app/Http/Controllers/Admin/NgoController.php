<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyNgoRequest;
use App\Http\Requests\StoreNgoRequest;
use App\Http\Requests\UpdateNgoRequest;
use App\Models\Ngo;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class NgoController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('ngo_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Ngo::query()->select(sprintf('%s.*', (new Ngo)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'ngo_show';
                $editGate      = 'ngo_edit';
                $deleteGate    = 'ngo_delete';
                $crudRoutePart = 'ngos';

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
            $table->editColumn('name', function ($row) {
                return $row->name ? $row->name : '';
            });
            $table->editColumn('contact_person', function ($row) {
                return $row->contact_person ? $row->contact_person : '';
            });
            $table->editColumn('contact_no', function ($row) {
                return $row->contact_no ? $row->contact_no : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('total_members', function ($row) {
                return $row->total_members ? $row->total_members : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.ngos.index');
    }

    public function create()
    {
        abort_if(Gate::denies('ngo_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.ngos.create');
    }

    public function store(StoreNgoRequest $request)
    {
        $ngo = Ngo::create($request->all());

        return redirect()->route('admin.ngos.index');
    }

    public function edit(Ngo $ngo)
    {
        abort_if(Gate::denies('ngo_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.ngos.edit', compact('ngo'));
    }

    public function update(UpdateNgoRequest $request, Ngo $ngo)
    {
        $ngo->update($request->all());

        return redirect()->route('admin.ngos.index');
    }

    public function show(Ngo $ngo)
    {
        abort_if(Gate::denies('ngo_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.ngos.show', compact('ngo'));
    }

    public function destroy(Ngo $ngo)
    {
        abort_if(Gate::denies('ngo_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $ngo->delete();

        return back();
    }

    public function massDestroy(MassDestroyNgoRequest $request)
    {
        $ngos = Ngo::find(request('ids'));

        foreach ($ngos as $ngo) {
            $ngo->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
