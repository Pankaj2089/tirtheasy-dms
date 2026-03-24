<footer class="mobile-footer" style="display: none;">
    <a href="#" class="footer-item">
        <!-- Home Icon -->
        <img src="{{$siteUrl}}public/assets/img/dashboard.svg" alt="">
        <span>Dashboard</span>
    </a>
    <a href="#" class="footer-item">
        <!-- History Icon -->
        <img src="{{$siteUrl}}public/assets/img/views.svg" alt="">
        <span>Stay View</span>
    </a>
    <a href="#" class="footer-item">
        <!-- Profile Icon -->
        <img src="{{$siteUrl}}public/assets/img/reservation.svg" alt="">
        <span>Reservation</span>
    </a>
    <a href="#" class="footer-item">
        <!-- Order Icon -->
        <img src="{{$siteUrl}}public/assets/img/share.svg" alt="">
        <span>Share Link</span>
    </a>
</footer>
<script>
    function formatDate(dateStr) {
    let date = new Date(dateStr);
    return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

    function viewBookingDetails(bookingId, allIds, csrfToken) {
        // Close the SweetAlert
        if (typeof swal.close === 'function') {
            swal.close();
        }
        
        // Mark this booking as read
        $.ajax({
            url: `{{ url('/vendor/mark-bookings-read') }}`,
            method: "POST",
            data: {
                ids: [bookingId],
                _token: csrfToken
            }
        });
        
        // Open booking details
        showBookingSummary({id: bookingId});
    }

    let isAlertOpen = false;
    setInterval(function () {
        if (isAlertOpen) return;
    $.ajax({
        url:  `{{ url('/vendor/new-bookings') }}`,
        method: "GET",
        success: function (response) {

            if (response.status && response.data.length > 0) {
                isAlertOpen = true;
                let bookings = response.data;
                let ids = [];
                let html = `<div style="text-align:left">`;

                bookings.forEach(function (item) {
                    ids.push(item.id);

                    html += `
                        <div style="margin-bottom:10px;padding:10px;border-bottom:1px solid #ddd; font-size:12px;">
                            <b>Reservation  No.:</b> ${item.invoice_id} <br>
                            <b>Hotel:</b> ${item.hotel_name} <br>
                            <b>Room:</b> ${item.room_name} <br>
                            <b>Client Name:</b> ${item.billing_name} <br>
                            <b>Check-in Date:</b> ${formatDate(item.check_in_date)} <br>
                            <b>Check-out Date:</b> ${formatDate(item.check_out_date)} <br>
                            <button type="button" class="btn btn-sm btn-primary mt-2" onclick="viewBookingDetails(${item.id}, [${ids.join(',')}], '{{ csrf_token() }}')">View Booking Details</button>
                        </div>
                    `;
                });

                html += `</div>`;

                swal({
                        title: "New Booking Alert 🔔",
                        content: {
                            element: "div",
                            attributes: {
                                innerHTML: html
                            }
                        },
                        icon: "info"
                    }).then(() => {
                    isAlertOpen = false;
                    // Mark remaining bookings as read (if any weren't viewed)
                    $.ajax({
                        url:  `{{ url('/vendor/mark-bookings-read') }}`,
                        method: "POST",
                        data: {
                            ids: ids,
                            _token: "{{ csrf_token() }}"
                        }
                    });

                });

            }
        }
    });

}, 15000);
</script>