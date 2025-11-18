<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\CsvImportTrait;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyDirectoryRequest;
use App\Http\Requests\StoreDirectoryRequest;
use App\Http\Requests\UpdateDirectoryRequest;
use App\Models\Barangay;
use App\Models\Directory;
use App\Models\Ngo;
use App\Models\SectorGroup;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class DirectoryController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('directory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $query = Directory::with(['barangay', 'ngos', 'sectors'])->select(sprintf('%s.*', (new Directory)->table));
            $table = Datatables::of($query);

            $table->addColumn('placeholder', '&nbsp;');
            $table->addColumn('actions', '&nbsp;');

            $table->editColumn('actions', function ($row) {
                $viewGate      = 'directory_show';
                $editGate      = 'directory_edit';
                $deleteGate    = 'directory_delete';
                $crudRoutePart = 'directories';

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
            $table->editColumn('last_name', function ($row) {
                return $row->last_name ? $row->last_name : '';
            });
            $table->editColumn('first_name', function ($row) {
                return $row->first_name ? $row->first_name : '';
            });
            $table->editColumn('middle_name', function ($row) {
                return $row->middle_name ? $row->middle_name : '';
            });
            $table->editColumn('profile_picture', function ($row) {
                if ($photo = $row->profile_picture) {
                    return sprintf(
                        '<a href="%s" target="_blank"><img src="%s" width="50px" height="50px"></a>',
                        $photo->url,
                        $photo->thumbnail
                    );
                }

                return '';
            });
            $table->editColumn('suffix', function ($row) {
                return $row->suffix ? $row->suffix : '';
            });
            $table->editColumn('email', function ($row) {
                return $row->email ? $row->email : '';
            });
            $table->editColumn('contact_no', function ($row) {
                return $row->contact_no ? $row->contact_no : '';
            });

            $table->editColumn('place_of_birth', function ($row) {
                return $row->place_of_birth ? $row->place_of_birth : '';
            });
            $table->editColumn('nationality', function ($row) {
                return $row->nationality ? $row->nationality : '';
            });
            $table->editColumn('gender', function ($row) {
                return $row->gender ? Directory::GENDER_SELECT[$row->gender] : '';
            });
            $table->editColumn('highest_edu', function ($row) {
                return $row->highest_edu ? Directory::HIGHEST_EDU_SELECT[$row->highest_edu] : '';
            });
            $table->editColumn('civil_status', function ($row) {
                return $row->civil_status ? Directory::CIVIL_STATUS_SELECT[$row->civil_status] : '';
            });
            $table->editColumn('religion', function ($row) {
                return $row->religion ? Directory::RELIGION_SELECT[$row->religion] : '';
            });
            $table->editColumn('street_no', function ($row) {
                return $row->street_no ? $row->street_no : '';
            });
            $table->editColumn('street', function ($row) {
                return $row->street ? $row->street : '';
            });
            $table->editColumn('city', function ($row) {
                return $row->city ? $row->city : '';
            });
            $table->editColumn('province', function ($row) {
                return $row->province ? $row->province : '';
            });
            $table->editColumn('occupation', function ($row) {
                return $row->occupation ? $row->occupation : '';
            });
            $table->addColumn('barangay_barangay_name', function ($row) {
                return $row->barangay ? $row->barangay->barangay_name : '';
            });

            $table->editColumn('ngo', function ($row) {
                $labels = [];
                foreach ($row->ngos as $ngo) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $ngo->name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('sector', function ($row) {
                $labels = [];
                foreach ($row->sectors as $sector) {
                    $labels[] = sprintf('<span class="label label-info label-many">%s</span>', $sector->name);
                }

                return implode(' ', $labels);
            });
            $table->editColumn('comelec_status', function ($row) {
                return $row->comelec_status ? $row->comelec_status : '';
            });
            $table->editColumn('life_status', function ($row) {
                return $row->life_status ? Directory::LIFE_STATUS_SELECT[$row->life_status] : '';
            });
            $table->editColumn('description', function ($row) {
                return $row->description ? $row->description : '';
            });
            $table->editColumn('remarks', function ($row) {
                return $row->remarks ? $row->remarks : '';
            });

            $table->rawColumns(['actions', 'placeholder', 'profile_picture', 'barangay', 'ngo', 'sector']);

            return $table->make(true);
        }

        return view('admin.directories.index');
    }

    public function create()
    {
        abort_if(Gate::denies('directory_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barangays = Barangay::pluck('barangay_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $ngos = Ngo::pluck('name', 'id');

        $sectors = SectorGroup::pluck('name', 'id');

        return view('admin.directories.create', compact('barangays', 'ngos', 'sectors'));
    }

    public function store(StoreDirectoryRequest $request)
    {
        $directory = null;

        DB::transaction(function () use ($request, &$directory) {
            // Create parent without child arrays / media helpers / pivots
            $directory = Directory::create(
                $request->except([
                    // child arrays
                    'family_name',
                    'family_birthday',
                    'family_relationship',
                    'family_civil_status',
                    'family_highest_edu',
                    'family_occupation',
                    'family_remarks',
                    'family_others',
                    // not DB columns for parent
                    'profile_picture',
                    'ck-media',
                    // handled as pivots
                    'ngos',
                    'sectors',
                ])
            );

            // Sync many-to-many (inside the transaction)
            $directory->ngos()->sync($request->input('ngos', []));
            $directory->sectors()->sync($request->input('sectors', []));

            // Create child rows (cap at 6; skip fully blank rows)
            $names         = $request->input('family_name', []);
            $birthdays     = $request->input('family_birthday', []);
            $relationships = $request->input('family_relationship', []);
            $civil         = $request->input('family_civil_status', []);
            $edu           = $request->input('family_highest_edu', []);
            $occupations = $request->input('family_occupation', []);
            $remarks     = $request->input('family_remarks', []);
            $others      = $request->input('family_others', []);


            $max = min(6, count($names));
            for ($i = 0; $i < $max; $i++) {
                $name = trim($names[$i] ?? '');

                if (
                    $name === '' &&
                    empty($relationships[$i]) &&
                    empty($civil[$i]) &&
                    empty($edu[$i]) &&
                    empty($occupations[$i]) &&
                    empty($remarks[$i]) &&
                    empty($birthdays[$i])
                ) {
                    continue; // skip fully empty row
                }

                $directory->familycompositions()->create([
                    'family_name'         => $name ?: null,
                    'family_birthday'     => $birthdays[$i] ?? null,
                    'family_relationship' => $relationships[$i] ?? null,
                    'family_civil_status' => $civil[$i] ?? null,
                    'family_highest_edu'  => $edu[$i] ?? null,
                    'occupation'          => $occupations[$i] ?? null,
                    'remarks'             => $remarks[$i] ?? null,
                    'others'              => $others[$i] ?? null,
                ]);
            }
        });



        // Handle media AFTER commit (avoids orphan files if DB rolls back)
        if ($request->filled('profile_picture')) {
            $directory
                ->addMedia(storage_path('tmp/uploads/' . basename($request->input('profile_picture'))))
                ->toMediaCollection('profile_picture');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $directory->id]);
        }
        if ($request->hasFile('profile_picture')) {
            $directory->addMediaFromRequest('profile_picture')->toMediaCollection('profile_picture');
        }


        // 👉 Redirect to SHOW page
        return redirect()
            ->route('admin.directories.show', $directory->id)
            ->with('message', 'Directory successfully created ✅');
    }


    public function edit(Directory $directory)
    {
        abort_if(Gate::denies('directory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $barangays = Barangay::pluck('barangay_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $ngos      = Ngo::pluck('name', 'id');
        $sectors   = SectorGroup::pluck('name', 'id');

        // Load everything the edit view needs (including family rows)
        $directory->load([
            'barangay',
            'ngos',
            'sectors',
            'familycompositions' => fn($q) => $q->orderBy('id'),
        ]);

        return view('admin.directories.edit', compact('barangays', 'ngos', 'sectors', 'directory'));
    }




    public function update(UpdateDirectoryRequest $request, Directory $directory)
    {
        DB::transaction(function () use ($request, $directory) {
            // Update parent without child arrays / media helpers / pivots
            $directory->update(
                $request->except([
                    // child arrays
                    'family_name',
                    'family_birthday',
                    'family_relationship',
                    'family_civil_status',
                    'family_highest_edu',
                    'family_occupation',
                    'family_remarks',
                    'family_others',
                    // not DB columns for parent
                    'profile_picture',
                    'ck-media',
                    // handled as pivots
                    'ngos',
                    'sectors',
                ])
            );

            // Sync many-to-many
            $directory->ngos()->sync($request->input('ngos', []));
            $directory->sectors()->sync($request->input('sectors', []));

            // Rebuild children (simple + reliable since you don't post row IDs)
            $directory->familycompositions()->delete();

            $names         = $request->input('family_name', []);
            $birthdays     = $request->input('family_birthday', []);
            $relationships = $request->input('family_relationship', []);
            $civil         = $request->input('family_civil_status', []);
            $edu           = $request->input('family_highest_edu', []);
            $occupations   = $request->input('family_occupation', []);
            $remarks       = $request->input('family_remarks', []);
            $others        = $request->input('family_others', []);

            $max = min(6, count($names));
            for ($i = 0; $i < $max; $i++) {
                $name = trim($names[$i] ?? '');

                // skip fully blank rows
                if (
                    $name === '' &&
                    empty($relationships[$i]) &&
                    empty($civil[$i]) &&
                    empty($edu[$i]) &&
                    empty($occupations[$i]) &&
                    empty($remarks[$i]) &&
                    empty($birthdays[$i])
                ) {
                    continue;
                }

                // normalize birthday to Y-m-d (handles dd/mm/yyyy or arrays)
                $raw = $birthdays[$i] ?? null;
                $iso = null;
                if ($raw) {
                    $pick = is_array($raw) ? (reset($raw) ?: null) : $raw;
                    if ($pick) {
                        try {
                            $iso = Carbon::parse(str_replace('/', '-', $pick))->format('Y-m-d');
                        } catch (\Throwable $e) {
                            $iso = $pick; // fallback
                        }
                    }
                }

                $directory->familycompositions()->create([
                    'family_name'         => $name ?: null,
                    'family_birthday'     => $iso,
                    'family_relationship' => $relationships[$i] ?? null,
                    'family_civil_status' => $civil[$i] ?? null,
                    'family_highest_edu'  => $edu[$i] ?? null,
                    'occupation'          => $occupations[$i] ?? null,
                    'remarks'             => $remarks[$i] ?? null,
                    'others'              => $others[$i] ?? null,
                ]);
            }
        });



        // Keep existing image unless a new one is uploaded
        if ($request->hasFile('profile_picture')) {
            if ($directory->profile_picture) {
                $directory->profile_picture->delete();
            }
            $directory
                ->addMediaFromRequest('profile_picture')
                ->toMediaCollection('profile_picture');
        }
        // else: do nothing; keep the current media





        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $directory->id]);
        }
        return redirect()
            ->route('admin.directories.show', $directory->id)
            ->with('message', 'Directory successfully modified ✅');
    }


    public function show(Directory $directory)
    {
        abort_if(Gate::denies('directory_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $directory->load(['barangay', 'ngos', 'sectors', 'familycompositions' => fn($q) => $q->orderBy('id')]);

        return view('admin.directories.show', compact('directory'));
    }


    public function destroy(Directory $directory)
    {
        abort_if(Gate::denies('directory_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $directory->delete();

        return back();
    }

    public function massDestroy(MassDestroyDirectoryRequest $request)
    {
        $directories = Directory::find(request('ids'));

        foreach ($directories as $directory) {
            $directory->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('directory_create') && Gate::denies('directory_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Directory();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
