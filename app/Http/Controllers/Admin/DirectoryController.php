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
use App\Models\FinancialAssistance;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class DirectoryController extends Controller
{
    use MediaUploadingTrait, CsvImportTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies('directory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            $dirTable = (new Directory)->getTable();

            // Subquery now tracks latest FA by unique id to avoid duplicates when multiple rows share identical created_at timestamps
            $latestFaSub = FinancialAssistance::select('directory_id', DB::raw('MAX(id) as latest_fa_id'))
                ->groupBy('directory_id');

            // Build ONE query only (do not overwrite later)
            $query = Directory::with(['barangay', 'ngos', 'sectors'])
                ->select("$dirTable.*")
                ->leftJoinSub($latestFaSub, 'fa_latest', function ($join) use ($dirTable) {
                    $join->on('fa_latest.directory_id', '=', "$dirTable.id");
                })
                ->leftJoin('financial_assistances as fa_last', function ($join) {
                    $join->on('fa_last.id', '=', 'fa_latest.latest_fa_id');
                })
                ->addSelect([
                    DB::raw('fa_last.created_at as latest_fa_created_at'),
                    DB::raw('fa_last.status as latest_fa_status'),
                    DB::raw('fa_last.date_claimed as latest_fa_date_claimed'),
                    DB::raw('fa_last.scheduled_fa as latest_fa_scheduled_fa'),
                ]);

            // Create the DataTable ONCE from $query
            $table = DataTables::of($query);

            // Virtual columns expected by your JS
            $table->addColumn('placeholder', function () {
                return '&nbsp;';
            });

            $table->addColumn('actions', function ($row) {
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

            // Renderers (return strings)
            $table->editColumn('id', fn($row) => $row->id ?: '');
            $table->editColumn('last_name', fn($row) => $row->last_name ?: '');
            $table->editColumn('first_name', fn($row) => $row->first_name ?: '');
            $table->editColumn('middle_name', fn($row) => $row->middle_name ?: '');
            $table->editColumn('suffix', fn($row) => $row->suffix ?: '');
            $table->editColumn('comelec_status', fn($row) => $row->comelec_status ?: '');
            $table->editColumn('life_status', fn($row) => $row->life_status ? Directory::LIFE_STATUS_SELECT[$row->life_status] : '');
            $table->addColumn('barangay_barangay_name', function ($row) {
                return optional($row->barangay)->barangay_name ?? ($row->barangay_other ?? '');
                $table->editColumn('latest_fa_date_claimed', function ($row) {
                    return $row->latest_fa_date_claimed ? (string) $row->latest_fa_date_claimed : '';
                });
            });




            $table->addColumn('remarks', function ($row) {
                $status = strtolower(trim((string) ($row->latest_fa_status ?? $row->status ?? '')));

                $EL   = '<span class="badge badge-success">Eligible</span>';
                $IN   = '<span class="badge badge-danger">Ineligible</span>';

                // Status-first
                if (in_array($status, ['pending', 'cancelled', 'canceled'], true)) {
                    return $IN;
                }

                // Claimed → date-based rule
                $raw = $row->latest_fa_date_claimed ?? $row->date_claimed ?? null;

                // If Eloquent casted it, it's already Carbon. If not, parse safely.
                $claimedAt = $raw instanceof \DateTimeInterface ? Carbon::instance($raw)
                    : (!empty($raw) ? Carbon::parse((string) $raw) : null);

                if ($status === 'claimed') {
                    if (!$claimedAt) return $EL; // fail-safe
                    return $claimedAt->copy()->addMonthsNoOverflow(6)->isPast() ? $EL : $IN;
                }

                // Other/unknown statuses → fall back to date rule
                if (!$claimedAt) return $EL;
                return $claimedAt->copy()->addMonthsNoOverflow(6)->isPast() ? $EL : $IN;
            })->rawColumns(['remarks']);





            // Profile picture (HTML). Adjust to how you store the image.
            $table->editColumn('profile_picture', function ($row) {
                $defaultAvatar = asset('upload/free-user-icon.png');

                // examples of possible sources; keep whichever applies to you
                if (!empty($row->profile_picture_url)) {
                    return '<img src="' . $row->profile_picture_url . '" width="50" height="50" class="img-thumbnail">';
                }
                if (!empty($row->profile_picture) && is_object($row->profile_picture)) {
                    $thumb = $row->profile_picture->thumbnail ?? $row->profile_picture->url ?? null;
                    if ($thumb) {
                        return '<img src="' . $thumb . '" width="50" height="50" class="img-thumbnail">';
                    }
                }
                
                // Return default avatar if no profile picture
                return '<img src="' . $defaultAvatar . '" width="50" height="50" class="img-thumbnail">';
            });

            // FA info (optional display/use later)
            $table->editColumn('latest_fa_created_at', fn($row) => $row->latest_fa_created_at ? (string) $row->latest_fa_created_at : '');
            $table->editColumn('latest_fa_status', fn($row) => $row->latest_fa_status ?: '');
            $table->editColumn('latest_fa_scheduled_fa', fn($row) => $row->latest_fa_scheduled_fa ? (string) $row->latest_fa_scheduled_fa : '');

            // Mark HTML columns
            $table->rawColumns(['actions', 'placeholder', 'profile_picture', 'remarks']);

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

        // If “other” is posted, convert barangay_id to null so it passes 'exists'
        $isOther = $request->input('barangay_id') === 'other';
        if ($isOther) {
            $request->merge(['barangay_id' => null]);
        }

        $validated = $request->validate([
            'barangay_id'    => ['nullable', 'integer', 'exists:barangays,id'],
            'barangay_other' => [Rule::requiredIf($isOther), 'nullable', 'string', 'max:191'],
            // ... your other rules ...
        ]);

        $data = $validated;

        // If a valid id was chosen, wipe the other field
        if (! $isOther) {
            $data['barangay_other'] = null;
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

        $directory->load(['barangay', 'ngos', 'sectors', 'financialAssistances', 'financialAssistances.addedBy', 'familycompositions' => fn($q) => $q->orderBy('id')]);

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


    public function matchUpload(Request $request)
    {
        abort_if(Gate::denies('directory_access'), \Symfony\Component\HttpFoundation\Response::HTTP_FORBIDDEN, '403 Forbidden');
        @set_time_limit(180); // safety: extend max execution time

        $request->validate([
            'upload_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
        ]);

        $path = $request->file('upload_file')->getRealPath();
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);

        // Pick first worksheet with data
        $sheet = null;
        foreach ($spreadsheet->getWorksheetIterator() as $ws) {
            if (count($ws->toArray(null, true, true, true)) > 0) {
                $sheet = $ws;
                break;
            }
        }
        if (!$sheet) return response()->json(['data' => []]);

        // Rows -> drop fully blank
        $raw  = $sheet->toArray(null, true, true, true);
        $rows = array_values(array_filter($raw, function ($r) {
            foreach ($r as $v) if (trim((string)$v) !== '') return true;
            return false;
        }));
        if (empty($rows)) return response()->json(['data' => []]);

        // Prefer exact header set (any order) within first 5 lines
        $expectedHeaders = ['last name', 'first name', 'middle initial', 'suffix'];
        $headerIdx = null;
        for ($i = 0; $i < min(5, count($rows)); $i++) {
            $labels = array_map(fn($v) => $this->normHeader($v), $rows[$i]);
            $labels = array_values(array_filter($labels, fn($v) => $v !== ''));
            if (empty(array_diff($expectedHeaders, $labels))) {
                $headerIdx = $i;
                break;
            }
        }
        if ($headerIdx === null) {
            for ($i = 0; $i < min(5, count($rows)); $i++) {
                if ($this->looksLikeHeader($rows[$i])) {
                    $headerIdx = $i;
                    break;
                }
            }
        }
        if ($headerIdx === null) $headerIdx = 0;

        $headerRow = $rows[$headerIdx];
        $dataRows  = array_slice($rows, $headerIdx + 1);

        // Map headers
        $want = [
            'last'   => ['last name', 'lastname', 'last', 'surname', 'family name'],
            'first'  => ['first name', 'firstname', 'first', 'given name', 'givenname', 'forename'],
            'middle' => ['middle name', 'middlename', 'middle', 'mi', 'm.i.', 'middle initial', 'middleinitial'],
            'suffix' => ['suffix', 'ext', 'extn', 'jr', 'sr', 'iii', 'iv', 'v'],
            'name'   => ['name', 'full name', 'fullname', 'claimant', 'employee name', 'member name'],
        ];
        $colMap = $this->mapHeaderColumns($headerRow, $want);
        if (!isset($colMap['name']) && (!isset($colMap['last']) || !isset($colMap['first']))) {
            $letters = array_keys($headerRow);
            if (count($letters) >= 2) {
                $colMap['last']   = $letters[0] ?? null;
                $colMap['first']  = $letters[1] ?? null;
                $colMap['middle'] = $letters[2] ?? null;
                $colMap['suffix'] = $letters[3] ?? null;
            }
        }

        // Load directories ONCE and build fast buckets by first letter of LAST NAME
        $directories = \App\Models\Directory::query()
            ->select(['id', 'last_name', 'first_name', 'middle_name', 'suffix'])
            ->get();

        $buckets = []; // e.g., ['a' => [ [id, name, norm, last_first], ... ], '#'=>[]]
        $allCands = []; // small capped array to fallback when bucket empty
        foreach ($directories as $d) {
            $full = $this->buildFullName($d->last_name, $d->first_name, $d->middle_name, $d->suffix);
            $norm = $this->normName($full);

            $lastNorm  = mb_strtolower(trim((string)$d->last_name));
            $firstNorm = mb_strtolower(trim((string)$d->first_name));
            $key = $this->bucketKey($lastNorm); // first letter or '#'

            $rec = [
                'id'    => (int)$d->id,
                'name'  => $full,
                'norm'  => $norm,
                'ln'    => $lastNorm,
                'fn'    => $firstNorm,
            ];

            $buckets[$key][] = $rec;
            // Keep a small fallback pool (cap ~2k to avoid memory blowup)
            if (count($allCands) < 2000) $allCands[] = $rec;
        }

        // Sort each bucket by id for deterministic tie-breaking
        foreach ($buckets as $k => &$arr) {
            usort($arr, fn($a, $b) => $a['id'] <=> $b['id']);
        }
        unset($arr);

        $results = [];
        $rowNo = 1;

        foreach ($dataRows as $r) {
            // Build uploaded full name
            if (isset($colMap['name'])) {
                $uploadedFull = trim((string)($r[$colMap['name']] ?? ''));
                // Try to split for better bucketing
                [$uLast, $uFirst] = $this->guessLastFirstFromFull($uploadedFull);
            } else {
                $uLast   = isset($colMap['last'])   ? (string)($r[$colMap['last']] ?? '')   : '';
                $uFirst  = isset($colMap['first'])  ? (string)($r[$colMap['first']] ?? '')  : '';
                $uMiddle = isset($colMap['middle']) ? (string)($r[$colMap['middle']] ?? '') : '';
                $uSuffix = isset($colMap['suffix']) ? (string)($r[$colMap['suffix']] ?? '') : '';
                $uploadedFull = $this->buildFullName($uLast, $uFirst, $uMiddle, $uSuffix);
            }

            $uploadedFull = $this->collapseSpaces($uploadedFull);
            $uploadedNorm = $this->normName($uploadedFull);
            if ($uploadedNorm === '') continue;

            $uLastNorm  = mb_strtolower(trim((string)($uLast ?? '')));
            $uFirstNorm = mb_strtolower(trim((string)($uFirst ?? '')));

            // Candidate shortlist from bucket (first letter of last name)
            $key = $this->bucketKey($uLastNorm);
            $cands = $buckets[$key] ?? [];

            // If too many in bucket, prefilter by first-name initial match to reduce set
            if (count($cands) > 1200 && $uFirstNorm !== '') {
                $fi = mb_substr($uFirstNorm, 0, 1);
                $cands = array_values(array_filter($cands, fn($c) => mb_substr($c['fn'], 0, 1) === $fi));
            }

            // Fallback: if bucket empty, use small global pool
            if (empty($cands)) $cands = $allCands;

            // Hard cap comparisons per row for performance (e.g., 800)
            if (count($cands) > 800) {
                // quick coarse filter by last-name prefix similarity (first 3 chars)
                $lp = mb_substr($uLastNorm, 0, 3);
                $cands = array_values(array_filter($cands, function ($c) use ($lp) {
                    return $lp === '' || mb_substr($c['ln'], 0, 3) === $lp;
                }));
                if (count($cands) > 800) {
                    $cands = array_slice($cands, 0, 800);
                }
            }

            // Find best using fast Levenshtein-based percent
            $best = null;
            foreach ($cands as $cand) {
                $score = $this->levenshteinPercent($uploadedNorm, $cand['norm']);
                if ($best === null || $score > $best['score'] || ($score === $best['score'] && $cand['id'] < $best['id'])) {
                    $best = ['id' => $cand['id'], 'name' => $cand['name'], 'score' => $score];
                }
            }

            // Columns per spec
            $colNo   = $rowNo;
            $colName = $uploadedFull;
            $colBest = 'No Match';
            $colID   = '';
            $col8084 = '';

            if ($best) {
                if ($best['score'] >= 90) {
                    // ≥90% → Best Match + ID
                    $colBest = $best['name'];
                    $colID   = (string)$best['id'];
                } elseif ($best['score'] >= 80) {
                    // 85–89% → Best Match, no ID
                    $colBest = $best['name'];
                } elseif ($best['score'] >= 65 && $best['score'] <= 80) {
                    // 75–80% → Low-confidence suggestion (show in Suggestion column) + include ID
                    $col8084 = $best['name'];          // (keep the same array key, label will say 75–80%)
                    $colID   = (string)$best['id'];    // NEW: also show ID for suggestions
                    // Best column stays "No Match"
                }
                // Scores 81–84% and <75% → remain "No Match" everywhere
            }

            $results[] = [
                'no'        => $colNo,
                'uploaded'  => $colName,
                'best'      => $colBest,
                'id_match'  => $colID,
                'suggest80' => $col8084,
            ];
            $rowNo++;
        }

        session(['name_match_results' => $results]);
        return response()->json(['data' => $results]);
    }


    public function matchDownload(Request $request)
    {
        abort_if(Gate::denies('directory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $rows = session('name_match_results', []);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Results');

        $sheet->fromArray([
            ['No.', 'Name (Uploaded)', 'Best Match (Directories, ≥85%)', 'ID Match (≥90% or 75–80% Suggestion)', '75–80% Suggestion'],
        ], null, 'A1');


        // Body
        $r = 2;
        foreach ($rows as $row) {
            $sheet->setCellValue("A{$r}", $row['no'] ?? '');
            $sheet->setCellValue("B{$r}", $row['uploaded'] ?? '');
            $sheet->setCellValue("C{$r}", $row['best'] ?? '');
            $sheet->setCellValue("D{$r}", $row['id_match'] ?? '');
            $sheet->setCellValue("E{$r}", $row['suggest80'] ?? '');
            $r++;
        }

        $fileName = 'name-match-results.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // Bucket by first letter of last name; non-letters -> '#'
    private function bucketKey(string $lastNorm): string
    {
        $ch = mb_substr($lastNorm, 0, 1);
        if ($ch === '' || !preg_match('/[a-z]/u', $ch)) return '#';
        return $ch;
    }

    // Try to split "Last, First ..." or "First Last" to (Last, First)
    private function guessLastFirstFromFull(string $full): array
    {
        $s = trim($full);
        if ($s === '') return ['', ''];
        // If there's a comma, assume "Last, First ..."
        if (mb_strpos($s, ',') !== false) {
            [$last, $rest] = array_pad(array_map('trim', explode(',', $s, 2)), 2, '');
            $first = trim(explode(' ', $rest, 2)[0] ?? '');
            return [$last, $first];
        }
        // Else assume "First ... Last"
        $parts = array_values(array_filter(preg_split('/\s+/u', $s)));
        if (count($parts) === 1) return [$parts[0], '']; // single token -> last
        $last  = array_pop($parts);
        $first = $parts[0] ?? '';
        return [$last, $first];
    }

    // Fast normalized Levenshtein score => 0..100 (higher is better)
    private function levenshteinPercent(string $a, string $b): float
    {
        if ($a === '' || $b === '') return 0.0;
        $dist = levenshtein($a, $b);
        $max  = max(mb_strlen($a), mb_strlen($b));
        if ($max === 0) return 100.0;
        $pct = (1 - ($dist / $max)) * 100.0;
        if ($pct < 0) $pct = 0;
        if ($pct > 100) $pct = 100;
        return $pct;
    }


    // --- Helpers ---

    // Detect if a row looks like a header (any cell matches known labels)
    private function looksLikeHeader(array $row): bool
    {
        $labels = array_filter(array_map(fn($v) => $this->normHeader($v), $row));
        if (empty($labels)) return false;

        $known = [
            'last name',
            'lastname',
            'last',
            'surname',
            'family name',
            'first name',
            'firstname',
            'first',
            'given name',
            'givenname',
            'forename',
            'middle name',
            'middlename',
            'middle',
            'mi',
            'm.i.',
            'middle initial',
            'middleinitial',
            'suffix',
            'ext',
            'extn',
            'jr',
            'sr',
            'iii',
            'iv',
            'v',
            'name',
            'full name',
            'fullname',
            'claimant',
            'employee name',
            'member name',
        ];
        $known = array_map(fn($s) => $this->normHeader($s), $known);

        foreach ($labels as $lab) {
            if (in_array($lab, $known, true)) return true;
        }
        return false;
    }

    // Map header row to column letters for desired keys
    private function mapHeaderColumns(array $headerRow, array $want): array
    {
        $colMap = [];
        foreach ($headerRow as $colLetter => $label) {
            $labelNorm = $this->normHeader($label);
            foreach ($want as $key => $alts) {
                foreach ($alts as $alt) {
                    if ($labelNorm === $this->normHeader($alt)) {
                        $colMap[$key] = $colLetter;
                    }
                }
            }
        }
        return $colMap;
    }

    // Collapse multiple spaces (and trim)
    private function collapseSpaces(string $s): string
    {
        return trim(preg_replace('/\s+/', ' ', $s));
    }

    // Build "Last, First Middle Suffix" (skip empties, collapse spaces)
    private function buildFullName(?string $last, ?string $first, ?string $middle, ?string $suffix): string
    {
        $last   = trim((string)$last);
        $first  = trim((string)$first);
        $middle = trim((string)$middle);
        $suffix = trim((string)$suffix);

        $left = $last;
        $rightParts = array_filter([$first, $middle, $suffix], fn($v) => $v !== '');

        if ($left !== '' && !empty($rightParts)) {
            return $left . ', ' . $this->collapseSpaces(implode(' ', $rightParts));
        }
        if ($left !== '') {
            return $left;
        }
        return $this->collapseSpaces(implode(' ', $rightParts));
    }

    // Normalize header labels
    private function normHeader($s): string
    {
        $s = mb_strtolower((string)$s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    // Normalize names for comparison (case/space/punct insensitive)
    private function normName(string $s): string
    {
        $s = mb_strtolower($s);
        $s = preg_replace('/[^\p{L}\p{N}]+/u', '', $s); // keep letters & numbers only
        return $s;
    }

    // Similarity percent using similar_text over normalized strings
    private function similarityPercent(string $a, string $b): float
    {
        if ($a === '' || $b === '') return 0.0;
        similar_text($a, $b, $percent);
        return (float)$percent; // 0..100
    }

    /**
     * Send bulk SMS to selected directories
     */
    public function sendBulkSms(Request $request)
    {
        abort_if(Gate::denies('directory_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'exists:directories,id',
                'message' => 'required|string|max:160',
            ]);

            \Log::info('SMS Request received', [
                'ids_count' => count($request->ids),
                'message_length' => strlen($request->message),
            ]);

            $directories = Directory::whereIn('id', $request->ids)->get();
            
            // Collect valid phone numbers
            $phoneNumbers = [];
            $invalidContacts = [];
            
            foreach ($directories as $directory) {
                if ($directory->contact_no && $directory->contact_no !== 'N/A' && !empty(trim($directory->contact_no))) {
                    $phoneNumbers[] = $directory->contact_no;
                } else {
                    $invalidContacts[] = $directory->first_name . ' ' . $directory->last_name;
                }
            }

            \Log::info('Phone numbers collected', [
                'valid_count' => count($phoneNumbers),
                'invalid_count' => count($invalidContacts),
            ]);

            if (empty($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid phone numbers found in selected records.',
                ], 400);
            }

            // Send SMS using Semaphore
            $smsService = new \App\Services\SemaphoreSmsService();
            $result = $smsService->sendBulkSms($phoneNumbers, $request->message);

            \Log::info('SMS send result', $result);

            if ($result['success']) {
                $response = [
                    'success' => true,
                    'message' => $result['message'],
                    'sent_count' => count($phoneNumbers),
                    'total_selected' => count($request->ids),
                ];

                if (!empty($invalidContacts)) {
                    $response['warning'] = count($invalidContacts) . ' record(s) skipped (no valid contact number): ' . implode(', ', array_slice($invalidContacts, 0, 5));
                }

                return response()->json($response);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('SMS Validation error', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all()),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('SMS Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
