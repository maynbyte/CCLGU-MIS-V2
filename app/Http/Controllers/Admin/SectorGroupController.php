<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySectorGroupRequest;
use App\Http\Requests\StoreSectorGroupRequest;
use App\Http\Requests\UpdateSectorGroupRequest;
use App\Models\SectorGroup;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class SectorGroupController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('sector_group_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = SectorGroup::query()->select(sprintf('%s.*', (new SectorGroup)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'sector_group_show';
                $editGate      = 'sector_group_edit';
                $deleteGate    = 'sector_group_delete';
                $crudRoutePart = 'sector-groups';

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

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.sectorGroups.index');
    }

    public function create()
    {
        abort_if(Gate::denies('sector_group_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sectorGroups.create');
    }

    public function store(StoreSectorGroupRequest $request)
    {
        $sectorGroup = SectorGroup::create($request->all());

        return redirect()->route('admin.sector-groups.index');
    }

    public function edit(SectorGroup $sectorGroup)
    {
        abort_if(Gate::denies('sector_group_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sectorGroups.edit', compact('sectorGroup'));
    }

    public function update(UpdateSectorGroupRequest $request, SectorGroup $sectorGroup)
    {
        $sectorGroup->update($request->all());

        return redirect()->route('admin.sector-groups.index');
    }

    public function show(SectorGroup $sectorGroup)
    {
        abort_if(Gate::denies('sector_group_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.sectorGroups.show', compact('sectorGroup'));
    }

    public function destroy(SectorGroup $sectorGroup)
    {
        abort_if(Gate::denies('sector_group_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sectorGroup->delete();

        return back();
    }

    public function massDestroy(MassDestroySectorGroupRequest $request)
    {
        $sectorGroups = SectorGroup::find(request('ids'));

        foreach ($sectorGroups as $sectorGroup) {
            $sectorGroup->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
