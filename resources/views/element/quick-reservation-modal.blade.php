@php
$siteUrl = env('APP_URL');
@endphp
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

