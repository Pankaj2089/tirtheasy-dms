@extends('layout.calendar')
@section('calendar-content')

<div class="main-bg">
    <div class="database-box">
        <div class="d-flex flex-column database-top pb-0 px-0">
            <div class="tp d-flex justify-content-between px-3 align-items-center w-100">
                <h4 class="fw-semibold mb-0 title">Guest Database</h4>
                <div class="d-flex gap-3">
                    <a href="{{ route('guest.database.export') }}" class="btn export-btn" id="exportBtn">Export</a>
                </div>
            </div>
            <table class="table align-middle mt-3 mb-0 guest-table">
                <thead>
                    <tr>
                        <th>Guest Name</th>
                        <th>Phone No.</th>
                        <th>Source</th>
                        <th>Rooms</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 guest-table">
                <tbody>
                    @forelse($guests as $guest)
                    <tr>
                        <td>{{ $guest['guest_name'] }}</td>
                        <td>{{ $guest['phone'] ?: '-' }}</td>
                        <td>{{ $guest['source'] }}</td>
                        <td>{{ $guest['rooms'] }}</td>
                        <td><a href="#" class="view-log">View Logs</a></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                No guest data found<br>
                                <small>Guests from your bookings will appear here</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Toggle sidebar menu on hamburger button click
        $(".humberger-ic").on("click", function () {
            $(".profile-sidebar").toggleClass("active");
        });

        // Close sidebar when close button is clicked
        $(".close-profile").on("click", function () {
            $(".profile-sidebar").removeClass("active");
        });

        // Handle export button click
        $('#exportBtn').on('click', function(e) {
            const $btn = $(this);
            const originalText = $btn.html();

            // Show loading state
            $btn.html('Exporting...').prop('disabled', true);

            // Reset button after a short delay (in case user stays on page)
            setTimeout(function() {
                $btn.html(originalText).prop('disabled', false);
            }, 2000);
        });
    });
</script>

@endsection
