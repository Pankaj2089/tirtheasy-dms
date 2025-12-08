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
    
    /* Room Number Dropdown Styles */
    .room-number-select-wrapper {
        position: relative;
    }
    
    .room-number-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        margin-top: 2px;
    }
    
    .room-number-dropdown .list-group-item {
        cursor: pointer;
        padding: 8px 12px;
        border: none;
        border-bottom: 1px solid #eee;
    }
    
    .room-number-dropdown .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .room-number-dropdown .list-group-item:last-child {
        border-bottom: none;
    }
    
    .selected-room-numbers .badge {
        font-size: 0.875rem;
        padding: 4px 8px;
    }
    
    .selected-room-numbers .btn-close {
        margin-left: 4px;
        opacity: 0.8;
    }
    
    .selected-room-numbers .btn-close:hover {
        opacity: 1;
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
                                    <select class="form-select form-select-sm" id="hotelSelector">
                                        <option value="">All Properties</option>
                                        @if(isset($hotels) && (is_array($hotels) || is_iterable($hotels)))
                                            @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->id }}">{{ $hotel->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    <select class="form-select form-select-sm mobile-room"
                                        style="width: 210px; display: none;" data-room-type-selector="true">
                                        <option value="">All Room Types</option>
                                        @if(isset($roomCategories) && (is_array($roomCategories) || is_iterable($roomCategories)))
                                            @foreach($roomCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->title }}</option>
                                            @endforeach
                                        @endif
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
                            <select class="form-select form-select-sm" style="width: 210px;" data-room-type-selector="true">
                                <option value="">All Room Types</option>
                                @if(isset($roomCategories) && (is_array($roomCategories) || is_iterable($roomCategories)))
                                    @foreach($roomCategories as $category)
                                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                                    @endforeach
                                @endif
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
                        aria-selected="false">Payment Details</button>
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
                            <!-- Hidden fields -->
                            <input type="hidden" id="quickRoomsCount" name="total_rooms" value="0">
                            <input type="hidden" id="quickReservationType" name="reservation_type" value="confirmed">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 d-flex align-items-center justify-content-between">
                                <h5 class="mt-3 mb-0">Room Details</h5>
                                <!-- <small class="text-muted">Add one entry per room and rate plan</small> -->
                            </div>
                            <div class="col-12">
                                <div id="roomDetailsContainer" class="room-details-container"></div>
                            </div>
                        </div>

                        <!-- Room Number Selection -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h5 class="mt-3 mb-2">Room Number Selection</h5>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Select Room Numbers</label>
                                <div class="room-number-select-wrapper position-relative">
                                    <input type="text" id="quickRoomNumberSearch" class="form-control" placeholder="Search and select room numbers..." autocomplete="off">
                                    <div id="quickRoomNumberDropdown" class="room-number-dropdown" style="display: none;"></div>
                                    <input type="hidden" id="quickRoomNumberIds" name="room_number_ids" value="">
                                    <small class="form-text text-muted">Type to search. Only available rooms for selected dates will be shown.</small>
                                    <div id="quickSelectedRoomNumbers" class="selected-room-numbers mt-2"></div>
                                </div>
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
                                <input type="tel" class="form-control" maxlength="10" id="quickGuestMobile" name="guest[mobile]" placeholder="0123456789">
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
                                <button type="button" class="theme-btn text-white ms-auto text-center text-decoration-none" id="quickReservationPaymentBtn">
                                    Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row">
                                <div class="col-md-6 px-0 border-box">
                                    <div class="sub-heading px-3 d-flex justify-content-between">
                                        <p class="mb-0">Date : <strong id="quickPaymentDate">-</strong></p>
                                    </div>
                                    <hr class="mt-2 mb-0">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2 ">Details</h5>
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Room Price</p>
                                                <p><strong id="quickPaymentRoomPrice">Rs. 0</strong></p>
                                            </li>
                                            <!-- <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Charges</p>
                                                <p><strong id="quickPaymentTotalCharges">Rs. 0</strong></p>
                                            </li> -->
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total SGST</p>
                                                <p><strong id="quickPaymentSGST">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total CGST</p>
                                                <p><strong id="quickPaymentCGST">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Extra Charges</p>
                                                <p><strong id="quickPaymentExtraCharges">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Payable</p>
                                                <p><strong id="quickPaymentTotalPayable">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Balance</p>
                                                <p><strong id="quickPaymentBalance">Rs. 0</strong></p>
                                            </li>
                                        </ul>
                                    </div>
                                    <h5 class="mt-4 mb-2">Folio History</h5>
                                    <div class="p-details">
                                        <!-- Charges & Payments History with Summary -->
                                        <div id="quickFolioHistoryDetails"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 bl-main-popup">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2">Add Charges</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="quickChargeComment" class="form-label mt-0">Comment</label>
                                                    <input type="text" id="quickChargeComment" class="form-control bg-white" placeholder="Enter comment">
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="quickChargeAmount" class="form-label mt-0">Amount</label>
                                                    <input type="number" id="quickChargeAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
                                                </div>
                                            </div>

                                            <div class="col-md-12 d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <button type="button" id="quickChargeAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Charge</button>
                                                </div>
                                            </div>

                                            <!-- Added Charges List -->
                                            <div class="col-md-12 mt-3">
                                                <div id="quickChargesList"></div>
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
                                                    <label for="quickPaymentType" class="form-label mt-0">Type</label>
                                                    <select id="quickPaymentType" class="form-control bg-white">
                                                        <option value="">Select Type</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="Online">Online</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="quickPaymentMethod" class="form-label mt-0">Method</label>
                                                    <select id="quickPaymentMethod" class="form-control bg-white" disabled>
                                                        <option value="">Select Method</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="quickPaymentComment" class="form-label mt-0">Comment</label>
                                                    <input type="text" id="quickPaymentComment" placeholder="Add comment..." class="form-control bg-white">
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="quickPaymentAmount" class="form-label mt-0">Amount</label>
                                                    <input type="number" id="quickPaymentAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
                                                </div>
                                            </div>

                                            <div class="col-md-12 d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <button type="button" id="quickPaymentAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Payment</button>
                                                </div>
                                            </div>

                                            <!-- Added Payments List -->
                                            <div class="col-md-12 mt-3">
                                                <div id="quickPaymentsList"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>

                        <div class="row justify-content-between   position-absolute align-items-center reservation-footer w-100">
                            <hr>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0 fw-bold">Total</p>
                                    <p class="mb-0 fw-bold">Rs. <span id="quickPaymentFooterTotal">0.00</span></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0">Paid</p>
                                    <p class="mb-0">Rs. <span id="quickPaymentFooterPaid">0.00</span></p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    <p class="mb-0 text-info">Balance</p>
                                    <p class="mb-0 text-info">Rs. <span id="quickPaymentFooterBalance">0.00</span></p>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-end gap-2">
                                <button type="button" class="theme-btn text-white ms-auto text-center text-decoration-none" id="quickPaymentConfirmBtn" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    <span class="label-default">Confirm</span>
                                    <span class="label-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
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
                                    <h6 id="bookingGuestName">-</h6> &nbsp;
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt="">
                                </div>
                                <p>Folio #: <strong id="bookingFolio">-</strong> &nbsp; Date : <strong id="bookingDate">-</strong></p>
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
                            <div class="bottom pb-4">
                                <ul class="d-flex flex-wrap justify-content-between">
                                    <li class="mb-4">
                                        <p>Reservation Number</p>
                                        <p><small class="" id="bookingReservationNumber">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Arrival Date</p>
                                        <p><small class="" id="bookingArrivalDate">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Booking Date</p>
                                        <p><small class="" id="bookingBookingDate">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Departure Date</p>
                                        <p><small class="" id="bookingDepartureDate">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Rate Plan</p>
                                        <p><small class="" id="bookingRatePlan">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Number</p>
                                        <p><small class="" id="bookingRoomNumber">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Type</p>
                                        <p><small class="" id="bookingRoomType">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Number Of Guest</p>
                                        <p><small class="" id="bookingNumberOfGuest">-</small></p>
                                    </li>
                                    <li class="mb-4">
                                        <p>Room Booked</p>
                                        <p><small class="" id="bookingRoomBooked">-</small></p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-pane p-4 fade" id="Payment" role="tabpanel"
                            aria-labelledby="Payment-tab">
                            <div class="row">
                                <div class="col-md-6 px-0 border-box">
                                    <div class="sub-heading px-3 d-flex justify-content-between">
                                        <p class="mb-0"> Folio #: <strong id="paymentFolio">-</strong></p>
                                        <p class="mb-0">Date : <strong id="paymentDate">-</strong></p>
                                    </div>
                                    <hr class="mt-2 mb-0">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2 ms-3">Details</h5>
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Room Price</p>
                                                <p><strong id="paymentRoomPrice">Rs. 0</strong></p>
                                            </li>
                                            <!-- <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Charges</p>
                                                <p><strong id="paymentTotalCharges">Rs. 0</strong></p>
                                            </li> -->
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total SGST</p>
                                                <p><strong id="paymentSGST">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total CGST</p>
                                                <p><strong id="paymentCGST">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Extra Charges</p>
                                                <p><strong id="paymentExtraCharges">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Total Payable</p>
                                                <p><strong id="paymentTotalPayable">Rs. 0</strong></p>
                                            </li>
                                            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                                                <p>Balance</p>
                                                <p><strong id="paymentBalance">Rs. 0</strong></p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="p-details">
                                        <h5 class="mt-4 mb-2">Folio History</h5>
                                        <!-- Existing & New Charges & Payments History -->
                                        <div id="bookingFolioHistoryDetails"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 bl-main-popup">
                                    <div class="p-details">
                                        <h5 class="mt-3 mb-2">Add Charges</h5>
                                        <div class="row align-items-center mb-3">
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="bookingChargeComment" class="form-label mt-0">Comment</label>
                                                    <input type="text" id="bookingChargeComment" class="form-control bg-white" placeholder="Enter comment">
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="bookingChargeAmount" class="form-label mt-0">Amount</label>
                                                    <input type="number" id="bookingChargeAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
                                                </div>
                                            </div>

                                            <div class="col-md-12 d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <button type="button" id="bookingChargeAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Charge</button>
                                                </div>
                                            </div>

                                            <!-- Added Charges List -->
                                            <div class="col-md-12 mt-3">
                                                <div id="bookingChargesList"></div>
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
                                                    <label for="bookingPaymentType" class="form-label mt-0">Type</label>
                                                    <select id="bookingPaymentType" class="form-control bg-white">
                                                        <option value="">Select Type</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="Online">Online</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="bookingPaymentMethod" class="form-label mt-0">Method</label>
                                                    <select id="bookingPaymentMethod" class="form-control bg-white" disabled>
                                                        <option value="">Select Method</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="bookingPaymentComment" class="form-label mt-0">Comment</label>
                                                    <input type="text" id="bookingPaymentComment" placeholder="Add comment..." class="form-control bg-white">
                                                </div>
                                            </div>

                                            <div class="col-md-6 d-flex flex-column mb-3">
                                                <div class="shadow-sm p-2 rounded bg-light border">
                                                    <label for="bookingPaymentAmount" class="form-label mt-0">Amount</label>
                                                    <input type="number" id="bookingPaymentAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
                                                </div>
                                            </div>

                                            <div class="col-md-12 d-flex align-items-center justify-content-end">
                                                <div class="bl-btn">
                                                    <button type="button" id="bookingPaymentAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Payment</button>
                                                </div>
                                            </div>

                                            <!-- Added Payments List -->
                                            <div class="col-md-12 mt-3">
                                                <div id="bookingPaymentsList"></div>
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
                                <p class="mb-0 fw-bold" id="footerTotal">Rs. 0</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <p class="mb-0">Paid</p>
                                <p class="mb-0" id="footerPaid">Rs. 0.00</p>
                            </div>
                            <div class="d-flex justify-content-between align-items-center my-1">
                                <p class="mb-0">Balance</p>
                                <p class="mb-0 text-danger" id="footerBalance">Rs. 0</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex w-100 justify-content-end">
                                <button type="button" id="bookingSaveBtn" onclick="saveBookingChargesPayments()"
                                    class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Save Changes</button>
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
                swal("Error!", error.message || 'Unable to block dates. Please try again.');
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
    const roomTypeSelectors = Array.from(document.querySelectorAll('[data-room-type-selector="true"]'));

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
            const hotelSelector = document.getElementById('hotelSelector');
            const selectedHotelId = hotelSelector ? hotelSelector.value : '';
            const selectedRoomTypeId = roomTypeSelectors.length ? roomTypeSelectors[0].value : '';
            const params = new URLSearchParams({
                start_date: startIso,
                end_date: endIso
            });
            if(selectedHotelId){
                params.append('hotel_id', selectedHotelId);
            }
            if(selectedRoomTypeId){
                params.append('room_id', selectedRoomTypeId); // Changed from room_category_id to room_id
            }
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
                swal("Error!", error.message || 'Unable to load calendar data.');
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
    
    async function showMaintenanceBlockDetails(booking) {
        if (!booking || !booking.id) {
            console.error('No booking object or ID provided for maintenance block');
            return;
        }
        
        // Open the maintenance block popup modal
        const maintenanceModal = document.getElementById('maintenance-block-pop');
        if (!maintenanceModal) {
            console.error('Maintenance modal element not found');
            return;
        }
        
        // Fetch maintenance block details
        try {
            const bookingId = encodeURIComponent(booking.id);
            const baseUrl = `{{ url('/booking') }}`;
            const bookingEndpoint = `${baseUrl}/${bookingId}`;
            
            const response = await fetch(bookingEndpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Failed to fetch maintenance block details: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success && data.booking) {
                const maintenanceData = data.booking;
                
                // Populate maintenance modal with fetched data
                if (maintenanceStartInput) {
                    maintenanceStartInput.value = maintenanceData.check_in_date || maintenanceData.start_date || '';
                }
                if (maintenanceEndInput) {
                    maintenanceEndInput.value = maintenanceData.check_out_date || maintenanceData.end_date || maintenanceData.check_in_date || maintenanceData.start_date || '';
                }
                if (maintenanceRoomIdInput) {
                    // Get room number ID from booking - for maintenance blocks, room_id is the room number ID
                    const roomNumberId = booking.roomId || maintenanceData.room_id;
                    maintenanceRoomIdInput.value = roomNumberId || '';
                }
                if (maintenanceReasonInput) {
                    maintenanceReasonInput.value = maintenanceData.guest_name || maintenanceData.reason || '';
                }
                
                // Disable form fields since we're viewing, not editing
                if (maintenanceStartInput) maintenanceStartInput.disabled = true;
                if (maintenanceEndInput) maintenanceEndInput.disabled = true;
                if (maintenanceRoomIdInput) maintenanceRoomIdInput.disabled = true;
                if (maintenanceReasonInput) maintenanceReasonInput.disabled = true;
                
                // Hide submit button or change it to "Close"
                const submitBtn = document.getElementById('blockDatesBtn');
                if (submitBtn) {
                    submitBtn.style.display = 'none';
                }
            } else {
                throw new Error(data.message || 'Maintenance block not found');
            }
        } catch (error) {
            console.error('Error fetching maintenance block details:', error);
            // Still open modal but show error
            if (maintenanceReasonInput) {
                maintenanceReasonInput.value = `Error: ${error.message}`;
                maintenanceReasonInput.disabled = true;
            }
        }
        
        // Open modal
        try {
            if (window.bootstrap?.Modal) {
                const modalInstance = window.bootstrap.Modal.getOrCreateInstance(maintenanceModal);
                modalInstance.show();
            } else {
                maintenanceModal.classList.add('show');
                maintenanceModal.style.display = 'block';
                maintenanceModal.removeAttribute('aria-hidden');
            }
        } catch (modalError) {
            console.error('Error opening maintenance modal:', modalError);
        }
    }

    function populateQuickReservationDates(startDate, endDate, isFromSelection = false) {
        const checkInInput = document.getElementById('quickCheckInDate');
        const checkOutInput = document.getElementById('quickCheckOutDate');
        const startValue = ensureISODate(startDate);
        const endValue = ensureISODate(endDate) || startValue;
        
        if (checkInInput && startValue) {
            checkInInput.value = startValue;
            // Set readonly if dates come from calendar selection
            if (isFromSelection) {
                checkInInput.setAttribute('readonly', 'readonly');
                checkInInput.style.backgroundColor = '#e9ecef';
                checkInInput.style.cursor = 'not-allowed';
            } else {
                checkInInput.removeAttribute('readonly');
                checkInInput.style.backgroundColor = '';
                checkInInput.style.cursor = '';
            }
            checkInInput.dispatchEvent(new Event('change'));
        }
        if (checkOutInput && endValue) {
            checkOutInput.value = endValue;
            // Set readonly if dates come from calendar selection
            if (isFromSelection) {
                checkOutInput.setAttribute('readonly', 'readonly');
                checkOutInput.style.backgroundColor = '#e9ecef';
                checkOutInput.style.cursor = 'not-allowed';
            } else {
                checkOutInput.removeAttribute('readonly');
                checkOutInput.style.backgroundColor = '';
                checkOutInput.style.cursor = '';
            }
            checkOutInput.dispatchEvent(new Event('change'));
        }
    }

    function getSelectionContextForModal() {
        // Collect all selected room number IDs from roomSelections
        const selectedRoomNumberIds = [];
        roomSelections.forEach((context, roomId) => {
            if (context && roomId) {
                selectedRoomNumberIds.push(roomId);
            }
        });
        
        // If we have active selection, include it too
        if (activeSelectionContext?.range && activeSelectionContext.roomId) {
            if (!selectedRoomNumberIds.includes(activeSelectionContext.roomId)) {
                selectedRoomNumberIds.push(activeSelectionContext.roomId);
            }
        }
        
        // If we have lastCommittedSelectionContext, include its roomId too (for single date clicks)
        if (lastCommittedSelectionContext?.roomId && !selectedRoomNumberIds.includes(lastCommittedSelectionContext.roomId)) {
            selectedRoomNumberIds.push(lastCommittedSelectionContext.roomId);
        }
        
        let context = null;
        if (activeSelectionContext?.range) {
            context = {
                ...activeSelectionContext,
                roomNumberIds: selectedRoomNumberIds
            };
        } else if (lastCommittedSelectionContext?.range) {
            context = {
                ...lastCommittedSelectionContext,
                roomNumberIds: selectedRoomNumberIds
            };
        }
        
        return context;
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
            const reservationModal = document.getElementById('reservation-pop');
            
            if (context?.range) {
                // Dates selected from calendar - set readonly
                populateQuickReservationDates(context.range.start, context.range.end, true);
                // Set data attribute to indicate dates are from calendar selection
                if (reservationModal) {
                    reservationModal.dataset.datesFromCalendar = 'true';
                }
            } else {
                // No dates selected - set default dates (today and tomorrow) and make editable
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const todayIso = today.toISOString().split('T')[0];
                const tomorrowIso = tomorrow.toISOString().split('T')[0];
                populateQuickReservationDates(todayIso, tomorrowIso, false);
                // Set data attribute to indicate dates are NOT from calendar selection
                if (reservationModal) {
                    reservationModal.dataset.datesFromCalendar = 'false';
                }
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
            const start = ensureISODate(entry.start_date ?? entry.startDate);
            const end = ensureISODate(entry.end_date ?? entry.endDate ?? entry.start_date ?? entry.startDate);
            if (!start) {
                return;
            }
            const normalizedRange = (start && end && start > end)
                ? { start: end, end: start }
                : { start, end: end || start };
            const isBlocked = Boolean(entry.is_blocked || entry.isBlocked);
            const inclusiveEnd = Boolean(entry.inclusive_end ?? entry.inclusiveEnd);
            const statusValue = (entry.status || '').toLowerCase() || (isBlocked ? 'blocked' : 'reserved');
            const isMaintenance = statusValue === 'maintenance' || isBlocked;

            let roomIds = [];
            
            // Maintenance blocks use old method - room_id is the room number ID directly
            if (isMaintenance) {
                const roomNumberId = entry.room_id ?? entry.roomId;
                if (roomNumberId) {
                    roomIds = [Number(roomNumberId)];
                }
            } else {
                // Regular bookings: Only show if room_number_ids exists
                if (!entry.room_number_ids || typeof entry.room_number_ids !== 'string' || !entry.room_number_ids.trim()) {
                    return;
                }
                // Parse comma-separated room number IDs
                roomIds = entry.room_number_ids.split(',').map(id => id.trim()).filter(Boolean).map(Number);
            }
            
            if (roomIds.length === 0) {
                return;
            }

            roomIds.forEach((roomId) => {
                normalized.push({
                    id: entry.id ?? `booking-${index}-${roomId}`,
                    roomId: Number(roomId),
                    startDate: normalizedRange.start,
                    endDate: normalizedRange.end,
                    label: entry.label || entry.guest_name || entry.guestName || 'Reserved',
                    tooltip: entry.tooltip || entry.notes || entry.source || '',
                    status: statusValue,
                    isBlocked,
                    inclusiveEnd: inclusiveEnd || isBlocked,
                    room_number_ids: entry.room_number_ids || null,
                    // Preserve original entry data for maintenance blocks
                    guest_name: entry.guest_name,
                    source: entry.source
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

    /**
     * Check if any date in the given range is booked for the specified room
     * @param {number} roomId - The room ID to check
     * @param {string} startIso - Start date in ISO format (YYYY-MM-DD)
     * @param {string} endIso - End date in ISO format (YYYY-MM-DD)
     * @returns {boolean} - Returns true if any date in the range is booked
     */
    function isDateRangeBooked(roomId, startIso, endIso) {
        if (!roomId || !startIso || !endIso) {
            return false;
        }
        const roomMap = bookingDateLookup.get(roomId);
        if (!roomMap) {
            return false;
        }
        const range = normalizeRange(startIso, endIso);
        if (!range) {
            return false;
        }
        // Check each date in the range
        let hasBooking = false;
        eachDateInRange(range.start, range.end, (iso) => {
            if (roomMap.has(iso)) {
                hasBooking = true;
            }
        });
        return hasBooking;
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
        if (amount === null || amount === undefined || amount === '') {
            return '';
        }
        const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
        if (isNaN(numAmount)) {
            return '';
        }
        return currencyFormatter.format(numAmount);
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
            const hasCount = typeof count === 'number' && !Number.isNaN(count);
            const countMarkup = hasCount ? `<span${count <= 0 ? ' class="no-availability"' : ''}>${count}</span>` : '';
            const numPrice = price !== null && price !== undefined ? (typeof price === 'string' ? parseFloat(price) : price) : null;
            const hasPrice = numPrice !== null && !Number.isNaN(numPrice);
            let priceMarkup = '';
            if (hasPrice) {
                const formattedPrice = formatPrice(numPrice);
                priceMarkup = hasCount ? `<br>${formattedPrice}` : formattedPrice;
            } else if (hasCount) {
                priceMarkup = '<br>&nbsp;';
            } else {
                priceMarkup = '&nbsp;';
            }
            priceLabel.innerHTML = `${countMarkup}${priceMarkup}`;
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
                // Open maintenance popup for maintenance blocks, booking popup for regular bookings
                if (bookingAtDate.isBlocked || bookingAtDate.status === 'maintenance') {
                    cell.addEventListener('click', () => showMaintenanceBlockDetails(bookingAtDate));
                } else {
               cell.addEventListener('click', () => showBookingSummary(bookingAtDate));
                }
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
            let displayLabel = booking.label;
            
            // Get room numbers from room_number_ids if available
            if (booking.room_number_ids) {
                const roomNumberIds = booking.room_number_ids.split(',').map(id => id.trim()).filter(Boolean);
                const roomNumberNames = [];
                roomNumberIds.forEach((roomNumberId) => {
                    // Find room number name from roomsData
                    let found = false;
                    roomsData.forEach((category) => {
                        if (category.rooms && Array.isArray(category.rooms)) {
                            const room = category.rooms.find((r) => String(r.id) === String(roomNumberId));
                            if (room) {
                                roomNumberNames.push(room.number);
                                found = true;
                            }
                        }
                    });
                    if (!found) {
                        roomNumberNames.push(`#${roomNumberId}`);
                    }
                });
                if (roomNumberNames.length > 0) {
                    displayLabel = `${roomNumberNames.join(', ')} - ${booking.label}`;
                }
            } else if ((booking.isBlocked || booking.status === 'maintenance') && booking.roomNumber) {
                displayLabel = `${booking.roomNumber} ${booking.label}`;
            }
            
            labelText.textContent = displayLabel;
            labelWrapper.appendChild(labelText);

            block.appendChild(labelWrapper);
            // Open maintenance popup for maintenance blocks, booking popup for regular bookings
            if (booking.isBlocked || booking.status === 'maintenance') {
                block.addEventListener('click', () => showMaintenanceBlockDetails(booking));
            } else {
            block.addEventListener('click', () => showBookingSummary(booking));
            }
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
            
            // Check if this cell is booked
            const isBooked = Boolean(getBookingForCell(roomId, iso));
            
            // Check if preview range includes booked dates (for visual feedback)
            const isInvalidPreview = isPreview && isBooked;

            cell.classList.toggle('selected-range', Boolean(isStored));
            cell.classList.toggle('drag-preview', Boolean(isPreview) && !isInvalidPreview);
            cell.classList.toggle('drag-preview-invalid', Boolean(isInvalidPreview));
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

    async function showBookingSummary(booking) {
        if (!booking) {
            console.error('No booking object provided');
            return;
        }
        
        // Check if booking has an ID
        if (!booking.id) {
            console.error('Booking object missing id:', booking);
            return;
        }
        
        // If it's a maintenance block, don't open booking popup - open maintenance popup instead
        if (booking.isBlocked || booking.status === 'maintenance' || booking.type === 'maintenance') {
            showMaintenanceBlockDetails(booking);
            return;
        }
        
        // Open the booking popup modal
        const bookingModal = document.getElementById('booking-pop');
        if (!bookingModal) {
            console.error('Booking modal element not found');
            return;
        }
        
        // Show loading state
        populateBookingModal({ loading: true });
        
        // Open modal first
        try {
            if (window.bootstrap?.Modal) {
                const modalInstance = window.bootstrap.Modal.getOrCreateInstance(bookingModal);
                modalInstance.show();
            } else {
                bookingModal.classList.add('show');
                bookingModal.style.display = 'block';
                bookingModal.removeAttribute('aria-hidden');
            }
        } catch (modalError) {
            console.error('Error opening modal:', modalError);
        }
        
        // Fetch booking details
        try {
            const bookingId = encodeURIComponent(booking.id);
            // Build the endpoint URL - Laravel route helper needs proper replacement
            const baseUrl = `{{ url('/booking') }}`;
            const bookingEndpoint = `${baseUrl}/${bookingId}`;
            
            const response = await fetch(bookingEndpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Failed to fetch booking details: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success && data.booking) {
                populateBookingModal(data.booking);
            } else {
                throw new Error(data.message || 'Booking not found');
            }
        } catch (error) {
            console.error('Error fetching booking details:', error);
            populateBookingModal({ error: error.message || 'Failed to load booking details' });
        }
    }
    
    function populateBookingModal(booking) {
        if (booking.loading) {
            document.getElementById('bookingGuestName').textContent = 'Loading...';
            return;
        }
        
        if (booking.error) {
            document.getElementById('bookingGuestName').textContent = booking.error;
            return;
        }
        
        // Format date helper
        const formatDate = (dateString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        };
        
        const formatDateTime = (dateString, timeString) => {
            if (!dateString) return '-';
            const date = new Date(dateString);
            const dateStr = date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            if (timeString) {
                return `${dateStr} ${timeString}`;
            }
            return dateStr;
        };
        
        const formatCurrency = (amount) => {
            if (!amount && amount !== 0) return 'Rs. 0';
            return `Rs. ${parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };
        
        const isMaintenance = booking.type === 'maintenance' || booking.id?.startsWith('MB-');
        
        // Populate header
        const bookingGuestName = document.getElementById('bookingGuestName');
        const bookingFolio = document.getElementById('bookingFolio');
        const bookingDate = document.getElementById('bookingDate');
        
        if (bookingGuestName) bookingGuestName.textContent = booking.guest_name || (isMaintenance ? 'Maintenance Block' : '-');
        if (bookingFolio) bookingFolio.textContent = booking.invoice_id || booking.id || '-';
        if (bookingDate) bookingDate.textContent = formatDate(booking.created_at || booking.check_in_date);
        
        // Populate Booking Details tab
        const bookingReservationNumber = document.getElementById('bookingReservationNumber');
        const bookingArrivalDate = document.getElementById('bookingArrivalDate');
        const bookingBookingDate = document.getElementById('bookingBookingDate');
        const bookingDepartureDate = document.getElementById('bookingDepartureDate');
        const bookingRatePlan = document.getElementById('bookingRatePlan');
        const bookingRoomNumber = document.getElementById('bookingRoomNumber');
        const bookingRoomType = document.getElementById('bookingRoomType');
        const bookingNumberOfGuest = document.getElementById('bookingNumberOfGuest');
        const bookingRoomBooked = document.getElementById('bookingRoomBooked');
        
        if (bookingReservationNumber) bookingReservationNumber.textContent = booking.invoice_id || booking.id || '-';
        if (bookingArrivalDate) bookingArrivalDate.textContent = formatDateTime(booking.check_in_date, booking.check_in_time);
        if (bookingBookingDate) bookingBookingDate.textContent = formatDate(booking.created_at);
        if (bookingDepartureDate) bookingDepartureDate.textContent = formatDateTime(booking.check_out_date, booking.check_out_time);
        
        // For maintenance blocks, show "Maintenance" as rate plan, otherwise show reservation type
        if (isMaintenance) {
            if (bookingRatePlan) bookingRatePlan.textContent = 'Maintenance';
        } else {
            if (bookingRatePlan) bookingRatePlan.textContent = booking.reservation_type ? booking.reservation_type.charAt(0).toUpperCase() + booking.reservation_type.slice(1) : '-';
        }
        
        // Room number - handle both array and single value
        const roomNumberDisplay = Array.isArray(booking.room_numbers) 
            ? booking.room_numbers.join(', ') 
            : (booking.room_number || '-');
        if (bookingRoomNumber) bookingRoomNumber.textContent = roomNumberDisplay;
        if (bookingRoomType) bookingRoomType.textContent = booking.room_type || '-';
        
        // For maintenance blocks, these fields don't apply
        if (isMaintenance) {
            if (bookingNumberOfGuest) bookingNumberOfGuest.textContent = '-';
            if (bookingRoomBooked) bookingRoomBooked.textContent = '-';
        } else {
            if (bookingNumberOfGuest) bookingNumberOfGuest.textContent = booking.adults ? `${booking.adults}${booking.children ? ' + ' + booking.children + ' children' : ''}` : '-';
            if (bookingRoomBooked) bookingRoomBooked.textContent = booking.rooms || '-';
        }
        
        // Populate Payment Details tab
        const paymentFolio = document.getElementById('paymentFolio');
        const paymentDate = document.getElementById('paymentDate');
        
        if (paymentFolio) paymentFolio.textContent = booking.invoice_id || booking.id || '-';
        if (paymentDate) paymentDate.textContent = formatDate(booking.created_at || booking.check_in_date);
        
        // For maintenance blocks, payment details don't apply
        if (isMaintenance) {
            const paymentRoomPrice = document.getElementById('paymentRoomPrice');
            const paymentTotalCharges = document.getElementById('paymentTotalCharges');
            const paymentSGST = document.getElementById('paymentSGST');
            const paymentCGST = document.getElementById('paymentCGST');
            const paymentExtraCharges = document.getElementById('paymentExtraCharges');
            const paymentTotalPayable = document.getElementById('paymentTotalPayable');
            const paymentBalance = document.getElementById('paymentBalance');
            const footerTotal = document.getElementById('footerTotal');
            const footerPaid = document.getElementById('footerPaid');
            const footerBalance = document.getElementById('footerBalance');
            
            if (paymentRoomPrice) paymentRoomPrice.textContent = formatCurrency(0);
            if (paymentTotalCharges) paymentTotalCharges.textContent = formatCurrency(0);
            if (paymentSGST) paymentSGST.textContent = formatCurrency(0);
            if (paymentCGST) paymentCGST.textContent = formatCurrency(0);
            if (paymentExtraCharges) paymentExtraCharges.textContent = formatCurrency(0);
            if (paymentTotalPayable) paymentTotalPayable.textContent = formatCurrency(0);
            if (paymentBalance) paymentBalance.textContent = formatCurrency(0);
            if (footerTotal) footerTotal.textContent = formatCurrency(0);
            if (footerPaid) footerPaid.textContent = formatCurrency(0);
            if (footerBalance) footerBalance.textContent = formatCurrency(0);
        } else {
            // Get grand_total from orders table - ensure it's a number
            const grandTotal = parseFloat(booking.grand_total) || 0;
            const roomPrice = parseFloat(booking.room_price) || 0;
            const subTotal = parseFloat(booking.sub_total) || 0;
            const discount = parseFloat(booking.discount) || 0;
            
            const paymentRoomPrice = document.getElementById('paymentRoomPrice');
            const paymentTotalCharges = document.getElementById('paymentTotalCharges');
            const paymentSGST = document.getElementById('paymentSGST');
            const paymentCGST = document.getElementById('paymentCGST');
            const paymentExtraCharges = document.getElementById('paymentExtraCharges');
            const paymentTotalPayable = document.getElementById('paymentTotalPayable');
            const paymentBalance = document.getElementById('paymentBalance');
            const footerTotal = document.getElementById('footerTotal');
            const footerPaid = document.getElementById('footerPaid');
            const footerBalance = document.getElementById('footerBalance');
            
            if (paymentRoomPrice) paymentRoomPrice.textContent = formatCurrency(roomPrice);
            if (paymentTotalCharges) paymentTotalCharges.textContent = formatCurrency(subTotal);
            if (paymentSGST) paymentSGST.textContent = formatCurrency(0); // SGST not in current data
            if (paymentCGST) paymentCGST.textContent = formatCurrency(0); // CGST not in current data
            if (paymentExtraCharges) paymentExtraCharges.textContent = formatCurrency(0); // Extra charges not in current data
            if (paymentTotalPayable) paymentTotalPayable.textContent = formatCurrency(grandTotal);
            
            const balance = grandTotal - discount;
            if (paymentBalance) paymentBalance.textContent = formatCurrency(balance);
            
            // Populate footer section with dynamic values
            if (footerTotal) footerTotal.textContent = formatCurrency(grandTotal);
            
            // Calculate paid amount based on payment_status
            const paymentStatus = (booking.payment_status || '').toUpperCase();
            const isPaid = paymentStatus === 'PAID' || paymentStatus === 'COMPLETED' || paymentStatus === 'SUCCESS';
            const paidAmount = isPaid ? grandTotal : 0;
            const footerBalanceAmount = isPaid ? 0 : balance;
            
            if (footerPaid) footerPaid.textContent = formatCurrency(paidAmount);
            if (footerBalance) footerBalance.textContent = formatCurrency(footerBalanceAmount);
        }
        
        // Store current booking data for calculations
        currentBookingData = booking;
        
        // Fetch and display existing charges and payments
        // Use numeric order_id if available, otherwise skip for maintenance blocks
        if (booking.order_id) {
            loadBookingPaymentHistory(booking.order_id);
        } else if (!isMaintenance) {
            // If no order_id but not maintenance, try to extract from id
            const numericId = booking.id?.toString().replace(/[^0-9]/g, '');
            if (numericId) {
                loadBookingPaymentHistory(numericId);
            } else {
                console.warn('No valid order ID found for booking:', booking);
                renderBookingFolioHistory(); // Show empty state
            }
        }
    }
    
    // Booking Modal - Charges & Payments Functionality
    let currentBookingId = null;
    let currentBookingData = null;
    let bookingCharges = [];
    let bookingPayments = [];
    
    // Load existing charges and payments from database
    async function loadBookingPaymentHistory(orderId) {
        console.log('Loading payment history for order ID:', orderId);
        currentBookingId = orderId;
        bookingCharges = [];
        bookingPayments = [];
        
        try {
            const url = `{{ url('/order-payments') }}/${orderId}`;
            console.log('Fetching from URL:', url);
            const response = await fetch(url);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success && data.payments) {
                console.log('Found payments:', data.payments.length);
                console.log('Payments data:', data.payments);
                // Separate charges and payments
                data.payments.forEach(item => {
                    console.log('Processing item:', item);
                    if (item.type === 'Charge') {
                        const charge = {
                            id: item.id,
                            comment: item.comment || 'Charge',
                            amount: parseFloat(item.amount) || 0,
                            display_date: new Date(item.created_at).toLocaleString('en-GB', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }),
                            existing: true
                        };
                        console.log('Adding charge:', charge);
                        bookingCharges.push(charge);
                    } else if (item.type === 'Payment') {
                        const payment = {
                            id: item.id,
                            type: item.payment_type || 'Cash',
                            method: item.payment_method || 'Cash',
                            comment: item.comment || 'Payment',
                            amount: parseFloat(item.amount) || 0,
                            display_date: new Date(item.created_at).toLocaleString('en-GB', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            }),
                            existing: true
                        };
                        console.log('Adding payment:', payment);
                        bookingPayments.push(payment);
                    }
                });
                console.log('Total charges loaded:', bookingCharges.length);
                console.log('Total payments loaded:', bookingPayments.length);
            } else {
                console.warn('No payments data in response or not successful');
            }
        } catch (error) {
            console.error('Error loading payment history:', error);
        }
        
        console.log('Rendering lists...');
        renderBookingChargesList();
        renderBookingPaymentsList();
        renderBookingFolioHistory();
        recalcBookingSummary();
        console.log('Rendering complete');
    }
    
    // Handle payment type change
    document.getElementById('bookingPaymentType').addEventListener('change', function() {
        const methodSelect = document.getElementById('bookingPaymentMethod');
        methodSelect.innerHTML = '<option value="">Select Method</option>';
        
        if (this.value === 'Cash') {
            methodSelect.innerHTML += '<option value="Cash" selected>Cash</option>';
            methodSelect.disabled = true;
            methodSelect.value = 'Cash';
        } else if (this.value === 'Online') {
            methodSelect.innerHTML += `
                <option value="UPI">UPI</option>
                <option value="Card">Card</option>
                <option value="Net Banking">Net Banking</option>
            `;
            methodSelect.disabled = false;
        } else {
            methodSelect.disabled = true;
        }
    });
    
    // Add Charge
    document.getElementById('bookingChargeAddBtn').addEventListener('click', function() {
        const commentInput = document.getElementById('bookingChargeComment');
        const amountInput = document.getElementById('bookingChargeAmount');
        
        const comment = commentInput.value.trim();
        const amount = parseFloat(amountInput.value);
        
        if (!comment) {
            swal("Error!", 'Please enter a comment');    
            return;
        }
        
        if (!amount || amount <= 0) {
            swal("Error!", 'Please enter a valid amount');
            return;
        }
        
        bookingCharges.push({
            comment,
            amount,
            display_date: new Date().toLocaleString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }),
            existing: false
        });
        
        commentInput.value = '';
        amountInput.value = '';
        
        renderBookingChargesList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    });
    
    // Add Payment
    document.getElementById('bookingPaymentAddBtn').addEventListener('click', function() {
        const typeSelect = document.getElementById('bookingPaymentType');
        const methodSelect = document.getElementById('bookingPaymentMethod');
        const commentInput = document.getElementById('bookingPaymentComment');
        const amountInput = document.getElementById('bookingPaymentAmount');
        
        const type = typeSelect.value;
        const method = methodSelect.value;
        const comment = commentInput.value.trim();
        const amount = parseFloat(amountInput.value);
        
        if (!type) {
            swal("Error!", 'Please select a payment type');
            return;
        }
        
        if (!method) {
            swal("Error!", 'Please select a payment method');
            return;
        }
        
        if (!amount || amount <= 0) {
            swal("Error!", 'Please enter a valid amount');
            return;
        }
        
        bookingPayments.push({
            type,
            method,
            comment: comment || 'Payment',
            amount,
            display_date: new Date().toLocaleString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            }),
            existing: false
        });
        
        typeSelect.value = '';
        methodSelect.value = '';
        methodSelect.disabled = true;
        commentInput.value = '';
        amountInput.value = '';
        
        renderBookingPaymentsList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    });
    
    // Render charges list
    function renderBookingChargesList() {
        const container = document.getElementById('bookingChargesList');
        if (!container) return;
        
        const newCharges = bookingCharges.filter(c => !c.existing);
        
        if (newCharges.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0">No new charges added</p>';
            return;
        }
        
        container.innerHTML = newCharges.map((charge, index) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <div>
                    <strong>${charge.comment}</strong>
                    <br><small style="font-size: 12px;">${charge.display_date}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">Rs. ${charge.amount.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBookingCharge(${bookingCharges.indexOf(charge)})">X</button>
                </div>
            </div>
        `).join('');
    }
    
    // Render payments list
    function renderBookingPaymentsList() {
        const container = document.getElementById('bookingPaymentsList');
        if (!container) return;
        
        const newPayments = bookingPayments.filter(p => !p.existing);
        
        if (newPayments.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0">No new payments added</p>';
            return;
        }
        
        container.innerHTML = newPayments.map((payment, index) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <div>
                    <strong>${payment.comment}</strong>
                    <br><small>${payment.type} - ${payment.method}</small>
                    <br><small style="font-size: 12px;">${payment.display_date}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">Rs. ${payment.amount.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBookingPayment(${bookingPayments.indexOf(payment)})">X</button>
                </div>
            </div>
        `).join('');
    }
    
    // Render folio history
    function renderBookingFolioHistory() {
        const container = document.getElementById('bookingFolioHistoryDetails');
        if (!container) return;
        
        const formatAmount = (amount) => {
            return parseFloat(amount).toFixed(2);
        };
        
        let html = '<ul class="list-unstyled">';
        
        // Show all charges (existing + new)
        bookingCharges.forEach((charge) => {
            html += `
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <div>
                    <p class="mb-0">${charge.comment}</p>
                    <small class="text-muted" style="font-size: 12px;">${charge.display_date}</small>
                </div>
                <p class="mb-0 text-danger"><strong>+ Rs. ${formatAmount(charge.amount)}</strong></p>
            </li>
            `;
        });
        
        // Show all payments (existing + new)
        bookingPayments.forEach((payment) => {
            html += `
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <div>
                    <p class="mb-0">${payment.type} - ${payment.method}</p>
                    <small class="text-muted" style="font-size: 12px;">${payment.comment} | ${payment.display_date}</small>
                </div>
                <p class="mb-0 text-success">- Rs. ${formatAmount(payment.amount)}</p>
            </li>
            `;
        });
        
        html += '</ul>';
        
        if (bookingCharges.length === 0 && bookingPayments.length === 0) {
            html = '<p class="text-muted">No charges or payments recorded yet.</p>';
        }
        
        container.innerHTML = html;
    }
    
    // Calculate summary
    function recalcBookingSummary() {
        if (!currentBookingData) return;
        
        const formatCurrency = (amount) => {
            if (!amount && amount !== 0) return 'Rs. 0';
            return `Rs. ${parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };
        
        // Calculate new charges and payments only (existing ones are already in grand_total)
        const newCharges = bookingCharges.filter(c => !c.existing);
        const newPayments = bookingPayments.filter(p => !p.existing);
        
        const totalNewCharges = newCharges.reduce((sum, c) => sum + c.amount, 0);
        const totalNewPayments = newPayments.reduce((sum, p) => sum + p.amount, 0);
        
        // Calculate total existing payments from database
        const totalExistingPayments = bookingPayments.filter(p => p.existing).reduce((sum, p) => sum + p.amount, 0);
        
        // Get original values
        const originalGrandTotal = parseFloat(currentBookingData.grand_total) || 0;
        
        // Calculate new totals
        const newGrandTotal = originalGrandTotal + totalNewCharges;
        // Balance = Grand Total - All Payments (existing + new)
        const newBalance = newGrandTotal - (totalExistingPayments + totalNewPayments);
        
        // Update Extra Charges display (show total new charges)
        const paymentExtraCharges = document.getElementById('paymentExtraCharges');
        if (paymentExtraCharges) {
            paymentExtraCharges.textContent = formatCurrency(totalNewCharges);
        }
        
        // Update Total Payable
        const paymentTotalPayable = document.getElementById('paymentTotalPayable');
        if (paymentTotalPayable) {
            paymentTotalPayable.textContent = formatCurrency(newGrandTotal);
        }
        
        // Update Balance
        const paymentBalance = document.getElementById('paymentBalance');
        if (paymentBalance) {
            paymentBalance.textContent = formatCurrency(newBalance);
        }
        
        // Update footer
        const footerTotal = document.getElementById('footerTotal');
        const footerPaid = document.getElementById('footerPaid');
        const footerBalance = document.getElementById('footerBalance');
        
        if (footerTotal) footerTotal.textContent = formatCurrency(newGrandTotal);
        if (footerPaid) footerPaid.textContent = formatCurrency(totalNewPayments);
        if (footerBalance) footerBalance.textContent = formatCurrency(newBalance);
    }
    
    // Remove charge
    window.removeBookingCharge = function(index) {
        bookingCharges.splice(index, 1);
        renderBookingChargesList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    };
    
    // Remove payment
    window.removeBookingPayment = function(index) {
        bookingPayments.splice(index, 1);
        renderBookingPaymentsList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    };
    
    // Save charges and payments to database
    window.saveBookingChargesPayments = async function() {
        if (!currentBookingId) {
            swal("Error!", 'No booking selected');
            return false;
        }
        
        if (!currentBookingData || currentBookingData.type === 'maintenance') {
            swal("Error!", 'Cannot add charges or payments to maintenance blocks');
            return false;
        }
        
        const newCharges = bookingCharges.filter(c => !c.existing);
        const newPayments = bookingPayments.filter(p => !p.existing);
        
        if (newCharges.length === 0 && newPayments.length === 0) {
            swal("Error!", 'No new charges or payments to save');
            return true; // Nothing to save
        }
        
        try {
            const payload = {
                order_id: currentBookingId,
                charges: newCharges,
                payments: newPayments
            };
            
            console.log('Saving charges and payments:', payload);
            
            const response = await fetch('{{ route("booking.saveChargesPayments") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });
            
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                swal("Success!", 'Charges and payments saved successfully!');
                // Reload the payment history to show all as existing
                await loadBookingPaymentHistory(currentBookingId);
                return true;
            } else {
                let errorMessage = data.message || 'Failed to save charges and payments';
                
                // Show validation errors if available
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nValidation Errors:\n' + errorDetails;
                }
                
                // Show technical error if available
                if (data.error) {
                    errorMessage += '\n\nError: ' + data.error;
                    if (data.file && data.line) {
                        errorMessage += '\nFile: ' + data.file + ' (Line ' + data.line + ')';
                    }
                }
                
                console.error('Save failed:', data);
                swal("Error!", errorMessage);
                return false;
            }
        } catch (error) {
            console.error('Error saving charges and payments:', error);
            swal("Error!", 'An error occurred while saving: ' + error.message);
            return false;
        }
    };

    function handleCellMouseDown(event, room, category, iso) {
        if (event) {
            event.preventDefault();
        }
        // Check if the initial date is already booked
        if (isDateRangeBooked(room.id, iso, iso)) {
            // Prevent selection if the date is booked
            return;
        }
        hideSelectionTooltip();
        // Clear all previous selections when starting a new selection
        roomSelections.clear();
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
        // Check if the current cell itself is booked
        if (getBookingForCell(roomId, iso)) {
            // Don't allow extending selection to a booked date
            return;
        }
        // Check if extending the selection to this date would include any booked dates
        const currentStart = selectionState.dragStartDate;
        const proposedRange = normalizeRange(currentStart, iso);
        if (proposedRange && isDateRangeBooked(roomId, proposedRange.start, proposedRange.end)) {
            // Don't allow extending selection into booked dates
            // Keep the end date at the last valid (non-booked) position
            return;
        }
        // Only update if the proposed range doesn't include booked dates
        selectionState.dragEndDate = iso;
        updateSelectionClasses();
    }

    function commitSelection() {
        if (!selectionState.activeRoomId || !selectionState.dragStartDate) {
            return;
        }

        const range = normalizeRange(selectionState.dragStartDate, selectionState.dragEndDate || selectionState.dragStartDate);
        
        // Final validation: check if any date in the range is booked
        if (isDateRangeBooked(selectionState.activeRoomId, range.start, range.end)) {
            // Cancel selection if any date in the range is booked
            resetDragging();
            updateSelectionClasses();
            // Optionally show an alert to the user                            
            swal("Error!", 'Cannot select dates that are already booked. Please choose different dates.');
            return;
        }

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
        const totalRoomsInput = document.getElementById('quickRoomsCount');
        const totalAmountLabel = document.getElementById('quickTotalAmountLabel');
        const paidAmountLabel = document.getElementById('quickPaidAmountLabel');
        const balanceAmountLabel = document.getElementById('quickBalanceAmountLabel');
        const errorsBox = document.getElementById('quickReservationErrors');
        const checkInDateInput = document.getElementById('quickCheckInDate');
        const checkOutDateInput = document.getElementById('quickCheckOutDate');
        const roomNumberSearchInput = document.getElementById('quickRoomNumberSearch');
        const roomNumberDropdown = document.getElementById('quickRoomNumberDropdown');
        const roomNumberIdsInput = document.getElementById('quickRoomNumberIds');
        const selectedRoomNumbersDiv = document.getElementById('quickSelectedRoomNumbers');
        const endpoint = form.dataset.endpoint;
        if (!detailsContainer) {
            return;
        }
        
        // Track if dates are from calendar selection
        let isDatesFromCalendarSelection = false;
        
        // Track selected room numbers
        let selectedRoomNumbers = new Map(); // Map<roomNumberId, {id, number, roomType}>

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

        // Function to filter available room types based on calendar availability
        function getAvailableRoomTypes() {
            const checkInDate = checkInDateInput?.value;
            const checkOutDate = checkOutDateInput?.value;
            
            if (!checkInDate || !checkOutDate || !serverRoomsData || !Array.isArray(serverRoomsData)) {
                return roomsList; // Return all if dates not set or data not available
            }
            
            // Filter room types that have availability for the selected dates
            const availableRoomTypes = [];
            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);
            
            roomsList.forEach((roomType) => {
                // Find matching room type in serverRoomsData
                const roomData = serverRoomsData.find((rd) => rd.title === roomType.title);
                if (!roomData || !roomData.summary) {
                    return;
                }
                
                // Check availability for each date in the range
                let hasAvailability = false;
                const currentDate = new Date(checkIn);
                
                while (currentDate < checkOut) {
                    const dateIso = currentDate.toISOString().split('T')[0];
                    const summary = roomData.summary;
                    const overrides = summary.overrides || {};
                    const override = overrides[dateIso];
                    
                    // Get count for this date (override or default)
                    const count = override?.count ?? summary.defaultCount ?? 0;
                    
                    if (count > 0) {
                        hasAvailability = true;
                        break;
                    }
                    
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                if (hasAvailability) {
                    availableRoomTypes.push(roomType);
                }
            });
            
            return availableRoomTypes.length > 0 ? availableRoomTypes : roomsList; // Fallback to all if none available
        }

        const roomOptionsMarkup = (() => {
            const options = ['<option value="">Select</option>'];
            const availableRooms = getAvailableRoomTypes();
            availableRooms.forEach((room) => {
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

        // Function to get available rooms count for a room type
        function getAvailableRoomsCount(roomTypeTitle) {
            const checkInDate = checkInDateInput?.value;
            const checkOutDate = checkOutDateInput?.value;
            
            if (!checkInDate || !checkOutDate || !serverRoomsData || !Array.isArray(serverRoomsData)) {
                return null; // Return null if dates not set
            }
            
            // Find matching room type in serverRoomsData
            const roomData = serverRoomsData.find((rd) => rd.title === roomTypeTitle);
            if (!roomData || !roomData.summary) {
                return null;
            }
            
            // Find minimum availability across all dates in the range
            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);
            let minAvailability = Infinity;
            const currentDate = new Date(checkIn);
            
            while (currentDate < checkOut) {
                const dateIso = currentDate.toISOString().split('T')[0];
                const summary = roomData.summary;
                const overrides = summary.overrides || {};
                const override = overrides[dateIso];
                
                // Get count for this date (override or default)
                const count = override?.count ?? summary.defaultCount ?? 0;
                
                if (count < minAvailability) {
                    minAvailability = count;
                }
                
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            return minAvailability === Infinity ? null : minAvailability;
        }

        // Function to get available room numbers for a room type based on dates
        function getAvailableRoomNumbers(roomTypeId) {
            const checkInDate = checkInDateInput?.value;
            const checkOutDate = checkOutDateInput?.value;
            
            if (!checkInDate || !checkOutDate || !serverRoomsData || !Array.isArray(serverRoomsData)) {
                return []; // Return empty if dates not set
            }
            
            // Find room type in serverRoomsData
            const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
            if (!roomTypeSelect || !roomTypeSelect.value) {
                return [];
            }
            
            const roomTypeTitle = roomTypeSelect.options[roomTypeSelect.selectedIndex]?.text || '';
            const roomData = serverRoomsData.find((rd) => rd.title === roomTypeTitle);
            
            if (!roomData || !roomData.rooms || !Array.isArray(roomData.rooms)) {
                return [];
            }
            
            // Get booking lookup from bookingData
            const checkIn = new Date(checkInDate);
            const checkOut = new Date(checkOutDate);
            const bookingLookup = new Map();
            
            // Build booking lookup from bookingData
            if (bookingData && Array.isArray(bookingData)) {
                bookingData.forEach((booking) => {
                    const bookingStart = booking.start_date || booking.startDate;
                    const bookingEnd = booking.end_date || booking.endDate || bookingStart;
                    if (!bookingStart) return;
                    
                    try {
                        const start = new Date(bookingStart);
                        const end = new Date(bookingEnd);
                        const inclusiveEnd = booking.inclusive_end || booking.is_blocked;
                        
                        let currentDate = new Date(start);
                        while (true) {
                            const shouldInclude = inclusiveEnd ? currentDate <= end : currentDate < end;
                            if (!shouldInclude) break;
                            
                            if (currentDate >= checkIn && currentDate < checkOut) {
                                const dateIso = currentDate.toISOString().split('T')[0];
                                
                                // Get room number IDs from booking
                                let roomNumberIds = [];
                                if (booking.room_number_ids) {
                                    roomNumberIds = booking.room_number_ids.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
                                } else if (booking.is_blocked && booking.room_id) {
                                    roomNumberIds = [parseInt(booking.room_id)];
                                }
                                
                                roomNumberIds.forEach((roomNumberId) => {
                                    if (!bookingLookup.has(roomNumberId)) {
                                        bookingLookup.set(roomNumberId, new Set());
                                    }
                                    bookingLookup.get(roomNumberId).add(dateIso);
                                });
                            }
                            
                            currentDate.setDate(currentDate.getDate() + 1);
                            if (currentDate > end) break;
                        }
                    } catch (e) {
                        // Skip invalid dates
                    }
                });
            }
            
            // Filter available room numbers
            const availableRooms = [];
            roomData.rooms.forEach((room) => {
                const roomNumberId = room.id;
                const bookedDates = bookingLookup.get(roomNumberId);
                
                // Check if room is available for all dates in the range
                let isAvailable = true;
                const currentDate = new Date(checkIn);
                
                while (currentDate < checkOut) {
                    const dateIso = currentDate.toISOString().split('T')[0];
                    if (bookedDates && bookedDates.has(dateIso)) {
                        isAvailable = false;
                        break;
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                if (isAvailable) {
                    availableRooms.push({
                        id: roomNumberId,
                        number: room.number,
                        roomType: roomTypeTitle
                    });
                }
            });
            
            return availableRooms;
        }

        function addRoomRow(initial = {}, isFromCalendarSelection = false) {
            const currentIndex = rowIndex++;
            const wrapper = document.createElement('div');
            wrapper.className = 'room-detail-item';
            wrapper.dataset.index = currentIndex;
            
            // Determine rooms input attributes based on whether dates are from calendar selection
            const roomsReadonly = isFromCalendarSelection ? 'readonly' : '';
            const roomsValue = isFromCalendarSelection ? '1' : (initial.rooms || '1');
            const roomsStyle = isFromCalendarSelection ? 'style="background-color: #e9ecef; cursor: not-allowed;"' : '';
            
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
                        <input type="number" class="form-control room-qty-input" name="room_details[${currentIndex}][rooms]" min="1" max="" value="${roomsValue}" ${roomsReadonly} ${roomsStyle}>
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
                </div>
            `;
            detailsContainer.appendChild(wrapper);
            const roomSelect = wrapper.querySelector('.room-type-select');
            const roomsInput = wrapper.querySelector('.room-qty-input');
            
            if (initial.room_id) {
                roomSelect.value = initial.room_id;
            }
            
            // Update rooms field max value based on selected room type (if not from calendar selection)
            if (!isFromCalendarSelection && roomSelect.value) {
                const selectedRoomType = roomsList.find(r => r.id == roomSelect.value);
                if (selectedRoomType) {
                    const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                    if (availableCount !== null) {
                        roomsInput.setAttribute('max', availableCount);
                    }
                }
            }
            
            updateRateOptions(wrapper, roomSelect.value || null, initial.room_price_id);
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
            
            // Update Payment Details tab
            updatePaymentDetails(totalAmount);
            
            // Update folio history with charges and payments
            updatePaymentCalculations();
        }

        function updatePaymentDetails(totalAmount) {
            // Get current date
            const currentDate = new Date().toLocaleDateString('en-GB');
            
            // Update Payment Details section
            const quickPaymentDate = document.getElementById('quickPaymentDate');
            const quickPaymentRoomPrice = document.getElementById('quickPaymentRoomPrice');
            const quickPaymentTotalCharges = document.getElementById('quickPaymentTotalCharges');
            const quickPaymentTotalPayable = document.getElementById('quickPaymentTotalPayable');
            const quickPaymentBalance = document.getElementById('quickPaymentBalance');
            
            // Update Folio History section
            const quickFolioRoomPrice = document.getElementById('quickFolioRoomPrice');
            const quickFolioTotalCharges = document.getElementById('quickFolioTotalCharges');
            const quickFolioSGST = document.getElementById('quickFolioSGST');
            const quickFolioCGST = document.getElementById('quickFolioCGST');
            const quickFolioExtraCharges = document.getElementById('quickFolioExtraCharges');
            const quickFolioTotalPayable = document.getElementById('quickFolioTotalPayable');
            const quickFolioBalance = document.getElementById('quickFolioBalance');
            
            // Update Payment Details footer
            const quickPaymentFooterTotal = document.getElementById('quickPaymentFooterTotal');
            const quickPaymentFooterPaid = document.getElementById('quickPaymentFooterPaid');
            const quickPaymentFooterBalance = document.getElementById('quickPaymentFooterBalance');
            
            if (quickPaymentDate) {
                quickPaymentDate.textContent = currentDate;
            }
            if (quickPaymentRoomPrice) {
                quickPaymentRoomPrice.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickPaymentTotalCharges) {
                quickPaymentTotalCharges.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickPaymentTotalPayable) {
                quickPaymentTotalPayable.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickPaymentBalance) {
                quickPaymentBalance.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            
            // Update Folio History section with same values
            if (quickFolioRoomPrice) {
                quickFolioRoomPrice.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickFolioTotalCharges) {
                quickFolioTotalCharges.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickFolioSGST) {
                quickFolioSGST.textContent = 'Rs. 0';
            }
            if (quickFolioCGST) {
                quickFolioCGST.textContent = 'Rs. 0';
            }
            if (quickFolioExtraCharges) {
                quickFolioExtraCharges.textContent = 'Rs. 0';
            }
            if (quickFolioTotalPayable) {
                quickFolioTotalPayable.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            if (quickFolioBalance) {
                quickFolioBalance.textContent = 'Rs. ' + formatAmount(totalAmount);
            }
            
            // Update footer values
            if (quickPaymentFooterTotal) {
                quickPaymentFooterTotal.textContent = formatAmount(totalAmount);
            }
            if (quickPaymentFooterPaid) {
                quickPaymentFooterPaid.textContent = formatAmount(0);
            }
            if (quickPaymentFooterBalance) {
                quickPaymentFooterBalance.textContent = formatAmount(totalAmount);
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
            
            // Get selected room number IDs from the room number selection field or selection context
            let selectedRoomNumberIds = [];
            
            // First, try to get from room number selection field
            if (roomNumberIdsInput && roomNumberIdsInput.value) {
                selectedRoomNumberIds = roomNumberIdsInput.value.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
            }
            
            // Fallback to selection context if no room numbers selected in field
            if (selectedRoomNumberIds.length === 0) {
            const selectionContext = getSelectionContextForModal();
                selectedRoomNumberIds = selectionContext?.roomNumberIds || [];
            }
            
            // Build mapping from room number ID to room type ID from roomsData
            const roomNumberToRoomTypeMap = new Map();
            roomsData.forEach((category) => {
                if (category.rooms && Array.isArray(category.rooms)) {
                    // Find matching room type in quickReservationRooms by title
                    const matchingRoomType = quickReservationRooms.find((rt) => rt.title === category.title);
                    if (matchingRoomType) {
                        category.rooms.forEach((room) => {
                            roomNumberToRoomTypeMap.set(room.id, matchingRoomType.id);
                        });
                    }
                }
            });
            
            const roomLines = [];
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const roomId = row.querySelector('.room-type-select')?.value;
                const rateId = row.querySelector('.room-rate-select')?.value;
                const qty = row.querySelector('.room-qty-input')?.value;
                if (roomId && rateId && qty) {
                    // Get room number IDs for this specific room type from the selection
                    const roomTypeId = Number(roomId);
                    const roomNumberIdsForThisRoom = selectedRoomNumberIds.filter((roomNumberId) => {
                        const mappedRoomTypeId = roomNumberToRoomTypeMap.get(roomNumberId);
                        return mappedRoomTypeId === roomTypeId;
                    });
                    
                    roomLines.push({
                        room_id: roomTypeId,
                        room_price_id: Number(rateId),
                        rooms: Number(qty),
                        adults: Number(row.querySelector('.room-adult-input')?.value || 0),
                        children: Number(row.querySelector('.room-child-input')?.value || 0),
                        price: Number(row.querySelector('.room-price-input')?.value || 0),
                        room_number_ids: roomNumberIdsForThisRoom.length > 0 ? roomNumberIdsForThisRoom.join(',') : null
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
                    reservation_type: 'confirmed', // Always set to confirmed
                    room_details: roomLines,
                    guest: {
                        name: document.getElementById('quickGuestName')?.value,
                        gender: document.getElementById('quickGuestGender')?.value || null,
                        email: document.getElementById('quickGuestEmail')?.value || null,
                        mobile: document.getElementById('quickGuestMobile')?.value
                    }
                };
        }

        // Removed add room button functionality - single entry only
        
        // Alias for form data collection (used by confirm button)
        const collectFormData = buildPayload;

        // Function to check if room type is selected and update Payment Details tab state
        function updatePaymentDetailsTabState() {
            const paymentDetailsTab = document.getElementById('nav-profile-tab');
            if (!paymentDetailsTab) {
                return;
            }
            
            // Check if any room type is selected
            const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
            const isRoomTypeSelected = roomTypeSelect && roomTypeSelect.value;
            
            if (isRoomTypeSelected) {
                // Enable the Payment Details tab
                paymentDetailsTab.classList.remove('disabled');
                paymentDetailsTab.removeAttribute('aria-disabled');
                paymentDetailsTab.style.pointerEvents = '';
                paymentDetailsTab.style.opacity = '';
            } else {
                // Disable the Payment Details tab
                paymentDetailsTab.classList.add('disabled');
                paymentDetailsTab.setAttribute('aria-disabled', 'true');
                paymentDetailsTab.style.pointerEvents = 'none';
                paymentDetailsTab.style.opacity = '0.5';
            }
        }

        detailsContainer.addEventListener('change', (event) => {
            const row = event.target.closest('.room-detail-item');
            if (!row) {
                return;
            }
            if (event.target.classList.contains('room-type-select')) {
                updateRateOptions(row, event.target.value);
                
                // Update rooms field max value based on selected room type (if not readonly)
                const roomsInput = row.querySelector('.room-qty-input');
                if (roomsInput && !roomsInput.hasAttribute('readonly')) {
                    const selectedRoomType = roomsList.find(r => r.id == event.target.value);
                    if (selectedRoomType) {
                        const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                        if (availableCount !== null) {
                            roomsInput.setAttribute('max', availableCount);
                            // If current value exceeds max, set to max
                            if (parseInt(roomsInput.value, 10) > availableCount) {
                                roomsInput.value = availableCount;
                            }
                        }
                    }
                }
                
                // Update room number options when room type changes
                updateRoomNumberOptions();
                
                // Update Payment Details tab state
                updatePaymentDetailsTabState();
            } else if (event.target.classList.contains('room-rate-select')) {
                updateRateOptions(row, row.querySelector('.room-type-select')?.value, event.target.value);
            }
            recalcSummary();
        });

        // Function to update room number dropdown options
        function updateRoomNumberOptions() {
            if (!roomNumberSearchInput || !roomNumberDropdown) {
                return;
            }
            
            const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
            if (!roomTypeSelect || !roomTypeSelect.value) {
                roomNumberDropdown.innerHTML = '';
                roomNumberDropdown.style.display = 'none';
                return;
            }
            
            const availableRooms = getAvailableRoomNumbers(roomTypeSelect.value);
            
            // Store available rooms for search
            roomNumberDropdown.dataset.availableRooms = JSON.stringify(availableRooms);
            
            // Set readonly state based on calendar selection
            if (isDatesFromCalendarSelection) {
                roomNumberSearchInput.setAttribute('readonly', 'readonly');
                roomNumberSearchInput.style.backgroundColor = '#e9ecef';
                roomNumberSearchInput.style.cursor = 'not-allowed';
            } else {
                roomNumberSearchInput.removeAttribute('readonly');
                roomNumberSearchInput.style.backgroundColor = '';
                roomNumberSearchInput.style.cursor = '';
            }
        }
        
        // Function to render selected room numbers
        function renderSelectedRoomNumbers() {
            if (!selectedRoomNumbersDiv) return;
            
            if (selectedRoomNumbers.size === 0) {
                selectedRoomNumbersDiv.innerHTML = '';
                roomNumberIdsInput.value = '';
                return;
            }
            
            const selectedIds = Array.from(selectedRoomNumbers.keys());
            roomNumberIdsInput.value = selectedIds.join(',');
            
            let html = '<div class="d-flex flex-wrap gap-2">';
            selectedRoomNumbers.forEach((room, id) => {
                html += `<span class="badge bg-primary d-flex align-items-center gap-1">
                    ${room.number}
                    ${!isDatesFromCalendarSelection ? `<button type="button" class="btn-close btn-close-white" style="font-size: 0.7em;" data-room-id="${id}"></button>` : ''}
                </span>`;
            });
            html += '</div>';
            selectedRoomNumbersDiv.innerHTML = html;
            
            // Add remove handlers
            selectedRoomNumbersDiv.querySelectorAll('[data-room-id]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    const roomId = parseInt(e.target.dataset.roomId);
                    selectedRoomNumbers.delete(roomId);
                    renderSelectedRoomNumbers();
                    roomNumberSearchInput.value = '';
                    roomNumberDropdown.style.display = 'none';
                });
            });
        }
        
        // Room number search functionality
        if (roomNumberSearchInput && roomNumberDropdown) {
            let searchTimeout = null;
            
            roomNumberSearchInput.addEventListener('input', (e) => {
                if (isDatesFromCalendarSelection) {
                    return; // Don't allow search if readonly
                }
                
                const searchTerm = e.target.value.toLowerCase().trim();
                
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const availableRoomsJson = roomNumberDropdown.dataset.availableRooms;
                    if (!availableRoomsJson) {
                        updateRoomNumberOptions();
                return;
            }
                    
                    const availableRooms = JSON.parse(availableRoomsJson);
                    const filtered = availableRooms.filter(room => {
                        // Filter out already selected rooms
                        if (selectedRoomNumbers.has(room.id)) {
                            return false;
                        }
                        // Filter by search term
                        return room.number.toLowerCase().includes(searchTerm);
                    });
                    
                    if (filtered.length === 0 || !searchTerm) {
                        roomNumberDropdown.style.display = 'none';
                        return;
                    }
                    
                    // Render dropdown
                    let dropdownHtml = '<div class="list-group" style="max-height: 200px; overflow-y: auto;">';
                    filtered.forEach((room) => {
                        dropdownHtml += `<a href="#" class="list-group-item list-group-item-action" data-room-id="${room.id}" data-room-number="${room.number}">${room.number}</a>`;
                    });
                    dropdownHtml += '</div>';
                    roomNumberDropdown.innerHTML = dropdownHtml;
                    roomNumberDropdown.style.display = 'block';
                    
                    // Add click handlers
                    roomNumberDropdown.querySelectorAll('[data-room-id]').forEach((item) => {
                        item.addEventListener('click', (e) => {
                            e.preventDefault();
                            const roomId = parseInt(e.target.dataset.roomId);
                            const roomNumber = e.target.dataset.roomNumber;
                            const roomType = e.target.closest('.room-detail-item')?.querySelector('.room-type-select')?.options[detailsContainer.querySelector('.room-type-select')?.selectedIndex]?.text || '';
                            
                            selectedRoomNumbers.set(roomId, {
                                id: roomId,
                                number: roomNumber,
                                roomType: roomType
                            });
                            
                            renderSelectedRoomNumbers();
                            roomNumberSearchInput.value = '';
                            roomNumberDropdown.style.display = 'none';
                        });
                    });
                }, 300);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!roomNumberSearchInput.contains(e.target) && !roomNumberDropdown.contains(e.target)) {
                    roomNumberDropdown.style.display = 'none';
                }
            });
        }

        detailsContainer.addEventListener('input', (event) => {
            if (event.target.classList.contains('room-qty-input')
                || event.target.classList.contains('room-price-input')) {
            recalcSummary();
            }
        });

        // Removed remove room button functionality - single entry only

        checkInDateInput?.addEventListener('change', () => {
            const minDate = checkInDateInput.value || todayIso;
            if (checkOutDateInput) {
                checkOutDateInput.setAttribute('min', minDate);
                if (checkOutDateInput.value && checkOutDateInput.value < minDate) {
                    checkOutDateInput.value = minDate;
                }
            }
            // Update room type options based on new dates
            updateRoomTypeOptions();
            // Update rooms max value for all rows (if not readonly)
            updateRoomsMaxValues();
            recalcSummary();
        });

        checkOutDateInput?.addEventListener('change', () => {
            // Update room type options based on new dates
            updateRoomTypeOptions();
            // Update rooms max value for all rows (if not readonly)
            updateRoomsMaxValues();
            recalcSummary();
        });
        
        // Function to update max value for rooms input fields
        function updateRoomsMaxValues() {
            if (isDatesFromCalendarSelection) {
                return; // Don't update if from calendar selection (readonly)
            }
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const roomsInput = row.querySelector('.room-qty-input');
                const roomSelect = row.querySelector('.room-type-select');
                if (roomsInput && !roomsInput.hasAttribute('readonly') && roomSelect?.value) {
                    const selectedRoomType = roomsList.find(r => r.id == roomSelect.value);
                    if (selectedRoomType) {
                        const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                        if (availableCount !== null) {
                            roomsInput.setAttribute('max', availableCount);
                            // If current value exceeds max, set to max
                            if (parseInt(roomsInput.value, 10) > availableCount) {
                                roomsInput.value = availableCount;
                            }
                        }
                    }
                }
            });
        }
        
        // Function to update room type dropdown options based on availability
        function updateRoomTypeOptions(preserveValue = null) {
            const availableRooms = getAvailableRoomTypes();
            const roomSelects = detailsContainer.querySelectorAll('.room-type-select');
            roomSelects.forEach((select) => {
                const currentValue = preserveValue !== null ? preserveValue : select.value;
                const wasDisabled = select.disabled;
                select.innerHTML = '<option value="">Select</option>';
                
                // Add available rooms
                availableRooms.forEach((room) => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.title;
                    select.appendChild(option);
                });
                
                // If preserveValue is set (from calendar selection) and not in available list, add it
                if (preserveValue !== null && !availableRooms.find(r => r.id == preserveValue)) {
                    const preservedRoomType = roomsList.find(r => r.id == preserveValue);
                    if (preservedRoomType) {
                        const option = document.createElement('option');
                        option.value = preservedRoomType.id;
                        option.textContent = preservedRoomType.title;
                        select.appendChild(option);
                    }
                }
                
                // Restore previous selection if still available, or if preserveValue is set (from calendar selection)
                if (currentValue) {
                    if (availableRooms.find(r => r.id == currentValue) || preserveValue !== null) {
                        select.value = currentValue;
                    }
                }
                
                // Restore disabled state if it was disabled
                if (wasDisabled) {
                    select.disabled = true;
                    select.style.backgroundColor = '#e9ecef';
                    select.style.cursor = 'not-allowed';
                }
            });
        }

        // checkOutDateInput change handler is already added above with updateRoomTypeOptions

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
                
                // Clear selected room numbers
                selectedRoomNumbers.clear();
                renderSelectedRoomNumbers();
                if (roomNumberSearchInput) {
                    roomNumberSearchInput.value = '';
                    roomNumberSearchInput.removeAttribute('readonly');
                    roomNumberSearchInput.style.backgroundColor = '';
                    roomNumberSearchInput.style.cursor = '';
                }
                if (roomNumberDropdown) {
                    roomNumberDropdown.style.display = 'none';
                    roomNumberDropdown.innerHTML = '';
                }
                
                // Reset date fields to editable state
                const checkInInput = document.getElementById('quickCheckInDate');
                const checkOutInput = document.getElementById('quickCheckOutDate');
                if (checkInInput) {
                    checkInInput.removeAttribute('readonly');
                    checkInInput.style.backgroundColor = '';
                    checkInInput.style.cursor = '';
                }
                if (checkOutInput) {
                    checkOutInput.removeAttribute('readonly');
                    checkOutInput.style.backgroundColor = '';
                    checkOutInput.style.cursor = '';
                }
                // Set default dates (today and tomorrow)
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const todayIso = today.toISOString().split('T')[0];
                const tomorrowIso = tomorrow.toISOString().split('T')[0];
                if (checkInInput) checkInInput.value = todayIso;
                if (checkOutInput) checkOutInput.value = tomorrowIso;
                addRoomRow({}, false);
                updateRoomTypeOptions();
                updateRoomNumberOptions();
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

        // Set default dates on initial load if not set
        if (!checkInDateInput?.value || !checkOutDateInput?.value) {
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const todayIso = today.toISOString().split('T')[0];
            const tomorrowIso = tomorrow.toISOString().split('T')[0];
            if (checkInDateInput) checkInDateInput.value = todayIso;
            if (checkOutDateInput) checkOutDateInput.value = tomorrowIso;
        }
        
        // Handle modal show event - set default dates if opened directly
        const reservationModal = document.getElementById('reservation-pop');
        if (reservationModal) {
            reservationModal.addEventListener('show.bs.modal', () => {
                // Check if dates are from calendar selection
                isDatesFromCalendarSelection = reservationModal.dataset.datesFromCalendar === 'true';
                
                // Also check if dates are already populated and readonly (indicates calendar selection)
                const hasDates = checkInDateInput?.value && checkOutDateInput?.value;
                if (!isDatesFromCalendarSelection && hasDates && checkInDateInput?.hasAttribute('readonly')) {
                    isDatesFromCalendarSelection = true;
                    reservationModal.dataset.datesFromCalendar = 'true';
                }
                
                // Check if dates are already set (from calendar selection)
                if (!checkInDateInput?.value || !checkOutDateInput?.value) {
                    // No dates set - set default dates (today and tomorrow) and make editable
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    const todayIso = today.toISOString().split('T')[0];
                    const tomorrowIso = tomorrow.toISOString().split('T')[0];
                    populateQuickReservationDates(todayIso, tomorrowIso, false);
                    isDatesFromCalendarSelection = false;
                    reservationModal.dataset.datesFromCalendar = 'false';
                    updateRoomTypeOptions();
                }
                
                // Clear and re-add room row with correct flag
                detailsContainer.innerHTML = '';
                rowIndex = 0;
                
                // If dates are from calendar selection, find room type ID first
                let roomTypeId = null;
                if (isDatesFromCalendarSelection) {
                    const selectionContext = getSelectionContextForModal();
                    let contextRoomNumberIds = selectionContext?.roomNumberIds || [];
                    
                    // Also check if roomId is directly available in the context (for single date clicks)
                    if (contextRoomNumberIds.length === 0 && selectionContext?.roomId) {
                        contextRoomNumberIds = [selectionContext.roomId];
                    }
                    
                    // Find room type ID from the selected room number
                    if (contextRoomNumberIds.length > 0 && roomsData && Array.isArray(roomsData)) {
                        const firstRoomNumberId = Number(contextRoomNumberIds[0]);
                        // Find room type ID by matching room number ID in roomsData
                        for (const category of roomsData) {
                            if (category.rooms && Array.isArray(category.rooms)) {
                                const room = category.rooms.find(r => Number(r.id) === firstRoomNumberId);
                                if (room) {
                                    // Find matching room type in quickReservationRooms by title
                                    const matchingRoomType = quickReservationRooms.find((rt) => rt.title === category.title);
                                    if (matchingRoomType) {
                                        roomTypeId = matchingRoomType.id;
                                        break; // Found it, no need to continue
                                    }
                                }
                            }
                        }
                    }
                }
                
                // Add room row with pre-selected room type if from calendar selection
                addRoomRow(roomTypeId ? { room_id: roomTypeId } : {}, isDatesFromCalendarSelection);
                
                // Update room type options, preserving the room type ID if from calendar selection
                updateRoomTypeOptions(isDatesFromCalendarSelection && roomTypeId ? roomTypeId : null);
                
                if (!isDatesFromCalendarSelection) {
                    updateRoomsMaxValues();
                }
                
                // Update Payment Details tab state on modal show
                updatePaymentDetailsTabState();
                
                // If dates are from calendar selection, make room type readonly
                if (isDatesFromCalendarSelection && roomTypeId) {
                    const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
                    if (roomTypeSelect) {
                        // Ensure value is set
                        roomTypeSelect.value = roomTypeId;
                        // Make it readonly
                        roomTypeSelect.disabled = true;
                        roomTypeSelect.style.backgroundColor = '#e9ecef';
                        roomTypeSelect.style.cursor = 'not-allowed';
                        
                        // Update rate options for the selected room type
                        updateRateOptions(detailsContainer.querySelector('.room-detail-item'), roomTypeId, null);
                    }
                }
                
                // If dates are from calendar selection, populate room numbers
                if (isDatesFromCalendarSelection) {
                    const selectionContext = getSelectionContextForModal();
                    let contextRoomNumberIds = selectionContext?.roomNumberIds || [];
                    
                    // If no room number IDs in array, check if roomId is directly available (for single date clicks)
                    if (contextRoomNumberIds.length === 0 && selectionContext?.roomId) {
                        contextRoomNumberIds = [selectionContext.roomId];
                    }
                    
                    // Populate selected room numbers from context
                    selectedRoomNumbers.clear();
                    if (contextRoomNumberIds.length > 0 && roomsData && Array.isArray(roomsData)) {
                        contextRoomNumberIds.forEach((roomNumberId) => {
                            const roomNumberIdNum = Number(roomNumberId);
                            // Find room number in roomsData
                            for (const category of roomsData) {
                                if (category.rooms && Array.isArray(category.rooms)) {
                                    const room = category.rooms.find(r => Number(r.id) === roomNumberIdNum);
                                    if (room) {
                                        selectedRoomNumbers.set(roomNumberIdNum, {
                                            id: roomNumberIdNum,
                                            number: room.number,
                                            roomType: category.title
                                        });
                                        break; // Found it, no need to continue searching
                                    }
                                }
                            }
                        });
                    }
                    renderSelectedRoomNumbers();
                    
                    // Update room number options (will set readonly state)
                    updateRoomNumberOptions();
                } else {
                    // Clear selected room numbers if not from calendar selection
                    selectedRoomNumbers.clear();
                    renderSelectedRoomNumbers();
                    
                    // Update room number options
                    updateRoomNumberOptions();
                }
                
                recalcSummary();
            });
            
            // Handle Payment Details tab click - update values
            const paymentDetailsTab = document.getElementById('nav-profile-tab');
            if (paymentDetailsTab) {
                paymentDetailsTab.addEventListener('shown.bs.tab', () => {
                    // Recalculate and update payment details when tab is shown
                    const nights = calculateNights();
                    let totalAmount = 0;
                    detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                        const qty = parseInt(row.querySelector('.room-qty-input')?.value, 10) || 0;
                        const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
                        const lineTotal = qty * price * nights;
                        totalAmount += lineTotal;
                    });
                    updatePaymentDetails(totalAmount);
                    updatePaymentCalculations(); // Update folio history with charges and payments
                });
            }
            
            // Handle modal close event - reset form fields
            reservationModal.addEventListener('hidden.bs.modal', () => {
                // Reset form
                form.reset();
                clearErrors();
                
                // Clear room details container
                detailsContainer.innerHTML = '';
                rowIndex = 0;
                
                // Reset flag
                isDatesFromCalendarSelection = false;
                if (reservationModal) {
                    reservationModal.dataset.datesFromCalendar = 'false';
                }
                
                // Clear selected room numbers
                selectedRoomNumbers.clear();
                renderSelectedRoomNumbers();
                if (roomNumberSearchInput) {
                    roomNumberSearchInput.value = '';
                    roomNumberSearchInput.removeAttribute('readonly');
                    roomNumberSearchInput.style.backgroundColor = '';
                    roomNumberSearchInput.style.cursor = '';
                }
                if (roomNumberDropdown) {
                    roomNumberDropdown.style.display = 'none';
                    roomNumberDropdown.innerHTML = '';
                }
                
                // Reset room type select to editable (will be set when re-adding row)
                // This is handled by addRoomRow({}, false) below
                
                // Re-add initial room row after clearing
                addRoomRow({}, false);
                
                // Reset date fields to editable state
                if (checkInDateInput) {
                    checkInDateInput.removeAttribute('readonly');
                    checkInDateInput.style.backgroundColor = '';
                    checkInDateInput.style.cursor = '';
                }
                if (checkOutDateInput) {
                    checkOutDateInput.removeAttribute('readonly');
                    checkOutDateInput.style.backgroundColor = '';
                    checkOutDateInput.style.cursor = '';
                }
                
                // Reset to default dates (today and tomorrow)
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(tomorrow.getDate() + 1);
                const todayIso = today.toISOString().split('T')[0];
                const tomorrowIso = tomorrow.toISOString().split('T')[0];
                if (checkInDateInput) checkInDateInput.value = '';
                if (checkOutDateInput) checkOutDateInput.value = '';
                
                // Reset guest fields
                const guestNameInput = document.getElementById('quickGuestName');
                const guestEmailInput = document.getElementById('quickGuestEmail');
                const guestMobileInput = document.getElementById('quickGuestMobile');
                const guestGenderInput = document.getElementById('quickGuestGender');
                if (guestNameInput) guestNameInput.value = '';
                if (guestEmailInput) guestEmailInput.value = '';
                if (guestMobileInput) guestMobileInput.value = '';
                if (guestGenderInput) guestGenderInput.value = '';
                
                // Reset time fields
                const checkInTimeInput = document.getElementById('quickCheckInTime');
                const checkOutTimeInput = document.getElementById('quickCheckOutTime');
                if (checkInTimeInput) checkInTimeInput.value = '';
                if (checkOutTimeInput) checkOutTimeInput.value = '';
                
                // Reset notes
                const notesInput = document.getElementById('quickNotes');
                if (notesInput) notesInput.value = '';
                
                // Reset summary fields
                if (totalRoomsInput) totalRoomsInput.value = '0';
                if (totalAmountLabel) totalAmountLabel.textContent = '0.00';
                if (paidAmountLabel) paidAmountLabel.textContent = '0.00';
                if (balanceAmountLabel) balanceAmountLabel.textContent = '0.00';
                
                // Reset Payment Details section
                const quickPaymentDate = document.getElementById('quickPaymentDate');
                const quickPaymentRoomPrice = document.getElementById('quickPaymentRoomPrice');
                const quickPaymentTotalCharges = document.getElementById('quickPaymentTotalCharges');
                const quickPaymentTotalPayable = document.getElementById('quickPaymentTotalPayable');
                const quickPaymentBalance = document.getElementById('quickPaymentBalance');
                const quickPaymentFooterTotal = document.getElementById('quickPaymentFooterTotal');
                const quickPaymentFooterPaid = document.getElementById('quickPaymentFooterPaid');
                const quickPaymentFooterBalance = document.getElementById('quickPaymentFooterBalance');
                
                if (quickPaymentDate) quickPaymentDate.textContent = '-';
                if (quickPaymentRoomPrice) quickPaymentRoomPrice.textContent = 'Rs. 0';
                if (quickPaymentTotalCharges) quickPaymentTotalCharges.textContent = 'Rs. 0';
                if (quickPaymentTotalPayable) quickPaymentTotalPayable.textContent = 'Rs. 0';
                if (quickPaymentBalance) quickPaymentBalance.textContent = 'Rs. 0';
                if (quickPaymentFooterTotal) quickPaymentFooterTotal.textContent = '0.00';
                if (quickPaymentFooterPaid) quickPaymentFooterPaid.textContent = '0.00';
                if (quickPaymentFooterBalance) quickPaymentFooterBalance.textContent = '0.00';
                
                // Update room type options
                updateRoomTypeOptions();
                recalcSummary();
            });
        }
        
        // Quick Reservation - Charges and Payments functionality (initialize before recalcSummary)
        const quickCharges = [];
        const quickPayments = [];
        
        addRoomRow({}, isDatesFromCalendarSelection);
        updateRoomTypeOptions();
        recalcSummary();
        
        const chargeCommentInput = document.getElementById('quickChargeComment');
        const chargeAmountInput = document.getElementById('quickChargeAmount');
        const chargeAddBtn = document.getElementById('quickChargeAddBtn');
        const chargesListDiv = document.getElementById('quickChargesList');
        
        const paymentTypeSelect = document.getElementById('quickPaymentType');
        const paymentMethodSelect = document.getElementById('quickPaymentMethod');
        const paymentCommentInput = document.getElementById('quickPaymentComment');
        const paymentAmountInput = document.getElementById('quickPaymentAmount');
        const paymentAddBtn = document.getElementById('quickPaymentAddBtn');
        const paymentsListDiv = document.getElementById('quickPaymentsList');
        
        const quickPaymentBtn = document.getElementById('quickReservationPaymentBtn');
        const quickConfirmBtn = document.getElementById('quickPaymentConfirmBtn');
        const paymentDetailsTab = document.getElementById('nav-profile-tab');
        
        // Payment Type/Method logic
        if (paymentTypeSelect && paymentMethodSelect) {
            paymentTypeSelect.addEventListener('change', function() {
                const type = this.value;
                paymentMethodSelect.innerHTML = '<option value="">Select Method</option>';
                
                if (type === 'Cash') {
                    paymentMethodSelect.innerHTML = '<option value="Cash">Cash</option>';
                    paymentMethodSelect.value = 'Cash';
                    paymentMethodSelect.disabled = true;
                    paymentMethodSelect.style.backgroundColor = '#e9ecef';
                } else if (type === 'Online') {
                    paymentMethodSelect.innerHTML = `
                        <option value="">Select Method</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Net Banking">Net Banking</option>
                    `;
                    paymentMethodSelect.disabled = false;
                    paymentMethodSelect.style.backgroundColor = '';
                } else {
                    paymentMethodSelect.disabled = true;
                    paymentMethodSelect.style.backgroundColor = '#e9ecef';
                }
            });
        }
        
        // Payment button - auto-open Payment Details tab with validation
        if (quickPaymentBtn && paymentDetailsTab) {
            quickPaymentBtn.addEventListener('click', function() {
                // Validate Guest Information
                const guestName = document.getElementById('quickGuestName')?.value.trim();
                const guestMobile = document.getElementById('quickGuestMobile')?.value.trim();
                
                if (!guestName) {
                    
                    swal("Error!", 'Please enter guest name before proceeding to payment.');
                    document.getElementById('quickGuestName')?.focus();
                    return;
                }
                
                if (!guestMobile) {
                    swal("Error!", 'Please enter guest mobile number before proceeding to payment.');
                    document.getElementById('quickGuestMobile')?.focus();
                    return;
                }
                
                // Trigger Payment Details tab
                const tabTrigger = new bootstrap.Tab(paymentDetailsTab);
                tabTrigger.show();
            });
        }
        
        // Add Charge functionality
        if (chargeAddBtn) {
            chargeAddBtn.addEventListener('click', function() {
                const comment = chargeCommentInput?.value.trim();
                const amount = parseFloat(chargeAmountInput?.value || 0);
                
                if (!comment) {
                    swal("Error!", 'Please enter a comment for the charge.');
                    return;
                }
                if (amount <= 0) {
                    swal("Error!", 'Please enter a valid amount greater than 0.');
                    return;
                }
                
                // Add charge to array
                const charge = {
                    id: Date.now(),
                    comment: comment,
                    amount: amount,
                    date: new Date().toISOString().split('T')[0], // YYYY-MM-DD format for database
                    display_date: new Date().toLocaleString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }) // DD/MM/YYYY HH:MM AM/PM for display
                };
                quickCharges.push(charge);
                
                // Clear inputs
                if (chargeCommentInput) chargeCommentInput.value = '';
                if (chargeAmountInput) chargeAmountInput.value = '';
                
                // Render charges and update calculations
                renderCharges();
                updatePaymentCalculations();
            });
        }
        
        // Add Payment functionality
        if (paymentAddBtn) {
            paymentAddBtn.addEventListener('click', function() {
                const type = paymentTypeSelect?.value;
                const method = paymentMethodSelect?.value;
                const comment = paymentCommentInput?.value.trim();
                const amount = parseFloat(paymentAmountInput?.value || 0);
                
                if (!type) {
                    swal("Error!", 'Please select a payment type.');
                    return;
                }
                if (!method) {
                    swal("Error!", 'Please select a payment method.');

                    return;
                }
                if (amount <= 0) {
                    swal("Error!", 'Please enter a valid amount greater than 0.');
                    return;
                }
                
                // Add payment to array
                const payment = {
                    id: Date.now(),
                    type: type,
                    method: method,
                    comment: comment || '-',
                    amount: amount,
                    date: new Date().toISOString().split('T')[0], // YYYY-MM-DD format for database
                    display_date: new Date().toLocaleString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }) // DD/MM/YYYY HH:MM AM/PM for display
                };
                quickPayments.push(payment);
                
                // Clear inputs
                if (paymentTypeSelect) paymentTypeSelect.value = '';
                if (paymentMethodSelect) {
                    paymentMethodSelect.value = '';
                    paymentMethodSelect.disabled = true;
                    paymentMethodSelect.style.backgroundColor = '#e9ecef';
                }
                if (paymentCommentInput) paymentCommentInput.value = '';
                if (paymentAmountInput) paymentAmountInput.value = '';
                
                // Render payments and update calculations
                renderPayments();
                updatePaymentCalculations();
                updateConfirmButtonState();
            });
        }
        
        // Render charges list
        function renderCharges() {
            if (!chargesListDiv) return;
            
            if (quickCharges.length === 0) {
                chargesListDiv.innerHTML = '';
            } else {
                let html = '<div class="list-group small">';
                quickCharges.forEach((charge, index) => {
                    html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${charge.comment}</strong><br>
                            <small class="text-muted" style="font-size: 12px;">${charge.display_date}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <strong>Rs. ${formatAmount(charge.amount)}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeCharge(${charge.id})">X</button>
                        </div>
                    </div>
                    `;
                });
                html += '</div>';
                chargesListDiv.innerHTML = html;
            }
        }
        
        // Render payments list
        function renderPayments() {
            if (!paymentsListDiv) return;
            
            if (quickPayments.length === 0) {
                paymentsListDiv.innerHTML = '';
            } else {
                let html = '<div class="list-group small">';
                quickPayments.forEach((payment, index) => {
                    html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${payment.type} - ${payment.method}</strong><br>
                            <small class="text-muted" style="font-size: 12px;">${payment.comment} | ${payment.display_date}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <strong>Rs. ${formatAmount(payment.amount)}</strong>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removePayment(${payment.id})">X</button>
                        </div>
                    </div>
                    `;
                });
                html += '</div>';
                paymentsListDiv.innerHTML = html;
            }
        }
        
        // Render folio history details (charges and payments with summary)
        function renderFolioHistory(summaryData = null) {
            const folioHistoryDiv = document.getElementById('quickFolioHistoryDetails');
            if (!folioHistoryDiv) return;
            
            let html = '<ul class="list-unstyled">';
            
            // Show charges as rows
            if (quickCharges.length > 0) {
                html += '';
                quickCharges.forEach((charge) => {
                    html += `
                    <li class="d-flex align-items-center justify-content-between my-2 px-2">
                        <div>
                            <p class="mb-0">${charge.comment}</p>
                            <small class="text-muted" style="font-size: 12px;">${charge.display_date}</small>
                        </div>
                        <p class="mb-0 text-danger"><strong>+ Rs. ${formatAmount(charge.amount)}</strong></p>
                    </li>
                    `;
                });
            }
            
            // Show payments as rows
            if (quickPayments.length > 0) {
                html += '';
                quickPayments.forEach((payment) => {
                    html += `
                    <li class="d-flex align-items-center justify-content-between my-2 px-2">
                        <div>
                            <p class="mb-0">${payment.type} - ${payment.method}</p>
                            <small class="text-muted" style="font-size: 12px;">${payment.comment} | ${payment.display_date}</small>
                        </div>
                        <p class="mb-0 text-success">- Rs. ${formatAmount(payment.amount)}</p>
                    </li>
                    `;
                });
            }
            
            // Add summary section if data provided
            // if (summaryData) {
            //     html += '<hr class="my-3">';
            //     html += `
            //     `;
            // }
            
            html += '</ul>';
            folioHistoryDiv.innerHTML = html;
        }
        
        // Remove charge
        window.removeCharge = function(chargeId) {
            const index = quickCharges.findIndex(c => c.id === chargeId);
            if (index > -1) {
                quickCharges.splice(index, 1);
                renderCharges();
                updatePaymentCalculations();
            }
        };
        
        // Remove payment
        window.removePayment = function(paymentId) {
            const index = quickPayments.findIndex(p => p.id === paymentId);
            if (index > -1) {
                quickPayments.splice(index, 1);
                renderPayments();
                updatePaymentCalculations();
                updateConfirmButtonState();
            }
        };
        
        // Update payment calculations
        function updatePaymentCalculations() {
            // Calculate totals
            const nights = calculateNights();
            let roomTotal = 0;
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const qty = parseInt(row.querySelector('.room-qty-input')?.value, 10) || 0;
                const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
                roomTotal += qty * price * nights;
            });
            
            const totalCharges = quickCharges.reduce((sum, charge) => sum + charge.amount, 0);
            const totalPayments = quickPayments.reduce((sum, payment) => sum + payment.amount, 0);
            
            const extraCharges = totalCharges;
            const grandTotal = roomTotal + extraCharges;
            const balance = grandTotal - totalPayments;
            
            // Update Details section
            const quickPaymentExtraCharges = document.getElementById('quickPaymentExtraCharges');
            const quickPaymentSGST = document.getElementById('quickPaymentSGST');
            const quickPaymentCGST = document.getElementById('quickPaymentCGST');
            const quickPaymentTotalPayable = document.getElementById('quickPaymentTotalPayable');
            const quickPaymentBalance = document.getElementById('quickPaymentBalance');
            
            if (quickPaymentExtraCharges) quickPaymentExtraCharges.textContent = 'Rs. ' + formatAmount(extraCharges);
            if (quickPaymentSGST) quickPaymentSGST.textContent = 'Rs. 0';
            if (quickPaymentCGST) quickPaymentCGST.textContent = 'Rs. 0';
            if (quickPaymentTotalPayable) quickPaymentTotalPayable.textContent = 'Rs. ' + formatAmount(grandTotal);
            if (quickPaymentBalance) quickPaymentBalance.textContent = 'Rs. ' + formatAmount(balance);
            
            // Update footer
            const quickPaymentFooterTotal = document.getElementById('quickPaymentFooterTotal');
            const quickPaymentFooterPaid = document.getElementById('quickPaymentFooterPaid');
            const quickPaymentFooterBalance = document.getElementById('quickPaymentFooterBalance');
            
            if (quickPaymentFooterTotal) quickPaymentFooterTotal.textContent = formatAmount(grandTotal);
            if (quickPaymentFooterPaid) quickPaymentFooterPaid.textContent = formatAmount(totalPayments);
            if (quickPaymentFooterBalance) quickPaymentFooterBalance.textContent = formatAmount(balance);
            
            // Update Folio History section with charges, payments, and summary
            renderFolioHistory({
                roomPrice: roomTotal,
                totalCharges: roomTotal,
                extraCharges: extraCharges,
                totalPayable: grandTotal,
                balance: balance
            });
        }
        
        // Update Confirm button state
        function updateConfirmButtonState() {
            if (!quickConfirmBtn) return;
            
            if (quickPayments.length > 0) {
                quickConfirmBtn.disabled = false;
                quickConfirmBtn.style.opacity = '1';
                quickConfirmBtn.style.cursor = 'pointer';
            } else {
                quickConfirmBtn.disabled = true;
                quickConfirmBtn.style.opacity = '0.5';
                quickConfirmBtn.style.cursor = 'not-allowed';
            }
        }
        
        // Confirm button submit
        if (quickConfirmBtn) {
            quickConfirmBtn.addEventListener('click', async function() {
                if (quickPayments.length === 0) {
                    swal("Error!", 'Please add at least one payment before confirming.');
                    return;
                }
                
                // Collect form data
                const formData = collectFormData();
                if (!formData) {
                    return; // Validation failed
                }
                
                // Calculate totals
                const nights = calculateNights();
                let roomTotal = 0;
                detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                    const qty = parseInt(row.querySelector('.room-qty-input')?.value, 10) || 0;
                    const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
                    roomTotal += qty * price * nights;
                });
                
                const totalCharges = quickCharges.reduce((sum, charge) => sum + charge.amount, 0);
                const totalPayments = quickPayments.reduce((sum, payment) => sum + payment.amount, 0);
                
                const grandTotal = roomTotal + totalCharges;
                const balance = grandTotal - totalPayments;
                
                // Add charges and payments to form data
                formData.charges = quickCharges;
                formData.payments = quickPayments;
                
                // Add calculated totals for orders table
                formData.room_total = roomTotal;
                formData.total_charges = totalCharges;
                formData.total_payments = totalPayments;
                formData.grand_total = grandTotal;
                
                // Debug: Log the data being sent
                console.log('Submitting reservation data:', JSON.stringify(formData, null, 2));
                
                // Submit the form
                const submitLabelDefault = quickConfirmBtn.querySelector('.label-default');
                const submitLabelLoading = quickConfirmBtn.querySelector('.label-loading');
                
                try {
                    if (submitLabelDefault) submitLabelDefault.classList.add('d-none');
                    if (submitLabelLoading) submitLabelLoading.classList.remove('d-none');
                    quickConfirmBtn.disabled = true;
                    
                    const response = await fetch(form.dataset.endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(formData)
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(reservationModal);
                        if (modal) modal.hide();
                        
                        // Refresh calendar
                        fetchCalendarDataForCurrentView();
                        
                        // Show success message
                        swal("Success!", 'Reservation created successfully!', "success");
                    } else {
                        // Handle error response with detailed information
                        let errorMessages = [];
                        if (data.error) {
                            errorMessages.push(`Error: ${data.error}`);
                            if (data.line) errorMessages.push(`Line: ${data.line}`);
                            if (data.file) errorMessages.push(`File: ${data.file}`);
                        } else if (data.errors) {
                            if (typeof data.errors === 'object') {
                                Object.values(data.errors).forEach(err => {
                                    if (Array.isArray(err)) errorMessages.push(...err);
                                    else errorMessages.push(err);
                                });
                            } else {
                                errorMessages.push(data.errors);
                            }
                        } else if (data.message) {
                            errorMessages.push(data.message);
                        } else {
                            errorMessages.push('An error occurred while creating the reservation.');
                        }
                        showErrors(errorMessages);
                    }
                } catch (error) {
                    console.error('Error creating reservation:', error);
                    showErrors(['An error occurred while creating the reservation. Please try again.', error.message]);
                } finally {
                    if (submitLabelDefault) submitLabelDefault.classList.remove('d-none');
                    if (submitLabelLoading) submitLabelLoading.classList.add('d-none');
                    quickConfirmBtn.disabled = false;
                }
            });
        }
        
        // Reset charges and payments on modal close
        if (reservationModal) {
            reservationModal.addEventListener('hidden.bs.modal', () => {
                // Clear charges and payments
                quickCharges.length = 0;
                quickPayments.length = 0;
                renderCharges();
                renderPayments();
                updateConfirmButtonState();
                
                // Clear input fields
                if (chargeCommentInput) chargeCommentInput.value = '';
                if (chargeAmountInput) chargeAmountInput.value = '';
                if (paymentTypeSelect) paymentTypeSelect.value = '';
                if (paymentMethodSelect) {
                    paymentMethodSelect.value = '';
                    paymentMethodSelect.disabled = true;
                    paymentMethodSelect.style.backgroundColor = '#e9ecef';
                }
                if (paymentCommentInput) paymentCommentInput.value = '';
                if (paymentAmountInput) paymentAmountInput.value = '';
                
                // Switch back to first tab
                const firstTab = document.getElementById('nav-home-tab');
                if (firstTab) {
                    const tabTrigger = new bootstrap.Tab(firstTab);
                    tabTrigger.show();
                }
            });
        }
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
    
    const hotelSelector = document.getElementById('hotelSelector');
    hotelSelector?.addEventListener('change', () => {
        fetchCalendarDataForCurrentView();
    });

    if (roomTypeSelectors.length) {
        roomTypeSelectors.forEach((selector) => {
            selector.addEventListener('change', (event) => {
                const value = event.target.value || '';
                roomTypeSelectors.forEach((other) => {
                    if (other !== event.target) {
                        other.value = value;
                    }
                });
                fetchCalendarDataForCurrentView();
            });
        });
    }

    syncDatePicker();
    renderCalendar();
});
</script>
@endsection