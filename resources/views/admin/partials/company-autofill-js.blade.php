{{--
    Company suggestion + autofill for the contract forms.

    Params:
      $suggestUrl  route that returns { companies: [...] }
      $dataUrl     base for the per-company lookup, company name appended
--}}
<script>
    $(document).ready(function () {

        const FIELDS = ['email', 'address', 'contactno', 'gst', 'type', 'type1',
                        'contact', 'payment', 'bdmname', 'remark', 'state'];

        let suggestTimer;

        // Populate company suggestions dynamically
        $('#company_name').on('keyup', function () {
            let query = $(this).val();
            if (!query.length) return;

            clearTimeout(suggestTimer);
            suggestTimer = setTimeout(function () {
                $.ajax({
                    url: `{{ $suggestUrl }}`,
                    type: 'GET',
                    data: { query: query },
                    success: function (response) {
                        if (response.companies) {
                            let options = '';
                            response.companies.forEach(function (company) {
                                options += `<option value="${company.company}">`;
                            });
                            $('#company_list').html(options);
                            $('#company_name').attr('list', 'company_list');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                    }
                });
            }, 250);
        });

        // Autofill form fields based on selected company
        $('#company_name').on('change', function () {
            let companyName = $(this).val();

            if (!companyName.length) {
                clearFormFields();
                return;
            }

            $.ajax({
                url: `{{ $dataUrl }}/${encodeURIComponent(companyName)}`,
                type: 'GET',
                success: function (data) {
                    if (data) {
                        FIELDS.forEach(function (f) {
                            $('#' + f).val(data[f] || '');
                        });
                    } else {
                        clearFormFields();
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    clearFormFields();
                }
            });
        });

        function clearFormFields() {
            FIELDS.forEach(function (f) {
                $('#' + f).val('');
            });
        }
    });
</script>

<script>
    function calculateTotals() {
        let totalAmount = 0;
        let receivedAmount = 0;

        // Sum all total_amount fields
        document.querySelectorAll('.total-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            totalAmount += value;
        });

        // Sum all paid_amount fields
        document.querySelectorAll('.paid-amount').forEach(input => {
            const value = parseFloat(input.value) || 0;
            receivedAmount += value;
        });

        // Update the total and received amount fields
        document.getElementById('totalAmount').value = totalAmount.toFixed(2);
        document.getElementById('receivedAmount').value = receivedAmount.toFixed(2);

        const bal = document.getElementById('balanceAmount');
        if (bal) bal.textContent = (totalAmount - receivedAmount).toFixed(2);
    }
</script>
