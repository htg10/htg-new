<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Testing</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</head>

<body>
    <div class="container">
        <div class="form-group mt-5">
            <input type="text" id="company_name" placeholder="Type company name">
        </div>
        <div class="form-group mt-5">
            <input type="text" id="company_address" placeholder="Company address" readonly>
        </div>
        <div class="form-group mt-5">
            <input type="text" id="company_phone" placeholder="Company phone" readonly>
        </div>
    </div>


    <!-- jQuery script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#company_name').on('keyup', function() {
                let companyName = $(this).val();

                if (companyName.length > 0) {
                    $.ajax({
                        url: `/get-company-data/${companyName}`,
                        type: 'GET',
                        success: function(data) {
                            if (data) {
                                $('#company_address').val(data.address);
                                $('#company_phone').val(data.contactno);
                            } else {
                                $('#company_address').val('');
                                $('#company_phone').val('');
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }
                    });
                } else {
                    $('#company_address').val('');
                    $('#company_phone').val('');
                }
            });
        });
    </script>

</body>

</html>
