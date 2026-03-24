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
                                                        <option value="Cash">Cash</option>
                                                        <option value="Card">Card</option>
                                                        <option value="UPI">UPI</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Cheque">Cheque</option>
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
                                 <!-- Check-in / Checkout Buttons -->
                                    <div id="checkinCheckoutSection">
                                        <button type="button" id="bookingCheckInBtn" onclick="checkInBooking()"
                                            class="theme-btn-sm rounded text-white border-0">Check-in</button>

                                        <button type="button" id="bookingCheckoutBtn" onclick="checkoutBooking()"
                                            class="theme-btn-sm rounded text-white border-0" style="display:none;">Checkout</button>
                                    </div>

                                    <!-- Save Button -->
                                    <div id="saveChangesSection" style="display:none;">
                                        <button type="button" id="bookingSaveBtn" onclick="saveBookingChargesPayments()"
                                            class="theme-btn-sm rounded text-white border-0">Save Changes</button>
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