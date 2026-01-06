@php
$siteUrl = env('APP_URL');
@endphp

@extends('layout.default')

@section('content')
    @include('account.header')

    <div class="wrapper">
        <div class="d-flex align-items-start justify-content-between">
            @include('element.sidebar')
            <div class="main dashboard w-100 rounded shadow" style="width: 100% !important; margin-left: 1.5rem; margin-right: 1.5rem;">

                <nav class="bg-white d-flex align-items-center ps-md-3 justify-content-between">
                    <h6>Dashboard</h6>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link rounded-0 active" id="nav-Analatics-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-Analatics" type="button" role="tab" aria-controls="nav-Analatics"
                            aria-selected="true"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17.2 14C17.477 14 17.6155 14 17.7278 14.0615C17.8204 14.1122 17.9065 14.2075 17.9478 14.3047C17.9978 14.4225 17.9852 14.5479 17.96 14.7987C17.8296 16.0987 17.3822 17.3514 16.6518 18.4445C15.7727 19.7601 14.5233 20.7855 13.0615 21.391C11.5997 21.9965 9.99113 22.155 8.43928 21.8463C6.88743 21.5376 5.46197 20.7757 4.34315 19.6568C3.22433 18.538 2.4624 17.1126 2.15372 15.5607C1.84504 14.0089 2.00347 12.4003 2.60897 10.9385C3.21447 9.47671 4.23985 8.22728 5.55544 7.34823C6.64856 6.61783 7.90125 6.17039 9.20131 6.03995C9.45207 6.01479 9.57745 6.00221 9.69528 6.0522C9.79249 6.09344 9.88776 6.17964 9.9385 6.27224C10 6.38449 10 6.52299 10 6.79999V13.2C10 13.48 10 13.62 10.0545 13.727C10.1024 13.8211 10.1789 13.8976 10.273 13.9455C10.38 14 10.52 14 10.8 14H17.2Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M14 2.79999C14 2.52298 14 2.38448 14.0615 2.27223C14.1122 2.17963 14.2075 2.09344 14.3047 2.0522C14.4225 2.0022 14.5479 2.01478 14.7987 2.03993C16.6271 2.22333 18.346 3.03229 19.6569 4.34313C20.9677 5.65398 21.7767 7.37289 21.9601 9.20129C21.9852 9.45206 21.9978 9.57744 21.9478 9.69527C21.9055 9.79248 21.8204 9.88774 21.7278 9.93848C21.6155 9.99998 21.477 9.99999 21.2 9.99999L14.8 9.99999C14.52 9.99999 14.38 9.99999 14.273 9.94549C14.1789 9.89755 14.1024 9.82106 14.0545 9.72698C14 9.62003 14 9.48001 14 9.19999V2.79999Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg> &nbsp; Analatics</button>
                        <button class="nav-link rounded-0" id="nav-Revenue-tab" data-bs-toggle="tab"
                            data-bs-target="#nav-revenue" type="button" role="tab" aria-controls="nav-Revenue"
                            aria-selected="false"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M13.5295 8.35186C12.9571 8.75995 12.2555 9 11.5 9C9.567 9 8 7.433 8 5.5C8 3.567 9.567 2 11.5 2C12.753 2 13.8522 2.65842 14.4705 3.64814M6 20.0872H8.61029C8.95063 20.0872 9.28888 20.1277 9.61881 20.2086L12.3769 20.8789C12.9753 21.0247 13.5988 21.0388 14.2035 20.9214L17.253 20.3281C18.0585 20.1712 18.7996 19.7854 19.3803 19.2205L21.5379 17.1217C22.154 16.5234 22.154 15.5524 21.5379 14.9531C20.9832 14.4134 20.1047 14.3527 19.4771 14.8103L16.9626 16.6449C16.6025 16.9081 16.1643 17.0498 15.7137 17.0498H13.2855L14.8311 17.0498C15.7022 17.0498 16.4079 16.3633 16.4079 15.5159V15.2091C16.4079 14.5055 15.9156 13.892 15.2141 13.7219L12.8286 13.1417C12.4404 13.0476 12.0428 13 11.6431 13C10.6783 13 8.93189 13.7988 8.93189 13.7988L6 15.0249M20 6.5C20 8.433 18.433 10 16.5 10C14.567 10 13 8.433 13 6.5C13 4.567 14.567 3 16.5 3C18.433 3 20 4.567 20 6.5ZM2 14.6L2 20.4C2 20.9601 2 21.2401 2.10899 21.454C2.20487 21.6422 2.35785 21.7951 2.54601 21.891C2.75992 22 3.03995 22 3.6 22H4.4C4.96005 22 5.24008 22 5.45399 21.891C5.64215 21.7951 5.79513 21.6422 5.89101 21.454C6 21.2401 6 20.9601 6 20.4V14.6C6 14.0399 6 13.7599 5.89101 13.546C5.79513 13.3578 5.64215 13.2049 5.45399 13.109C5.24008 13 4.96005 13 4.4 13L3.6 13C3.03995 13 2.75992 13 2.54601 13.109C2.35785 13.2049 2.20487 13.3578 2.10899 13.546C2 13.7599 2 14.0399 2 14.6Z"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg> &nbsp; Revenue</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="nav-Analatics" role="tabpanel"
                        aria-labelledby="nav-Analatics-tab">
                        <div class="d-flex p-3 bg-light justify-content-start align-items-center mb-3 gap-2 filter-tabs">
                            <div class="d-flex gap-2">
                                <button class="btn border-0 filter-btn" data-filter="today"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg> &nbsp; Today</button>
                                <button class="btn border-0 filter-btn active" data-filter="week"><svg width="100%" height="100%" viewBox="0 0 24 24"
                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg> &nbsp; Week</button>
                                <button class="btn border-0 filter-btn" data-filter="month"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg> &nbsp; Month</button>
                                <button class="btn border-0 filter-btn" data-filter="year"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg> &nbsp; Year</button>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" class="form-control form-control-sm date-filter" style="width:auto;" value="{{ $currentDate }}">
                            </div>
                        </div>

                        <div class="row g-3 justify-content-start p-2 my-3 rounded mx-1" id="analyticsData">
                            @include('dashboard.partials.analytics', ['data' => $dashboardData])
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-revenue" role="tabpanel" aria-labelledby="nav-revenue-tab">
                        <div class="d-flex p-3 bg-light justify-content-start align-items-center mb-3 gap-2 filter-tabs">
                            <button class="btn border-0 filter-btn" data-filter="today"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg> &nbsp; Today</button>
                            <button class="btn border-0 filter-btn active" data-filter="week"><svg width="100%" height="100%" viewBox="0 0 24 24"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg> &nbsp; Week</button>
                            <button class="btn border-0 filter-btn" data-filter="month"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg> &nbsp; Month</button>
                            <button class="btn border-0 filter-btn" data-filter="year"><svg width="100%" height="100%" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M21 10H3M16 2V6M8 2V6M9 16L11 18L15.5 13.5M7.8 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V8.8C21 7.11984 21 6.27976 20.673 5.63803C20.3854 5.07354 19.9265 4.6146 19.362 4.32698C18.7202 4 17.8802 4 16.2 4H7.8C6.11984 4 5.27976 4 4.63803 4.32698C4.07354 4.6146 3.6146 5.07354 3.32698 5.63803C3 6.27976 3 7.11984 3 8.8V17.2C3 18.8802 3 19.7202 3.32698 20.362C3.6146 20.9265 4.07354 21.3854 4.63803 21.673C5.27976 22 6.11984 22 7.8 22Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg> &nbsp; Year</button>
                            <div class="d-flex align-items-center gap-2">
                                <input type="date" class="form-control form-control-sm date-filter" style="width:auto;" value="{{ $currentDate }}">
                            </div>
                        </div>

                        <div class="row g-4 mb-3 p-4" id="revenueData">
                            @include('dashboard.partials.revenue', ['data' => $dashboardData])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('element.footer')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentFilter = '{{ $currentFilter }}';
            let currentDate = '{{ $currentDate }}';

            // Filter button click handler
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                loadDashboardData();
            });

            // Date change handler
            $('.date-filter').on('change', function() {
                currentDate = $(this).val();
                loadDashboardData();
            });

            function loadDashboardData() {
                $.ajax({
                    url: '{{ route("dashboard.data") }}',
                    type: 'GET',
                    data: {
                        filter: currentFilter,
                        date: currentDate
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            updateAnalyticsData(response.data);
                            updateRevenueData(response.data);
                        }
                    },
                    error: function() {
                        if (typeof swal === 'function') {
                            swal("Error!", 'Error loading dashboard data', "error");
                        }
                    }
                });
            }

            function updateAnalyticsData(data) {
                // Update arrival
                updateRadialChart('.progress-arrival', data.arrivals.percentage);
                $('#arrivalCompleted').text(data.arrivals.completed);
                $('#arrivalPending').text(data.arrivals.pending);
                $('#arrivalPercentage').text(data.arrivals.percentage + '%');

                // Update departure
                updateRadialChart('.progress-departure', data.departures.percentage);
                $('#departureCheckedOut').text(data.departures.checkedOut);
                $('#departurePending').text(data.departures.pending);
                $('#departurePercentage').text(data.departures.percentage + '%');

                // Update in-house
                updateRadialChart('.progress-inhouse', data.inHouse.percentage);
                $('#inHouseAdults').text(data.inHouse.adults);
                $('#inHouseChildren').text(data.inHouse.children);
                $('#inHousePercentage').text(data.inHouse.percentage + '%');

                // Update room status
                updateRadialChart('.progress-room', data.roomStatus.percentage);
                $('#roomVacant').text(data.roomStatus.vacant);
                $('#roomComplimentary').text(data.roomStatus.complimentary);
                $('#roomPercentage').text(data.roomStatus.percentage + '%');

                // Update housekeeping
                updateHousekeepingBars(data.housekeeping);

                // Update activity feeds
                updateActivityFeeds(data.activityFeeds);
            }

            function updateRevenueData(data) {
                $('#occupancy').text(data.revenue.occupancy + '%');
                $('#pickup').text(data.revenue.pickup);
                $('#cancellations').text(data.revenue.cancellations);
                $('#alos').text(data.revenue.alos);
                $('#avgLeadTime').text(data.revenue.avgLeadTime);
                $('#adr').text('Rs ' + formatNumber(data.revenue.adr));
                $('#revenue').text('Rs ' + formatNumber(data.revenue.revenue));
                $('#revpar').text('Rs ' + formatNumber(data.revenue.revpar));
                $('#revpac').text('Rs ' + formatNumber(data.revenue.revpac));
            }

            function updateRadialChart(selector, percentage) {
                const circumference = 276;
                const offset = circumference - (percentage / 100) * circumference;
                $(selector).attr('stroke-dashoffset', offset);
            }

            function updateHousekeepingBars(data) {
                const maxValue = Math.max(data.clean, data.assign, data.dirty, data.block);
                $('.hk-bar.clean').css('width', (data.clean / maxValue * 100) + '%');
                $('.hk-bar.assign').css('width', (data.assign / maxValue * 100) + '%');
                $('.hk-bar.dirty').css('width', (data.dirty / maxValue * 100) + '%');
                $('.hk-bar.block').css('width', (data.block / maxValue * 100) + '%');
                $('.hk-bar-wrap').each(function() {
                    const value = $(this).find('.hk-value').text();
                    $(this).find('.hk-value').text(value);
                });
            }

            function updateActivityFeeds(feeds) {
                const feedsHtml = feeds.map(feed => `
                    <li class="d-flex justify-content-between align-items-center mb-2">
                        <p class="small-text w-75">${feed.message}</p>
                        <div class="btn btn-sm text-white ${feed.class}">${feed.type}</div>
                    </li>
                `).join('');
                $('#activityFeedsList').html(feedsHtml);
            }

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
        });
    </script>
@endsection

