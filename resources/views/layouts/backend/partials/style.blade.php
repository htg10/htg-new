<!-- App favicon -->
<link rel="shortcut icon" href="{{ asset('assets/favicon.png') }}">

<!-- Typography: Inter. Figures use its tabular-numeral feature, no second face. -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;450;500;550;600;650;700&display=swap"
    rel="stylesheet">

<!-- Bootstrap Css -->
<link href="{{ asset('assets/admin/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- Select2 css -->
<link href="{{ asset('assets/admin/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/admin/libs/spectrum-colorpicker2/spectrum.min.css') }}" rel="stylesheet" type="text/css">

<!-- Datepicker Css -->
<link href="{{ asset('assets/admin/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet"
    type="text/css">
<link rel="stylesheet" href="{{ asset('assets/admin/libs/@chenfengyuan/datepicker/datepicker.min.css') }}">

<!-- dropify file-upload css -->
<link rel="stylesheet" href="{{ asset('assets/admin/libs/dropify/dropify.min.css') }}">

<!-- DataTables -->
<link href="{{ asset('assets/admin/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/admin/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />

<!-- Responsive datatable examples -->
<link href="{{ asset('assets/admin/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
    rel="stylesheet" type="text/css" />

<!-- Sweet Alert-->
<link href="{{ asset('assets/admin/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

<!-- App Css-->
<link href="{{ asset('assets/admin/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/admin/css/style.css') }}" rel="stylesheet" type="text/css" />

<!-- Icons Css -->
<link href="{{ asset('assets/admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<!-- include summernote css -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

{{-- HTG Ledger theme — must stay last so it wins the cascade --}}
<link href="{{ asset('assets/admin/css/htg-theme.css') }}?v=2.0" rel="stylesheet" type="text/css" />

{{-- Apply the stored colour mode before first paint so there is no flash --}}
<script>
    (function () {
        try {
            var m = localStorage.getItem('htg.theme') || 'light';
            document.documentElement.setAttribute('data-htg-theme', m);
        } catch (e) {
            document.documentElement.setAttribute('data-htg-theme', 'light');
        }
    })();
</script>
