const table = new DataTable('#datatable', {
    serverSide: true,
    processing: true,
    searching: true,
    ajax: {url: 'api_registrations.php', type: 'GET'},
    order: [[0, 'desc']],
    columns: [
        {data: 'id'},
        {data: 'full_name'},
        {data: 'email'},
        {data: 'company'},
        {data: 'created_at'}
    ],
    lengthMenu: [10, 25, 50, 100]
});

function buildExportQuery() {
    const params = new URLSearchParams();
    const search = table.search();
    const order = table.order();
    const info = table.page.info();

    if (search) params.set('search[value]', search);
    if (order && order.length) {
        params.set('order[column]', order[0][0]);
        params.set('order[dir]', order[0][1]);
    }
    if (info) {
        params.set('start', info.start)
        params.set('length', info.length)
    }
    return params.toString();
}

$('#btnXlsx').on('click', function () {
    const qs = buildExportQuery();
    window.location.href = 'export_xlsx.php' + (qs ? ('?' + qs) : '');
});
$('#btnPdf').on('click', function () {
    const qs = buildExportQuery();
    window.location.href = 'export_pdf.php' + (qs ? ('?' + qs) : '');
});
