<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Requests\MassDestroyGuaranteeLetterRequest;
use App\Http\Requests\StoreGuaranteeLetterRequest;
use App\Http\Requests\UpdateGuaranteeLetterRequest;
use App\Models\GuaranteeLetter;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;

class GuaranteeLetterController extends Controller
{
    use CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('guarantee_letter_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = GuaranteeLetter::query()->select(sprintf('%s.*', (new GuaranteeLetter)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'guarantee_letter_show';
                $editGate      = 'guarantee_letter_edit';
                $deleteGate    = 'guarantee_letter_delete';
                $crudRoutePart = 'guarantee-letters';

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

            $table->rawColumns(['actions', 'placeholder']);

            return $table->make(true);
        }

        return view('admin.guaranteeLetters.index');
    }

    public function create()
    {
        abort_if(Gate::denies('guarantee_letter_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.guaranteeLetters.create');
    }

    public function store(StoreGuaranteeLetterRequest $request)
    {
        $guaranteeLetter = GuaranteeLetter::create($request->all());

        return redirect()->route('admin.guarantee-letters.index');
    }

    public function edit(GuaranteeLetter $guaranteeLetter)
    {
        abort_if(Gate::denies('guarantee_letter_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.guaranteeLetters.edit', compact('guaranteeLetter'));
    }

    public function update(UpdateGuaranteeLetterRequest $request, GuaranteeLetter $guaranteeLetter)
    {
        $guaranteeLetter->update($request->all());

        return redirect()->route('admin.guarantee-letters.index');
    }

    public function show(GuaranteeLetter $guaranteeLetter)
    {
        abort_if(Gate::denies('guarantee_letter_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.guaranteeLetters.show', compact('guaranteeLetter'));
    }

    public function destroy(GuaranteeLetter $guaranteeLetter)
    {
        abort_if(Gate::denies('guarantee_letter_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $guaranteeLetter->delete();

        return back();
    }

    public function massDestroy(MassDestroyGuaranteeLetterRequest $request)
    {
        $guaranteeLetters = GuaranteeLetter::find(request('ids'));

        foreach ($guaranteeLetters as $guaranteeLetter) {
            $guaranteeLetter->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
