@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.familycomposition.title_singular') }}
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('admin.familycompositions.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-muted font-weight-bold mb-0">Family Composition</h6>
                <small class="text-muted">Up to 6 rows</small>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm" id="family-table">
                    <thead class="thead-light">
                        <tr>
                            <th>Names</th>
                            <th>Birthday</th>
                            <th>Relationship</th>
                            <th>Civil Status</th>
                            <th>Highest Education</th>
                            <th>Occupation / Remarks</th>
                            <th style="width: 70px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Initial Row --}}
                        <tr class="fam-row">
                            <td>
                                <input type="text" name="family_name[]" class="form-control" placeholder="Full name">
                            </td>
                            <td>
                                <input type="date" name="family_birthday[]" class="form-control">
                            </td>
                            <td>
                                <select name="family_relationship[]" class="form-control">
                                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="family_civil_status[]" class="form-control">
                                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="family_highest_edu[]" class="form-control">
                                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                                    @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <input type="text" name="occupation[]" class="form-control mb-1" placeholder="Occupation">
                                    <input type="text" name="remarks[]" class="form-control" placeholder="Remarks">
                                    <input type="hidden" name="others[]" value="">
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <button type="button" class="btn btn-sm btn-success add-row" title="Add row">+</button>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">–</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Validation feedback (top-level, optional) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="font-weight-bold mb-1">Please fix the errors below:</div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group mt-3">
                <button class="btn btn-danger" type="submit">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .gap-2 > * { margin-right: .25rem; }
    .gap-2 > *:last-child { margin-right: 0; }
</style>
@endpush

@section('scripts')
@parent
<script>
(function () {
    const MAX_ROWS = 6;
    const table    = document.getElementById('family-table');
    const tbody    = table.querySelector('tbody');

    function rowCount() {
        return tbody.querySelectorAll('tr.fam-row').length;
    }

    function updateAddButtons() {
        const full = rowCount() >= MAX_ROWS;
        tbody.querySelectorAll('.add-row').forEach(btn => {
            btn.disabled = full;
            btn.classList.toggle('disabled', full);
        });
    }

    function makeRow() {
        const tr = document.createElement('tr');
        tr.className = 'fam-row';
        tr.innerHTML = `
            <td>
                <input type="text" name="family_name[]" class="form-control" placeholder="Full name">
            </td>
            <td>
                <input type="date" name="family_birthday[]" class="form-control">
            </td>
            <td>
                <select name="family_relationship[]" class="form-control">
                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_RELATIONSHIP_SELECT as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="family_civil_status[]" class="form-control">
                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_CIVIL_STATUS_SELECT as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="family_highest_edu[]" class="form-control">
                    <option value="" selected disabled>{{ trans('global.pleaseSelect') }}</option>
                    @foreach(App\Models\Familycomposition::FAMILY_HIGHEST_EDU_SELECT as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <div class="d-flex gap-2">
                    <input type="text" name="occupation[]" class="form-control mb-1" placeholder="Occupation">
                    <input type="text" name="remarks[]" class="form-control" placeholder="Remarks">
                    <input type="hidden" name="others[]" value="">
                </div>
            </td>
            <td class="text-nowrap">
                <button type="button" class="btn btn-sm btn-success add-row" title="Add row">+</button>
                <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">–</button>
            </td>
        `;
        return tr;
    }

    // Delegate click events
    tbody.addEventListener('click', function (e) {
        const target = e.target;
        if (target.classList.contains('add-row')) {
            if (rowCount() < MAX_ROWS) {
                tbody.appendChild(makeRow());
                updateAddButtons();
            }
        }
        if (target.classList.contains('remove-row')) {
            const rows = rowCount();
            const tr   = target.closest('tr.fam-row');
            if (rows > 1 && tr) {
                tr.remove();
                updateAddButtons();
            }
        }
    });

    updateAddButtons();
})();
</script>
@endsection
