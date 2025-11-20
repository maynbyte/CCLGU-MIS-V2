
// helper – compute Eligible vs Ineligible from created_at
function eligibilityBadge(createdAt) {
    if (!createdAt) {
        return '<span class="badge badge-secondary">No record</span>';
    }

    // Parse safely (MySQL format "YYYY-MM-DD HH:mm:ss" → "YYYY-MM-DDTHH:mm:ss")
    var d = new Date(String(createdAt).replace(' ', 'T'));
    if (isNaN(d.getTime())) {
        return '<span class="badge badge-secondary">—</span>';
    }

    var now = new Date();
    var diffMs = now - d;
    var days = diffMs / (1000 * 60 * 60 * 24);

    // 3 months ≈ 90 days (simple business rule)
    var eligible = days > 90;

    return eligible
        ? '<span class="badge badge-success">Eligible</span>'
        : '<span class="badge badge-danger">Ineligible</span>';
}

$(function () {


    function toTitleCase(str) {
        if (!str) return '';
        return str.toLowerCase().replace(/\b[\p{L}’']+/gu, function (w) {
            return w.charAt(0).toUpperCase() + w.slice(1);
        });
    }

    function statusBadge(raw) {
        if (!raw) return '<span class="text-muted">—</span>';
        const val = String(raw).toLowerCase();
        if (val === 'claimed') {
            return '<span class="badge badge-success">Claimed</span>';
        }
        if (val === 'cancelled' || val === 'canceled') {
            return '<span class="badge badge-danger">Cancelled</span>';
        }
        // Treat everything else (e.g. Pending) as Ongoing
        return '<span class="badge badge-warning">Ongoing</span>';
    }

    let dtOverrideGlobals = {
        buttons: dtButtons,
        processing: true,
        serverSide: true,
        retrieve: true,
        aaSorting: [],
        ajax: "{{ route('admin.directories.index') }}",
        columns: [

            // Count / Row number (continuous per page)
            {
                data: null,
                name: '_row',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },

            { data: 'profile_picture', name: 'profile_picture', orderable: false, searchable: false },

            // Combined Name column
            {
                data: null,
                name: 'last_name', // for sorting/search by last name
                render: function (data, type, row) {
                    var ln = row.last_name || '';
                    var sx = row.suffix || '';
                    var fn = row.first_name || '';
                    var mn = row.middle_name || '';

                    var left = [ln, sx].filter(Boolean).join(' ').trim();   // "Dela Cruz Jr"
                    var right = [fn, mn].filter(Boolean).join(' ').trim();  // "Christian Carlo Antonio"
                    var full = [left, right].filter(Boolean).join(', ');    // "Dela Cruz Jr, Christian Carlo Antonio"

                    return toTitleCase(full);
                }
            },

            { data: 'barangay_barangay_name', name: 'barangay.barangay_name' },
            { data: 'comelec_status', name: 'comelec_status' },

            // NEW: Latest FA (type)
            {
                data: 'created_at',
                name: 'created_at',
                render: function (data) {
                    return data ? toTitleCase(data) : '<span class="text-muted">—</span>';
                }
            },
            // NEW: Latest FA status as colored badge (Pending -> Ongoing)
            {
                data: 'latest_fa_created_at',
                name: 'latest_fa_created_at',
                orderable: false,   // IMPORTANT: avoid DB ORDER BY on a subselect alias
                searchable: false,  // IMPORTANT: avoid DB WHERE on a subselect alias
                render: function (createdAt, type, row, meta) {
                    return eligibilityBadge(createdAt);
                }
            },


            // FA Add link
            {
                data: 'id',
                name: 'fa_link',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    let url = "{{ route('admin.financial-assistances.create') }}?directory_id=" + data;
                    return '<a class="btn btn-sm btn-primary" href="' + url + '">Add</a>';
                }
            }
        ],
        // Sort by Name (index 3) ascending
        orderCellsTop: true,
        order: [[3, 'asc']],
        pageLength: 10,
    };

    let table = $('.datatable-Directory').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab click', function () {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
});
