<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyBarangayRequest;
use App\Http\Requests\StoreBarangayRequest;
use App\Http\Requests\UpdateBarangayRequest;
use App\Models\Barangay;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class BarangayController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('barangay_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Barangay::query()->select(sprintf('%s.*', (new Barangay)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'barangay_show';
                $editGate      = 'barangay_edit';
                $deleteGate    = 'barangay_delete';
                $crudRoutePart = 'barangays';

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
            $table->editColumn('barangay_name', function ($row) {
                return $row->barangay_name ? $row->barangay_name : '';
            });
            $table->editColumn('barangay', function ($row) {
                return $row->barangay ? $row->barangay : '';
            });
            $table->editColumn('barangay_chairman', function ($row) {
                return $row->barangay_chairman ? $row->barangay_chairman : '';
            });
            $table->editColumn('sk_chairman', function ($row) {
                return $row->sk_chairman ? $row->sk_chairman : '';
            });
            $table->editColumn('total_no_of_voters', function ($row) {
                return $row->total_no_of_voters ? $row->total_no_of_voters : '';
            });

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.barangays.index');
    }

    public function create()
    {
        abort_if(Gate::denies('barangay_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barangays.create');
    }

    public function store(StoreBarangayRequest $request)
    {
        $barangay = Barangay::create($request->all());

        return redirect()->route('admin.barangays.index');
    }

    public function edit(Barangay $barangay)
    {
        abort_if(Gate::denies('barangay_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barangays.edit', compact('barangay'));
    }

    public function update(UpdateBarangayRequest $request, Barangay $barangay)
    {
        $barangay->update($request->all());

        return redirect()->route('admin.barangays.index');
    }

    public function show(Barangay $barangay)
    {
        abort_if(Gate::denies('barangay_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.barangays.show', compact('barangay'));
    }

    public function destroy(Barangay $barangay)
    {
        abort_if(Gate::denies('barangay_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barangay->delete();

        return back();
    }

    public function massDestroy(MassDestroyBarangayRequest $request)
    {
        $barangays = Barangay::find(request('ids'));

        foreach ($barangays as $barangay) {
            $barangay->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
