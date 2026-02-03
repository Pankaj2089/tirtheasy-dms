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
    
    .room-number-dropdown .room-number-checkbox {
        cursor: pointer;
        margin-top: 0.125rem;
    }
    
    .room-number-dropdown .list-group-item input[type="checkbox"]:disabled {
        cursor: not-allowed;
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
    
    /* Assign Room Dropdown Menu Styles */
    #assignModalAccordion .dropdown-item {
        font-size: 13px !important;
    }
    
    #assignModalAccordion .dropdown-item:hover,
    #assignModalAccordion .dropdown-item:focus,
    #assignModalAccordion .dropdown-item:active {
        background-color: transparent !important;
        color: inherit !important;
    }
</style>

<div class="main-dash top-report mob-device p-0 mx-md-4 overflow-hidden" style="overflow-y: auto; max-height: calc(100vh - 80px); width: 96.5%;">
    <div class="calender-box">
        <!-- Calendar Grid -->
        <div class="calendar-container">
        <div class="">
            <!-- Date Header Navigation -->
            <div class="date-header-wrapper">
                <div class="bg-white border-bottom py-2 fillter">
                    <div class="container-fluid">
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
                                    <div class="filter m-1" id="calendarFilter">
                                        <div class="circle"></div>
                                    </div>
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
                <div class="container-fluid" style="padding-left: 0; padding-right: 0;">
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                        <div class="room-label-header"></div>
                    </div>
                </div>
            </div>

            <!-- Room Rows -->
            <div class="rooms-container">
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
    <div class="container-fluid" style="padding-left: 0; padding-right: 0;">
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
<div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><img
                src="{{$siteUrl}}public/assets/img/close.svg" alt=""></button>
        <div class="modal-body p-0" style="max-height: calc(100vh - 120px); overflow-y: auto;">
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
                    <form id="quickReservationForm" class="bl-reserve-form position-relative" data-endpoint="{{ route('quickReservations.store') }}" style="padding-bottom: 120px;">
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
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" id="quickRoomNumberSearch" class="form-control" placeholder="Search and select room numbers..." autocomplete="off">
                                        <button type="button" id="quickSelectAllRooms" class="btn btn-outline-primary btn-sm" style="white-space: nowrap;">Select Room</button>
                                    </div>
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
                                <div class="invalid-feedback">Name should contain only alphabetic characters.</div>
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
                                <div class="invalid-feedback">Mobile number must be exactly 10 digits.</div>
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
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" style="padding-bottom: 120px;">
                            <div class="row">
                                <!-- Add left padding so Details & Folio History align like other popups -->
                                <div class="col-md-6 px-3 border-box">
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
                <!-- Hidden input to store booking ID -->
                <input type="hidden" id="bookingModalBookingId" value="">
                
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
                            <div class="d-flex justify-content-center align-items-center pe-4 gap-2">
                                <a href="#" id="bookingPopupPrintBtn" data-booking-id=""
                                    class="theme-btn-sm rounded text-white text-center text-decoration-none">Print/Send</a>
                                <!-- Three dots menu button -->
                                <div class="dropdown">
                                    <button class="btn p-0 border-0 bg-transparent" type="button" id="bookingPopupMenuBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                        <img src="{{$siteUrl}}public/assets/img/box-option.svg" alt="" style="width: 20px; height: 20px;">
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" id="bookingPopupMenu" aria-labelledby="bookingPopupMenuBtn">
                                        <li><a class="dropdown-item" href="#" id="bookingPopupExtendDay" style="font-size: 13px; display: none;">Extend Day</a></li>
                                        <li><a class="dropdown-item" href="#" id="bookingPopupRoomMove" style="font-size: 13px;">Room move</a></li>
                                        <li><a class="dropdown-item" href="#" id="bookingPopupCancelReservation" style="font-size: 13px;">Cancel Reservation</a></li>
                                    </ul>
                                </div>
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
                            <div class="bottom pb-2 book-cont p-3">
                                <ul class="d-flex flex-wrap w-100 justify-content-start">
                                    <li class="mb-4 w-50">
                                        <p>Name</p>
                                        <p><small class="fw-medium" id="guestName">-</small></p>
                                    </li>
                                    <li class="mb-4 w-50">
                                        <p>Email</p>
                                        <p><small class="fw-medium" id="guestEmail">-</small></p>
                                    </li>
                                    <li class="mb-4 w-50">
                                        <p>Mobile Number</p>
                                        <p><small class="fw-medium" id="guestPhone">-</small></p>
                                    </li>
                                    <li class="mb-4 w-50">
                                        <p>City</p>
                                        <p><small class="fw-medium" id="guestCity">-</small></p>
                                    </li>
                                    <li class="mb-4 w-50">
                                        <p>State</p>
                                        <p><small class="fw-medium" id="guestState">-</small></p>
                                    </li>
                                    <li class="mb-4 w-50">
                                        <p>Address</p>
                                        <p><small class="fw-medium" id="guestAddress">-</small></p>
                                    </li>
                                </ul>
                            </div>
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
                    <button class="bg-transpaent border-0 arrow-slide left" id="assignModalPrevBtn"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg"
                            alt=""></button>
                    <div class="date-columns" id="assignModalDateColumns">
                    </div>
                    <button class="bg-transpaent border-0 arrow-slide right" id="assignModalNextBtn"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg"
                            alt=""></button>
                </div>
                <div class="date-cal d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="assignModalTodayBtn">Today</button>
                    <div class="position-relative">
                        <input type="date" class="position-absolute w-100 h-100 opacity-0" id="assignModalDatePicker" aria-label="Select date" style="cursor: pointer; z-index: 1;">
                        <button type="button" class="bg-transpaent border-0" id="assignModalDatePickerBtn" style="position: relative; z-index: 2; cursor: pointer;">
                            <img src="{{$siteUrl}}public/assets/img/cir-calendar.svg" alt="" style="pointer-events: none;">
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <div id="assignModalStatus" class="alert alert-info small mb-3">Loading bookings...</div>
                <div class="accordion" id="accordionExample">
                    <!-- Dynamic booking list will be inserted here -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Room Selection Modal -->
<div class="modal fade" id="assignRoomSelectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="min-width: 440px; max-width: 440px;">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Select Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="{{$siteUrl}}public/assets/img/close.svg" alt="">
                </button>
            </div>
            <div class="modal-body" id="assignRoomSelectBody">
                <p class="text-muted small mb-0">Loading available rooms...</p>
            </div>
        </div>
    </div>
</div>
<!-- assign room popup -->

<!-- Extend Day Modal -->
<div class="modal fade" id="extendDayModal" tabindex="-1" aria-labelledby="extendDayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="extendDayModalLabel">Extend Stay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="{{$siteUrl}}public/assets/img/close.svg" alt="">
                </button>
            </div>
            <div class="modal-body">
                <form id="extendDayForm">
                    <input type="hidden" id="extendDayBookingId" value="">
                    <input type="hidden" id="extendDayCurrentCheckOut" value="">
                    <input type="hidden" id="extendDayRoomNumberIds" value="">
                    <input type="hidden" id="extendDayRoomPriceId" value="">
                    <input type="hidden" id="extendDayRoomPrice" value="">
                    
                    <div class="mb-3">
                        <label for="extendDayNewCheckOut" class="form-label">New Check-out Date</label>
                        <input type="date" class="form-control" id="extendDayNewCheckOut" required>
                        <small class="text-muted">Select a date after the current check-out date</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Current Check-out Date</label>
                        <p class="form-control-plaintext" id="extendDayCurrentCheckOutDisplay">-</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Additional Days</label>
                        <p class="form-control-plaintext" id="extendDayAdditionalDays">0</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Additional Amount</label>
                        <p class="form-control-plaintext fw-bold" id="extendDayAdditionalAmount">Rs. 0.00</p>
                    </div>
                    
                    <div id="extendDayError" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="extendDayConfirmBtn">Extend Stay</button>
            </div>
        </div>
    </div>
</div>
<!-- Extend Day Modal -->

<!-- Room Move Modal -->
<div class="modal fade" id="roomMoveModal" tabindex="-1" aria-labelledby="roomMoveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="roomMoveModalLabel">Move Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="{{$siteUrl}}public/assets/img/close.svg" alt="">
                </button>
            </div>
            <div class="modal-body">
                <form id="roomMoveForm">
                    <input type="hidden" id="roomMoveBookingId" value="">

                    <div class="mb-2">
                        <label class="form-label mb-0">Booking</label>
                        <p class="form-control-plaintext fw-semibold mb-1" id="roomMoveBookingCode">-</p>
                    </div>

                    <div class="mb-2">
                        <label class="form-label mb-0">Guest</label>
                        <p class="form-control-plaintext mb-1" id="roomMoveGuestName">-</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-0">Stay Dates</label>
                        <p class="form-control-plaintext mb-1" id="roomMoveStayLabel">-</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Available Rooms (same hotel & room type)</label>
                        <div id="roomMoveRoomsContainer" class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                            <p class="text-muted mb-0">Loading available rooms...</p>
                        </div>
                        <small class="text-muted d-block mt-1">Select the room(s) you want to move this booking to. Only rooms available for the entire stay are shown.</small>
                    </div>

                    <div id="roomMoveError" class="alert alert-danger" style="display: none;"></div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="roomMoveConfirmBtn" disabled>Move Room</button>
            </div>
        </div>
    </div>
</div>
<!-- Room Move Modal -->

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
    let roomsData = Array.isArray(serverRoomsData) ? serverRoomsData : [];

    const serverBookingData = @json($bookings ?? []);
    let bookingData = Array.isArray(serverBookingData) ? serverBookingData : [];

    let normalizedRoomsData = [];
    let roomDictionary = new Map();
    let normalizedBookings = [];
    let bookingsByRoom = new Map();
    let bookingDateLookup = new Map();
    let unassignedBookings = []; // Track bookings without room assignments

    function refreshDerivedDatasets() {
        normalizedRoomsData = dedupeRoomCategories(roomsData);
        roomDictionary = buildRoomDictionary(normalizedRoomsData);
        normalizedBookings = normalizeBookings(bookingData);
        // Separate assigned and unassigned bookings
        const assignedBookings = normalizedBookings.filter(b => !b.isUnassigned);
        unassignedBookings = normalizedBookings.filter(b => b.isUnassigned);
        bookingsByRoom = groupBookingsByRoom(assignedBookings, roomDictionary);
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

    // Initialize referenceDate to today (set to start of day)
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const calendarState = {
        viewMode: 'week',
        referenceDate: today, // Start with today as first date
        visibleDates: [],
        isNavigating: false // Track if we're navigating to add active state
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
        // Calculate nights: Jan 1 to Jan 2 = 1 night (12:00 to 12:00 booking)
        const nights = Math.max(1, Math.round(durationMs / DAY_IN_MS));
        const label = nights === 1 ? '1 night' : `${nights} nights`;
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
        // Only show tooltip when at least 2 days are selected
        const startDate = parseISO(range.start);
        const endDate = parseISO(range.end);
        if (!startDate || !endDate) {
            return;
        }
        const durationMs = Math.abs(endDate.getTime() - startDate.getTime());
        const daysSelected = Math.round(durationMs / DAY_IN_MS) + 1;
        if (daysSelected < 2) {
            hideSelectionTooltip();
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
                // Regular bookings: Check if room_number_ids exists
                if (!entry.room_number_ids || typeof entry.room_number_ids !== 'string' || !entry.room_number_ids.trim()) {
                    // This is an unassigned booking - store it separately
                    normalized.push({
                        id: entry.id ?? `unassigned-${index}`,
                        roomId: null, // No room assigned
                        startDate: normalizedRange.start,
                        endDate: normalizedRange.end,
                        label: entry.label || entry.guest_name || entry.guestName || 'Reserved',
                        tooltip: entry.tooltip || entry.notes || entry.source || '',
                        status: statusValue,
                        isBlocked: false,
                        inclusiveEnd: inclusiveEnd || false,
                        room_number_ids: null,
                        guest_name: entry.guest_name,
                        source: entry.source,
                        payment_status: entry.payment_status || null,
                        grand_total: entry.grand_total || null,
                        balance: entry.balance || null,
                        order_id: entry.order_id || entry.id || null,
                        room_id: entry.room_id || entry.room_category_id || null, // Room type/category ID
                        room_category_id: entry.room_category_id || entry.room_id || null,
                        rooms: parseInt(entry.rooms) || 1, // Number of rooms selected in booking
                        isUnassigned: true
                    });
                    return; // Don't add to room-specific bookings
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
                    source: entry.source,
                    // Preserve payment data for payment-pending indicator
                    payment_status: entry.payment_status || null,
                    grand_total: entry.grand_total || null,
                    balance: entry.balance || null,
                    order_id: entry.order_id || null
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

        const today = toISO(new Date());
        const referenceIso = toISO(calendarState.referenceDate);

        calendarState.visibleDates.forEach((date, index) => {
            const iso = toISO(date);
            const column = document.createElement('div');
            column.className = 'date-column';

            const cell = document.createElement('div');
            cell.className = 'date-cell-button';

            if (iso === today) {
                cell.classList.add('is-today');
            }

            // Add active class to first date when navigating (if it matches referenceDate and is not today)
            if (calendarState.isNavigating && index === 0 && iso === referenceIso && iso !== today) {
                cell.classList.add('is-active');
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

        // Reset navigation flag after rendering
        calendarState.isNavigating = false;
    }

    function buildCategoryHeader(label) {
        const wrapper = document.createElement('div');
        wrapper.className = 'border-bottom room-cat';
        wrapper.innerHTML = `
            <div class="container-fluid" style="padding-left: 0; padding-right: 0;">
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
        label.className = 'room-name bg-white title';
        label.textContent = category.title;

        const grid = document.createElement('div');
        grid.className = 'room-grid';
        calendarState.visibleDates.forEach((date) => {
            const iso = toISO(date);
            const { count, price, tooltip } = getCategoryDailyMetrics(category, iso);
            
            // Check for unassigned bookings for this date
            // IMPORTANT: Show unassigned rooms ONLY on check-in date (startDate), not on all dates in booking range
            // Match bookings to category by checking if any room in category matches booking's room_id
            const unassignedForDate = unassignedBookings.filter(booking => {
                if (!booking.startDate) return false;
                const start = new Date(booking.startDate);
                const current = new Date(iso);
                
                // CRITICAL: Only show on check-in date (startDate), not on other dates in the range
                // Convert both to ISO date strings for exact date comparison (ignore time)
                const startIso = start.toISOString().split('T')[0];
                const currentIso = current.toISOString().split('T')[0];
                
                // Only match if current date exactly equals check-in date
                if (currentIso !== startIso) return false;
                
                // Match by room_id - check if booking's room_id matches any room in this category
                if (booking.room_id && category.rooms) {
                    return category.rooms.some(room => room.id == booking.room_id);
                }
                return false;
            });
            
            // Calculate total unassigned rooms count (sum of rooms from all unassigned bookings)
            const totalUnassignedRooms = unassignedForDate.reduce((sum, booking) => {
                const roomsCount = parseInt(booking.rooms) || 1;
                return sum + roomsCount;
            }, 0);
            
            const cell = document.createElement('div');
            cell.className = 'grid-cell bg-transparent border-0';
            
            const priceLabel = document.createElement('div');
            priceLabel.className = 'price-label';
            priceLabel.style.textAlign = 'center';
            const hasCount = typeof count === 'number' && !Number.isNaN(count);
            
            // Calculate actual available rooms: Total Available = Total Hotel Rooms - Unassigned Rooms
            const actualAvailableCount = hasCount ? Math.max(0, count - totalUnassignedRooms) : 0;
            
            // Build count markup with blue box on same line if unassigned bookings exist
            let countMarkup = '';
            if (hasCount) {
                // Show actual available count (after subtracting unassigned rooms)
                countMarkup = `<span${actualAvailableCount <= 0 ? ' class="no-availability"' : ''} style="display: inline-block; text-align: center;">${actualAvailableCount}</span>`;
            }
            
            // If there are unassigned bookings, add blue box on same line showing total unassigned rooms
            if (unassignedForDate.length > 0 && totalUnassignedRooms > 0) {
                const unassignedBox = document.createElement('span');
                unassignedBox.className = 'text-white';
                unassignedBox.style.cssText = 'cursor: pointer; padding: 1px 5px; border-radius: 3px; font-size: 9px; background: #7ba4d2 !important; margin-left: 4px; display: inline-block; text-align: center; line-height: 15px;';
                unassignedBox.title = `${totalUnassignedRooms} unassigned room(s) from ${unassignedForDate.length} booking(s) - Click to assign room`;
                
                // Show total unassigned rooms count (sum of rooms from all bookings)
                unassignedBox.textContent = totalUnassignedRooms;
                
                // Add click handler to open assign room modal
                unassignedBox.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Open assign room modal (id="assign") with unassigned bookings for this date
                    const assignModal = document.getElementById('assign');
                    if (!assignModal) {
                        if (typeof swal === 'function') {
                            swal('Error', 'Assign room modal not found.', 'error');
                        }
                        return;
                    }
                    
                    // Get assign room modal instance
                    let modalInstance = null;
                    if (window.bootstrap && window.bootstrap.Modal) {
                        modalInstance = window.bootstrap.Modal.getOrCreateInstance(assignModal);
                    }
                    
                    // Set the selected date in the modal's state BEFORE showing modal
                    const modalBody = assignModal.querySelector('.modal-body');
                    if (modalBody && window.assignRoomModalState) {
                        // Parse the ISO date
                        const dateObj = parseISO(iso);
                        if (dateObj) {
                            // Update state.selectedDate
                            window.assignRoomModalState.selectedDate = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
                            window.assignRoomModalState.selectedDate.setHours(0, 0, 0, 0);
                            
                            // Update referenceDate to start of week containing this date
                            const startOfWeek = (date) => {
                                const base = new Date(date);
                                base.setHours(0, 0, 0, 0);
                                const day = base.getDay();
                                const diff = day === 0 ? -6 : 1 - day;
                                base.setDate(base.getDate() + diff);
                                return base;
                            };
                            window.assignRoomModalState.referenceDate = startOfWeek(window.assignRoomModalState.selectedDate);
                            
                            // Update date picker value
                            const datePicker = modalBody.querySelector('#assignModalDatePicker');
                            if (datePicker) {
                                datePicker.value = iso;
                            }
                            
                            // Re-render dates to update selected state
                            if (typeof window.renderAssignRoomDates === 'function') {
                                window.renderAssignRoomDates();
                            }
                        }
                    }
                    
                    // Show the modal
                    if (modalInstance) {
                        modalInstance.show();
                        
                        // Wait for modal to be fully shown, then fetch bookings
                        const fetchOnShow = function() {
                            // Remove listener after first use
                            assignModal.removeEventListener('shown.bs.modal', fetchOnShow);
                            
                            // Fetch bookings for the selected date
                            if (typeof window.fetchAssignRoomBookings === 'function') {
                                window.fetchAssignRoomBookings();
                            }
                        };
                        assignModal.addEventListener('shown.bs.modal', fetchOnShow, { once: true });
                    }
                });
                
                // Add count and blue box to priceLabel on same line
                if (hasCount) {
                    priceLabel.innerHTML = countMarkup;
                    priceLabel.appendChild(unassignedBox);
                } else {
                    priceLabel.appendChild(unassignedBox);
                }
            } else {
                // No unassigned bookings, just show count markup
                if (hasCount) {
                    priceLabel.innerHTML = countMarkup;
                }
            }
            
            const numPrice = price !== null && price !== undefined ? (typeof price === 'string' ? parseFloat(price) : price) : null;
            const hasPrice = numPrice !== null && !Number.isNaN(numPrice);
            let priceMarkup = '';
            if (hasPrice) {
                const formattedPrice = formatPrice(numPrice);
                priceMarkup = (hasCount || unassignedForDate.length > 0) ? `<br>${formattedPrice}` : formattedPrice;
            } else if (hasCount || unassignedForDate.length > 0) {
                priceMarkup = '<br>&nbsp;';
            } else {
                priceMarkup = '&nbsp;';
            }
            // Append price markup without overwriting existing content
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = priceMarkup;
            while (tempDiv.firstChild) {
                priceLabel.appendChild(tempDiv.firstChild);
            }
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
        roomLabel.innerHTML = `<div>${room.number}</div>`;//<small class="text-muted">${category.title}</small>

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

    // Check if payment is pending for a booking
    function checkIfPaymentPending(booking) {
        // Skip maintenance blocks
        if (booking.isBlocked || booking.status === 'maintenance') {
            return false;
        }
        
        // Check payment_status first
        const paymentStatus = (booking.payment_status || '').toUpperCase();
        const isPaid = paymentStatus === 'PAID' || paymentStatus === 'COMPLETED' || paymentStatus === 'SUCCESS';
        
        // If explicitly paid, not pending
        if (isPaid) {
            return false;
        }
        
        // If payment_status is PENDING, it's pending
        if (paymentStatus === 'PENDING') {
            return true;
        }
        
        // Check grand_total - if there's an amount due and payment_status is not paid, it's pending
        const grandTotal = parseFloat(booking.grand_total) || 0;
        
        // If grand_total > 0 and payment_status is not paid (or null/empty), payment is pending
        if (grandTotal > 0 && !isPaid) {
            return true;
        }
        
        // Check balance if available - if balance > 0, payment is pending
        const balance = parseFloat(booking.balance) || 0;
        if (balance > 0) {
            return true;
        }
        
        return false;
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
            // Check if payment is pending
            const isPaymentPending = checkIfPaymentPending(booking);
            const blockClasses = ['booking-block', booking.cssClass];
            if (isPaymentPending) {
                blockClasses.push('payment-pending');
            }
            block.className = blockClasses.filter(Boolean).join(' ');
            block.dataset.bookingId = booking.id;
            block.style.gridColumn = `${indices.start + 1} / ${indices.end + 2}`;
            block.style.gridRow = '1';
            block.title = booking.tooltip || `${booking.label} (${booking.startDate} - ${booking.endDate})`;
            
            // Check if only 1 day is visible (single-day checkout view)
            const isSingleDayVisible = indices.start === indices.end;
            if (isSingleDayVisible) {
                // Get the visible day ISO string
                const visibleDay = visibleIso[indices.start];
                
                // Get booking display range to check start and end dates
                const displayRange = getBookingDisplayRange(booking);
                
                // Check if the visible day is both start and checkout day (actual single-day booking)
                const isStartSameAsCheckout = displayRange && displayRange.start === displayRange.end;
                const isVisibleDaySameAsStart = displayRange && visibleDay === displayRange.start;
                
                // Only apply single-day style if it's NOT a same-day booking (start = checkout)
                // If start is same as checkout day, use old style
                if (!isStartSameAsCheckout || !isVisibleDaySameAsStart) {
                    // Apply single-day booking style (partial visibility - checkout day only)
                    block.style.height = '19px';
                    block.style.marginTop = '7px';
                    block.style.width = '89%';
                    block.style.marginLeft = '3%';
                }
            }

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
            // Show only user name (booking.label), remove room number
            let displayLabel = booking.label;
            
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
        // Only show empty state if we're not loading and there are no rooms
        // This prevents showing the message during calendar navigation
        const isLoading = roomsContainer?.classList.contains('is-loading');
        if (!normalizedRoomsData.length && !isLoading) {
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
        
        // Store booking ID in hidden input for menu actions
        const bookingModalBookingId = document.getElementById('bookingModalBookingId');
        if (bookingModalBookingId && booking.id) {
            bookingModalBookingId.value = booking.id;
        }
        
        // Update Print/Send button with booking ID
        const printBtn = document.getElementById('bookingPopupPrintBtn');
        if (printBtn && booking.id) {
            printBtn.setAttribute('data-booking-id', booking.id);
        }
        
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
        
        // Show/hide Extend Day option based on room count (only for single room bookings)
        const extendDayOption = document.getElementById('bookingPopupExtendDay');
        if (extendDayOption) {
            if (!isMaintenance && booking.rooms === 1) {
                extendDayOption.style.display = 'block';
            } else {
                extendDayOption.style.display = 'none';
            }
        }
        
        // Populate Guest Details tab
        const guestName = document.getElementById('guestName');
        const guestEmail = document.getElementById('guestEmail');
        const guestPhone = document.getElementById('guestPhone');
        const guestCity = document.getElementById('guestCity');
        const guestState = document.getElementById('guestState');
        const guestAddress = document.getElementById('guestAddress');
        
        if (isMaintenance) {
            // For maintenance blocks, show maintenance reason as name
            if (guestName) guestName.textContent = booking.guest_name || booking.reason || '-';
            if (guestEmail) guestEmail.textContent = '-';
            if (guestPhone) guestPhone.textContent = '-';
            if (guestCity) guestCity.textContent = '-';
            if (guestState) guestState.textContent = '-';
            if (guestAddress) guestAddress.textContent = '-';
        } else {
            if (guestName) guestName.textContent = booking.guest_name || '-';
            if (guestEmail) guestEmail.textContent = booking.guest_email || '-';
            if (guestPhone) guestPhone.textContent = booking.guest_phone || '-';
            if (guestCity) guestCity.textContent = booking.guest_city || '-';
            if (guestState) guestState.textContent = booking.guest_state || '-';
            if (guestAddress) guestAddress.textContent = booking.guest_address || '-';
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
    
    // Booking Modal - Print Invoice handler
    (function initBookingPopupPrint() {
        const printBtn = document.getElementById('bookingPopupPrintBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const bookingId = this.getAttribute('data-booking-id') || document.getElementById('bookingModalBookingId')?.value;
                if (!bookingId) {
                    swal('Error', 'Booking ID not found.', 'error');
                    return;
                }
                
                // Open PDF in new window
                const invoiceUrl = `{{ url('/booking') }}/${encodeURIComponent(bookingId)}/invoice`;
                window.open(invoiceUrl, '_blank');
            });
        }
    })();
    
    // Booking Modal - Menu Actions (Cancel Reservation, Extend Day, Room Move)
    (function initBookingPopupMenu() {
        // Extend Day handler
        const extendDayLink = document.getElementById('bookingPopupExtendDay');
        if (extendDayLink) {
            extendDayLink.addEventListener('click', function(e) {
                e.preventDefault();
                const bookingId = document.getElementById('bookingModalBookingId')?.value;
                if (!bookingId) {
                    swal('Error', 'Booking ID not found.', 'error');
                    return;
                }
                
                // Get current booking data
                if (!currentBookingData) {
                    swal('Error', 'Booking data not available.', 'error');
                    return;
                }
                
                // Check if single room booking
                if (currentBookingData.rooms !== 1) {
                    swal('Error', 'Extend Day is only available for single room bookings.', 'error');
                    return;
                }
                
                // Populate extend day modal
                const extendModal = document.getElementById('extendDayModal');
                if (!extendModal) {
                    swal('Error', 'Extend Day modal not found.', 'error');
                    return;
                }
                
                // Set booking data in modal
                document.getElementById('extendDayBookingId').value = bookingId;
                document.getElementById('extendDayCurrentCheckOut').value = currentBookingData.check_out_date || '';
                document.getElementById('extendDayCurrentCheckOutDisplay').textContent = currentBookingData.check_out_date || '-';
                
                // Get room number IDs from booking
                const roomNumberIds = currentBookingData.room_numbers_details?.map(r => r.id).join(',') || 
                                    currentBookingData.room_number_ids || '';
                document.getElementById('extendDayRoomNumberIds').value = roomNumberIds;
                
                // Get room price ID from booking
                const roomPriceId = currentBookingData.room_price_id || '';
                document.getElementById('extendDayRoomPriceId').value = roomPriceId;
                
                // Get room price per night (calculate from room_price / number_of_nights)
                const roomPrice = parseFloat(currentBookingData.room_price) || 0;
                const numberOfNights = parseInt(currentBookingData.number_of_nights) || 1;
                const pricePerNight = numberOfNights > 0 ? roomPrice / numberOfNights : roomPrice;
                document.getElementById('extendDayRoomPrice').value = pricePerNight;
                
                // Get room_price_id from booking (we'll need to fetch it from order)
                // For now, we'll get it from the booking details
                fetch(`{{ url('/booking') }}/${bookingId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.booking) {
                        // Set minimum date to day after current check-out
                        const currentCheckOut = new Date(currentBookingData.check_out_date);
                        currentCheckOut.setDate(currentCheckOut.getDate() + 1);
                        const minDate = currentCheckOut.toISOString().split('T')[0];
                        document.getElementById('extendDayNewCheckOut').min = minDate;
                        document.getElementById('extendDayNewCheckOut').value = '';
                        
                        // Reset additional days and amount
                        document.getElementById('extendDayAdditionalDays').textContent = '0';
                        document.getElementById('extendDayAdditionalAmount').textContent = 'Rs. 0.00';
                        document.getElementById('extendDayError').style.display = 'none';
                        
                        // Open modal
                        const modalInstance = window.bootstrap?.Modal?.getOrCreateInstance(extendModal);
                        if (modalInstance) {
                            modalInstance.show();
                        }
                    } else {
                        swal('Error', 'Failed to load booking details.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading booking:', error);
                    swal('Error', 'Failed to load booking details.', 'error');
                });
            });
        }

        // Room Move handler
        const roomMoveLink = document.getElementById('bookingPopupRoomMove');
        if (roomMoveLink) {
            roomMoveLink.addEventListener('click', function(e) {
                e.preventDefault();
                const bookingIdRaw = document.getElementById('bookingModalBookingId')?.value;
                if (!bookingIdRaw) {
                    swal('Error', 'Booking ID not found.', 'error');
                    return;
                }

                if (!currentBookingData) {
                    swal('Error', 'Booking data not available.', 'error');
                    return;
                }

                // Prevent room move for maintenance blocks
                const isMaintenance = currentBookingData.type === 'maintenance' || bookingIdRaw?.toString().startsWith('MB-');
                if (isMaintenance) {
                    swal('Error', 'Room move is not available for maintenance blocks.', 'error');
                    return;
                }

                // Extract numeric booking ID - use order_id if available, otherwise extract from id
                let bookingId = currentBookingData.order_id || null;
                
                // If order_id is not available, extract from bookingIdRaw
                if (!bookingId || bookingId === 0) {
                    const rawStr = bookingIdRaw.toString().trim();
                    // Handle formats like "ORD-123" or "MB-123" or just "123"
                    if (rawStr.startsWith('ORD-')) {
                        bookingId = rawStr.replace('ORD-', '');
                    } else if (rawStr.startsWith('MB-')) {
                        swal('Error', 'Room move is not available for maintenance blocks.', 'error');
                        return;
                    } else {
                        // Extract first sequence of digits
                        const numericMatch = rawStr.match(/^\d+/);
                        if (numericMatch) {
                            bookingId = numericMatch[0];
                        } else {
                            swal('Error', 'Invalid booking ID format. Please ensure this is a valid booking.', 'error');
                            return;
                        }
                    }
                }
                
                // Ensure bookingId is numeric and valid
                bookingId = parseInt(bookingId, 10);
                if (isNaN(bookingId) || bookingId <= 0) {
                    swal('Error', 'Invalid booking ID. Please ensure this is a valid booking.', 'error');
                    return;
                }

                const roomMoveModal = document.getElementById('roomMoveModal');
                if (!roomMoveModal) {
                    swal('Error', 'Room Move modal not found.', 'error');
                    return;
                }

                // Populate basic details
                const bookingCodeEl = document.getElementById('roomMoveBookingCode');
                const guestNameEl = document.getElementById('roomMoveGuestName');
                const stayLabelEl = document.getElementById('roomMoveStayLabel');
                const bookingIdInput = document.getElementById('roomMoveBookingId');
                const roomsContainer = document.getElementById('roomMoveRoomsContainer');
                const errorBox = document.getElementById('roomMoveError');

                if (bookingIdInput) bookingIdInput.value = bookingId;
                if (bookingCodeEl) {
                    const code = currentBookingData.code || currentBookingData.invoice_id || ('ORD-' + bookingId);
                    bookingCodeEl.textContent = code;
                }
                if (guestNameEl) {
                    guestNameEl.textContent = currentBookingData.guest_name || currentBookingData.billing_name || 'Guest';
                }
                if (stayLabelEl) {
                    const stayLabel = currentBookingData.stay_label ||
                        ((currentBookingData.check_in_date && currentBookingData.check_out_date)
                            ? (currentBookingData.check_in_date + ' to ' + currentBookingData.check_out_date)
                            : '-');
                    stayLabelEl.textContent = stayLabel;
                }
                if (errorBox) {
                    errorBox.style.display = 'none';
                    errorBox.textContent = '';
                }
                if (roomsContainer) {
                    roomsContainer.innerHTML = '<p class="text-muted mb-0">Loading available rooms...</p>';
                }
                
                // Disable confirm button initially and reset text
                const confirmBtn = document.getElementById('roomMoveConfirmBtn');
                if (confirmBtn) {
                    confirmBtn.disabled = true;
                    confirmBtn.classList.remove('btn-primary');
                    confirmBtn.classList.add('btn-secondary');
                    confirmBtn.textContent = 'Move Room';
                }

                const assignConfig = window.assignRoomConfig || null;
                const availableEndpoint = assignConfig?.endpoints?.available || null;

                if (!availableEndpoint) {
                    if (roomsContainer) {
                        roomsContainer.innerHTML = '<div class="alert alert-danger mb-0">Room availability service is not ready. Please reload the page and try again.</div>';
                    }
                } else {
                    // Build URL with numeric booking_id parameter
                    const url = new URL(availableEndpoint, window.location.origin);
                    url.searchParams.set('booking_id', String(bookingId));

                    fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (!roomsContainer) return;
                            if (!data || data.success !== true) {
                                roomsContainer.innerHTML = '<div class="alert alert-danger mb-0">' +
                                    (data && data.message ? data.message : 'Failed to load available rooms.') +
                                    '</div>';
                                return;
                            }

                            const rooms = Array.isArray(data.rooms) ? data.rooms : [];
                            
                            // Get number of rooms needed from booking
                            const roomsNeeded = parseInt(currentBookingData.rooms) || 1;
                            
                            // Extract current room IDs from booking data - handle multiple rooms
                            let currentRoomIds = [];
                            
                            // Priority 1: room_numbers_details array (most reliable for multiple rooms)
                            if (currentBookingData.room_numbers_details && Array.isArray(currentBookingData.room_numbers_details) && currentBookingData.room_numbers_details.length > 0) {
                                currentRoomIds = currentBookingData.room_numbers_details.map(r => {
                                    // Handle both object with id property and direct ID values
                                    return String(r.id || r);
                                }).filter(Boolean);
                            }
                            
                            // Priority 2: room_number_ids (comma-separated string) - fallback or additional source
                            if (currentRoomIds.length === 0 && currentBookingData.room_number_ids) {
                                const idsFromString = String(currentBookingData.room_number_ids).split(',').map(id => id.trim()).filter(Boolean);
                                if (idsFromString.length > 0) {
                                    currentRoomIds = idsFromString;
                                }
                            }
                            
                            // Priority 3: Merge if both sources exist (to ensure we get all rooms)
                            if (currentBookingData.room_number_ids && currentBookingData.room_numbers_details) {
                                const idsFromString = String(currentBookingData.room_number_ids).split(',').map(id => id.trim()).filter(Boolean);
                                const idsFromDetails = currentBookingData.room_numbers_details.map(r => String(r.id || r)).filter(Boolean);
                                // Combine and deduplicate
                                currentRoomIds = [...new Set([...idsFromString, ...idsFromDetails])];
                            }
                            
                            // Priority 4: room_number_id (single ID) - last resort
                            if (currentRoomIds.length === 0 && currentBookingData.room_number_id) {
                                currentRoomIds = [String(currentBookingData.room_number_id)];
                            }
                            
                            // Ensure all IDs are strings for comparison
                            currentRoomIds = currentRoomIds.map(id => String(id).trim()).filter(Boolean);
                            
                            if (rooms.length === 0) {
                                roomsContainer.innerHTML = '<p class="text-muted mb-0">No rooms are available for this stay.</p>';
                                return;
                            }
                            
                            // Function to update button state based on selection (defined before use)
                            const updateConfirmButtonState = function() {
                                const selectedCount = roomsContainer.querySelectorAll('.room-move-checkbox:checked').length;
                                const confirmBtn = document.getElementById('roomMoveConfirmBtn');
                                if (confirmBtn) {
                                    // Enable button only if exactly the required number of rooms is selected
                                    if (selectedCount === roomsNeeded && selectedCount > 0) {
                                        confirmBtn.disabled = false;
                                        confirmBtn.classList.remove('btn-secondary');
                                        confirmBtn.classList.add('btn-primary');
                                        
                                        // Update button text to show selection count if multiple rooms needed
                                        if (roomsNeeded > 1) {
                                            confirmBtn.textContent = 'Move Room (' + selectedCount + '/' + roomsNeeded + ')';
                                        } else {
                                            confirmBtn.textContent = 'Move Room';
                                        }
                                    } else {
                                        confirmBtn.disabled = true;
                                        confirmBtn.classList.remove('btn-primary');
                                        confirmBtn.classList.add('btn-secondary');
                                        
                                        if (selectedCount > 0) {
                                            confirmBtn.textContent = 'Move Room (' + selectedCount + '/' + roomsNeeded + ')';
                                        } else {
                                            confirmBtn.textContent = 'Move Room';
                                        }
                                    }
                                }
                            };
                            
                            // Render rooms with selection limit logic
                            roomsContainer.innerHTML = rooms.map(function (room) {
                                const id = room.id;
                                const label = room.label || ('Room ' + id);
                                const isCurrentlyBooked = currentRoomIds.includes(String(id)) || room.is_current === true;
                                const checkedAttr = isCurrentlyBooked ? ' checked' : '';
                                const currentBadge = isCurrentlyBooked ? ' <span class="badge bg-info ms-2">Current</span>' : '';
                                return '<div class="form-check mb-1">' +
                                    '<input class="form-check-input room-move-checkbox" type="checkbox" name="roomMoveRoom" value="' + id + '" id="roomMoveRoom_' + id + '" data-room-id="' + id + '"' + checkedAttr + '>' +
                                    '<label class="form-check-label" for="roomMoveRoom_' + id + '">' + label + currentBadge + '</label>' +
                                    '</div>';
                            }).join('');

                            // Add instruction about number of rooms needed
                            if (roomsNeeded > 1) {
                                const instructionDiv = document.createElement('div');
                                instructionDiv.className = 'alert alert-info mt-2 mb-0 small';
                                instructionDiv.innerHTML = '<strong>Note:</strong> Please select exactly ' + roomsNeeded + ' room(s) for this booking. Once ' + roomsNeeded + ' rooms are selected, other rooms will be disabled.';
                                roomsContainer.appendChild(instructionDiv);
                            }
                            
                            // Add selection limit logic: disable rooms when limit is reached
                            const checkboxes = roomsContainer.querySelectorAll('.room-move-checkbox');
                            const updateRoomAvailability = function() {
                                const selectedCount = roomsContainer.querySelectorAll('.room-move-checkbox:checked').length;
                                
                                checkboxes.forEach(function(checkbox) {
                                    const isChecked = checkbox.checked;
                                    
                                    if (selectedCount >= roomsNeeded && !isChecked) {
                                        // Disable unchecked rooms when limit is reached
                                        checkbox.disabled = true;
                                        checkbox.parentElement.classList.add('text-muted');
                                    } else {
                                        // Enable all rooms when under limit
                                        checkbox.disabled = false;
                                        checkbox.parentElement.classList.remove('text-muted');
                                    }
                                });
                                
                                // Update button state
                                updateConfirmButtonState();
                            };
                            
                            // Attach change handlers
                            checkboxes.forEach(function(checkbox) {
                                checkbox.addEventListener('change', updateRoomAvailability);
                            });
                            
                            // Initial state update
                            updateRoomAvailability();
                        })
                        .catch(err => {
                            if (roomsContainer) {
                                roomsContainer.innerHTML = '<div class="alert alert-danger mb-0">Unable to load available rooms. Please try again.</div>';
                            }
                        });
                }

                if (window.bootstrap && window.bootstrap.Modal) {
                    const modalInstance = window.bootstrap.Modal.getOrCreateInstance(roomMoveModal);
                    modalInstance.show();
                } else {
                    roomMoveModal.classList.add('show');
                    roomMoveModal.style.display = 'block';
                }
            });
        }

        const roomMoveConfirmBtn = document.getElementById('roomMoveConfirmBtn');
        if (roomMoveConfirmBtn) {
            roomMoveConfirmBtn.addEventListener('click', function () {
                const bookingIdInput = document.getElementById('roomMoveBookingId');
                const errorBox = document.getElementById('roomMoveError');
                const bookingId = bookingIdInput ? bookingIdInput.value : '';

                const selectedInputs = document.querySelectorAll('.room-move-checkbox:checked');
                if (!bookingId || selectedInputs.length === 0) {
                    if (errorBox) {
                        errorBox.style.display = 'block';
                        errorBox.textContent = !bookingId
                            ? 'Booking ID not found.'
                            : 'Please select at least one room to move to.';
                    }
                    return;
                }

                // Get number of rooms needed from booking
                const roomsNeeded = parseInt(currentBookingData?.rooms) || 1;
                
                // Validate selection count - must match exactly
                if (selectedInputs.length !== roomsNeeded) {
                    if (errorBox) {
                        errorBox.style.display = 'block';
                        errorBox.textContent = 'Please select exactly ' + roomsNeeded + ' room(s) for this booking. You have selected ' + selectedInputs.length + ' room(s).';
                    }
                    return;
                }

                const assignConfig = window.assignRoomConfig || null;
                const assignEndpoint = assignConfig?.endpoints?.assign || null;
                const csrfToken = assignConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                if (!assignEndpoint || !csrfToken) {
                    if (errorBox) {
                        errorBox.style.display = 'block';
                        errorBox.textContent = 'Room move service is not properly configured. Please reload the page and try again.';
                    }
                    return;
                }

                // Collect all selected room IDs (comma-separated)
                const roomIds = Array.from(selectedInputs).map(input => input.value).join(',');
                const payload = new URLSearchParams();
                payload.append('booking_id', bookingId);
                payload.append('room_number_ids', roomIds);

                fetch(assignEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: payload.toString()
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data || data.success !== true) {
                            if (errorBox) {
                                errorBox.style.display = 'block';
                                errorBox.textContent = data && data.message ? data.message : 'Failed to move room. Please try again.';
                            }
                            return;
                        }

                        swal('Success', data.message || 'Room moved successfully.', 'success')
                            .then(function () {
                                // Reload calendar / booking details to reflect the change
                                if (typeof loadCalendarData === 'function') {
                                    loadCalendarData();
                                } else {
                                    window.location.reload();
                                }
                            });
                    })
                    .catch(err => {
                        if (errorBox) {
                            errorBox.style.display = 'block';
                            errorBox.textContent = 'Unable to move room. Please try again.';
                        }
                    });
            });
        }
        // Extend Day Modal - Date change handler
        const extendDayNewCheckOut = document.getElementById('extendDayNewCheckOut');
        if (extendDayNewCheckOut) {
            extendDayNewCheckOut.addEventListener('change', function() {
                const currentCheckOut = document.getElementById('extendDayCurrentCheckOut').value;
                const newCheckOut = this.value;
                const pricePerNight = parseFloat(document.getElementById('extendDayRoomPrice').value) || 0;
                
                if (!currentCheckOut || !newCheckOut) {
                    document.getElementById('extendDayAdditionalDays').textContent = '0';
                    document.getElementById('extendDayAdditionalAmount').textContent = 'Rs. 0.00';
                    return;
                }
                
                const currentDate = new Date(currentCheckOut);
                const newDate = new Date(newCheckOut);
                
                if (newDate <= currentDate) {
                    document.getElementById('extendDayError').textContent = 'New check-out date must be after current check-out date.';
                    document.getElementById('extendDayError').style.display = 'block';
                    document.getElementById('extendDayAdditionalDays').textContent = '0';
                    document.getElementById('extendDayAdditionalAmount').textContent = 'Rs. 0.00';
                    return;
                }
                
                // Calculate additional days
                const diffTime = newDate - currentDate;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                // Calculate additional amount
                const additionalAmount = diffDays * pricePerNight;
                
                document.getElementById('extendDayAdditionalDays').textContent = diffDays;
                document.getElementById('extendDayAdditionalAmount').textContent = 
                    'Rs. ' + additionalAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('extendDayError').style.display = 'none';
            });
        }
        
        // Extend Day Modal - Confirm button handler
        const extendDayConfirmBtn = document.getElementById('extendDayConfirmBtn');
        if (extendDayConfirmBtn) {
            extendDayConfirmBtn.addEventListener('click', function() {
                const bookingId = document.getElementById('extendDayBookingId').value;
                const newCheckOut = document.getElementById('extendDayNewCheckOut').value;
                const currentCheckOut = document.getElementById('extendDayCurrentCheckOut').value;
                const additionalDays = parseInt(document.getElementById('extendDayAdditionalDays').textContent) || 0;
                
                if (!bookingId || !newCheckOut) {
                    swal('Error', 'Please select a new check-out date.', 'error');
                    return;
                }
                
                if (additionalDays <= 0) {
                    swal('Error', 'Please select a date after the current check-out date.', 'error');
                    return;
                }
                
                // Disable button and show loading
                this.disabled = true;
                this.textContent = 'Processing...';
                
                // Call extend booking API
                fetch(`{{ url('/booking') }}/${bookingId}/extend`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({
                        new_check_out_date: newCheckOut,
                        additional_days: additionalDays
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    this.textContent = 'Extend Stay';
                    
                    if (data.success) {
                        // Close extend day modal
                        const extendModal = document.getElementById('extendDayModal');
                        if (extendModal && window.bootstrap) {
                            const modalInstance = window.bootstrap.Modal.getInstance(extendModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                        
                        // Show success message
                        swal({
                            title: 'Success!',
                            text: data.message || `Stay extended by ${additionalDays} day(s) successfully.`,
                            icon: 'success',
                            button: 'OK'
                        }).then(() => {
                            // Reload booking details to show updated information
                            if (bookingId) {
                                fetch(`{{ url('/booking') }}/${bookingId}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.booking) {
                                        populateBookingModal(data.booking);
                                    }
                                });
                            }
                            
                            // Refresh calendar
                            if (typeof window.loadCalendarData === 'function') {
                                window.loadCalendarData();
                            } else {
                                window.location.reload();
                            }
                        });
                    } else {
                        // Show error in modal
                        const errorDiv = document.getElementById('extendDayError');
                        if (errorDiv) {
                            errorDiv.textContent = data.message || 'Failed to extend stay. Please try again.';
                            errorDiv.style.display = 'block';
                        } else {
                            swal('Error', data.message || 'Failed to extend stay. Please try again.', 'error');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error extending stay:', error);
                    this.disabled = false;
                    this.textContent = 'Extend Stay';
                    swal('Error', 'An error occurred while extending the stay. Please try again.', 'error');
                });
            });
        }
        
        // Cancel Reservation handler
        const cancelReservationLink = document.getElementById('bookingPopupCancelReservation');
        if (cancelReservationLink) {
            cancelReservationLink.addEventListener('click', function(e) {
                e.preventDefault();
                const bookingId = document.getElementById('bookingModalBookingId')?.value;
                if (!bookingId) {
                    swal('Error', 'Booking ID not found.', 'error');
                    return;
                }
                
                swal({
                    title: 'Cancel Reservation',
                    text: 'Are you sure you want to cancel this reservation? This action cannot be undone.',
                    icon: 'warning',
                    buttons: {
                        cancel: {
                            text: 'No',
                            value: false,
                            visible: true,
                            className: 'btn-secondary'
                        },
                        confirm: {
                            text: 'Yes, Cancel',
                            value: true,
                            visible: true,
                            className: 'btn-danger'
                        }
                    },
                    dangerMode: true
                }).then((willCancel) => {
                    if (willCancel) {
                        // Show loading state
                        swal({
                            title: 'Processing...',
                            text: 'Please wait while we cancel the reservation.',
                            icon: 'info',
                            buttons: false,
                            closeOnClickOutside: false,
                            closeOnEsc: false
                        });
                        
                        // Call cancel booking API
                        fetch(`{{ url('/booking') }}/${bookingId}/cancel`, { 
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Close the booking modal
                                const bookingModal = document.getElementById('booking-pop');
                                if (bookingModal && window.bootstrap) {
                                    const modalInstance = window.bootstrap.Modal.getInstance(bookingModal);
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }
                                }
                                
                                // Show success message
                                swal({
                                    title: 'Success!',
                                    text: data.message || 'Reservation cancelled successfully.',
                                    icon: 'success',
                                    button: 'OK'
                                }).then(() => {
                                    // Refresh the calendar to reflect the changes
                                    if (typeof window.loadCalendarData === 'function') {
                                        window.loadCalendarData();
                                    } else {
                                        // Fallback: reload the page
                                        window.location.reload();
                                    }
                                });
                            } else {
                                swal('Error', data.message || 'Failed to cancel reservation.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error cancelling reservation:', error);
                            swal('Error', 'An error occurred while cancelling the reservation. Please try again.', 'error');
                        });
                    }
                });
            });
        }
    })();
    
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
                    <strong>${payment.type} - ${payment.method}</strong>
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
                    <small class="text-muted" style="font-size: 12px;">${payment.display_date}</small>
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
        
        // Calculate total ALL charges (existing + new) for display
        const totalAllCharges = bookingCharges.reduce((sum, c) => sum + c.amount, 0);
        
        // Calculate total existing payments from database
        const totalExistingPayments = bookingPayments.filter(p => p.existing).reduce((sum, p) => sum + p.amount, 0);
        
        // Get original values
        const originalGrandTotal = parseFloat(currentBookingData.grand_total) || 0;
        
        // Calculate new totals
        const newGrandTotal = originalGrandTotal + totalNewCharges;
        // Balance = Grand Total - All Payments (existing + new)
        const newBalance = newGrandTotal - (totalExistingPayments + totalNewPayments);
        
        // Update Extra Charges display (show ALL charges - existing + new)
        const paymentExtraCharges = document.getElementById('paymentExtraCharges');
        if (paymentExtraCharges) {
            paymentExtraCharges.textContent = formatCurrency(totalAllCharges);
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
        
        // Auto-fill payment amount with balance
        const bookingPaymentAmountInput = document.getElementById('bookingPaymentAmount');
        if (bookingPaymentAmountInput && newBalance > 0) {
            bookingPaymentAmountInput.value = newBalance.toFixed(2);
        }
        
        // Calculate total ALL payments (existing + new) for footer display
        const totalAllPayments = bookingPayments.reduce((sum, p) => sum + p.amount, 0);
        
        // Update footer
        const footerTotal = document.getElementById('footerTotal');
        const footerPaid = document.getElementById('footerPaid');
        const footerBalance = document.getElementById('footerBalance');
        
        if (footerTotal) footerTotal.textContent = formatCurrency(newGrandTotal);
        if (footerPaid) footerPaid.textContent = formatCurrency(totalAllPayments);
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
        // Set loading state before rendering to prevent showing empty state during navigation
        setCalendarLoading(true);
        renderCalendar();
        fetchCalendarDataForCurrentView();
    }

    function shiftRange(direction) {
        if (calendarState.viewMode === 'week') {
            if (direction > 0) {
                // Going forward: set referenceDate to the last date from previous view
                // Keep the original forward logic unchanged
                const lastDate = new Date(calendarState.referenceDate);
                lastDate.setDate(lastDate.getDate() + (WEEK_DAY_COUNT - 1));
                calendarState.referenceDate = lastDate;
                calendarState.isNavigating = true; // Mark as navigating to add active state
            } else {
                // Going backward: reverse the forward action
                // Since forward sets referenceDate to last date of range, backward should go back to first date of previous range
                // To reverse: subtract (WEEK_DAY_COUNT - 1) to get back to the first date that forward started from
                calendarState.referenceDate.setDate(
                    calendarState.referenceDate.getDate() - (WEEK_DAY_COUNT - 1)
                );
                calendarState.isNavigating = true;
            }
        } else {
            calendarState.referenceDate.setMonth(calendarState.referenceDate.getMonth() + direction);
            calendarState.isNavigating = true;
        }
        syncDatePicker();
        // Set loading state before rendering to prevent showing empty state during navigation
        setCalendarLoading(true);
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
        // Set loading state before rendering to prevent showing empty state during navigation
        setCalendarLoading(true);
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
            // Set minimum check-out date to check-in date + 1 day (same day not allowed)
            const checkInValue = checkInDateInput?.value;
            if (checkInValue) {
                const checkInDateObj = new Date(checkInValue);
                checkInDateObj.setDate(checkInDateObj.getDate() + 1);
                const minCheckOutDate = checkInDateObj.toISOString().split('T')[0];
                checkOutDateInput.setAttribute('min', minCheckOutDate);
            } else {
                // If no check-in date, set to tomorrow as minimum
                const tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                checkOutDateInput.setAttribute('min', tomorrow.toISOString().split('T')[0]);
            }
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

        // Function to build rooms dropdown options based on available count
        function buildRoomsDropdownOptions(roomTypeId, selectedValue = '0', isFromCalendarSelection = false) {
            if (isFromCalendarSelection) {
                // If from calendar selection, only show 1 room and disable
                return '<option value="1" selected disabled>1</option>';
            }
            
            // If no room type selected, show only 0 and disable
            if (!roomTypeId) {
                return '<option value="0" selected>0</option>';
            }
            
            // Default max rooms if dates not set
            let maxRooms = 10;
            
            // Try to get available count if room type is selected
            const selectedRoomType = roomsList.find(r => Number(r.id) === Number(roomTypeId));
            if (selectedRoomType) {
                try {
                    const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                    if (availableCount !== null && availableCount > 0) {
                        maxRooms = availableCount;
                    } else if (availableCount === 0) {
                        // If no availability, show only 0
                        return '<option value="0" selected>0</option>';
                    }
                } catch (e) {
                    // If getAvailableRoomsCount fails, use default
                    console.warn('Error getting available rooms count:', e);
                }
            }
            
            // Ensure selectedValue is valid (should be > 0 when room type is selected)
            const selectedNum = parseInt(selectedValue, 10) || 0;
            if (selectedNum > maxRooms) {
                selectedValue = maxRooms.toString();
            } else if (selectedNum < 1) {
                selectedValue = '1';
            }
            
            const options = [];
            for (let i = 1; i <= maxRooms; i++) {
                const selected = i == selectedValue ? 'selected' : '';
                options.push(`<option value="${i}" ${selected}>${i}</option>`);
            }
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
            
            // Find room type ID to match with unassigned bookings
            const roomTypeId = roomsList.find(r => r.title === roomTypeTitle)?.id;
            
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
                const totalCount = override?.count ?? summary.defaultCount ?? 0;
                
                // Calculate unassigned rooms for this date and room type
                // IMPORTANT: Only count unassigned rooms on check-in date (startDate), not on all dates in booking range
                let unassignedRoomsForDate = 0;
                if (roomTypeId && Array.isArray(unassignedBookings)) {
                    const unassignedForDate = unassignedBookings.filter(booking => {
                        if (!booking.startDate) return false;
                        const start = new Date(booking.startDate);
                        const current = new Date(dateIso);
                        
                        // CRITICAL: Only count on check-in date (startDate), not on other dates in the range
                        // Convert both to ISO date strings for exact date comparison (ignore time)
                        const startIso = start.toISOString().split('T')[0];
                        const currentIso = current.toISOString().split('T')[0];
                        
                        // Only match if current date exactly equals check-in date
                        if (currentIso !== startIso) return false;
                        
                        // Match by room_id
                        return booking.room_id == roomTypeId;
                    });
                    
                    // Sum up rooms from all unassigned bookings for this date
                    unassignedRoomsForDate = unassignedForDate.reduce((sum, booking) => {
                        return sum + (parseInt(booking.rooms) || 1);
                    }, 0);
                }
                
                // Calculate actual available: Total - Unassigned
                const availableCount = Math.max(0, totalCount - unassignedRoomsForDate);
                
                if (availableCount < minAvailability) {
                    minAvailability = availableCount;
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
            // Room availability rules:
            // 1. Checkout date is NON-BLOCKING - room becomes available on checkout day
            // 2. Check-in date is BLOCKING - room is occupied on check-in day
            // 3. If new booking's check-in equals existing booking's checkout, room is available
            // 4. Only true date overlaps (excluding checkout day) should block availability
            // 5. Overlap: newCheckIn < existingCheckOut AND newCheckOut > existingCheckIn
            
            if (bookingData && Array.isArray(bookingData)) {
                bookingData.forEach((booking) => {
                    const bookingStart = booking.start_date || booking.startDate;
                    const bookingEnd = booking.end_date || booking.endDate || bookingStart;
                    if (!bookingStart) return;
                    
                    try {
                        const start = new Date(bookingStart);
                        const end = new Date(bookingEnd);
                        const inclusiveEnd = booking.inclusive_end || booking.is_blocked;
                        
                        // Convert to ISO strings for comparison
                        const bookingStartIso = start.toISOString().split('T')[0];
                        const bookingEndIso = end.toISOString().split('T')[0];
                        const checkInIso = checkIn.toISOString().split('T')[0];
                        const checkOutIso = checkOut.toISOString().split('T')[0];
                        
                        // Key rule: Checkout date is non-blocking, check-in date is blocking
                        // For regular bookings (inclusive_end = true), checkout date IS included in booking
                        // But for availability purposes, checkout day should be non-blocking for new bookings
                        
                        // Check if existing checkout equals new check-in (checkout day exception)
                        // If checkout == new check-in, room is available (checkout happens, room becomes available)
                        const existingCheckoutEqualsNewCheckIn = bookingEndIso === checkInIso;
                        
                        // Standard overlap check: two date ranges overlap if they share any blocking dates
                        // Range 1 (existing): [bookingStart, bookingEnd] if inclusive, [bookingStart, bookingEnd) if not
                        // Range 2 (new): [checkIn, checkOut) - checkout is always exclusive for new bookings
                        // They overlap if: bookingStart < checkOut && checkIn < bookingEnd
                        // BUT: If bookingEnd == checkIn, no overlap (checkout day is non-blocking)
                        
                        const bookingStartBeforeNewCheckOut = bookingStartIso < checkOutIso;
                        // For overlap: new check-in must be before existing checkout (or equal if inclusive, but we exclude equal)
                        // If checkout == check-in, we already handled it above
                        const newCheckInBeforeBookingEnd = checkInIso < bookingEndIso;
                        
                        // Overlap exists if both conditions are true AND it's not a checkout day exception
                        // Note: Even if inclusive_end is true, checkout day is non-blocking for new bookings
                        const hasOverlap = bookingStartBeforeNewCheckOut && 
                                         newCheckInBeforeBookingEnd &&
                                         !existingCheckoutEqualsNewCheckIn;
                        
                        if (!hasOverlap) return; // No overlap, skip this booking
                        
                        // Get room number IDs from booking
                        let roomNumberIds = [];
                        if (booking.room_number_ids) {
                            roomNumberIds = booking.room_number_ids.split(',').map(id => parseInt(id.trim())).filter(id => !isNaN(id));
                        } else if (booking.is_blocked && booking.room_id) {
                            roomNumberIds = [parseInt(booking.room_id)];
                        }
                        
                        if (roomNumberIds.length === 0) return; // No room numbers, skip
                        
                        // Mark dates as booked
                        // Important: Check-in date is blocking, checkout date is NOT blocking
                        // Even if inclusive_end is true, checkout day should not block new bookings
                        let currentDate = new Date(start);
                        while (true) {
                            const shouldInclude = inclusiveEnd ? currentDate <= end : currentDate < end;
                            if (!shouldInclude) break;
                            
                            const dateIso = currentDate.toISOString().split('T')[0];
                            
                            // Skip checkout day - it's non-blocking for new bookings
                            // If this is the checkout day and it equals new check-in, room is available
                            if (dateIso === bookingEndIso && dateIso === checkInIso) {
                                currentDate.setDate(currentDate.getDate() + 1);
                                if (currentDate > end) break;
                                continue;
                            }
                            
                            // Only mark dates that fall within the new booking range
                            // Check-in date is blocking, checkout date is not
                            if (dateIso >= checkInIso && dateIso < checkOutIso) {
                                // Don't mark checkout day as blocked (even if it's in the range)
                                if (dateIso === bookingEndIso && dateIso === checkInIso) {
                                    // Skip - checkout day is non-blocking
                                } else {
                                    roomNumberIds.forEach((roomNumberId) => {
                                        if (!bookingLookup.has(roomNumberId)) {
                                            bookingLookup.set(roomNumberId, new Set());
                                        }
                                        bookingLookup.get(roomNumberId).add(dateIso);
                                    });
                                }
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
            
            // Determine rooms value based on whether dates are from calendar selection
            const roomsValue = isFromCalendarSelection ? '1' : (initial.rooms || (initial.room_id ? '1' : '0'));
            // Disable if from calendar selection OR if no room type selected
            const roomsDisabled = isFromCalendarSelection || !initial.room_id ? 'disabled' : '';
            const roomsStyle = (isFromCalendarSelection || !initial.room_id) ? 'style="background-color: #e9ecef; cursor: not-allowed;"' : '';
            
            // Build rooms dropdown options
            const roomsOptions = buildRoomsDropdownOptions(initial.room_id || null, roomsValue, isFromCalendarSelection);
            
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
                        <select class="form-control room-qty-select" name="room_details[${currentIndex}][rooms]" ${roomsDisabled} ${roomsStyle}>
                            ${roomsOptions}
                        </select>
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
            const roomsSelect = wrapper.querySelector('.room-qty-select');
            
            if (initial.room_id) {
                roomSelect.value = initial.room_id;
                // Update rooms dropdown when room type is set
                if (!isFromCalendarSelection && roomsSelect) {
                    const roomsOptions = buildRoomsDropdownOptions(initial.room_id, roomsValue, false);
                    roomsSelect.innerHTML = roomsOptions;
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
                const qty = parseInt(row.querySelector('.room-qty-select')?.value, 10) || 0;
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
            
            // Update all totals including charges and payments via updatePaymentCalculations
            // This ensures both tabs' footers are updated with the correct totals
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
            
            // Validate that check-in and check-out dates are not the same day
            if (checkInDate === checkOutDate) {
                showErrors(['Check-out date must be at least one day after check-in date. Same day check-in and check-out is not allowed.']);
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
                const qty = row.querySelector('.room-qty-select')?.value;
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
                
                // Update rooms dropdown based on selected room type
                const roomsSelect = row.querySelector('.room-qty-select');
                if (roomsSelect) {
                    const roomTypeId = event.target.value;
                    
                    if (roomTypeId) {
                        // Room type selected - enable dropdown and update options
                        roomsSelect.disabled = false;
                        roomsSelect.style.backgroundColor = '';
                        roomsSelect.style.cursor = '';
                        
                        const selectedRoomType = roomsList.find(r => r.id == roomTypeId);
                        if (selectedRoomType) {
                            const currentValue = roomsSelect.value && roomsSelect.value !== '0' ? roomsSelect.value : '1';
                            const roomsOptions = buildRoomsDropdownOptions(roomTypeId, currentValue, false);
                            roomsSelect.innerHTML = roomsOptions;
                            // If current value exceeds max, set to max
                            const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                            if (availableCount !== null && parseInt(currentValue, 10) > availableCount) {
                                roomsSelect.value = availableCount;
                            }
                        }
                    } else {
                        // No room type selected - disable dropdown and show 0
                        roomsSelect.disabled = true;
                        roomsSelect.style.backgroundColor = '#e9ecef';
                        roomsSelect.style.cursor = 'not-allowed';
                        roomsSelect.innerHTML = '<option value="0" selected>0</option>';
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
                if (selectAllBtn) {
                    selectAllBtn.disabled = true;
                    selectAllBtn.style.cursor = 'not-allowed';
                }
            } else {
                roomNumberSearchInput.removeAttribute('readonly');
                roomNumberSearchInput.style.backgroundColor = '';
                roomNumberSearchInput.style.cursor = '';
                if (selectAllBtn) {
                    selectAllBtn.disabled = false;
                    selectAllBtn.style.cursor = '';
                }
            }
            
            // If dropdown is currently visible, refresh it with checkboxes
            if (roomNumberDropdown.style.display === 'block') {
                const searchTerm = roomNumberSearchInput.value.toLowerCase().trim();
                showRoomNumberDropdown(searchTerm);
            } else {
                // Ensure dropdown is closed initially
                roomNumberDropdown.style.display = 'none';
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
        
        // Function to toggle room number dropdown
        function toggleRoomNumberDropdown() {
            if (isDatesFromCalendarSelection) {
                return; // Don't allow if readonly
            }
            
            // Toggle dropdown visibility
            const isVisible = roomNumberDropdown.style.display === 'block';
            if (isVisible) {
                roomNumberDropdown.style.display = 'none';
                return;
            }
            
            // Show dropdown with checkboxes
            showRoomNumberDropdown();
        }
        
        // Function to show room number dropdown with checkboxes
        function showRoomNumberDropdown(searchTerm = '', preserveScrollPosition = null) {
            if (isDatesFromCalendarSelection) {
                return; // Don't allow if readonly
            }
            
            const availableRoomsJson = roomNumberDropdown.dataset.availableRooms;
            if (!availableRoomsJson) {
                updateRoomNumberOptions();
                // Try again after a short delay
                setTimeout(() => showRoomNumberDropdown(searchTerm, preserveScrollPosition), 100);
                return;
            }
            
            const availableRooms = JSON.parse(availableRoomsJson);
            const filtered = availableRooms.filter(room => {
                // Filter by search term if provided
                if (searchTerm) {
                    return room.number.toLowerCase().includes(searchTerm.toLowerCase());
                }
                return true; // Show all if no search term
            });
            
            if (filtered.length === 0) {
                roomNumberDropdown.style.display = 'none';
                return;
            }
            
            // Get rooms quantity limit
            const roomsInput = detailsContainer.querySelector('.room-qty-select');
            const roomsQty = roomsInput ? parseInt(roomsInput.value, 10) || 0 : 0;
            const selectedCount = selectedRoomNumbers.size;
            const maxSelectable = roomsQty > 0 ? roomsQty : filtered.length;
            
            // Get room type for selected rooms
            const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
            const selectedRoomType = roomTypeSelect ? roomsList.find(r => r.id == roomTypeSelect.value) : null;
            const roomTypeTitle = selectedRoomType ? selectedRoomType.title : '';
            
            // Sort rooms by number (serially)
            const sortedRooms = filtered.sort((a, b) => {
                const numA = parseInt(a.number.match(/\d+/)?.[0] || '0', 10);
                const numB = parseInt(b.number.match(/\d+/)?.[0] || '0', 10);
                if (numA !== numB) {
                    return numA - numB;
                }
                return a.number.localeCompare(b.number);
            });
            
            // Render dropdown with checkboxes
            let dropdownHtml = '<div class="list-group" style="max-height: 200px; overflow-y: auto;">';
            sortedRooms.forEach((room) => {
                const isChecked = selectedRoomNumbers.has(room.id);
                const isDisabled = !isChecked && selectedCount >= maxSelectable && maxSelectable > 0;
                dropdownHtml += `
                    <label class="list-group-item d-flex align-items-center" style="cursor: ${isDisabled ? 'not-allowed' : 'pointer'}; ${isDisabled ? 'opacity: 0.6;' : ''}">
                        <input type="checkbox" class="form-check-input me-2 room-number-checkbox" 
                               value="${room.id}" 
                               data-room-id="${room.id}" 
                               data-room-number="${room.number}"
                               ${isChecked ? 'checked' : ''}
                               ${isDisabled ? 'disabled' : ''}>
                        <span>${room.number}</span>
                    </label>
                `;
            });
            dropdownHtml += '</div>';
            roomNumberDropdown.innerHTML = dropdownHtml;
            roomNumberDropdown.style.display = 'block';
            
            // Restore scroll position if provided
            if (preserveScrollPosition !== null) {
                const listGroup = roomNumberDropdown.querySelector('.list-group');
                if (listGroup) {
                    listGroup.scrollTop = preserveScrollPosition;
                }
            }
            
            // Add checkbox change handlers
            roomNumberDropdown.querySelectorAll('.room-number-checkbox').forEach((checkbox) => {
                checkbox.addEventListener('change', (e) => {
                    const roomId = parseInt(e.target.value);
                    const roomNumber = e.target.dataset.roomNumber;
                    const isChecked = e.target.checked;
                    
                    // Save scroll position before updating
                    const listGroup = roomNumberDropdown.querySelector('.list-group');
                    const scrollPosition = listGroup ? listGroup.scrollTop : 0;
                    
                    if (isChecked) {
                        // Check if we've reached the limit
                        if (maxSelectable > 0 && selectedRoomNumbers.size >= maxSelectable) {
                            e.target.checked = false;
                            swal("Error!", `You can only select up to ${maxSelectable} room(s) according to the rooms field value.`, "error");
                            return;
                        }
                        
                        // Add room to selection
                        selectedRoomNumbers.set(roomId, {
                            id: roomId,
                            number: roomNumber,
                            roomType: roomTypeTitle
                        });
                    } else {
                        // Remove room from selection
                        selectedRoomNumbers.delete(roomId);
                    }
                    
                    renderSelectedRoomNumbers();
                    
                    // Refresh dropdown to update disabled states while preserving scroll position
                    const currentSearchTerm = roomNumberSearchInput.value.toLowerCase().trim();
                    showRoomNumberDropdown(currentSearchTerm, scrollPosition);
                });
            });
        }
        
        // Room number search functionality
        if (roomNumberSearchInput && roomNumberDropdown) {
            let searchTimeout = null;
            
            // Toggle dropdown on click (closed by default, opens on click)
            roomNumberSearchInput.addEventListener('click', (e) => {
                if (isDatesFromCalendarSelection) {
                    return; // Don't allow if readonly
                }
                e.stopPropagation(); // Prevent document click from closing it immediately
                toggleRoomNumberDropdown();
            });
            
            // Update dropdown when typing (filter rooms)
            roomNumberSearchInput.addEventListener('input', (e) => {
                if (isDatesFromCalendarSelection) {
                    return; // Don't allow search if readonly
                }
                
                const searchTerm = e.target.value.toLowerCase().trim();
                
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    // Only update if dropdown is visible
                    if (roomNumberDropdown.style.display === 'block') {
                        showRoomNumberDropdown(searchTerm);
                    }
                }, 300);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!roomNumberSearchInput.contains(e.target) && 
                    !roomNumberDropdown.contains(e.target) &&
                    !selectAllBtn?.contains(e.target)) {
                    roomNumberDropdown.style.display = 'none';
                }
            });
        }
        
        // Guest Information Validation
        const guestNameInput = document.getElementById('quickGuestName');
        const guestMobileInput = document.getElementById('quickGuestMobile');
        
        // Name field - only allow alphabetic characters and spaces
        if (guestNameInput) {
            guestNameInput.addEventListener('input', (e) => {
                // Remove any non-alphabetic characters (except spaces)
                let value = e.target.value.replace(/[^a-zA-Z\s]/g, '');
                // Remove multiple consecutive spaces
                value = value.replace(/\s+/g, ' ');
                // Don't trim here to allow user to type spaces
                e.target.value = value;
                
                // Validate and add/remove invalid class
                const trimmedValue = value.trim();
                if (trimmedValue.length > 0 && !/^[a-zA-Z\s]+$/.test(trimmedValue)) {
                    e.target.classList.add('is-invalid');
                } else if (trimmedValue.length > 0) {
                    e.target.classList.remove('is-invalid');
                } else {
                    e.target.classList.remove('is-invalid');
                }
            });
            
            guestNameInput.addEventListener('blur', (e) => {
                const value = e.target.value.trim();
                if (value.length > 0 && !/^[a-zA-Z\s]+$/.test(value)) {
                    e.target.classList.add('is-invalid');
                } else if (value.length === 0) {
                    e.target.classList.remove('is-invalid');
                } else {
                    e.target.classList.remove('is-invalid');
                    // Trim the value on blur
                    e.target.value = value;
                }
            });
        }
        
        // Mobile field - only allow numeric digits, exactly 10 digits
        if (guestMobileInput) {
            guestMobileInput.addEventListener('input', (e) => {
                // Remove any non-numeric characters
                let value = e.target.value.replace(/[^0-9]/g, '');
                // Limit to 10 digits
                value = value.substring(0, 10);
                e.target.value = value;
                
                // Validate and add/remove invalid class
                if (value.length === 10) {
                    e.target.classList.remove('is-invalid');
                } else if (value.length > 0 && value.length < 10) {
                    e.target.classList.add('is-invalid');
                } else {
                    e.target.classList.remove('is-invalid');
                }
            });
            
            guestMobileInput.addEventListener('blur', (e) => {
                const value = e.target.value.trim();
                if (value.length > 0 && value.length !== 10) {
                    e.target.classList.add('is-invalid');
                    swal("Error!", "Mobile number must be exactly 10 digits.", "error");
                } else if (value.length === 10) {
                    e.target.classList.remove('is-invalid');
                } else {
                    e.target.classList.remove('is-invalid');
                }
            });
        }
        
        // Select All functionality
        const selectAllBtn = document.getElementById('quickSelectAllRooms');
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => {
                if (isDatesFromCalendarSelection) {
                    return; // Don't allow if readonly
                }
                
                // Get the selected room type from the room details
                const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
                if (!roomTypeSelect || !roomTypeSelect.value) {
                    swal("Error!", "Please select a room type first.", "error");
                    return;
                }
                
                // Get the rooms quantity value
                const roomsInput = detailsContainer.querySelector('.room-qty-select');
                if (!roomsInput) {
                    swal("Error!", "Please add a room detail first.", "error");
                    return;
                }
                
                const roomsQty = parseInt(roomsInput.value, 10) || 0;
                if (roomsQty <= 0) {
                    swal("Error!", "Please enter a valid number of rooms.", "error");
                    return;
                }
                
                // Get available room numbers for the selected room type
                const availableRooms = getAvailableRoomNumbers(roomTypeSelect.value);
                
                if (availableRooms.length === 0) {
                    swal("Error!", "No available rooms to select.", "error");
                    return;
                }
                
                // Clear old selection before selecting new rooms
                selectedRoomNumbers.clear();
                
                // Sort rooms by number (serially) and select up to roomsQty
                const sortedRooms = availableRooms.sort((a, b) => {
                    // Extract numeric part for proper sorting
                    const numA = parseInt(a.number.match(/\d+/)?.[0] || '0', 10);
                    const numB = parseInt(b.number.match(/\d+/)?.[0] || '0', 10);
                    if (numA !== numB) {
                        return numA - numB;
                    }
                    // If numbers are same, sort alphabetically
                    return a.number.localeCompare(b.number);
                });
                
                // Select rooms serially up to roomsQty
                const roomsToSelect = Math.min(roomsQty, sortedRooms.length);
                const selectedRoomType = roomsList.find(r => r.id == roomTypeSelect.value);
                const roomTypeTitle = selectedRoomType ? selectedRoomType.title : '';
                
                for (let i = 0; i < roomsToSelect; i++) {
                    const room = sortedRooms[i];
                    selectedRoomNumbers.set(room.id, {
                        id: room.id,
                        number: room.number,
                        roomType: roomTypeTitle
                    });
                }
                
                renderSelectedRoomNumbers();
                roomNumberSearchInput.value = '';
                roomNumberDropdown.style.display = 'none';
            });
        }

        // Clear Select Room Numbers when Rooms (quantity) value changes so selection matches new count
        function clearQuickReservationSelectedRoomNumbers() {
            selectedRoomNumbers.clear();
            if (roomNumberIdsInput) roomNumberIdsInput.value = '';
            if (selectedRoomNumbersDiv) selectedRoomNumbersDiv.innerHTML = '';
            if (roomNumberSearchInput) roomNumberSearchInput.value = '';
            if (roomNumberDropdown) {
                roomNumberDropdown.querySelectorAll('.room-number-checkbox').forEach((cb) => { cb.checked = false; });
                roomNumberDropdown.style.display = 'none';
            }
        }

        detailsContainer.addEventListener('change', (event) => {
            if (event.target.classList.contains('room-qty-select')) {
                clearQuickReservationSelectedRoomNumbers();
            }
            if (event.target.classList.contains('room-qty-select')
                || event.target.classList.contains('room-price-input')) {
            recalcSummary();
            }
        });
        
        detailsContainer.addEventListener('input', (event) => {
            if (event.target.classList.contains('room-price-input')) {
            recalcSummary();
            }
        });

        // Removed remove room button functionality - single entry only

        // Function to reset Room Type and Room Numbers when dates change
        function resetRoomSelection() {
            // Reset Room Type dropdown
            const roomTypeSelects = detailsContainer.querySelectorAll('.room-type-select');
            roomTypeSelects.forEach((select) => {
                select.value = '';
            });
            
            // Reset Room Numbers selection
            if (selectedRoomNumbers) {
                selectedRoomNumbers.clear();
            }
            if (roomNumberIdsInput) {
                roomNumberIdsInput.value = '';
            }
            if (selectedRoomNumbersDiv) {
                selectedRoomNumbersDiv.innerHTML = '';
            }
            if (roomNumberSearchInput) {
                roomNumberSearchInput.value = '';
            }
            if (roomNumberDropdown) {
                roomNumberDropdown.style.display = 'none';
                roomNumberDropdown.innerHTML = '';
                roomNumberDropdown.removeAttribute('data-available-rooms');
            }
            
            // Reset rate type, rooms, adults, children, and price fields
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const rateSelect = row.querySelector('.room-rate-select');
                const roomsInput = row.querySelector('.room-qty-select');
                const adultsInput = row.querySelector('.room-adult-input');
                const childrenInput = row.querySelector('.room-child-input');
                const priceInput = row.querySelector('.room-price-input');
                
                if (rateSelect) rateSelect.innerHTML = '<option value="">Select</option>';
                if (roomsInput) roomsInput.value = '1';
                if (adultsInput) adultsInput.value = '1';
                if (childrenInput) childrenInput.value = '0';
                if (priceInput) priceInput.value = '';
            });
        }

        checkInDateInput?.addEventListener('change', () => {
            const checkInDate = checkInDateInput.value || todayIso;
            if (checkOutDateInput && checkInDate) {
                // Set minimum check-out date to check-in date + 1 day (same day not allowed)
                const checkInDateObj = new Date(checkInDate);
                checkInDateObj.setDate(checkInDateObj.getDate() + 1);
                const minCheckOutDate = checkInDateObj.toISOString().split('T')[0];
                checkOutDateInput.setAttribute('min', minCheckOutDate);
                
                // If current check-out date is same as or before check-in, update it
                if (checkOutDateInput.value && (checkOutDateInput.value <= checkInDate)) {
                    checkOutDateInput.value = minCheckOutDate;
                }
            }
            // Reset room selection when dates change
            resetRoomSelection();
            // Update room type options based on new dates
            updateRoomTypeOptions();
            // Update rooms max value for all rows (if not readonly)
            updateRoomsMaxValues();
            recalcSummary();
        });

        checkOutDateInput?.addEventListener('change', () => {
            // Validate that check-out date is not the same as check-in date
            if (checkInDateInput?.value && checkOutDateInput?.value) {
                if (checkInDateInput.value === checkOutDateInput.value) {
                    // Set check-out to check-in + 1 day
                    const checkInDateObj = new Date(checkInDateInput.value);
                    checkInDateObj.setDate(checkInDateObj.getDate() + 1);
                    const minCheckOutDate = checkInDateObj.toISOString().split('T')[0];
                    checkOutDateInput.value = minCheckOutDate;
                    
                    // Show error message
                    if (errorsBox) {
                        errorsBox.classList.remove('d-none');
                        errorsBox.innerHTML = '<div>Check-out date must be at least one day after check-in date. Same day check-in and check-out is not allowed.</div>';
                        setTimeout(() => {
                            errorsBox.classList.add('d-none');
                        }, 5000);
                    }
                }
            }
            // Reset room selection when dates change
            resetRoomSelection();
            // Update room type options based on new dates
            updateRoomTypeOptions();
            // Update rooms max value for all rows (if not readonly)
            updateRoomsMaxValues();
            recalcSummary();
        });
        
        // Function to update rooms dropdown options for all rows
        function updateRoomsMaxValues() {
            if (isDatesFromCalendarSelection) {
                return; // Don't update if from calendar selection (disabled)
            }
            detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
                const roomsSelect = row.querySelector('.room-qty-select');
                const roomSelect = row.querySelector('.room-type-select');
                if (roomsSelect && roomSelect) {
                    const roomTypeId = roomSelect.value;
                    
                    if (roomTypeId) {
                        // Room type selected - enable and update dropdown
                        roomsSelect.disabled = false;
                        roomsSelect.style.backgroundColor = '';
                        roomsSelect.style.cursor = '';
                        
                        const selectedRoomType = roomsList.find(r => r.id == roomTypeId);
                        if (selectedRoomType) {
                            const currentValue = roomsSelect.value && roomsSelect.value !== '0' ? roomsSelect.value : '1';
                            const roomsOptions = buildRoomsDropdownOptions(roomTypeId, currentValue, false);
                            roomsSelect.innerHTML = roomsOptions;
                            // If current value exceeds max, set to max
                            const availableCount = getAvailableRoomsCount(selectedRoomType.title);
                            if (availableCount !== null && parseInt(currentValue, 10) > availableCount) {
                                roomsSelect.value = availableCount;
                            }
                        }
                    } else {
                        // No room type selected - disable and show 0
                        roomsSelect.disabled = true;
                        roomsSelect.style.backgroundColor = '#e9ecef';
                        roomsSelect.style.cursor = 'not-allowed';
                        roomsSelect.innerHTML = '<option value="0" selected>0</option>';
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
                
                // Update Select All button state
                if (selectAllBtn) {
                    if (isDatesFromCalendarSelection) {
                        selectAllBtn.disabled = true;
                        selectAllBtn.style.cursor = 'not-allowed';
                    } else {
                        selectAllBtn.disabled = false;
                        selectAllBtn.style.cursor = '';
                    }
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
                        const qty = parseInt(row.querySelector('.room-qty-select')?.value, 10) || 0;
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
                
                // Update Select All button state
                if (selectAllBtn) {
                    selectAllBtn.disabled = false;
                    selectAllBtn.style.cursor = '';
                }
                
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
                
                // Remove validation classes on reset
                if (guestNameInput) {
                    guestNameInput.classList.remove('is-invalid');
                }
                if (guestMobileInput) {
                    guestMobileInput.classList.remove('is-invalid');
                }
                
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
                // Clear payment amount - it will be auto-filled with new balance by updatePaymentCalculations
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
                            <small class="text-muted" style="font-size: 12px;">${payment.display_date}</small>
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
                            <small class="text-muted" style="font-size: 12px;">${payment.display_date}</small>
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
                const qty = parseInt(row.querySelector('.room-qty-select')?.value, 10) || 0;
                const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
                roomTotal += qty * price * nights;
            });
            
            const totalCharges = quickCharges.reduce((sum, charge) => sum + charge.amount, 0);
            const totalPayments = quickPayments.reduce((sum, payment) => sum + payment.amount, 0);
            
            const extraCharges = totalCharges;
            const grandTotal = roomTotal + extraCharges;
            const balance = grandTotal - totalPayments;
            
            // Get current date
            const currentDate = new Date().toLocaleDateString('en-GB');
            
            // Update Details section
            const quickPaymentDate = document.getElementById('quickPaymentDate');
            const quickPaymentRoomPrice = document.getElementById('quickPaymentRoomPrice');
            const quickPaymentExtraCharges = document.getElementById('quickPaymentExtraCharges');
            const quickPaymentSGST = document.getElementById('quickPaymentSGST');
            const quickPaymentCGST = document.getElementById('quickPaymentCGST');
            const quickPaymentTotalPayable = document.getElementById('quickPaymentTotalPayable');
            const quickPaymentBalance = document.getElementById('quickPaymentBalance');
            
            if (quickPaymentDate) quickPaymentDate.textContent = currentDate;
            if (quickPaymentRoomPrice) quickPaymentRoomPrice.textContent = 'Rs. ' + formatAmount(roomTotal);
            
            if (quickPaymentExtraCharges) quickPaymentExtraCharges.textContent = 'Rs. ' + formatAmount(extraCharges);
            if (quickPaymentSGST) quickPaymentSGST.textContent = 'Rs. 0';
            if (quickPaymentCGST) quickPaymentCGST.textContent = 'Rs. 0';
            if (quickPaymentTotalPayable) quickPaymentTotalPayable.textContent = 'Rs. ' + formatAmount(grandTotal);
            if (quickPaymentBalance) quickPaymentBalance.textContent = 'Rs. ' + formatAmount(balance);
            
            // Auto-fill payment amount with balance
            const quickPaymentAmountInput = document.getElementById('quickPaymentAmount');
            if (quickPaymentAmountInput && balance > 0) {
                quickPaymentAmountInput.value = balance.toFixed(2);
            }
            
            // Update footer in Payment Details tab
            const quickPaymentFooterTotal = document.getElementById('quickPaymentFooterTotal');
            const quickPaymentFooterPaid = document.getElementById('quickPaymentFooterPaid');
            const quickPaymentFooterBalance = document.getElementById('quickPaymentFooterBalance');
            
            if (quickPaymentFooterTotal) quickPaymentFooterTotal.textContent = formatAmount(grandTotal);
            if (quickPaymentFooterPaid) quickPaymentFooterPaid.textContent = formatAmount(totalPayments);
            if (quickPaymentFooterBalance) quickPaymentFooterBalance.textContent = formatAmount(balance);
            
            // Update footer in Normal Booking tab (first tab) - includes charges
            if (totalAmountLabel) totalAmountLabel.textContent = formatAmount(grandTotal);
            if (paidAmountLabel) paidAmountLabel.textContent = formatAmount(totalPayments);
            if (balanceAmountLabel) balanceAmountLabel.textContent = formatAmount(balance);
            
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
                    const qty = parseInt(row.querySelector('.room-qty-select')?.value, 10) || 0;
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

    // Assign Room Modal Functionality
    (function initAssignRoomModal() {
        const assignModalEl = document.getElementById('assign');
        if (!assignModalEl) return;
        
        const modalBody = assignModalEl.querySelector('.modal-body');
        const assignEndpoints = {
            unassigned: @json(route('assign.rooms.unassigned')),
            available: @json(route('assign.rooms.available')),
            assign: @json(route('assign.rooms.assign'))
        };
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const hotelSelector = document.getElementById('hotelSelector');
        const assetBaseUrl = @json($siteUrl.'public/assets/img/');

        // Expose assign room configuration globally so other features (like Room Move)
        // can reuse the same endpoints and CSRF token.
        window.assignRoomConfig = {
            endpoints: assignEndpoints,
            csrfToken: csrfToken,
            assetBaseUrl: assetBaseUrl
        };
        
        let assignRoomSelectModalInstance = null;
        
        // Helper functions
        function startOfWeek(date) {
            const base = new Date(date);
            base.setHours(0, 0, 0, 0);
            const day = base.getDay();
            const diff = day === 0 ? -6 : 1 - day;
            base.setDate(base.getDate() + diff);
            return base;
        }
        
        function parseISO(value) {
            // Parse date string in YYYY-MM-DD format as local date (not UTC)
            if (typeof value === 'string' && value.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const [year, month, day] = value.split('-').map(Number);
                const date = new Date(year, month - 1, day);
                date.setHours(0, 0, 0, 0);
                return date;
            }
            const date = new Date(value);
            if (isNaN(date.getTime())) return null;
            date.setHours(0, 0, 0, 0);
            return date;
        }
        
        function toISO(date) {
            // Convert date to YYYY-MM-DD format using local timezone (not UTC)
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        // Use today as reference date instead of start of week, so today appears first
        const refDate = new Date(today);
        refDate.setHours(0, 0, 0, 0);
        
        const state = {
            referenceDate: refDate,
            selectedDate: new Date(today),
            bookings: [],
            pendingSelection: new Map(),
            hydrated: false
        };
        
        function syncFromCalendar(resetToToday = false) {
            const base = resetToToday ? new Date() : (window.calendarState?.referenceDate ? new Date(window.calendarState.referenceDate) : new Date());
            base.setHours(0, 0, 0, 0);
            state.selectedDate = base;
            state.referenceDate = startOfWeek(base);
        }
        
        function renderDates() {
            const container = modalBody?.querySelector('#assignModalDateColumns');
            const picker = modalBody?.querySelector('#assignModalDatePicker');
            if (!container) return;
            
            // Expose renderDates globally
            window.renderAssignRoomDates = renderDates;
            
            const dates = [];
            for (let i = 0; i < 7; i++) {
                const d = new Date(state.referenceDate);
                d.setDate(state.referenceDate.getDate() + i);
                d.setHours(0, 0, 0, 0); // Normalize to midnight local time
                dates.push(d);
            }
            
            // Normalize selected date for comparison
            const selectedDateNormalized = new Date(state.selectedDate);
            selectedDateNormalized.setHours(0, 0, 0, 0);
            const selectedIso = toISO(selectedDateNormalized);
            container.innerHTML = dates.map((d) => {
                const dayLabel = d.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase();
                const dateLabel = String(d.getDate()).padStart(2, '0');
                const monthLabel = d.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
                const active = toISO(d) === selectedIso ? 'selected' : '';
                return `
                    <div class="date-column">
                        <div class="date-cell ${active}" data-date="${toISO(d)}">
                            <div class="day">${dayLabel}</div>
                            <div class="date">${dateLabel}</div>
                            <div class="month">${monthLabel}</div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.querySelectorAll('.date-cell').forEach((cell) => {
                cell.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const dateStr = cell.dataset.date;
                    if (!dateStr) return;
                    
                    const dt = parseISO(dateStr);
                    if (!dt) return;
                    
                    // Update selected date (ensure it's a new Date object with local timezone)
                    state.selectedDate = new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
                    state.selectedDate.setHours(0, 0, 0, 0);
                    
                    // Update date picker value
                    if (picker) {
                        picker.value = toISO(state.selectedDate);
                    }
                    
                    // Re-render dates to update selected state
                    renderDates();
                    
                    // Fetch bookings for the selected date
                    fetchBookings();
                });
            });
            
            if (picker) {
                picker.value = selectedIso;
            }
        }
        
        function setStatus(msg, variant = 'info', busy = false) {
            const statusEl = modalBody?.querySelector('#assignModalStatus');
            if (!statusEl) return;
            statusEl.className = `alert alert-${variant} small mb-3`;
            statusEl.textContent = msg;
            modalBody?.querySelector('#accordionExample')?.setAttribute('aria-busy', busy ? 'true' : 'false');
        }
        
        async function fetchBookings() {
            if (!assignEndpoints.unassigned) return;
            const params = new URLSearchParams({ date: toISO(state.selectedDate) });
            if (hotelSelector?.value) params.append('hotel_id', hotelSelector.value);
            
            setStatus('Loading bookings...', 'info', true);
            state.pendingSelection.clear();
            
            try {
                const response = await fetch(`${assignEndpoints.unassigned}?${params.toString()}`);
                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to load bookings.');
                }
                state.bookings = Array.isArray(payload.bookings) ? payload.bookings : [];
                renderBookings();
            } catch (err) {
                console.error(err);
                setStatus(err.message || 'Unable to load bookings.', 'danger', false);
                const list = modalBody?.querySelector('#accordionExample');
                if (list) list.innerHTML = '';
            }
        }
        
        // Expose fetchBookings globally for external calls
        window.fetchAssignRoomBookings = fetchBookings;
        // Expose state for external access
        window.assignRoomModalState = state;
        
        function renderBookings() {
            const list = modalBody?.querySelector('#accordionExample');
            if (!list) return;
            
            list.innerHTML = '';
            if (!state.bookings.length) {
                setStatus('No unassigned bookings for the selected date.', 'warning', false);
                return;
            }
            
            setStatus(`${state.bookings.length} booking(s) need room assignment.`, 'success', false);
            
            // Group bookings by room type
            const bookingsByRoomType = {};
            state.bookings.forEach((booking) => {
                const roomType = booking.room_type || 'Unknown';
                if (!bookingsByRoomType[roomType]) {
                    bookingsByRoomType[roomType] = [];
                }
                bookingsByRoomType[roomType].push(booking);
            });
            
            // Create accordion items for each room type
            let accordionIndex = 0;
            Object.keys(bookingsByRoomType).forEach((roomType) => {
                const bookings = bookingsByRoomType[roomType];
                accordionIndex++;
                const headingId = `heading${accordionIndex}`;
                const collapseId = `collapse${accordionIndex}`;
                const isFirst = accordionIndex === 1;
                
                const accordionItem = document.createElement('div');
                accordionItem.className = 'accordion-item border-0';
                accordionItem.dataset.roomType = roomType;
                
                accordionItem.innerHTML = `
                    <h2 class="accordion-header ${accordionIndex === 2 ? 'mb-2' : ''}" id="${headingId}">
                        <button class="accordion-button ${isFirst ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="${collapseId}">
                            ${bookings.length} - ${roomType}
                        </button>
                    </h2>
                    <div id="${collapseId}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}" aria-labelledby="${headingId}" data-bs-parent="#accordionExample">
                        <div class="accordion-body border-0 p-0">
                            ${bookings.map((booking) => {
                                const selection = state.pendingSelection.get(booking.id);
                                const roomLabel = selection?.label || 'Not selected';
                                
                                return `
                                    <div class="p-3 py-2 rounded my-2 bg-pink" data-booking-id="${booking.id}">
                                        <div class="d-flex align-items-start justify-content-between">
                                            <div class="username d-flex flex-column">
                                                <h4>${booking.guest_name || 'Guest'}</h4>
                                                <p>${booking.code || ''}</p>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn p-0 border-0 bg-transparent" type="button" ${selection ? 'data-bs-toggle="dropdown"' : 'disabled'} aria-expanded="false" data-booking-menu="${booking.id}" style="${selection ? '' : 'opacity: 0.5; cursor: not-allowed;'}">
                                                    <img src="${assetBaseUrl}box-option.svg" alt="" style="pointer-events: none;">
                                                </button>
                                                ${selection ? 
                                                    `<ul class="dropdown-menu dropdown-menu-end" data-booking-dropdown="${booking.id}">
                                                        <li><a class="dropdown-item" href="#" data-edit-rooms="${booking.id}" style="font-size: 13px;">Edit Rooms</a></li>
                                                    </ul>` :
                                                    ''
                                                }
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-1">
                                            <div class="username d-flex flex-column w-50">
                                                <h3>Room Booked: <strong>${booking.rooms || 0}</strong></h3>
                                                <p>Total Guest: <strong>${booking.adults || 0}</strong></p>
                                                <p>Extra Metrress: <strong>${booking.children || 0}</strong></p>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column mt-2 align-items-start justify-content-start">
                                            <h3 class="mb-1">Room No.</h3>
                                            <div class="box d-flex px-2 py-1 justify-content-start flex-wrap align-items-center rounded border bg-white" data-room-box>
                                                ${selection && selection.labels ? 
                                                    selection.labels.map(label => `<p class="mb-0 px-1" data-room-display>${label}</p>`).join('') :
                                                    `<p class="mb-0 px-1" data-room-display>${roomLabel}</p>`
                                                }
                                            </div>
                                        </div>
                                        <div class="d-flex mt-2 align-items-center justify-content-between">
                                            <p class="mb-0"><img src="${assetBaseUrl}cal-ic.svg" width="13px" alt=""> &nbsp;
                                                <strong>${booking.stay_label || ''}</strong>
                                            </p>
                                            ${selection ? 
                                                `<button class="theme-btn text-decoration-none" type="button" data-assign-room style="min-width: 120px;">Assign Room</button>` :
                                                `<button class="theme-btn text-decoration-none outline" type="button" data-select-room style="min-width: 120px;">Select Room</button>`
                                            }
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
                
                list.appendChild(accordionItem);
                
                // Attach event listeners to buttons
                bookings.forEach((booking) => {
                    const bookingCard = accordionItem.querySelector(`[data-booking-id="${booking.id}"]`);
                    if (bookingCard) {
                        bookingCard.querySelector('[data-select-room]')?.addEventListener('click', () => openRoomSelector(booking));
                        bookingCard.querySelector('[data-assign-room]')?.addEventListener('click', () => triggerAssign(booking));
                        
                        // Handle Edit Rooms link in dropdown menu
                        const editRoomsLink = bookingCard.querySelector(`[data-edit-rooms="${booking.id}"]`);
                        if (editRoomsLink) {
                            editRoomsLink.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                // Close dropdown
                                const dropdown = bookingCard.querySelector(`[data-booking-dropdown="${booking.id}"]`);
                                const dropdownInstance = dropdown ? window.bootstrap?.Dropdown?.getInstance(dropdown.closest('.dropdown')) : null;
                                if (dropdownInstance) {
                                    dropdownInstance.hide();
                                }
                                // Open room selector
                                openRoomSelector(booking);
                            });
                        }
                    }
                });
            });
        }
        
        async function openRoomSelector(booking) {
            const modal = document.getElementById('assignRoomSelectModal');
            if (!modal) {
                swal('Error', 'Room selection modal not found.', 'error');
                return;
            }
            
            const modalBodyEl = modal.querySelector('#assignRoomSelectBody');
            if (!modalBodyEl) {
                swal('Error', 'Modal body not found.', 'error');
                return;
            }
            
            modalBodyEl.innerHTML = '<p class="text-muted small mb-0">Loading available rooms...</p>';
            
            if (!assignRoomSelectModalInstance && window.bootstrap && window.bootstrap.Modal) {
                assignRoomSelectModalInstance = new window.bootstrap.Modal(modal);
            }
            if (assignRoomSelectModalInstance) {
                assignRoomSelectModalInstance.show();
            }
            
            const params = new URLSearchParams({
                booking_id: booking.id,
                date: toISO(state.selectedDate)
            });
            
            try {
                const response = await fetch(`${assignEndpoints.available}?${params.toString()}`);
                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'No rooms available.');
                }
                
                const rooms = Array.isArray(payload.rooms) ? payload.rooms : [];
                if (!rooms.length) {
                    modalBodyEl.innerHTML = '<p class="text-muted small mb-0">No available rooms for the selected date.</p>';
                    return;
                }
                
                // Get current selection for this booking
                const currentSelection = state.pendingSelection.get(booking.id);
                const selectedRoomIds = currentSelection ? (Array.isArray(currentSelection.roomIds) ? currentSelection.roomIds : [currentSelection.roomId]) : [];
                
                // Get the number of rooms required for this booking
                const requiredRooms = parseInt(booking.rooms) || 1;
                
                const container = document.createElement('div');
                container.innerHTML = `
                    <div class="mb-3">
                        <p class="small text-muted mb-2">Select exactly <strong>${requiredRooms}</strong> room(s) (Booking requires ${requiredRooms} room${requiredRooms > 1 ? 's' : ''}):</p>
                        <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                            ${rooms.map((room) => {
                                const isChecked = selectedRoomIds.includes(room.id);
                                return `
                                    <label class="list-group-item d-flex align-items-center" style="cursor: pointer;">
                                        <input type="checkbox" class="form-check-input me-2 room-checkbox" value="${room.id}" data-room-label="${room.label}" ${isChecked ? 'checked' : ''}>
                                        ${room.label}
                                    </label>
                                `;
                            }).join('')}
                        </div>
                        <p class="small text-info mt-2 mb-0" id="room-selection-status">
                            Selected: <strong>${selectedRoomIds.length}</strong> / ${requiredRooms}
                        </p>
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-secondary btn-sm" data-cancel-selection>Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm" data-save-selection>Save Selection</button>
                    </div>
                `;
                
                modalBodyEl.innerHTML = '';
                modalBodyEl.appendChild(container);
                
                // Function to update checkbox states based on selection count
                const updateCheckboxStates = () => {
                    const checkedBoxes = container.querySelectorAll('input.room-checkbox:checked');
                    const checkedCount = checkedBoxes.length;
                    const statusEl = container.querySelector('#room-selection-status');
                    
                    if (statusEl) {
                        statusEl.innerHTML = `Selected: <strong>${checkedCount}</strong> / ${requiredRooms}`;
                        if (checkedCount === requiredRooms) {
                            statusEl.className = 'small text-success mt-2 mb-0';
                        } else {
                            statusEl.className = 'small text-info mt-2 mb-0';
                        }
                    }
                    
                    // Enable/disable checkboxes based on limit
                    container.querySelectorAll('input.room-checkbox').forEach(checkbox => {
                        if (!checkbox.checked && checkedCount >= requiredRooms) {
                            checkbox.disabled = true;
                            checkbox.closest('label').style.opacity = '0.5';
                            checkbox.closest('label').style.cursor = 'not-allowed';
                        } else {
                            checkbox.disabled = false;
                            checkbox.closest('label').style.opacity = '1';
                            checkbox.closest('label').style.cursor = 'pointer';
                        }
                    });
                };
                
                // Add change event listeners to checkboxes
                container.querySelectorAll('input.room-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        updateCheckboxStates();
                    });
                });
                
                // Initialize checkbox states
                updateCheckboxStates();
                
                // Handle cancel
                container.querySelector('[data-cancel-selection]')?.addEventListener('click', () => {
                    if (assignRoomSelectModalInstance) {
                        assignRoomSelectModalInstance.hide();
                    }
                });
                
                // Handle save
                container.querySelector('[data-save-selection]')?.addEventListener('click', () => {
                    const checkedBoxes = container.querySelectorAll('input.room-checkbox:checked');
                    const selectedRooms = Array.from(checkedBoxes).map(cb => ({
                        id: parseInt(cb.value),
                        label: cb.dataset.roomLabel
                    }));
                    
                    if (selectedRooms.length === 0) {
                        swal('Please select at least one room.', '', 'warning');
                        return;
                    }
                    
                    if (selectedRooms.length !== requiredRooms) {
                        swal(`Please select exactly ${requiredRooms} room(s). You have selected ${selectedRooms.length} room(s).`, '', 'warning');
                        return;
                    }
                    
                    // Store as array of room objects
                    state.pendingSelection.set(booking.id, {
                        roomIds: selectedRooms.map(r => r.id),
                        labels: selectedRooms.map(r => r.label),
                        roomId: selectedRooms[0].id, // Keep for backward compatibility
                        label: selectedRooms.map(r => r.label).join(', ') // Display label
                    });
                    
                    updateBookingSelection(booking.id);
                    
                    if (assignRoomSelectModalInstance) {
                        assignRoomSelectModalInstance.hide();
                    }
                });
            } catch (err) {
                console.error(err);
                modalBodyEl.innerHTML = `<p class="text-danger small mb-0">${err.message || 'Unable to load rooms.'}</p>`;
            }
        }
        
        function updateBookingSelection(bookingId) {
            const card = modalBody?.querySelector(`[data-booking-id="${bookingId}"]`);
            if (!card) return;
            
            const selection = state.pendingSelection.get(bookingId);
            const roomBox = card.querySelector('[data-room-box]');
            const buttonContainer = card.querySelector('.d-flex.mt-2.align-items-center.justify-content-between');
            
            if (!buttonContainer) return;
            
            // Update room display - show multiple rooms as separate tags
            if (roomBox) {
                if (selection && selection.labels && selection.labels.length > 0) {
                    roomBox.innerHTML = selection.labels.map(label => 
                        `<p class="mb-0 px-1" data-room-display>${label}</p>`
                    ).join('');
                } else {
                    roomBox.innerHTML = '<p class="mb-0 px-1" data-room-display>Not selected</p>';
                }
            }
            
            // Find the booking object to re-attach event listeners
            const booking = state.bookings.find(b => b.id === bookingId);
            if (!booking) return;
            
            // Replace button based on selection
            const existingBtn = buttonContainer.querySelector('[data-select-room], [data-assign-room]');
            if (existingBtn) {
                if (selection) {
                    // Room selected - show Assign Room button
                    const assignBtn = document.createElement('button');
                    assignBtn.className = 'theme-btn text-decoration-none';
                    assignBtn.type = 'button';
                    assignBtn.setAttribute('data-assign-room', '');
                    assignBtn.style.minWidth = '120px';
                    assignBtn.textContent = 'Assign Room';
                    assignBtn.addEventListener('click', () => triggerAssign(booking));
                    existingBtn.replaceWith(assignBtn);
                } else {
                    // No room selected - show Select Room button
                    const selectBtn = document.createElement('button');
                    selectBtn.className = 'theme-btn text-decoration-none outline';
                    selectBtn.type = 'button';
                    selectBtn.setAttribute('data-select-room', '');
                    selectBtn.style.minWidth = '120px';
                    selectBtn.textContent = 'Select Room';
                    selectBtn.addEventListener('click', () => openRoomSelector(booking));
                    existingBtn.replaceWith(selectBtn);
                }
            }
            
            // Update dropdown button and menu
            const dropdownBtn = card.querySelector(`[data-booking-menu="${bookingId}"]`);
            const dropdownContainer = card.querySelector('.dropdown');
            let dropdownMenu = card.querySelector(`[data-booking-dropdown="${bookingId}"]`);
            
            if (selection) {
                // Rooms selected - enable dropdown button
                if (dropdownBtn) {
                    dropdownBtn.setAttribute('data-bs-toggle', 'dropdown');
                    dropdownBtn.removeAttribute('disabled');
                    dropdownBtn.style.opacity = '1';
                    dropdownBtn.style.cursor = 'pointer';
                }
                
                // Create dropdown menu if it doesn't exist
                if (!dropdownMenu && dropdownContainer) {
                    dropdownMenu = document.createElement('ul');
                    dropdownMenu.className = 'dropdown-menu dropdown-menu-end';
                    dropdownMenu.setAttribute('data-booking-dropdown', bookingId);
                    dropdownContainer.appendChild(dropdownMenu);
                }
                
                // Add Edit Rooms link if not exists
                if (dropdownMenu) {
                    const existingEditLink = dropdownMenu.querySelector(`[data-edit-rooms="${bookingId}"]`);
                    if (!existingEditLink) {
                        const editItem = document.createElement('li');
                        const editLink = document.createElement('a');
                        editLink.className = 'dropdown-item';
                        editLink.href = '#';
                        editLink.setAttribute('data-edit-rooms', bookingId);
                        editLink.style.fontSize = '13px';
                        editLink.textContent = 'Edit Rooms';
                        editItem.appendChild(editLink);
                        dropdownMenu.appendChild(editItem);
                        
                        // Attach event listener
                        editLink.addEventListener('click', (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            // Close dropdown
                            const dropdown = card.querySelector('.dropdown');
                            const dropdownInstance = dropdown ? window.bootstrap?.Dropdown?.getInstance(dropdown) : null;
                            if (dropdownInstance) {
                                dropdownInstance.hide();
                            }
                            // Open room selector
                            openRoomSelector(booking);
                        });
                    }
                }
            } else {
                // No rooms selected - disable dropdown button and remove menu
                if (dropdownBtn) {
                    dropdownBtn.removeAttribute('data-bs-toggle');
                    dropdownBtn.setAttribute('disabled', 'disabled');
                    dropdownBtn.style.opacity = '0.5';
                    dropdownBtn.style.cursor = 'not-allowed';
                }
                
                // Remove dropdown menu
                if (dropdownMenu) {
                    dropdownMenu.remove();
                }
            }
        }
        
        function triggerAssign(booking) {
            const selection = state.pendingSelection.get(booking.id);
            if (!selection) {
                swal('Select a room first.', '', 'warning');
                return;
            }
            
            const roomLabels = selection.labels ? selection.labels.join(', ') : selection.label;
            const roomCount = selection.roomIds ? selection.roomIds.length : 1;
            const roomText = roomCount > 1 ? `Room Numbers ${roomLabels}` : `Room Number ${roomLabels}`;
            
            swal({
                title: 'Confirm assignment',
                text: `Are you sure you want to assign ${roomText}?`,
                icon: 'warning',
                buttons: ['Cancel', 'Assign']
            }).then((willAssign) => {
                if (willAssign) {
                    assignRoom(booking, selection);
                }
            });
        }
        
        async function assignRoom(booking, selection) {
            const formData = new FormData();
            formData.append('booking_id', booking.id);
            
            // Send comma-separated room IDs
            const roomIds = selection.roomIds ? selection.roomIds : [selection.roomId];
            formData.append('room_number_ids', roomIds.join(','));
            
            if (csrfToken) {
                formData.append('_token', csrfToken);
            }
            
            try {
                const response = await fetch(assignEndpoints.assign, {
                    method: 'POST',
                    headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
                    body: formData
                });
                
                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Unable to assign room.');
                }
                
                state.pendingSelection.delete(booking.id);
                state.bookings = state.bookings.filter((b) => b.id !== booking.id);
                renderBookings();
                swal('Success!', 'Room assigned successfully.', 'success');
                
                if (typeof window.fetchCalendarDataForCurrentView === 'function') {
                    window.fetchCalendarDataForCurrentView();
                }
            } catch (err) {
                console.error(err);
                swal('Error', err.message || 'Unable to assign room. Please try again.', 'error');
            }
        }
        
        function shiftRange(dir) {
            state.referenceDate.setDate(state.referenceDate.getDate() + (dir * 5));
            state.selectedDate = new Date(state.referenceDate);
            renderDates();
            fetchBookings();
        }
        
        // Initialize modal on show
        assignModalEl.addEventListener('shown.bs.modal', () => {
            // Always reset to today when modal opens
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            state.selectedDate = new Date(today);
            state.referenceDate = new Date(today);
            
            // Update date picker
            const picker = modalBody.querySelector('#assignModalDatePicker');
            if (picker) {
                picker.value = toISO(today);
            }
            
            if (!state.hydrated) {
                // Set up event listeners
                modalBody.querySelector('#assignModalPrevBtn')?.addEventListener('click', () => shiftRange(-1));
                modalBody.querySelector('#assignModalNextBtn')?.addEventListener('click', () => shiftRange(1));
                modalBody.querySelector('#assignModalTodayBtn')?.addEventListener('click', () => {
                    syncFromCalendar(true);
                    renderDates();
                    fetchBookings();
                });
                
                const picker = modalBody.querySelector('#assignModalDatePicker');
                const pickerBtn = modalBody.querySelector('#assignModalDatePickerBtn');
                picker?.addEventListener('change', (e) => {
                    const dateStr = e.target.value;
                    if (!dateStr) return;
                    
                    const dt = parseISO(dateStr);
                    if (!dt) return;
                    
                    // Update selected date (ensure it's a new Date object with local timezone)
                    state.selectedDate = new Date(dt.getFullYear(), dt.getMonth(), dt.getDate());
                    state.selectedDate.setHours(0, 0, 0, 0);
                    
                    // Update reference date to start of week for the selected date
                    state.referenceDate = startOfWeek(state.selectedDate);
                    
                    renderDates();
                    fetchBookings();
                });
                // Handle button click - trigger date picker
                pickerBtn?.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (picker && typeof picker.showPicker === 'function') {
                        picker.showPicker();
                    } else {
                        // Fallback: trigger click on the input
                        picker?.click();
                    }
                });
                
                // Also handle clicks on the image directly
                const calendarImg = pickerBtn?.querySelector('img');
                if (calendarImg) {
                    calendarImg.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        if (picker && typeof picker.showPicker === 'function') {
                            picker.showPicker();
                        } else {
                            picker?.click();
                        }
                    });
                }
                
                state.hydrated = true;
            }
            
            // Don't sync from calendar - always show today first
            // syncFromCalendar(false); // Removed - always show current week starting from today
            renderDates();
            fetchBookings();
        });
        
        // Handle hotel selector change
        if (hotelSelector) {
            hotelSelector.addEventListener('change', () => {
                if (state.hydrated) {
                    fetchBookings();
                }
            });
        }

        // Filter toggle functionality - same as HTML
        const calendarFilter = document.querySelector('.filter');
        if (calendarFilter) {
            calendarFilter.addEventListener('click', function() {
                this.classList.toggle('active');
                
                // Toggle room-label-header visibility
                const roomLabelHeaders = document.querySelectorAll('.room-label-header');
                roomLabelHeaders.forEach(header => {
                    if (header.style.display === 'none') {
                        header.style.display = '';
                    } else {
                        header.style.display = 'none';
                    }
                });
            });
        }
    })();
});
</script>
@endsection