{{--
    Behaviour for admin/partials/product-matrix.
    Include inside @section('script').

    NOTE: the original pages re-loaded jQuery and Bootstrap from a CDN here.
    The layout already loads both — loading jQuery a second time replaces
    window.$ and drops every plugin registered against the first copy
    (DataTables, Select2, dropify), so those tags are gone.
--}}
<script>
    $(document).ready(function () {

        function syncRow(cb) {
            var id = cb.id.replace('checkbox', '');
            $('#fields' + id).toggle(cb.checked);
            $(cb).closest('.htg-prod').toggleClass('is-on', cb.checked);
        }

        function countSelected() {
            $('#productCount').text($('.toggle-fields:checked').length);
        }

        // Reveal rows that are already ticked on load (edit / validation replay).
        $('.toggle-fields').each(function () {
            syncRow(this);
        });
        countSelected();

        $('.toggle-fields').on('change', function () {
            syncRow(this);
            countSelected();
        });

        // Filter the list. Purely visual — hidden rows keep their values and
        // still submit, so a filtered view never silently drops a product.
        $('#productSearch').on('input', function () {
            var q = $(this).val().trim().toLowerCase();
            var shown = 0;

            $('#productRows .htg-prod').each(function () {
                var match = !q || $(this).data('name').indexOf(q) !== -1;
                $(this).toggle(match);
                if (match) shown++;
            });

            $('#productNoMatch').prop('hidden', shown !== 0);
        });

    });
</script>
