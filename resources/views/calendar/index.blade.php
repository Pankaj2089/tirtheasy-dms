@php
$siteUrl = env('APP_URL');
@endphp
@extends('layout.calendar')
@section('calendar-content')

<style>
    :root {
        --calendar-column-count: 7;
        --calendar-cell-min-width: 110px;
    }
    .cell-booked{
        display: none !important;
    }
    .selection-action-tooltip {
        position: absolute;
        z-index: 1200;
        min-width: 220px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        box-shadow: 0 0.75rem 1.5rem rgba(15, 23, 42, 0.15);
        padding: 14px 16px;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }
    .selection-action-tooltip .selection-action-title {
        font-size: 0.85rem;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #6c757d;
        margin-bottom: 6px;
    }
    .selection-action-tooltip .selection-action-range {
        font-size: 0.85rem;
        color: #495057;
        margin-bottom: 10px;
        line-height: 1.3;
    }
    .selection-action-tooltip .selection-action-duration {
        font-size: 0.82rem;
        color: #6c757d;
        margin-bottom: 10px;
    }
    .selection-action-tooltip .selection-action-btn {
        border: 1px solid transparent;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 0.9rem;
        font-weight: 600;
        display: block;
        width: 100%;
        text-align: left;
        transition: background-color 0.15s ease, border-color 0.15s ease;
    }
    .selection-action-tooltip .selection-action-btn + .selection-action-btn {
        margin-top: 6px;
    }
    .selection-action-tooltip .selection-action-btn[data-action="reservation"] {
        background-color: #dc3545;
        color: #fff;
    }
    .selection-action-tooltip .selection-action-btn[data-action="reservation"]:hover {
        background-color: #bb2d3b;
    }
    .selection-action-tooltip .selection-action-btn[data-action="maintenance"] {
        background-color: #f8f9fa;
        color: #212529;
        border-color: #dee2e6;
    }
    .selection-action-tooltip .selection-action-btn[data-action="maintenance"]:hover {
        background-color: #e9ecef;
    }
    .rooms-container.is-loading {
        opacity: 0.5;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
    .booking-label{
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 400;
    }
    .booking-label .booking-icon{
        width: 14px;
        height: 14px;
        object-fit: contain;
    }
    
</style>

   
<div class="calender-box">
    <!-- Calendar Grid -->
    <div class="calendar-container">
        <div class="">
            <!-- Date Header Navigation -->
            <div class="date-header-wrapper">
                <div class="bg-white border-bottom py-2 fillter">
                    <div class="container-fluid px-3">
                        <div class="row align-items-center g-2">
                            <!-- Date Selector -->
                            <div class="col-12 col-md-auto d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <!-- <span class="">04, October, 2025</span> -->
                                    <button class="btn btn-link text-dark px-3 py-0 cal">
                                        <input type="date" id="datePicker" placeholder="04, October, 2025" class="border-0">
                                        <!-- <img src="{{$siteUrl}}public/assets/img/cal-ic.svg" alt=""> -->
                                    </button>
                                </div>
                                <button class="btn btn-outline-danger btn-sm text-nowrap assign-mobile"
                                    data-bs-toggle="modal" data-bs-target="#assign">Assign Rooms</button>
                            </div>

                            <!-- Status Filters -->
                            <div class="col-12 col-md filter-md">
                                <div class="d-flex flex-wrap gap-2">
                                    <button class="bg-transparent">All <span
                                            class="badge bg-secondary">0</span></button>
                                    <button class="bg-transparent">Vacant <span
                                            class="badge bg-secondary">0</span></button>
                                    <button class="bg-transparent">Occupied <span
                                            class="badge bg-secondary">0</span></button>
                                    <button class="bg-transparent">Reserved <span
                                            class="badge bg-secondary">0</span></button>
                                    <button class="bg-transparent">Blocked <span
                                            class="badge bg-secondary">0</span></button>
                                    <button class="bg-transparent">Due Date <span
                                            class="badge bg-secondary">0</span></button>
                                </div>
                            </div>

                            <!-- Property Selector & Assign Button -->
                            <div class="col-12 col-md-auto">
                                <div class="d-flex gap-4">
                                    <select class="form-select form-select-sm">
                                        <option>Select Property</option>
                                        <option>Property 1</option>
                                        <option>Property 2</option>
                                    </select>

                                    <select class="form-select form-select-sm mobile-room"
                                        style="width: 210px; display: none;">
                                        <option>Room Type</option>
                                        <option>Villa</option>
                                        <option>Deluxe</option>
                                        <option>Suite</option>
                                    </select>
                                    <button class="btn btn-outline-danger btn-sm text-nowrap assign-desktop"
                                        data-bs-toggle="modal" data-bs-target="#assign">Assign Rooms</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="date-header-scroll shadow-sm">
                    <div class="date-header-container">
                        <div class="d-flex align-items-center px-3 bg-light desktop-room">
                            <select class="form-select form-select-sm" style="width: 210px;">
                                <option>Room Type</option>
                                <option>Villa</option>
                                <option>Deluxe</option>
                                <option>Suite</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center position-relative gap-2 flex-wrap flex-md-nowrap">
                            <div class="btn-group btn-group-sm view-toggle d-none" role="group" aria-label="Calendar view">
                                <button type="button" class="btn btn-outline-secondary active" data-view="week"
                                    id="weekViewBtn">Week</button>
                                <button type="button" class="btn btn-outline-secondary" data-view="month"
                                    id="monthViewBtn">Month</button>
                            </div>
                            <!-- <input type="date" class="form-control form-control-sm w-auto" id="datePicker"> -->
                            <button class="bg-transpaent border-0 arrow-slide left" id="prevRangeBtn">
                                <img src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt="">
                            </button>
                            <div class="date-columns flex-grow-1" id="dateColumns"></div>
                            <button class="bg-transpaent border-0 arrow-slide right" id="nextRangeBtn">
                                <img src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt="">
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Room Type Filter & Info -->
            <div class="border-bottom room-cat">
                <div class="container-fluid px-3">
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                        <div class="room-label-header"></div>
                    </div>
                </div>
            </div>

            <!-- Room Rows -->
            <div class="rooms-container">
                <!-- 4 ICR Villa Section -->
                



                <!-- Room Availability Footer -->
                <div class="availability-footer fixed-bottom border-top">
                    <div class="footer-row position-relative">
                        <div class="footer-label">Room Availability</div>
                        <div class="footer-grid">
                            <div class="footer-cell">34</div>
                            <div class="footer-cell">55</div>
                            <div class="footer-cell">68</div>
                            <div class="footer-cell">64</div>
                            <div class="footer-cell">66</div>
                            <div class="footer-cell">74</div>
                            <div class="footer-cell">78</div>
                            <div class="footer-cell">84</div>
                            <div class="footer-cell">86</div>
                            <div class="footer-cell">89</div>
                        </div>
                        <div class="drop-one"><img src="{{$siteUrl}}public/assets/img/arrow.svg" alt=""></div>
                    </div>
                    <div id="coll-one" style="display: none;">
                    </div>


                    <div class="footer-row">
                        <div class="footer-label">Occupancy (%)</div>
                        <div class="footer-grid">
                            <div class="footer-cell">72</div>
                            <div class="footer-cell">46</div>
                            <div class="footer-cell">35</div>
                            <div class="footer-cell">39</div>
                            <div class="footer-cell">23</div>
                            <div class="footer-cell">16</div>
                            <div class="footer-cell">13</div>
                            <div class="footer-cell">11</div>
                            <div class="footer-cell">07</div>
                            <div class="footer-cell">03</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<div id="selection-action-tooltip" class="selection-action-tooltip d-none">
    <div class="selection-action-title">Create for selection</div>
    <div class="selection-action-range" id="selection-action-range"></div>
    <div class="selection-action-duration" id="selection-action-duration"></div>
    <button type="button" class="selection-action-btn" data-action="reservation">
        Reservation
    </button>
    <button type="button" class="selection-action-btn" data-action="maintenance">
        Maintenance Block
    </button>
</div>

<!-- Legend -->
<div class="legend-container fixed-bottom pb-2">
    <div class="container-fluid px-3">
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <div class="legend-item">
                <span class="legend-dot" style="background: #f8d7a1;"></span>
                <span>Offline</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #ffb3b3;"></span>
                <span>Tirtheasy</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #dc3545;"></span>
                <span>Assign Room</span>
            </div>
            <div class="legend-item">
                <span class="legend-dot" style="background: #212529;"></span>
                <span>Maintenance</span>
            </div>
        </div>
    </div>
</div>
</div>

<!-- quick reservation popup start -->
<!-- Modal -->
<div class="modal fade" id="reservation-pop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><img
                src="{{$siteUrl}}public/assets/img/close.svg" alt=""></button>
        <div class="modal-body p-0">
            <h4>Quick Reservation</h4>


            <nav class="bl-tabs">
                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                        aria-selected="true">Normal Booking</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                        aria-selected="false">Group Booking</button>
                </div>
            </nav>
            <div class="tab-content bl-tab-cont" id="nav-tabContent">
                <div class="tab-pane fade active show" id="nav-home" role="tabpanel"
                    aria-labelledby="nav-home-tab">
                    <h5>Booking Details</h5>
                    <form id="quickReservationForm" class="bl-reserve-form position-relative" data-endpoint="{{ route('quickReservations.store') }}">
                        @csrf
                        <div id="quickReservationErrors" class="alert alert-danger small d-none"></div>
                        <div class="row align-items-end mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Check-In</label>
                                <div class="time">
                                    <input type="date" class="form-control" id="quickCheckInDate" name="check_in_date">
                                    <input type="time" class="form-control" id="quickCheckInTime" name="check_in_time">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Check-Out</label>
                                <div class="time">
                                    <input type="date" class="form-control" id="quickCheckOutDate" name="check_out_date">
                                    <input type="time" class="form-control" id="quickCheckOutTime" name="check_out_time">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label class="form-label">Rooms</label>
                                <input type="number" class="form-control" id="quickRoomsCount" name="total_rooms" readonly placeholder="0">
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label class="form-label">Reservation Type</label>
                                <select class="form-control" id="quickReservationType" name="reservation_type">
                                    <option value="confirmed">Confirm</option>
                                    <option value="hold">Hold</option>
                                    <option value="inquiry">Inquiry</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 d-flex align-items-center justify-content-between">
                                <h5 class="mt-3 mb-0">Room Details</h5>
                                <!-- <small class="text-muted">Add one entry per room and rate plan</small> -->
                            </div>
                            <div class="col-12">
                                <div id="roomDetailsContainer" class="room-details-container"></div>
                            </div>
                            <div class="col-md-3 add-rom text-center mx-auto">
                                <button type="button" class="theme-btn-text text-white w-100 h-auto" id="addRoomDetailBtn">+ Add Room</button>
                            </div>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-12">
                                <h5 class="mt-3">Guest Information</h5>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" id="quickGuestName" name="guest[name]" placeholder="Enter guest name">
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label class="form-label">Gender</label>
                                <select class="form-control" id="quickGuestGender" name="guest[gender]">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="quickGuestEmail" name="guest[email]" placeholder="sample@gmail.com">
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label class="form-label">Mobile</label>
                                <input type="tel" class="form-control" id="quickGuestMobile" name="guest[mobile]" placeholder="+91 0123456789">
                            </div>
                        </div>

                        <div class="row justify-content-between   position-absolute align-items-center reservation-footer w-100">
                            <hr>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0 fw-bold">Total</p>
                                    <p class="mb-0 fw-bold">Rs. <span id="quickTotalAmountLabel">0.00</span></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0">Paid</p>
                                    <p class="mb-0">Rs. <span id="quickPaidAmountLabel">0.00</span></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0 text-info">Balance</p>
                                    <p class="mb-0 text-info">Rs. <span id="quickBalanceAmountLabel">0.00</span></p>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-end gap-2">
                                <button type="submit" class="theme-btn text-white ms-auto text-center text-decoration-none" id="quickReservationSubmitBtn">
                                    <span class="label-default">Confirm</span>
                                    <span class="label-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- quick reservation popup end-->

<!-- maintenance block popup start -->
<div class="modal fade" id="maintenance-block-pop" tabindex="-1" aria-labelledby="maintenanceBlockLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <img src="{{$siteUrl}}public/assets/img/close.svg" alt="">
        </button>
        <div class="modal-body p-4">
            <h4 id="maintenanceBlockLabel" class="mb-3">Maintenance Block</h4>
            <form class="maintenance-form" id="maintenanceForm">
                <input type="hidden" id="maintenanceRoomIdInput" name="room_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="maintenanceStartInput" name="start_date">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" id="maintenanceEndInput" name="end_date">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" rows="3" id="maintenanceReasonInput" name="reason" placeholder="Describe the maintenance work"></textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="blockDatesBtn" onclick="blockDates(event)" class="btn btn-dark">Block Dates</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
<!-- maintenance block popup end -->


<!--booking Modal -->
<div class="modal fade" id="booking-pop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <button type="button" class="btn-close left-close-button bg-white" data-bs-dismiss="modal"
            aria-label="Close"><img src="{{$siteUrl}}public/assets/img/close.svg" class="bg-white rounded-circle" alt=""></button>

        <div class="modal-body p-0">
            <div class="booking-popup">
                <div class="main-popup">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="p-3 top">
                                <div class="d-flex align-items-center">
                                    <div class="cat-badge"></div>
                                    <h6>Mr. Chiranjeev Singh </h6> &nbsp;
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt="">
                                </div>
                                <p>Folio #: <strong>35596895</strong> &nbsp; Date : <strong>08/01/2025</strong></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex justify-content-center pe-4">
                                <a href=""
                                    class="theme-btn-sm rounded text-white text-center text-decoration-none">Print/Send</a>
                            </div>
                        </div>
                    </div>

                    <nav>
                        <div class="nav nav-tabs d-flex justify-content-between" id="nav-tab" role="tablist">
                            <button class="nav-link border-0 rounded-0 active" id="Booking-tab"
                                data-bs-toggle="tab" data-bs-target="#Booking" type="button" role="tab"
                                aria-controls="Booking" aria-selected="true">Booking Details</button>
                            <button class="nav-link border-0 rounded-0" id="Payment-tab" data-bs-toggle="tab"
                                data-bs-target="#Payment" type="button" role="tab" aria-controls="Payment"
                                aria-selected="false">Payment Details</button>
                            <button class="nav-link border-0 rounded-0" id="Guest-tab" data-bs-toggle="tab"
                                data-bs-target="#Guest" type="button" role="tab" aria-controls="Guest"
                                aria-selected="false">Guest
                                Details</button>
                        </div>
                    </nav>
                    <div class="tab-content " id="nav-tabContent">
                        <div class="tab-pane p-3 fade active show book-cont" id="Booking" role="tabpanel"
                            aria-labelledby="Booking-tab">
                            <div class="bottom pb-2">
                                <ul class="d-flex flex-wrap justify-content-between">
                                    <li class="mb-4">
                                        <p>Reservation Number</p>
                                        <p><small class="">Rdo 0215635</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Arival Date</p>
                                        <p><small class="">19 Sept. 2025 11:25:22</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Booking Date</p>
                                        <p><small class="">15 Sept. 2025</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Departure Date</p>
                                        <p><small class="">20 Sept. 2025 10:35:20</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Rate Plan</p>
                                        <p><small class="">20 Sept. 2025</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Number</p>
                                        <p><small class="">V1205</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Type</p>
                                        <p><small class="">Luxury</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Number Of Guest</p>
                                        <p><small class="">13</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Booked</p>
                                        <p><small class="">10</small></p>
                                    </li>
                                    <li class="">
                                        <p>Rooms</p>
                                        <p><small class="">231, 263, 265, 266, 265,</small></p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-pane p-2 fade" id="Payment" role="tabpanel"
                            aria-labelledby="Payment-tab">
                            <div class="row">
                                <div class="col-md-6 px-0 border-box">
                                    <div class="sub-heading px-3 d-flex justify-content-between">
                                        <p class="mb-0"> Folio #: <strong> 35596895</strong></p>
                                        <p class="mb-0">Date : <strong>08/01/2025</strong></p>
                                    </div>
                                    <hr class="mt-2 mb-0">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2 ms-3">Datails</h5>
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Room Price</p>
                                                <p><strong>Rs. 2000</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Charges</p>
                                                <p><strong>Rs. 2000</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total SGST</p>
                                                <p><strong>Rs. 50</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total CGST</p>
                                                <p><strong>Rs. 50</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Extra Charges</p>
                                                <p><strong>Rs. 400</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Payable</p>
                                                <p><strong>Rs. 2500</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Balance</p>
                                                <p><strong>Rs. 2000</strong></p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="p-details">
                                        <h5 class="mt-4 mb-2 ms-3">Folio History</h5>
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Room Price</p>
                                                <p>Rs. 2000</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Charges</p>
                                                <p>Rs. 2000</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total SGST</p>
                                                <p>Rs. 50</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total CGST</p>
                                                <p>Rs. 50</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Extra Charges</p>
                                                <p>Rs. 400</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Payable</p>
                                                <p>Rs. 2500</p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Balance</p>
                                                <p>Rs. 2000</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6 bl-main-popup">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2">Add Charges</h5>
                                        <div class="row align-items-center mb-3">

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Room Charges</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="Normal">Normal</option>
                                                    <option value="Mint">Mint</option>
                                                    <option value="Deluxe">Deluxe</option>
                                                    <option value="Non Deluxe">Non Deluxe</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Room Quantity</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="Select">Select</option>
                                                </select>
                                            </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Comment</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                            </div>
                                            </div>

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Amount</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                            </div>
                                            </div>

                                            <div class="col-md-6 ms-auto d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <a href=""
                                                        class="theme-outline-btn-sm rounded text-center text-decoration-none">Cancel</a>
                                                </div>
                                                <div class="bl-btn">
                                                    <a href=""
                                                        class="theme-btn-sm rounded text-white text-center text-decoration-none">Save</a>
                                                </div>
                                            </div>

                                            <div class="col-md-12 px-0">
                                                <hr>
                                            </div>



                                        </div>
                                    </div>

                                        <div class="p-details">
                                        <h5 class="mt-3 mb-2">Add Payment</h5>
                                        <div class="row align-items-center mb-3">

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="check-out" class="form-label mt-0">Type</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="Normal">Normal</option>
                                                    <option value="Mint">Mint</option>
                                                    <option value="Deluxe">Deluxe</option>
                                                    <option value="Non Deluxe">Non Deluxe</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Method</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="Select">Select</option>
                                                </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Comment</label>
                                                <input type="text" placeholder="Add comm..." class="form-control bg-white comment">
                                            </div>
                                            </div>

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                <label for="check-out" class="form-label mt-0">Amount</label>
                                                <select name="" id="" class="form-control bg-white">
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>
                                            </div>
                                            </div>

                                            <div class="col-md-6 ms-auto d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <a href=""
                                                        class="theme-outline-btn-sm rounded text-center text-decoration-none">Cancel</a>
                                                </div>
                                                <div class="bl-btn">
                                                    <a href=""
                                                        class="theme-btn-sm rounded text-white text-center text-decoration-none">Save</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="Guest" role="tabpanel" aria-labelledby="Guest-tab">
                            <p>3</p>
                        </div>
                    </div>

                    <div class="row bg-white p-2 justify-content-between align-items-center border-top pop-up-footer">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <p class="mb-0 fw-bold">Total</p>
                                <p class="mb-0 fw-bold">Rs. 1,48,680</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <p class="mb-0">Paid</p>
                                <p class="mb-0">Rs. 0.00</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <p class="mb-0">Balance</p>
                                <p class="mb-0 text-danger">Rs. 1,48,680</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex w-100 justify-content-end">
                                <a href=""
                                    class="theme-btn-sm rounded text-white text-center text-decoration-none">Payment</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="book-cont">
                    <div class="details">
                        <div class="top">
                            <div class="btns">
                                <div class="head-menu">
                                    <ul
                                        class="d-flex align-items-center justify-content-between list-style-none pe-2">
                                        <li class=""><a href="" class="theme-btn text-white">Edit</a></li>
                                        <li class="">
                                            <select name="" id="">
                                                <option value="">Options</option>
                                                <option value="">Undo Check In</option>
                                                <option value="">Amend Stey</option>
                                                <option value="">Room Move</option>
                                                <option value="">Exchange Room</option>
                                                <option value="">Stop Room Move</option>
                                                <option value="">Inclusion List</option>
                                                <option value="">Void Resevation</option>
                                            </select>
                                        </li>
                                        <li class=""><a href="">Print</a></li>
                                        <li class=""><a href="">Ask AI</a></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                        <div class="bottom pb-2">
                            <ul class="d-flex flex-wrap">
                                <li class="mb-3">
                                    <p>Reservation Number</p>
                                    <p><small class="">Rdo 0215635</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Arival Date</p>
                                    <p><small class="">19 Sept. 2025 11:25:22</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Booking Date</p>
                                    <p><small class="">15 Sept. 2025</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Departure Date</p>
                                    <p><small class="">20 Sept. 2025 10:35:20</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Rate Plan</p>
                                    <p><small class="">20 Sept. 2025</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Room Number</p>
                                    <p><small class="">V1205</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Room Type</p>
                                    <p><small class="">Luxury</small></p>
                                </li>
                                <li class="w-50 mb-3">
                                    <p>Number Of Guest</p>
                                    <p><small class="">13</small></p>
                                </li>
                                <li class="w-50">
                                    <p>Extra Metress</p>
                                    <p><small class="">...</small></p>
                                </li>
                            </ul>

                            <hr>

                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center my-1">
                                        <p class="mb-0 fw-bold">Total</p>
                                        <p class="mb-0 fw-bold">Rs. 1,48,680</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center my-1">
                                        <p class="mb-0">Paid</p>
                                        <p class="mb-0">Rs. 0.00</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center my-1">
                                        <p class="mb-0">Balance</p>
                                        <p class="mb-0 text-danger">Rs. 1,48,680</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex ">
                                        <a href=""
                                            class="theme-btn text-white w-100 text-center text-decoration-none">Payment</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sum">
                        <h4>Group Reserved</h4>
                        <hr>
                        <div class="box">
                            <h6 class="mb-2">Double Deluxe</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>

                            <h6 class="mb-2">Mint</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>

                            <h6 class="mb-2">Double Deluxe</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>

                            <h6 class="mb-2">Mint</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>
                            <h6 class="mb-2">Double Deluxe</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>

                            <h6 class="mb-2">Mint</h6>
                            <a class="room-box rounded bg-white shadow-sm p-3 py-2 mb-3">
                                <div class="d-flex flex-column">
                                    <p>Mr. Chiranjeev Singh</p>
                                    <p><span>RSN456-5</span></p>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <div class="d-flex flex-column w-50">
                                        <p>Room</p>
                                        <p><span>EM2</span></p>
                                    </div>

                                    <div class="d-flex flex-column w-50">
                                        <p>Pax</p>
                                        <p><span>M<sub>2</sub></span>&nbsp; <span>C<sub>3</sub></span></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>
</div>

<!-- assign room popup -->
<!-- Modal -->
<div class="modal fade" id="assign" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
aria-labelledby="assignLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header border-0 d-flex justify-content-between align-items-center">
            <h5 class="modal-title" id="quickReservationLabel">Assign Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><img
                    src="{{$siteUrl}}public/assets/img/close.svg" alt=""></button>
        </div>
        <div class="modal-body py-0">
            <div class="top d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center position-relative w-80">
                    <button class="bg-transpaent border-0 arrow-slide left"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg"
                            alt=""></button>
                    <div class="date-columns">
                        <div class="date-column">
                            <div class="date-cell selected">
                                <div class="day">MON</div>
                                <div class="date">05</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">TUE</div>
                                <div class="date">07</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">WED</div>
                                <div class="date">08</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">THU</div>
                                <div class="date">09</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">FRI</div>
                                <div class="date">10</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">SAT</div>
                                <div class="date">11</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">SUN</div>
                                <div class="date">12</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">MON</div>
                                <div class="date">13</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">TUE</div>
                                <div class="date">14</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">WED</div>
                                <div class="date">15</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">THU</div>
                                <div class="date">16</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">FRI</div>
                                <div class="date">17</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">MON</div>
                                <div class="date">18</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">TUE</div>
                                <div class="date">19</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">WED</div>
                                <div class="date">20</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                        <div class="date-column">
                            <div class="date-cell">
                                <div class="day">THU</div>
                                <div class="date">21</div>
                                <div class="month">OCT</div>
                            </div>
                        </div>
                    </div>
                    <button class="bg-transpaent border-0 arrow-slide right"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg"
                            alt=""></button>
                </div>
                <div class="date-cal">
                    <a href=""><img src="{{$siteUrl}}public/assets/img/cir-calendar.svg" alt=""></a>
                </div>
            </div>

            <div class="accordion" id="accordionExample">
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            1 - Family Room
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body border-0 p-0">
                            <div class="p-3 py-2 rounded my-2 bg-pink">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="username d-flex flex-column">
                                        <h4>Mr Rohan Patel</h4>
                                        <p>RSN649</p>
                                    </div>
                                    <a href=""><img src="{{$siteUrl}}public/assets/img/box-option.svg" alt=""></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div class="username d-flex flex-column w-50">
                                        <h3>Room Booked: <strong>25</strong></h3>
                                        <p>Total Guest: <strong>50 </strong></p>
                                        <p>Extra Metrress: <strong>5</strong></p>
                                    </div>
                                    <!-- <div class="d-flex flex-column w-50">
                                    <h3 class="mb-1">Room No.</h3>
                                    <div
                                        class="box d-flex px-2 py-1 justify-content-start flex-wrap align-items-center rounded border bg-white">
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                    </div>
                                    </div> -->
                                </div>

                                <div class="d-flex flex-column mt-2 align-items-start justify-content-start">
                                    <h3 class="mb-1">Room No.</h3>
                                    <div
                                        class="box d-flex px-2 py-1 justify-content-start flex-wrap align-items-center rounded border bg-white">
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                    </div>
                                </div>

                                <div class="d-flex mt-2 align-items-center justify-content-between">
                                    <p class="mb-0"><img src="{{$siteUrl}}public/assets/img/cal-ic.svg" width="13px" alt=""> &nbsp;
                                        <strong>03 mar To 04 Mar,
                                            2025</strong>
                                    </p>
                                    <a href="" class="theme-btn text-decoration-none outline">Assign Room</a>
                                </div>
                            </div>

                            <div class="p-3 py-2 rounded my-2 bg-pink">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="username d-flex flex-column">
                                        <h4>Mr Rohan Patel</h4>
                                        <p>RSN649</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div class="username d-flex flex-column">
                                        <h3>Room Booked: <strong>25</strong></h3>
                                        <p>Extra Metrress: <strong>5</strong></p>
                                    </div>
                                </div>

                                <div class="d-flex mt-2 align-items-center justify-content-between">
                                    <p class="mb-0"><img src="{{$siteUrl}}public/assets/img/cal-ic.svg" width="13px" alt=""> &nbsp;
                                        <strong>03 mar To 04 Mar,
                                            2025</strong>
                                    </p>
                                    <a href="" class="theme-btn text-decoration-none outline">Assign Room</a>
                                </div>
                            </div>

                            <div class="p-3 py-2 rounded my-2 bg-pink">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="username d-flex flex-column">
                                        <h4>Mr Rohan Patel</h4>
                                        <p>RSN649</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div class="username d-flex flex-column">
                                        <h3>Room Booked <strong>25</strong></h3>
                                        <p>Extra Metrress: <strong>5</strong></p>
                                    </div>
                                </div>

                                <div class="d-flex mt-2 align-items-center justify-content-between">
                                    <p class="mb-0"><img src="{{$siteUrl}}public/assets/img/cal-ic.svg" width="13px" alt=""> &nbsp;
                                        <strong>03 mar To 04 Mar,
                                            2025</strong>
                                    </p>
                                    <a href="" class="theme-btn text-decoration-none outline">Assign Room</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0">
                    <h2 class="accordion-header mb-2" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            1 - Mountain View
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body border-0 p-0">
                            <div class="p-3 py-2 rounded my-2 bg-pink">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="username d-flex flex-column">
                                        <h4>Mr Rohan Patel</h4>
                                        <p>RSN649</p>
                                    </div>
                                    <a href=""><img src="{{$siteUrl}}public/assets/img/box-option.svg" alt=""></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div class="username d-flex flex-column">
                                        <h3>Room Booked <strong>25</strong></h3>
                                        <p>Total Guest: <strong>50</strong> </p>
                                        <p>Extra Metrress: <strong>5</strong></p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column mt-2 align-items-start justify-content-start">
                                    <h3 class="mb-1">Room No.</h3>
                                    <div
                                        class="box d-flex px-2 py-1 justify-content-start flex-wrap align-items-center rounded border bg-white">
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                    </div>
                                </div>

                                <div class="d-flex mt-2 align-items-center justify-content-between">
                                    <p class="mb-0"><img src="{{$siteUrl}}public/assets/img/cal-ic.svg" width="13px" alt=""> &nbsp; 03 mar
                                        To 04 Mar, 2025
                                    </p>
                                    <a href="" class="theme-btn text-decoration-none outline">Assign Room</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            1 - Deluxe
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body border-0 p-0">
                            <div class="p-3 py-2 rounded my-2 bg-pink">
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="username d-flex flex-column">
                                        <h4>Mr Rohan Patel</h4>
                                        <p>RSN649</p>
                                    </div>
                                    <a href=""><img src="{{$siteUrl}}public/assets/img/box-option.svg" alt=""></a>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <div class="username d-flex flex-column">
                                        <h3>Room Booked <strong>25</strong></h3>
                                        <p>Total Guest: <strong>50</strong> </p>
                                        <p>Extra Metrress: <strong>5</strong></p>
                                    </div>
                                </div>

                                <div class="d-flex flex-column mt-2 align-items-start justify-content-start">
                                    <h3 class="mb-1">Room No.</h3>
                                    <div
                                        class="box d-flex px-2 py-1 justify-content-start flex-wrap align-items-center rounded border bg-white">
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                        <p class="mb-0 px-1">105A</p>
                                        <p class="mb-0 px-1">106A</p>
                                        <p class="mb-0 px-1">107A</p>
                                        <p class="mb-0 px-1">108A</p>
                                    </div>
                                </div>

                                <div class="d-flex mt-2 align-items-center justify-content-between">
                                    <p class="mb-0"><img src="{{$siteUrl}}public/assets/img/cal-ic.svg" width="13px" alt=""> &nbsp; 03 mar
                                        To 04 Mar, 2025
                                    </p>
                                    <a href="" class="theme-btn text-decoration-none outline">Assign Room</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- aassign room popup -->

<!-- Reservation New -->
<div class="modal fade  bl-reserve-tbl" id="reservation-new" tabindex="-1" aria-labelledby="quickReservationLabel"
aria-hidden="true">
<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <!-- Header -->
        <div class="modal-header d-flex flex-column p-0">
            <div class="tp p-3 d-flex justify-content-between align-items-center w-100">
                <h5 class="modal-title" id="quickReservationLabel">Quick Reservation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><img
                        src="{{$siteUrl}}public/assets/img/close.svg" alt=""></button>
            </div>
            <table class="table align-middle position-relative mb-0">
                <thead>
                    <tr>
                        <th>Date/ Time</th>
                        <th>Logs</th>
                        <th>User</th>
                        <th>Number</th>
                    </tr>
                </thead>
            </table>
        </div>

        <!-- Body -->
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table align-middle position-relative mb-0">
                    <tbody>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>PMSTrial@1</td>
                            <td>49.37.226.71</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.0.177</td>
                        </tr>
                        <tr>
                            <td>01-04-2025, 03:00pm</td>
                            <td>Add Guest</td>
                            <td>OTA</td>
                            <td>28.0.38.232</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>


<script>
$(function () {
    $(".drop-one").on("click", function () {
        $(".availability-footer").toggleClass("slide-box");
        $(this).toggleClass("rotate");
    });

    $(".humberger-ic").on("click", function () {
        $(".profile-sidebar").toggleClass("active");
    });

    $(".close-profile").on("click", function () {
        $(".profile-sidebar").removeClass("active");
    });
});

function blockDates(event) {
    if (event?.preventDefault) {
        event.preventDefault();
        event.stopPropagation();
    } else if (window.event?.preventDefault) {
        window.event.preventDefault();
        window.event.stopPropagation();
    }

    const form = document.getElementById('maintenanceForm');
    const startInput = document.getElementById('maintenanceStartInput');
    const endInput = document.getElementById('maintenanceEndInput');
    const reasonInput = document.getElementById('maintenanceReasonInput');
    const roomIdInput = document.getElementById('maintenanceRoomIdInput');
    const submitBtn = document.getElementById('blockDatesBtn');

    if (!form) {
        console.warn('Maintenance form not found.');
        return false;
    }

    const roomId = roomIdInput?.value?.trim();
    const startDate = startInput?.value?.trim();
    const endDate = endInput?.value?.trim();
    const reason = reasonInput?.value?.trim();

    if (!roomId || !startDate || !endDate || !reason) {
        swal("Error!", "Please fill the room, start date, end date and reason before blocking the dates.", "error");
        return false;
    }

    const startDateObj = new Date(startDate);
    const endDateObj = new Date(endDate);
    if (startDateObj > endDateObj) {
        swal("Error!", "End date cannot be before the start date.", "error");
        return false;
    }

    const actionUrl = "{{url('save-maintenance-block')}}";
    if (!actionUrl) {
        swal("Error!", "No action URL defined for maintenance form. Aborting AJAX submission.", "error");
        return false;
    }

    const formData = new FormData(form);
    formData.set('room_number_id', roomId);
    formData.set('start_date', startDate);
    formData.set('end_date', endDate);
    formData.set('reason', reason);

    submitBtn?.setAttribute('disabled', 'disabled');

    const requestHeaders = { 'X-Requested-With': 'XMLHttpRequest' };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrfToken) {
        requestHeaders['X-CSRF-TOKEN'] = csrfToken;
    }

    const method = (form.getAttribute('method') || 'POST').toUpperCase();

    fetch(actionUrl, {
        method,
        headers: requestHeaders,
        body: formData
    })
        .then((response) => {
            if (!response.ok) {
                return response.json().catch(() => ({})).then((payload) => {
                    throw new Error(payload.message || 'Unable to block dates.');
                });
            }
            return response.json().catch(() => ({}));
        })
        .then((payload) => {
            console.log('Maintenance block response:', payload);
            form.reset();
            if (roomIdInput) {
                roomIdInput.value = roomId;
            }
            const modalEl = document.getElementById('maintenance-block-pop');
            if (modalEl && window.bootstrap?.Modal) {
                const modalInstance = window.bootstrap.Modal.getInstance(modalEl);
                modalInstance?.hide();
            }
            // Refresh calendar data after modal is hidden
            if (typeof window.fetchCalendarDataForCurrentView === 'function') {
                window.fetchCalendarDataForCurrentView();
            }
            swal("Success!", "Dates blocked successfully.", "success");
        
        })
        .catch((error) => {
            console.error('Maintenance block error:', error);
            if (typeof swal === 'function') {
                swal("Error!", error.message || 'Unable to block dates. Please try again.', "error");
            } else {
                alert(error.message || 'Unable to block dates. Please try again.');
            }
        })
        .finally(() => {
            submitBtn?.removeAttribute('disabled');
        });

    return false;
}



</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dateColumnsEl = document.getElementById('dateColumns');
    const roomsContainer = document.querySelector('.rooms-container');
    const weekViewBtn = document.getElementById('weekViewBtn');
    const monthViewBtn = document.getElementById('monthViewBtn');
    const datePicker = document.getElementById('datePicker');
    const prevRangeBtn = document.getElementById('prevRangeBtn');
    const nextRangeBtn = document.getElementById('nextRangeBtn');
    const selectionTooltip = document.getElementById('selection-action-tooltip');
    const selectionTooltipRangeEl = document.getElementById('selection-action-range');
    const selectionTooltipDurationEl = document.getElementById('selection-action-duration');
    const maintenanceStartInput = document.getElementById('maintenanceStartInput');
    const maintenanceEndInput = document.getElementById('maintenanceEndInput');
    const maintenanceRoomIdInput = document.getElementById('maintenanceRoomIdInput');
    const maintenanceReasonInput = document.getElementById('maintenanceReasonInput');
    const maintenanceForm = document.getElementById('maintenanceForm');
    const calendarDataEndpoint = @json(route('calendar.data'));
    const assetBaseUrl = @json($siteUrl.'public/assets/img/');
    const DAY_IN_MS = 24 * 60 * 60 * 1000;

    if (!dateColumnsEl || !roomsContainer) {
        return;
    }

    const rootElement = document.documentElement;
    const roomsListEl = document.createElement('div');
    roomsListEl.className = 'room-category-list w-100';
    const emptyStateEl = document.createElement('div');
    emptyStateEl.id = 'roomsEmptyState';
    emptyStateEl.className = 'text-center py-4 text-muted small';
    emptyStateEl.textContent = 'No rooms available for the selected range.';
    emptyStateEl.style.display = 'none';

    roomsContainer.innerHTML = '';
    roomsContainer.appendChild(roomsListEl);
    roomsContainer.appendChild(emptyStateEl);

    if (selectionTooltip && selectionTooltip.parentElement !== document.body) {
        document.body.appendChild(selectionTooltip);
    }

    const BOOKING_STYLE_MAP = {
        reserved: 'booking-guest',
        group: 'booking-group',
        due: 'booking-due',
        maintenance: 'booking-maintenance',
        blocked: 'booking-maintenance'
    };

    const serverRoomsData = @json($rooms ?? []);
    const quickReservationServerRooms = @json($quickReservationRooms ?? []);
    const quickReservationRooms = Array.isArray(quickReservationServerRooms) ? quickReservationServerRooms : [];

    initQuickReservationForm(quickReservationRooms);
    const fallbackRoomsData = [
        {
            hostelName: "Room Category Name Here",
            title: "4 ICR Villa",
            summary: {
                defaultCount: 0,
                defaultPrice: 18000,
                tooltip: 'Available rooms / base rate',
                overrides: {
                    '2025-03-01': { count: 3, price: 18500 },
                    '2025-03-02': { count: 2, price: 18250 },
                    '2025-03-03': { count: 1, price: 19000 }
                }
            },
            rooms: [
                { id: 1, number: "1109" },
                { id: 2, number: "1201" },
                { id: 3, number: "1203" }
            ]
        },
        {
            hostelName: "Room Category Name Here",
            title: "Garden Suite",
            summary: {
                defaultCount: 1,
                defaultPrice: 15000,
                tooltip: 'Suites remaining / rack rate',
                overrides: {
                    '2025-03-02': { count: 0, price: 16000 },
                    '2025-03-05': { count: 2, price: 14800 }
                }
            },
            rooms: [
                { id: 4, number: "1210" },
                { id: 5, number: "1302" }
            ]
        },
        {
            hostelName: "Premium Suites",
            title: "ICR Deluxe",
            summary: {
                defaultCount: 2,
                defaultPrice: 22000,
                tooltip: 'Deluxe allocation / nightly rate',
                overrides: {
                    '2025-03-03': { count: 4, price: 21500 },
                    '2025-03-04': { count: 1, price: 23000 }
                }
            },
            rooms: [
                { id: 6, number: "2101" },
                { id: 7, number: "2102" }
            ]
        }
    ];

    let roomsData = (Array.isArray(serverRoomsData) && serverRoomsData.length) ? serverRoomsData : fallbackRoomsData;

    const serverBookingData = @json($bookings ?? []);
    let bookingData = Array.isArray(serverBookingData) ? serverBookingData : [];

    let normalizedRoomsData = [];
    let roomDictionary = new Map();
    let normalizedBookings = [];
    let bookingsByRoom = new Map();
    let bookingDateLookup = new Map();

    function refreshDerivedDatasets() {
        normalizedRoomsData = dedupeRoomCategories(roomsData);
        roomDictionary = buildRoomDictionary(normalizedRoomsData);
        normalizedBookings = normalizeBookings(bookingData);
        bookingsByRoom = groupBookingsByRoom(normalizedBookings, roomDictionary);
        bookingDateLookup = buildBookingDateLookup(bookingsByRoom);
    }

    refreshDerivedDatasets();

    function cloneSummary(summary) {
        if (!summary) {
            return null;
        }
        return {
            defaultCount: summary.defaultCount ?? null,
            defaultPrice: summary.defaultPrice ?? null,
            tooltip: summary.tooltip ?? null,
            overrides: { ...(summary.overrides || {}) }
        };
    }

    function mergeSummaries(target, source) {
        if (!source) {
            return target;
        }
        const merged = target || {
            defaultCount: null,
            defaultPrice: null,
            tooltip: null,
            overrides: {}
        };
        if (source.defaultCount !== undefined && source.defaultCount !== null) {
            merged.defaultCount = source.defaultCount;
        }
        if (source.defaultPrice !== undefined && source.defaultPrice !== null) {
            merged.defaultPrice = source.defaultPrice;
        }
        if (source.tooltip) {
            merged.tooltip = source.tooltip;
        }
        merged.overrides = {
            ...(merged.overrides || {}),
            ...(source.overrides || {})
        };
        return merged;
    }

    function dedupeRoomCategories(data) {
        const seen = new Map();
        data.forEach((category) => {
            const key = `${category.hostelName}|${category.title}`;
            const existing = seen.get(key);
            if (existing) {
                const existingIds = new Set(existing.rooms.map((room) => room.id));
                category.rooms.forEach((room) => {
                    if (!existingIds.has(room.id)) {
                        existing.rooms.push(room);
                        existingIds.add(room.id);
                    }
                });
                existing.summary = mergeSummaries(existing.summary, category.summary);
                return;
            }
            seen.set(key, {
                hostelName: category.hostelName,
                title: category.title,
                summary: cloneSummary(category.summary),
                rooms: category.rooms.slice()
            });
        });
        return Array.from(seen.values());
    }

    function buildRoomDictionary(data) {
        const map = new Map();
        data.forEach((category) => {
            (category.rooms || []).forEach((room) => {
                map.set(Number(room.id), {
                    number: room.number,
                    hostelName: category.hostelName,
                    title: category.title
                });
            });
        });
        return map;
    }

    function groupCategoriesByHostel(data) {
        const grouped = new Map();
        data.forEach((category) => {
            if (!grouped.has(category.hostelName)) {
                grouped.set(category.hostelName, []);
            }
            grouped.get(category.hostelName).push(category);
        });
        return grouped;
    }

    const dayFormatter = new Intl.DateTimeFormat('en-US', { weekday: 'short' });
    const monthFormatter = new Intl.DateTimeFormat('en-US', { month: 'short' });
    const currencyFormatter = new Intl.NumberFormat('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    const tooltipRangeFormatter = new Intl.DateTimeFormat('en-US', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    function getWeekStart(date) {
        const base = new Date(date);
        base.setHours(0, 0, 0, 0);
        const day = base.getDay();
        const diff = day === 0 ? -6 : 1 - day;
        base.setDate(base.getDate() + diff);
        return base;
    }

    const calendarState = {
        viewMode: 'week',
        referenceDate: getWeekStart(new Date()),
        visibleDates: []
    };

    const selectionState = {
        isDragging: false,
        activeRoomId: null,
        dragStartDate: null,
        dragEndDate: null,
        activeRoomMeta: null
    };

    const roomSelections = new Map();

    function replaceCalendarData(nextRooms, nextBookings) {
        roomsData = Array.isArray(nextRooms) ? nextRooms : [];
        bookingData = Array.isArray(nextBookings) ? nextBookings : [];
        refreshDerivedDatasets();
        roomSelections.clear();
        hideSelectionTooltip();
    }

    let activeSelectionContext = null;
    let lastCommittedSelectionContext = null;
    let suppressNextDocumentClick = false;
    const WEEK_DAY_COUNT = 10;

    function hideSelectionTooltip() {
        if (!selectionTooltip) {
            return;
        }
        selectionTooltip.classList.add('d-none');
        selectionTooltip.style.top = '-9999px';
        selectionTooltip.style.left = '-9999px';
        selectionTooltip.style.visibility = '';
        if (selectionTooltipRangeEl) {
            selectionTooltipRangeEl.textContent = '';
            selectionTooltipRangeEl.style.display = 'none';
        }
        if (selectionTooltipDurationEl) {
            selectionTooltipDurationEl.textContent = '';
            selectionTooltipDurationEl.style.display = 'none';
        }
        activeSelectionContext = null;
        suppressNextDocumentClick = false;
    }

    function formatTooltipRange(range) {
        if (!range?.start) {
            return '';
        }
        const startDate = parseISO(range.start);
        const endDate = range.end ? parseISO(range.end) : startDate;
        if (!startDate) {
            return '';
        }
        const startLabel = tooltipRangeFormatter.format(startDate);
        const endLabel = endDate ? tooltipRangeFormatter.format(endDate) : startLabel;
        return startLabel === endLabel ? startLabel : `${startLabel} - ${endLabel}`;
    }

    function formatTooltipDuration(range) {
        if (!range?.start) {
            return '';
        }
        const startDate = parseISO(range.start);
        const endDate = range.end ? parseISO(range.end) : startDate;
        if (!startDate) {
            return '';
        }
        const end = endDate || startDate;
        const durationMs = Math.abs(end.getTime() - startDate.getTime());
        const nights = Math.max(1, Math.round(durationMs / DAY_IN_MS) + 1);
        const label = nights === 1 ? '1 day' : `${nights} days`;
        return label;
    }

    function getCurrentViewRangeBounds() {
        const previewDates = generateVisibleDates();
        if (!previewDates.length) {
            return { startIso: null, endIso: null };
        }
        const startIso = toISO(previewDates[0]);
        const endIso = toISO(previewDates[previewDates.length - 1]);
        return { startIso, endIso };
    }

    function setCalendarLoading(isLoading) {
        roomsContainer?.classList.toggle('is-loading', Boolean(isLoading));
        if (roomsContainer) {
            roomsContainer.setAttribute('aria-busy', isLoading ? 'true' : 'false');
        }
    }

    async function fetchCalendarDataForCurrentView() {
        if (!calendarDataEndpoint) {
            return;
        }
        const { startIso, endIso } = getCurrentViewRangeBounds();
        if (!startIso || !endIso) {
            return;
        }
        try {
            setCalendarLoading(true);
            const params = new URLSearchParams({
                start_date: startIso,
                end_date: endIso
            });
            const fetchUrl = `${calendarDataEndpoint}?${params.toString()}`;
            const response = await fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) {
                const errorPayload = await response.json().catch(() => ({}));
                throw new Error(errorPayload.message || 'Unable to load calendar data.');
            }
            const payload = await response.json();
            replaceCalendarData(payload.rooms || [], payload.bookings || []);
            renderCalendar();
        } catch (error) {
            console.error('Calendar data refresh failed:', error);
            if (typeof swal === 'function') {
                swal("Error!", error.message || 'Unable to load calendar data.', "error");
            } else {
                alert(error.message || 'Unable to load calendar data.');
            }
        } finally {
            setCalendarLoading(false);
        }
    }

    // Make function globally accessible
    window.fetchCalendarDataForCurrentView = fetchCalendarDataForCurrentView;

    function populateMaintenanceModal(context) {
        if (!context?.range) {
            return;
        }
        if (maintenanceStartInput) {
            maintenanceStartInput.value = context.range.start || '';
        }
        if (maintenanceEndInput) {
            maintenanceEndInput.value = context.range.end || context.range.start || '';
        }
        if (maintenanceRoomIdInput) {
            maintenanceRoomIdInput.value = context.roomId || context.meta?.roomId || '';
        }
    }

    function populateQuickReservationDates(startDate, endDate) {
        const checkInInput = document.getElementById('quickCheckInDate');
        const checkOutInput = document.getElementById('quickCheckOutDate');
        const startValue = ensureISODate(startDate);
        const endValue = ensureISODate(endDate) || startValue;
        if (checkInInput && startValue) {
            checkInInput.value = startValue;
            checkInInput.dispatchEvent(new Event('change'));
        }
        if (checkOutInput && endValue) {
            checkOutInput.value = endValue;
            checkOutInput.dispatchEvent(new Event('change'));
        }
    }

    function getSelectionContextForModal() {
        if (activeSelectionContext?.range) {
            return activeSelectionContext;
        }
        if (lastCommittedSelectionContext?.range) {
            return lastCommittedSelectionContext;
        }
        return null;
    }

    function openModalById(modalId) {
        const modalEl = modalId ? document.getElementById(modalId) : null;
        if (!modalEl) {
            return;
        }
        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.removeAttribute('aria-hidden');
    }

    function showSelectionTooltipForRange(roomId, range, meta) {
        if (!selectionTooltip || !roomsListEl || !roomId || !range?.end) {
            return;
        }
        const targetCell = roomsListEl.querySelector(`.calendar-cell[data-room-id="${roomId}"][data-date="${range.end}"]`);
        if (!targetCell) {
            hideSelectionTooltip();
            return;
        }

        activeSelectionContext = { roomId, range, meta };
        suppressNextDocumentClick = true;
        if (selectionTooltipRangeEl) {
            const label = formatTooltipRange(range);
            selectionTooltipRangeEl.textContent = label;
            selectionTooltipRangeEl.style.display = label ? 'block' : 'none';
        }
        if (selectionTooltipDurationEl) {
            const durationLabel = formatTooltipDuration(range);
            selectionTooltipDurationEl.textContent = durationLabel;
            selectionTooltipDurationEl.style.display = durationLabel ? 'block' : 'none';
        }
        selectionTooltip.classList.remove('d-none');
        selectionTooltip.style.visibility = 'hidden';
        selectionTooltip.style.top = '-9999px';
        selectionTooltip.style.left = '-9999px';

        const cellRect = targetCell.getBoundingClientRect();
        const tooltipRect = selectionTooltip.getBoundingClientRect();
        const scrollY = window.scrollY || document.documentElement.scrollTop || 0;
        const scrollX = window.scrollX || document.documentElement.scrollLeft || 0;
        let top = cellRect.top + scrollY - tooltipRect.height - 12;
        if (top < scrollY + 16) {
            top = cellRect.bottom + scrollY + 12;
        }
        let left = cellRect.left + scrollX + (cellRect.width / 2) - (tooltipRect.width / 2);
        const maxLeft = scrollX + document.documentElement.clientWidth - tooltipRect.width - 16;
        left = Math.max(16, Math.min(left, maxLeft));

        selectionTooltip.style.top = `${top}px`;
        selectionTooltip.style.left = `${left}px`;
        selectionTooltip.style.visibility = 'visible';
    }

    function handleSelectionAction(action) {
        if (!action) {
            return;
        }
        if (action === 'reservation') {
            const context = getSelectionContextForModal();
            if (context?.range) {
                populateQuickReservationDates(context.range.start, context.range.end);
            }
            openModalById('reservation-pop');
        } else if (action === 'maintenance') {
            const context = getSelectionContextForModal();
            if (context?.range) {
                populateMaintenanceModal(context);
            }
            openModalById('maintenance-block-pop');
        }
        hideSelectionTooltip();
    }

    selectionTooltip?.addEventListener('click', (event) => {
        const button = event.target.closest('.selection-action-btn');
        if (!button) {
            return;
        }
        event.preventDefault();
        event.stopPropagation();
        handleSelectionAction(button.dataset.action);
    });

    document.addEventListener('click', (event) => {
        if (suppressNextDocumentClick) {
            suppressNextDocumentClick = false;
            return;
        }
        if (!selectionTooltip || selectionTooltip.classList.contains('d-none')) {
            return;
        }
        if (selectionTooltip.contains(event.target)) {
            return;
        }
        if (event.target.closest('.calendar-cell') && selectionState.isDragging) {
            return;
        }
        hideSelectionTooltip();
    });

    window.addEventListener('resize', hideSelectionTooltip);
    window.addEventListener('scroll', () => {
        if (!selectionTooltip || selectionTooltip.classList.contains('d-none')) {
            return;
        }
        hideSelectionTooltip();
    }, true);


    function toISO(date) {
        const d = new Date(date);
        d.setHours(0, 0, 0, 0);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function parseISO(value) {
        if (!value && value !== 0) {
            return null;
        }
        if (value instanceof Date) {
            const clone = new Date(value);
            clone.setHours(0, 0, 0, 0);
            return clone;
        }
        const safeValue = String(value).split('T')[0];
        const [year, month, day] = safeValue.split('-').map(Number);
        if ([year, month, day].some((segment) => Number.isNaN(segment))) {
            return null;
        }
        return new Date(year, month - 1, day);
    }

    function ensureISODate(value) {
        if (!value) {
            return null;
        }
        if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
            return value;
        }
        const parsed = parseISO(value);
        return parsed ? toISO(parsed) : null;
    }

    function normalizeBookings(data) {
        if (!Array.isArray(data)) {
            return [];
        }
        const normalized = [];
        data.forEach((entry, index) => {
            const roomIds = Array.isArray(entry.room_ids || entry.roomIds)
                ? (entry.room_ids || entry.roomIds)
                : [entry.room_id ?? entry.roomId];
            const start = ensureISODate(entry.start_date ?? entry.startDate);
            const end = ensureISODate(entry.end_date ?? entry.endDate ?? entry.start_date ?? entry.startDate);
            if (!roomIds?.length || !start) {
                return;
            }
            const normalizedRange = (start && end && start > end)
                ? { start: end, end: start }
                : { start, end: end || start };
            const isBlocked = Boolean(entry.is_blocked || entry.isBlocked);
            const inclusiveEnd = Boolean(entry.inclusive_end ?? entry.inclusiveEnd);
            const statusValue = (entry.status || '').toLowerCase() || (isBlocked ? 'blocked' : 'reserved');

            roomIds.filter(Boolean).forEach((roomId) => {
                normalized.push({
                    id: entry.id ?? `booking-${index}-${roomId}`,
                    roomId: Number(roomId),
                    startDate: normalizedRange.start,
                    endDate: normalizedRange.end,
                    label: entry.label || entry.guest_name || entry.guestName || 'Reserved',
                    tooltip: entry.tooltip || entry.notes || entry.source || '',
                    status: statusValue,
                    isBlocked,
                    inclusiveEnd: inclusiveEnd || isBlocked
                });
            });
        });
        return normalized;
    }

    function groupBookingsByRoom(bookings, dictionary = new Map()) {
        const grouped = new Map();
        bookings.forEach((booking) => {
            if (!grouped.has(booking.roomId)) {
                grouped.set(booking.roomId, []);
            }
            const roomMeta = dictionary.get(booking.roomId) || {};
            grouped.get(booking.roomId).push({
                ...booking,
                roomNumber: roomMeta.number || '',
                hostelName: roomMeta.hostelName || '',
                categoryTitle: roomMeta.title || '',
                cssClass: BOOKING_STYLE_MAP[booking.status] || BOOKING_STYLE_MAP.reserved
            });
        });
        return grouped;
    }

    function getBookingDisplayRange(booking) {
        if (!booking?.startDate) {
            return null;
        }
        const normalized = normalizeRange(booking.startDate, booking.endDate || booking.startDate);
        if (!normalized) {
            return null;
        }

        const startDate = parseISO(normalized.start);
        const endDate = parseISO(normalized.end);
        if (!startDate) {
            return null;
        }

        const inclusiveEnd = Boolean(booking.inclusiveEnd) || booking.isBlocked || booking.status === 'maintenance';

        if (!endDate || startDate.getTime() === endDate.getTime()) {
            return {
                start: toISO(startDate),
                end: toISO(startDate)
            };
        }

        const adjustedEnd = inclusiveEnd
            ? endDate
            : new Date(endDate.getTime() - DAY_IN_MS);

        if (adjustedEnd < startDate) {
            return {
                start: toISO(startDate),
                end: toISO(startDate)
            };
        }

        return {
            start: toISO(startDate),
            end: toISO(adjustedEnd)
        };
    }

    function buildBookingDateLookup(grouped) {
        const lookup = new Map();
        grouped.forEach((bookings, roomId) => {
            const dateMap = new Map();
            bookings.forEach((booking) => {
                const displayRange = getBookingDisplayRange(booking);
                if (!displayRange) {
                    return;
                }
                eachDateInRange(displayRange.start, displayRange.end, (iso) => {
                    if (!dateMap.has(iso)) {
                        dateMap.set(iso, booking);
                    }
                });
            });
            lookup.set(roomId, dateMap);
        });
        return lookup;
    }

    function eachDateInRange(startIso, endIso, callback) {
        const start = parseISO(startIso);
        const end = parseISO(endIso);
        if (!start || !end) {
            return;
        }
        const forward = start <= end ? { start, end } : { start: end, end: start };
        const cursor = new Date(forward.start);
        cursor.setHours(0, 0, 0, 0);
        while (cursor <= forward.end) {
            callback(toISO(cursor));
            cursor.setDate(cursor.getDate() + 1);
        }
    }

    function calculateSpan(startIso, endIso) {
        const start = parseISO(startIso);
        const end = parseISO(endIso);
        if (!start || !end) {
            return 1;
        }
        const diff = Math.floor((end.getTime() - start.getTime()) / (24 * 60 * 60 * 1000));
        return Math.max(diff, 0) + 1;
    }

    function getVisibleRangeIndices(booking, visibleDates) {
        const displayRange = getBookingDisplayRange(booking);
        if (!displayRange || !visibleDates.length) {
            return null;
        }

        const firstVisible = visibleDates[0];
        const lastVisible = visibleDates[visibleDates.length - 1];

        if (displayRange.end < firstVisible || displayRange.start > lastVisible) {
            return null;
        }

        const clampedStart = displayRange.start < firstVisible ? firstVisible : displayRange.start;
        const clampedEnd = displayRange.end > lastVisible ? lastVisible : displayRange.end;

        let startIndex = -1;
        let endIndex = -1;

        visibleDates.forEach((iso, index) => {
            if (iso < clampedStart || iso > clampedEnd) {
                return;
            }

            if (startIndex === -1) {
                startIndex = index;
            }
            endIndex = index;
        });

        if (startIndex === -1 || endIndex === -1 || startIndex > endIndex) {
            return null;
        }

        return { start: startIndex, end: endIndex };
    }

    function getBookingForCell(roomId, iso) {
        const roomMap = bookingDateLookup.get(roomId);
        return roomMap ? roomMap.get(iso) : null;
    }

    function isWeekend(date) {
        const day = date.getDay();
        return day === 0 || day === 6;
    }

    function generateVisibleDates() {
        const base = new Date(calendarState.referenceDate);
        base.setHours(0, 0, 0, 0);
        if (calendarState.viewMode === 'week') {
            const dates = [];
            for (let index = 0; index < WEEK_DAY_COUNT; index += 1) {
                const current = new Date(base);
                current.setDate(base.getDate() + index);
                dates.push(current);
            }
            return dates;
        }

        const firstDay = new Date(base.getFullYear(), base.getMonth(), 1);
        const daysInMonth = new Date(base.getFullYear(), base.getMonth() + 1, 0).getDate();
        const dates = [];
        for (let day = 1; day <= daysInMonth; day += 1) {
            dates.push(new Date(base.getFullYear(), base.getMonth(), day));
        }
        return dates;
    }

    function updateColumnCountStyles() {
        const count = calendarState.visibleDates.length || 0;
        rootElement.style.setProperty('--calendar-column-count', count || 1);
    }

    function renderDateHeader() {
        calendarState.visibleDates = generateVisibleDates();
        updateColumnCountStyles();
        dateColumnsEl.innerHTML = '';

        calendarState.visibleDates.forEach((date) => {
            const iso = toISO(date);
            const column = document.createElement('div');
            column.className = 'date-column';

            const cell = document.createElement('div');
            cell.className = 'date-cell-button';

            if (iso === toISO(new Date())) {
                cell.classList.add('is-today');
            }

            if (isWeekend(date)) {
                cell.classList.add('is-weekend');
            }

            cell.innerHTML = `
                <div class="day">${dayFormatter.format(date)}</div>
                <div class="date">${date.getDate().toString().padStart(2, '0')}</div>
                <div class="month">${monthFormatter.format(date)}</div>
            `;

            column.appendChild(cell);
            dateColumnsEl.appendChild(column);
        });
    }

    function buildCategoryHeader(label) {
        const wrapper = document.createElement('div');
        wrapper.className = 'border-bottom room-cat';
        wrapper.innerHTML = `
            <div class="container-fluid px-3">
                <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                    <div class="room-label-header">${label}</div>
                </div>
            </div>
        `;
        return wrapper;
    }

    function formatPrice(amount) {
        if (amount === null || amount === undefined) {
            return '';
        }
        return currencyFormatter.format(amount);
    }

    function getCategorySummary(category) {
        return category?.summary || {};
    }

    function getCategoryDailyMetrics(category, iso) {
        const summary = getCategorySummary(category);
        const overrides = summary.overrides || {};
        const override = overrides[iso];
        return {
            count: override?.count ?? summary.defaultCount ?? 0,
            price: override?.price ?? summary.defaultPrice ?? null,
            tooltip: override?.tooltip ?? summary.tooltip ?? null
        };
    }

    function buildTitleRow(category) {
        const row = document.createElement('div');
        row.className = 'room-row align-items-center';
        const label = document.createElement('div');
        label.className = 'room-name bg-white title fw-semibold';
        label.textContent = category.title;

        const grid = document.createElement('div');
        grid.className = 'room-grid';
        calendarState.visibleDates.forEach((date) => {
            const iso = toISO(date);
            const { count, price, tooltip } = getCategoryDailyMetrics(category, iso);
            const cell = document.createElement('div');
            cell.className = 'grid-cell bg-transparent border-0';
            const priceLabel = document.createElement('div');
            priceLabel.className = 'price-label';
            const priceMarkup = price !== null ? `<br>${formatPrice(price)}` : '';
            priceLabel.innerHTML = `<span>${count}</span>${priceMarkup || '<br>&nbsp;'}`;
            if (tooltip) {
                priceLabel.setAttribute('title', tooltip);
                priceLabel.dataset.bsToggle = 'tooltip';
            }
            cell.appendChild(priceLabel);
            grid.appendChild(cell);
        });

        row.appendChild(label);
        row.appendChild(grid);
        return row;
    }

    function buildRoomRow(room, category) {
        const row = document.createElement('div');
        row.className = 'room-row';
        const roomLabel = document.createElement('div');
        roomLabel.className = 'room-name bg-light';
        roomLabel.innerHTML = `<div class="fw-semibold">${room.number}</div>`;//<small class="text-muted">${category.title}</small>

        const grid = document.createElement('div');
        grid.className = 'room-grid';

        calendarState.visibleDates.forEach((date, index) => {
            const iso = toISO(date);
            const cell = document.createElement('div');
            cell.className = 'grid-cell calendar-cell';
            cell.dataset.roomId = String(room.id);
            cell.dataset.date = iso;
            cell.dataset.gridIndex = String(index);

            const bookingAtDate = getBookingForCell(room.id, iso);
            if (bookingAtDate) {
                cell.classList.add('cell-booked');
                if (bookingAtDate.isBlocked) {
                    cell.classList.add('cell-blocked');
                }
                cell.title = bookingAtDate.tooltip || `${bookingAtDate.label} (${bookingAtDate.startDate} - ${bookingAtDate.endDate})`;
               cell.addEventListener('click', () => showBookingSummary(bookingAtDate));
            } else {
                cell.addEventListener('mousedown', (event) => {
                    handleCellMouseDown(event, room, category, iso);
                });
                cell.addEventListener('mouseenter', () => {
                    handleCellMouseEnter(room.id, iso);
                });
                cell.addEventListener('mouseup', () => {
                    handleDocumentMouseUp();
                });
            }
            grid.appendChild(cell);
        });

        renderBookingBlocks(grid, room.id);

        row.appendChild(roomLabel);
        row.appendChild(grid);
        return row;
    }

    function renderBookingBlocks(gridEl, roomId) {
        const bookings = bookingsByRoom.get(roomId);
        if (!bookings || !bookings.length) {
            return;
        }
        const visibleIso = calendarState.visibleDates.map((date) => toISO(date));
        bookings.forEach((booking) => {
            const indices = getVisibleRangeIndices(booking, visibleIso);
            if (!indices) {
                return;
            }
            const block = document.createElement('div');
            block.className = ['booking-block', booking.cssClass].filter(Boolean).join(' ');
            block.dataset.bookingId = booking.id;
            block.style.gridColumn = `${indices.start + 1} / ${indices.end + 2}`;
            block.style.gridRow = '1';
            block.title = booking.tooltip || `${booking.label} (${booking.startDate} - ${booking.endDate})`;

            const labelWrapper = document.createElement('div');
            labelWrapper.className = 'booking-label';
            if (booking.isBlocked || booking.status === 'maintenance') {
                const icon = document.createElement('img');
                icon.src = assetBaseUrl + 'maintain.svg';
                icon.alt = 'Maintenance';
                icon.className = 'booking-icon';
                labelWrapper.appendChild(icon);
            } else {
                const icon = document.createElement('img');
                icon.src = assetBaseUrl + 'user.svg';
                icon.alt = 'Reservation';
                icon.className = 'booking-icon';
                labelWrapper.appendChild(icon);
            }
            const labelText = document.createElement('span');
            if ((booking.isBlocked || booking.status === 'maintenance') && booking.roomNumber) {
                labelText.textContent = `${booking.roomNumber} ${booking.label}`;
            } else {
                labelText.textContent = booking.label;
            }
            labelWrapper.appendChild(labelText);

            block.appendChild(labelWrapper);
            block.addEventListener('click', () => showBookingSummary(booking));
            gridEl.appendChild(block);
        });
    }

    function renderRooms() {
        roomsListEl.innerHTML = '';
        if (!normalizedRoomsData.length) {
            emptyStateEl.style.display = 'block';
            return;
        }
        emptyStateEl.style.display = 'none';

        const grouped = groupCategoriesByHostel(normalizedRoomsData);
        grouped.forEach((categories, hostelName) => {
            roomsListEl.appendChild(buildCategoryHeader(hostelName));
            categories.forEach((category) => {
                const categoryWrapper = document.createElement('div');
                categoryWrapper.className = 'room-category';
                categoryWrapper.appendChild(buildTitleRow(category));
                category.rooms.forEach((room) => {
                    categoryWrapper.appendChild(buildRoomRow(room, category));
                });
                roomsListEl.appendChild(categoryWrapper);
            });
        });
    }

    function normalizeRange(startIso, endIso) {
        if (!startIso || !endIso) {
            return null;
        }
        return startIso <= endIso
            ? { start: startIso, end: endIso }
            : { start: endIso, end: startIso };
    }

    function updateSelectionClasses() {
        const previewRange = selectionState.isDragging
            ? normalizeRange(selectionState.dragStartDate, selectionState.dragEndDate)
            : null;

        roomsListEl.querySelectorAll('.calendar-cell').forEach((cell) => {
            const roomId = Number(cell.dataset.roomId);
            const iso = cell.dataset.date;
            const stored = roomSelections.get(roomId);
            const isStored = stored ? iso >= stored.startDate && iso <= stored.endDate : false;
            const isPreview = previewRange
                && selectionState.activeRoomId === roomId
                && iso >= previewRange.start
                && iso <= previewRange.end;

            cell.classList.toggle('selected-range', Boolean(isStored));
            cell.classList.toggle('drag-preview', Boolean(isPreview));
        });
    }

    function resetDragging() {
        selectionState.isDragging = false;
        selectionState.activeRoomId = null;
        selectionState.dragStartDate = null;
        selectionState.dragEndDate = null;
        selectionState.activeRoomMeta = null;
    }

    function showSelectionAlert(meta, startIso, endIso) {
        if (!meta) {
            return;
        }
        showSelectionTooltipForRange(meta.roomId ?? selectionState.activeRoomId, {
            start: startIso,
            end: endIso
        }, meta);
    }

    function showBookingSummary(booking) {
        if (!booking) {
            return;
        }
        const lines = [
            booking.label || 'Reservation',
            booking.roomNumber ? `Room ${booking.roomNumber}` : `Room ID ${booking.roomId}`,
            `${booking.startDate} → ${booking.endDate}`
        ];
        if (booking.hostelName || booking.categoryTitle) {
            lines.splice(1, 0, [booking.hostelName, booking.categoryTitle].filter(Boolean).join(' • '));
        }
        if (booking.tooltip) {
            lines.push(booking.tooltip);
        }
        alert(lines.join('\n'));
    }

    function handleCellMouseDown(event, room, category, iso) {
        if (event) {
            event.preventDefault();
        }
        hideSelectionTooltip();
        selectionState.isDragging = true;
        selectionState.activeRoomId = room.id;
        selectionState.dragStartDate = iso;
        selectionState.dragEndDate = iso;
        selectionState.activeRoomMeta = {
            roomNumber: room.number,
            roomId: room.id,
            hostelName: category.hostelName,
            title: category.title
        };
        updateSelectionClasses();
    }

    function handleCellMouseEnter(roomId, iso) {
        if (!selectionState.isDragging || selectionState.activeRoomId !== roomId) {
            return;
        }
        selectionState.dragEndDate = iso;
        updateSelectionClasses();
    }

    function commitSelection() {
        if (!selectionState.activeRoomId || !selectionState.dragStartDate) {
            return;
        }

        const range = normalizeRange(selectionState.dragStartDate, selectionState.dragEndDate || selectionState.dragStartDate);
        const committedContext = {
            roomId: selectionState.activeRoomId,
            startDate: range.start,
            endDate: range.end,
            meta: selectionState.activeRoomMeta
        };
        roomSelections.set(selectionState.activeRoomId, committedContext);
        lastCommittedSelectionContext = {
            roomId: committedContext.roomId,
            range,
            meta: selectionState.activeRoomMeta
        };

        showSelectionAlert(selectionState.activeRoomMeta, range.start, range.end);
        resetDragging();
        updateSelectionClasses();
    }

    function handleDocumentMouseUp() {
        if (!selectionState.isDragging) {
            return;
        }
        commitSelection();
    }

    document.addEventListener('mouseup', handleDocumentMouseUp);

    function setView(mode) {
        if (calendarState.viewMode === mode) {
            return;
        }
        calendarState.viewMode = mode;
        weekViewBtn?.classList.toggle('active', mode === 'week');
        monthViewBtn?.classList.toggle('active', mode === 'month');
        renderCalendar();
        fetchCalendarDataForCurrentView();
    }

    function shiftRange(direction) {
        if (calendarState.viewMode === 'week') {
            calendarState.referenceDate.setDate(
                calendarState.referenceDate.getDate() + (direction * WEEK_DAY_COUNT)
            );
        } else {
            calendarState.referenceDate.setMonth(calendarState.referenceDate.getMonth() + direction);
        }
        syncDatePicker();
        renderCalendar();
        fetchCalendarDataForCurrentView();
    }

    function syncDatePicker() {
        if (datePicker) {
            datePicker.value = toISO(calendarState.referenceDate);
        }
    }

    function handleDatePickerChange(event) {
        const selected = parseISO(event.target.value);
        if (!selected) {
            return;
        }
        calendarState.referenceDate = calendarState.viewMode === 'week'
            ? new Date(selected.setHours(0, 0, 0, 0))
            : selected;
        renderCalendar();
        fetchCalendarDataForCurrentView();
    }

    function initQuickReservationForm(roomDataset) {
        const form = document.getElementById('quickReservationForm');
        if (!form) {
            return;
        }
        const detailsContainer = document.getElementById('roomDetailsContainer');
        const addRoomBtn = document.getElementById('addRoomDetailBtn');
        const totalRoomsInput = document.getElementById('quickRoomsCount');
        const totalAmountLabel = document.getElementById('quickTotalAmountLabel');
        const paidAmountLabel = document.getElementById('quickPaidAmountLabel');
        const balanceAmountLabel = document.getElementById('quickBalanceAmountLabel');
        const errorsBox = document.getElementById('quickReservationErrors');
        const checkInDateInput = document.getElementById('quickCheckInDate');
        const checkOutDateInput = document.getElementById('quickCheckOutDate');
        const endpoint = form.dataset.endpoint;
        if (!detailsContainer || !addRoomBtn) {
            return;
        }

        const csrfToken = form.querySelector('input[name="_token"]')?.value
            || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const roomsList = Array.isArray(roomDataset) ? roomDataset : [];
        const currencyFormatter = new Intl.NumberFormat('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        const todayIso = new Date().toISOString().split('T')[0];
        if (checkInDateInput) {
            checkInDateInput.setAttribute('min', todayIso);
        }
        if (checkOutDateInput) {
            checkOutDateInput.setAttribute('min', checkInDateInput?.value || todayIso);
        }
        let rowIndex = 0;

        const roomOptionsMarkup = (() => {
            const options = ['<option value="">Select</option>'];
            roomsList.forEach((room) => {
                options.push(`<option value="${room.id}">${room.title}</option>`);
            });
            return options.join('');
        })();

        function buildRateOptions(roomId) {
            const room = roomsList.find((entry) => Number(entry.id) === Number(roomId));
            if (!room || !Array.isArray(room.rates) || !room.rates.length) {
                return '<option value="">Select</option>';
            }
            const options = ['<option value="">Select</option>'];
            room.rates.forEach((rate) => {
                options.push(`<option value="${rate.id}" data-price="${rate.price}" data-max-adults="${rate.no_of_guest}" data-max-child="${rate.no_of_child}">${rate.label}</option>`);
            });
            return options.join('');
        }

        function addRoomRow(initial = {}) {
            const currentIndex = rowIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'room-detail-item';
            wrapper.dataset.index = currentIndex;
            wrapper.innerHTML = `
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Room Type</label>
                        <select class="form-control room-type-select" name="room_details[${currentIndex}][room_id]">
                            ${roomOptionsMarkup}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rate Type</label>
                        <select class="form-control room-rate-select" name="room_details[${currentIndex}][room_price_id]">
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rooms</label>
                        <input type="number" class="form-control room-qty-input" name="room_details[${currentIndex}][rooms]" min="1" value="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Adults</label>
                        <input type="number" class="form-control room-adult-input" name="room_details[${currentIndex}][adults]" min="1" value="1">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Child</label>
                        <input type="number" class="form-control room-child-input" name="room_details[${currentIndex}][children]" min="0" value="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <input type="number" readonly class="form-control room-price-input" name="room_details[${currentIndex}][price]" min="0" step="0.01" placeholder="0.00">
                    </div>
                    <div class="col-12 d-flex justify-content-between align-items-center mt-2">
                        <p class="mb-0 small text-muted">Line Total: Rs. <span class="room-line-total">0.00</span></p>
                        <button type="button" class="btn btn-link text-danger p-0 remove-room-row">Remove</button>
                    </div>
                </div>
            `;
            detailsContainer.appendChild(wrapper);
            const roomSelect = wrapper.querySelector('.room-type-select');
            if (initial.room_id) {
                roomSelect.value = initial.room_id;
            }
            updateRateOptions(wrapper, roomSelect.value || null, initial.room_price_id);
            wrapper.querySelector('.room-qty-input').value = initial.rooms || 1;
            wrapper.querySelector('.room-adult-input').value = initial.adults || 1;
            wrapper.querySelector('.room-child-input').value = initial.children || 0;
            if (initial.price) {
                wrapper.querySelector('.room-price-input').value = Number(initial.price).toFixed(2);
            }
            recalcSummary();
        }

        function updateRateOptions(row, roomId, selectedRateId) {
            const rateSelect = row.querySelector('.room-rate-select');
            if (!rateSelect) {
                return;
            }
            rateSelect.innerHTML = buildRateOptions(roomId);
            if (selectedRateId) {
                rateSelect.value = String(selectedRateId);
            } else if (rateSelect.options.length > 1) {
                rateSelect.selectedIndex = 1;
            }
            const selectedOption = rateSelect.selectedOptions[0];
            if (selectedOption && selectedOption.dataset.price) {
                row.querySelector('.room-price-input').value = parseFloat(selectedOption.dataset.price).toFixed(2);
            }
        }

        function formatAmount(value) {
            return currencyFormatter.format(Number(value) || 0);
        }

        function calculateNights() {
            const start = checkInDateInput?.value;
            const end = checkOutDateInput?.value;
            if (!start || !end) {
                return 1;
            }
            const startDate = new Date(start);
            const endDate = new Date(end);
            if (Number.isNaN(startDate.getTime()) || Number.isNaN(endDate.getTime())) {
                return 1;
            }
            const diff = Math.floor((endDate - startDate) / (1000 * 60 * 60 * 24));
            return diff > 0 ? diff : 1;
        }

        function recalcSummary() {
            const nights = calculateNights();
            let totalRooms = 0;
            let totalAmount = 0;
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const qty = parseInt(row.querySelector('.room-qty-input')?.value, 10) || 0;
                const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
                totalRooms += qty;
                const lineTotal = qty * price * nights;
                totalAmount += lineTotal;
                const lineLabel = row.querySelector('.room-line-total');
                if (lineLabel) {
                    lineLabel.textContent = formatAmount(lineTotal);
                }
            });
            if (totalRoomsInput) {
                totalRoomsInput.value = totalRooms || '';
            }
            if (totalAmountLabel) {
                totalAmountLabel.textContent = formatAmount(totalAmount);
            }
            if (paidAmountLabel) {
                paidAmountLabel.textContent = formatAmount(0);
            }
            if (balanceAmountLabel) {
                balanceAmountLabel.textContent = formatAmount(totalAmount);
            }
        }

        function showErrors(messages) {
            if (!errorsBox) {
                alert(messages.join('\n'));
                return;
            }
            errorsBox.classList.remove('d-none');
            errorsBox.innerHTML = `<ul class="mb-0">${messages.map((msg) => `<li>${msg}</li>`).join('')}</ul>`;
        }

        function clearErrors() {
            if (!errorsBox) {
                return;
            }
            errorsBox.classList.add('d-none');
            errorsBox.innerHTML = '';
        }

        const submitBtn = document.getElementById('quickReservationSubmitBtn');
        const submitLabelDefault = submitBtn?.querySelector('.label-default');
        const submitLabelLoading = submitBtn?.querySelector('.label-loading');

        function toggleFormDisabled(state) {
            form.querySelectorAll('input, select, textarea').forEach((element) => {
                element.disabled = state;
            });
            if (submitBtn) {
                submitBtn.disabled = state;
                submitLabelDefault?.classList.toggle('d-none', state);
                submitLabelLoading?.classList.toggle('d-none', !state);
            }
            addRoomBtn.disabled = state;
        }

        function buildPayload() {
            const checkInDate = checkInDateInput?.value;
            const checkOutDate = checkOutDateInput?.value;
            if (!checkInDate || !checkOutDate) {
                showErrors(['Please select both check-in and check-out dates.']);
                return null;
            }
            const roomLines = [];
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const roomId = row.querySelector('.room-type-select')?.value;
                const rateId = row.querySelector('.room-rate-select')?.value;
                const qty = row.querySelector('.room-qty-input')?.value;
                if (roomId && rateId && qty) {
                    roomLines.push({
                        room_id: Number(roomId),
                        room_price_id: Number(rateId),
                        rooms: Number(qty),
                        adults: Number(row.querySelector('.room-adult-input')?.value || 0),
                        children: Number(row.querySelector('.room-child-input')?.value || 0),
                        price: Number(row.querySelector('.room-price-input')?.value || 0)
                    });
                }
            });
            if (!roomLines.length) {
                showErrors(['Please add at least one room detail.']);
                return null;
            }
                return {
                    check_in_date: checkInDate,
                    check_out_date: checkOutDate,
                    check_in_time: document.getElementById('quickCheckInTime')?.value || null,
                    check_out_time: document.getElementById('quickCheckOutTime')?.value || null,
                    reservation_type: document.getElementById('quickReservationType')?.value,
                    room_details: roomLines,
                    guest: {
                        name: document.getElementById('quickGuestName')?.value,
                        gender: document.getElementById('quickGuestGender')?.value || null,
                        email: document.getElementById('quickGuestEmail')?.value || null,
                        mobile: document.getElementById('quickGuestMobile')?.value
                    }
                };
        }

        addRoomBtn.addEventListener('click', (event) => {
            event.preventDefault();
            addRoomRow();
        });

        detailsContainer.addEventListener('change', (event) => {
            const row = event.target.closest('.room-detail-item');
            if (!row) {
                return;
            }
            if (event.target.classList.contains('room-type-select')) {
                updateRateOptions(row, event.target.value);
            } else if (event.target.classList.contains('room-rate-select')) {
                updateRateOptions(row, row.querySelector('.room-type-select')?.value, event.target.value);
            }
            recalcSummary();
        });

        detailsContainer.addEventListener('input', (event) => {
            if (event.target.classList.contains('room-qty-input')
                || event.target.classList.contains('room-price-input')) {
                recalcSummary();
            }
        });

        detailsContainer.addEventListener('click', (event) => {
            if (!event.target.classList.contains('remove-room-row')) {
                return;
            }
            event.preventDefault();
            const row = event.target.closest('.room-detail-item');
            if (row) {
                row.remove();
            }
            if (!detailsContainer.querySelector('.room-detail-item')) {
                addRoomRow();
            }
            recalcSummary();
        });

        checkInDateInput?.addEventListener('change', () => {
            const minDate = checkInDateInput.value || todayIso;
            if (checkOutDateInput) {
                checkOutDateInput.setAttribute('min', minDate);
                if (checkOutDateInput.value && checkOutDateInput.value < minDate) {
                    checkOutDateInput.value = minDate;
                }
            }
            recalcSummary();
        });

        checkOutDateInput?.addEventListener('change', recalcSummary);

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearErrors();
            if (!endpoint) {
                showErrors(['Reservation endpoint is not configured.']);
                return;
            }
            const payload = buildPayload();
            if (!payload) {
                return;
            }
            toggleFormDisabled(true);
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify(payload)
                });
                const body = await response.json().catch(() => ({}));
                if (!response.ok || !body.success) {
                    const messages = [];
                    if (body?.errors) {
                        Object.values(body.errors).forEach((value) => {
                            if (Array.isArray(value)) {
                                messages.push(...value);
                            } else if (value) {
                                messages.push(value);
                            }
                        });
                    } else if (body?.message) {
                        messages.push(body.message);
                    }
                    showErrors(messages.length ? messages : ['Unable to save reservation.']);
                    return;
                }
                if (typeof swal === 'function') {
                    swal("Success!", body.message || 'Reservation saved successfully.', "success");
                } else {
                    alert(body.message || 'Reservation saved successfully.');
                }
                form.reset();
                detailsContainer.innerHTML = '';
                rowIndex = 0;
                addRoomRow();
                recalcSummary();
                if (typeof window.fetchCalendarDataForCurrentView === 'function') {
                    window.fetchCalendarDataForCurrentView();
                }
            } catch (error) {
                console.error('Quick reservation error:', error);
                showErrors(['Unable to save reservation. Please try again.']);
            } finally {
                toggleFormDisabled(false);
            }
        });

        addRoomRow();
        recalcSummary();
    }

    function renderCalendar() {
        renderDateHeader();
        renderRooms();
        updateSelectionClasses();
    }

    weekViewBtn?.addEventListener('click', () => setView('week'));
    monthViewBtn?.addEventListener('click', () => setView('month'));
    prevRangeBtn?.addEventListener('click', () => shiftRange(-1));
    nextRangeBtn?.addEventListener('click', () => shiftRange(1));
    datePicker?.addEventListener('change', handleDatePickerChange);

    syncDatePicker();
    renderCalendar();
});
</script>
@endsection