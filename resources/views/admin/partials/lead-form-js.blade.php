{{--
    Behaviour shared by the lead create / edit forms.

    Params:
      $requireLocation  true on create (the controller requires lat/lng there)

    NOTE: the original pages re-loaded jQuery and Bootstrap from a CDN here.
    The layout already loads both — a second jQuery replaces window.$ and drops
    every plugin bound to the first copy (DataTables, Select2, dropify), so
    those tags are gone.
--}}

@php $requireLocation = $requireLocation ?? false; @endphp

<script>
    /* ---- product picker ------------------------------------------------ */
    $(document).ready(function () {
        function refreshProducts() {
            $('.lead-product').each(function () {
                $(this).closest('.form-check').toggleClass('is-on', this.checked);
            });
            $('#leadProductCount').text($('.lead-product:checked').length);
        }

        refreshProducts();
        $('.lead-product').on('change', refreshProducts);
    });

    /* ---- geolocation --------------------------------------------------- */
    function setGeoState(message, tone) {
        const status = document.getElementById('location_status');
        if (!status) return;

        status.classList.remove('text-danger', 'text-success', 'text-muted');
        status.classList.add(tone || 'text-muted');
        status.innerHTML = message;
    }

    function getLeadLocation() {
        if (!navigator.geolocation) {
            setGeoState('This browser cannot share a location.', 'text-danger');
            return;
        }

        setGeoState('<i class="bx bx-loader-alt bx-spin"></i> Finding your location…');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                const mapUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('location_accuracy').value = accuracy;
                document.getElementById('location_url').value = mapUrl;

                setGeoState(
                    `<i class="bx bx-check-circle"></i> Location saved, accurate to about ${Math.round(accuracy)} m. ` +
                    `<a href="${mapUrl}" target="_blank" rel="noopener">Open in Maps</a>`,
                    'text-success'
                );
            },
            function (error) {
                let msg = 'Could not read your location.';

                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Location permission was denied. Allow it in your browser settings, then try again.';
                } else if (error.code === error.POSITION_UNAVAILABLE) {
                    msg = 'Your location is unavailable right now. Check that GPS is on.';
                } else if (error.code === error.TIMEOUT) {
                    msg = 'That took too long. Try again with a clearer view of the sky.';
                }

                setGeoState('<i class="bx bx-error-circle"></i> ' + msg, 'text-danger');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
</script>

@if ($requireLocation)
    <script>
        /* The controller requires latitude and longitude, so stop the submit
           here rather than letting the server bounce the whole form back. */
        document.getElementById('leadCreateForm').addEventListener('submit', function (e) {
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;

            if (latitude && longitude) return;

            e.preventDefault();

            setGeoState('<i class="bx bx-error-circle"></i> Capture the location before saving this lead.',
                'text-danger');

            document.getElementById('leadLocationCard').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Location needed',
                    text: 'Choose "Use current location" so this lead is recorded with where you met them.',
                    confirmButtonText: 'Got it'
                });
            }
        });
    </script>
@endif
