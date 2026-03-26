@php
$siteUrl = env('APP_URL');
@endphp

@extends('layout.default')

@section('content')
    @include('account.header')

    <style>
   .nav-link {
    font-size: 12px;
    color: #000;
    width: 150px;
}     
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #FFF;
    background-color: #000 !important;
    border-color: var(--bs-nav-tabs-link-active-border-color);
}

        </style>

    <div class="wrapper">
        <div class="d-flex align-items-start justify-content-between report-page">
            <div class="content-report setting mx-sm-4">
                <!-- Tab Navigation -->
                <div class="bg-white shadow-sm pe-3">
                    <nav>
                        <div class="nav nav-tabs" id="settingsTab" role="tablist">
                            <button class="nav-link active" id="priceDetail-tab" data-bs-toggle="tab"
                                data-bs-target="#priceDetail" type="button" role="tab" aria-controls="priceDetail"
                                aria-selected="true">Price Detail</button>
                            <button class="nav-link" id="setOrdering-tab" data-bs-toggle="tab"
                                data-bs-target="#setOrdering" type="button" role="tab" aria-controls="setOrdering"
                                aria-selected="false">Set Ordering</button>
                            <button class="nav-link" id="bookingSettings-tab" data-bs-toggle="tab"
                                data-bs-target="#bookingSettings" type="button" role="tab" aria-controls="bookingSettings"
                                aria-selected="false">Booking Settings</button>
                        </div>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="tab-content" id="settingsTabContent">
                    <!-- Price Detail Tab -->
                    <div class="tab-pane fade active show" id="priceDetail" role="tabpanel" aria-labelledby="priceDetail-tab">
                        <div class="top-report p-4">
                            <h5 class="mb-4">Price Detail</h5>
                            <form class="bl-reserve-form pe-0" id="priceSettingsForm">
                                @csrf
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-3 mb-2">
                                        <label for="hotel_id" class="form-label mt-0">Property Name</label>
                                        <select name="hotel_id" id="hotel_id" class="form-control" required>
                                            <option value="">Select Property</option>
                                            @foreach($hotels as $hotel)
                                                <option value="{{ $hotel->id }}">{{ $hotel->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label for="room_id" class="form-label">Rooms Type</label>
                                        <select name="room_id" id="room_id" class="form-control" required>
                                            <option value="">Select Room Type</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 mb-2 ms-auto d-flex flex-column">
                                        <label for="advance_booking_days" class="form-label">Advance Booking</label>
                                        <div class="d-flex align-items-center gap-2 justify-content-end">
                                            <input type="number" name="advance_booking_days" id="advance_booking_days" class="form-control w-50" placeholder="3 days" min="0">
                                            <a class="theme-btn rounded text-decoration-none" style="padding: 6px 20px; cursor: pointer;" onclick="applyAdvanceBooking()">Apply</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-4 col-lg-3 my-3 d-flex align-items-center justify-content-between">
                                        <p>Apply Dynamic Price</p>
                                        <button type="button" class="btn btn-sm btn-secondary btn-toggle" id="dynamicPriceToggle" data-toggle="button"
                                            aria-pressed="false" autocomplete="off">
                                            <div class="handle"></div>
                                        </button>
                                        <input type="hidden" name="apply_dynamic_price" id="apply_dynamic_price" value="0">
                                    </div>
                                    <div class="col-md-5 bl my-3 d-flex align-items-center gap-2">
                                        <p>Common Price</p>
                                        <input type="text" name="common_price" id="common_price" class="form-control w-25" placeholder="1500">
                                        <a class="theme-btn rounded text-decoration-none" style="padding: 6px 20px; cursor: pointer;" onclick="applyCommonPrice()">Apply</a>
                                    </div>
                                    <div class="col-md-3 ms-auto mb-2">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <input type="number" name="gst_percentage" id="gst_percentage" class="form-control w-50" placeholder="GST(%)" step="0.01" min="0" max="100">
                                            <a class="theme-btn rounded text-decoration-none" style="padding: 6px 20px; cursor: pointer;" onclick="applyGST()">Apply</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center blss p-3 my-4 mx-1 rounded shadow-sm bg-light">
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="january" class="form-label">January</label>
                                        <div class="time">
                                            <input type="number" name="january" id="january" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="february" class="form-label">February</label>
                                        <div class="time">
                                            <input type="number" name="february" id="february" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="march" class="form-label">March</label>
                                        <div class="time">
                                            <input type="number" name="march" id="march" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="april" class="form-label">April</label>
                                        <div class="time">
                                            <input type="number" name="april" id="april" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="may" class="form-label">May</label>
                                        <div class="time">
                                            <input type="number" name="may" id="may" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="june" class="form-label">June</label>
                                        <div class="time">
                                            <input type="number" name="june" id="june" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="july" class="form-label">July</label>
                                        <div class="time">
                                            <input type="number" name="july" id="july" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="august" class="form-label">August</label>
                                        <div class="time">
                                            <input type="number" name="august" id="august" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="september" class="form-label">September</label>
                                        <div class="time">
                                            <input type="number" name="september" id="september" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="october" class="form-label">October</label>
                                        <div class="time">
                                            <input type="number" name="october" id="october" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="november" class="form-label">November</label>
                                        <div class="time">
                                            <input type="number" name="november" id="november" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 my-2">
                                        <label for="december" class="form-label">December</label>
                                        <div class="time">
                                            <input type="number" name="december" id="december" class="form-control month disable" value="" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-12 my-4">
                                        <div class="head-menu ms-md-auto">
                                            <ul class="d-flex align-items-center justify-content-end list-style-none">
                                                <li><a class="rounded" onclick="resetForm()" style="cursor: pointer;">Cancel</a></li>
                                                <li><a class="theme-btn text-white rounded" id="saveSettingsBtn" onclick="saveSettings()" style="cursor: pointer;">
                                                    <span class="label-default">Save</span>
                                                    <span class="label-loading d-none">
                                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                        Saving...
                                                    </span>
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Set Ordering Tab -->
                    <div class="tab-pane fade" id="setOrdering" role="tabpanel" aria-labelledby="setOrdering-tab">
                        <div class="top-report p-4 mt-4 mb-5 position-relative" id="orderingSection">
                            <h5 class="mb-4">Set Ordering</h5>

                            <div class="row align-items-end mb-3">
                                <div class="col-md-3 mb-2">
                                    <label for="ordering_type" class="form-label mt-0">Select Ordering Type</label>
                                    <select id="ordering_type" class="form-control form-select">
                                        <option value="">Select Type</option>
                                        <option value="hotel">Update Hotel</option>
                                        <option value="room">Update Room</option>
                                        <option value="room_number">Update Room Number</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-2 d-none" id="orderingHotelWrap">
                                    <label for="ordering_hotel_id" class="form-label mt-0">Property Name</label>
                                    <select id="ordering_hotel_id" class="form-control form-select">
                                        <option value="">Select Property</option>
                                        @foreach($hotels as $hotel)
                                            <option value="{{ $hotel->id }}">{{ $hotel->title }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-2 d-none" id="orderingRoomWrap">
                                    <label for="ordering_room_id" class="form-label mt-0">Rooms Type</label>
                                    <select id="ordering_room_id" class="form-control form-select" disabled>
                                        <option value="">Select Room Type</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border rounded bg-light p-3 position-relative">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="text-muted small" id="orderingHint">Select a type to load the list.</div>
                                    <div class="text-muted small" id="orderingMeta"></div>
                                </div>

                                <div id="orderingEmptyState" class="text-center text-muted py-4 d-none">
                                    No data available.
                                </div>

                                <ul id="orderingList" class="list-group d-none"></ul>

                                <div id="orderingPager" class="d-flex align-items-center justify-content-between mt-3 d-none">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="orderingPrevBtn">Prev</button>
                                    <div class="small text-muted" id="orderingPageText"></div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="orderingNextBtn">Next</button>
                                </div>

                                <div id="orderingOverlay" class="d-none"
                                    style="position:absolute; inset:0; background: rgba(0,0,0,0.25); display:flex; align-items:center; justify-content:center; border-radius: 0.375rem; z-index: 10;">
                                    <div class="bg-white rounded px-3 py-2 shadow-sm d-flex align-items-center gap-2">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        <span class="small">Please wait...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Settings Tab -->
                    <div class="tab-pane fade" id="bookingSettings" role="tabpanel" aria-labelledby="bookingSettings-tab">
                        <div class="top-report p-4 mt-4 mb-5">
                            <h5 class="mb-4">Booking Settings</h5>
                            <form class="booking-settings-form" id="bookingSettingsForm">
                                @csrf
                                <div class="row align-items-end mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label for="room_assignment_type" class="form-label mt-0">Room Assignment Type</label>
                                        <select name="room_assignment_type" id="room_assignment_type" class="form-control form-select" required>
                                            <option value="">Select Assignment Type</option>
                                            <option value="assign_manually">Assign Manually</option>
                                            <option value="assign_auto">Assign Auto</option>
                                             <option value="all">Accept Both</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="head-menu ms-md-auto">
                                            <ul class="d-flex align-items-center justify-content-end list-style-none">
                                                <li><a class="rounded" onclick="resetBookingSettings()" style="cursor: pointer;">Cancel</a></li>
                                                <li><a class="theme-btn text-white rounded" id="saveBookingSettingsBtn" onclick="saveBookingSettings()" style="cursor: pointer;">
                                                    <span class="label-default">Save</span>
                                                    <span class="label-loading d-none">
                                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                        Saving...
                                                    </span>
                                                </a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customprice" tabindex="-1" aria-labelledby="custompriceLabel" aria-hidden="true">
        <div class="modal-dialog default">
            <div class="modal-content">
                <button type="button" class="btn-close left-close-button bg-white" data-bs-dismiss="modal"
                    aria-label="Close"><img src="{{$siteUrl}}public/assets/img/close.svg" class="bg-white rounded-circle" alt=""></button>
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="custompriceLabel">Apply Equal Price</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="equalPriceInput" placeholder="Enter price">
                </div>
                <div class="modal-footer border-0">
                    <a href="" class="theme-btn text-decoration-none" onclick="applyEqualPrice()" style="cursor: pointer;">Apply All</a>
                </div>
            </div>
        </div>
    </div>

    @include('element.footer')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.querySelectorAll('.btn-toggle').forEach(ele => ele.addEventListener('click', () => {
            if (ele.classList.contains("active")) {
                ele.classList.remove("active");
                document.getElementById('apply_dynamic_price').value = '0';
            } else {
                ele.classList.add("active");
                document.getElementById('apply_dynamic_price').value = '1';
            }
        }));

        $(document).ready(function () {
            $(".btn-toggle").click(function () {
                $("input.month").toggleClass("disable");
                $(".bl").toggleClass("op");
            });
            $(".profile").click(function () {
                $(".profile-menus").toggleClass("show");
            });

            // Dynamically load room types based on selected hotel
            $('#hotel_id').on('change', function() {
                const selectedHotelId = $(this).val();
                const roomSelect = $('#room_id');
                
                // Clear existing options
                roomSelect.html('<option value="">Select Room Type</option>');
                
                if (selectedHotelId) {
                    // Show loading state
                    roomSelect.prop('disabled', true);
                    
                    // Fetch room types for selected hotel
                    $.ajax({
                        url: '{{ route("settings.roomTypes") }}',
                        type: 'GET',
                        data: {
                            hotel_id: selectedHotelId
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                // Populate room types
                                response.data.forEach(function(room) {
                                    roomSelect.append(
                                        $('<option></option>')
                                            .attr('value', room.id)
                                            .text(room.title)
                                    );
                                });
                            }
                            roomSelect.prop('disabled', false);
                        },
                        error: function() {
                            if (typeof swal === 'function') {
                                swal("Error!", 'Error loading room types', "error");
                            }
                            roomSelect.prop('disabled', false);
                        }
                    });
                } else {
                    roomSelect.prop('disabled', false);
                }
                
                // Reset room selection and clear form
                $('#room_id').val('');
                resetFormFields();
            });

            // Load settings when room type is selected
            $('#room_id').on('change', function() {
                const hotelId = $('#hotel_id').val();
                const roomId = $(this).val();
                if (hotelId && roomId) {
                    // Load existing settings for this hotel and room combination
                    loadSettings(hotelId, roomId);
                } else {
                    // Reset form if no hotel or room selected
                    resetFormFields();
                }
            });
        });

        // -------- Set Ordering (drag & drop) --------
        const Ordering = {
            type: '',
            hotelId: '',
            roomId: '',
            page: 1,
            perPage: 50,
            lastPage: 1,
            isBusy: false,
            sortable: null,
        };

        function orderingSetBusy(isBusy) {
            Ordering.isBusy = isBusy;
            $('#orderingOverlay').toggleClass('d-none', !isBusy);
            $('#orderingSection select, #orderingPrevBtn, #orderingNextBtn').prop('disabled', isBusy);
            $('#orderingList').css('pointer-events', isBusy ? 'none' : 'auto');
        }

        function orderingAjax(opts) {
            const token = $('meta[name="csrf-token"]').attr('content');
            return $.ajax({
                ...opts,
                headers: {
                    ...(opts.headers || {}),
                    'X-CSRF-TOKEN': token,
                }
            });
        }

        function orderingResetUI(message) {
            $('#orderingHint').text(message || 'Select a type to load the list.');
            $('#orderingMeta').text('');
            $('#orderingEmptyState').addClass('d-none');
            $('#orderingList').addClass('d-none').empty();
            $('#orderingPager').addClass('d-none');
        }

        function orderingRenderList(items) {
            const list = $('#orderingList');
            list.empty();

            if (!items || items.length === 0) {
                $('#orderingEmptyState').removeClass('d-none');
                $('#orderingList').addClass('d-none');
                return;
            }

            $('#orderingEmptyState').addClass('d-none');
            $('#orderingList').removeClass('d-none');

            items.forEach((it) => {
                const title = it.title || it.full_title || it.name || '';
                const li = $(`
                    <li class="list-group-item d-flex align-items-center justify-content-between" data-id="${it.id}">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted" style="cursor: grab;">☰</span>
                            <span class="fw-medium">${$('<div/>').text(title).html()}</span>
                        </div>
                        <span class="badge bg-secondary">#${it.ordering ?? ''}</span>
                    </li>
                `);
                list.append(li);
            });

            if (Ordering.sortable) {
                Ordering.sortable.destroy();
                Ordering.sortable = null;
            }

            Ordering.sortable = new Sortable(document.getElementById('orderingList'), {
                animation: 150,
                handle: '.text-muted',
                ghostClass: 'bg-warning-subtle',
                onEnd: function() {
                    orderingPersistOrder();
                }
            });
        }

        function orderingUpdatePager(meta) {
            const hasPager = meta && meta.last_page && meta.last_page > 1;
            Ordering.lastPage = meta?.last_page || 1;
            $('#orderingPager').toggleClass('d-none', !hasPager);
            $('#orderingPrevBtn').prop('disabled', Ordering.page <= 1 || Ordering.isBusy);
            $('#orderingNextBtn').prop('disabled', Ordering.page >= Ordering.lastPage || Ordering.isBusy);
            $('#orderingPageText').text(`Page ${Ordering.page} of ${Ordering.lastPage}`);
            if (meta?.total != null) {
                $('#orderingMeta').text(`Total: ${meta.total}`);
            }
        }

        function orderingFetchList() {
            if (!Ordering.type) {
                orderingResetUI('Select a type to load the list.');
                return;
            }

            let url = '';
            const data = { page: Ordering.page, per_page: Ordering.perPage };

            if (Ordering.type === 'hotel') {
                url = '{{ route("settings.ordering.hotels") }}';
                $('#orderingHint').text('Drag & drop to reorder hotels.');
            } else if (Ordering.type === 'room') {
                if (!Ordering.hotelId) {
                    orderingResetUI('Select a property to load rooms.');
                    return;
                }
                url = '{{ route("settings.ordering.rooms") }}';
                data.hotel_id = Ordering.hotelId;
                $('#orderingHint').text('Drag & drop to reorder rooms for the selected property.');
            } else if (Ordering.type === 'room_number') {
                if (!Ordering.hotelId || !Ordering.roomId) {
                    orderingResetUI('Select a property and room type to load room numbers.');
                    return;
                }
                url = '{{ route("settings.ordering.roomNumbers") }}';
                data.hotel_id = Ordering.hotelId;
                data.room_id = Ordering.roomId;
                $('#orderingHint').text('Drag & drop to reorder room numbers for the selected room type.');
            }

            orderingSetBusy(true);
            orderingAjax({
                url,
                type: 'GET',
                data,
            }).done(function(res) {
                if (!res?.success) {
                    orderingResetUI(res?.message || 'Failed to load data.');
                    return;
                }
                orderingRenderList(res.data || []);
                orderingUpdatePager(res.meta || {});
            }).fail(function() {
                orderingResetUI('Failed to load data.');
            }).always(function() {
                orderingSetBusy(false);
            });
        }

        function orderingLoadRoomsForHotel(hotelId) {
            $('#ordering_room_id').html('<option value="">Select Room Type</option>').prop('disabled', true);
            Ordering.roomId = '';
            if (!hotelId) {
                return;
            }

            orderingSetBusy(true);
            orderingAjax({
                url: '{{ route("settings.ordering.rooms") }}',
                type: 'GET',
                data: { hotel_id: hotelId, per_page: 200, page: 1 },
            }).done(function(res) {
                if (res?.success && Array.isArray(res.data)) {
                    res.data.forEach(function(room) {
                        $('#ordering_room_id').append(
                            $('<option></option>').attr('value', room.id).text(room.title)
                        );
                    });
                }
            }).always(function() {
                $('#ordering_room_id').prop('disabled', false);
                orderingSetBusy(false);
            });
        }

        function orderingPersistOrder() {
            if (Ordering.isBusy) return;
            const ids = [];
            $('#orderingList').children('li').each(function() {
                const id = parseInt($(this).attr('data-id'), 10);
                if (!isNaN(id)) ids.push(id);
            });
            if (ids.length === 0) return;

            orderingSetBusy(true);
            orderingAjax({
                url: '{{ route("settings.ordering.update") }}',
                type: 'POST',
                data: {
                    type: Ordering.type,
                    ordered_ids: ids,
                    hotel_id: Ordering.hotelId || null,
                    room_id: Ordering.roomId || null,
                }
            }).done(function(res) {
                if (!res?.success) {
                    if (typeof swal === 'function') {
                        swal("Error!", res?.message || 'Failed to update ordering', "error");
                    }
                    orderingFetchList();
                    return;
                }
                // Refresh the list to show updated order numbers, keep filters.
                orderingFetchList();
            }).fail(function() {
                if (typeof swal === 'function') {
                    swal("Error!", 'Failed to update ordering', "error");
                }
                orderingFetchList();
            }).always(function() {
                orderingSetBusy(false);
            });
        }

        $(document).ready(function() {
            orderingResetUI();

            $('#ordering_type').on('change', function() {
                Ordering.type = $(this).val();
                Ordering.page = 1;
                $('#ordering_hotel_id,#ordering_room_id').val('');
                // Show/hide dependent filters
                if (Ordering.type === 'hotel') {
                    $('#orderingHotelWrap').addClass('d-none');
                    $('#orderingRoomWrap').addClass('d-none');
                    $('#ordering_room_id').val('');
                    Ordering.hotelId = '';
                    Ordering.roomId = '';
                    orderingFetchList();
                } else if (Ordering.type === 'room') {
                    $('#orderingHotelWrap').removeClass('d-none');
                    $('#orderingRoomWrap').addClass('d-none');
                    Ordering.roomId = '';
                    orderingResetUI('Select a property to load rooms.');
                } else if (Ordering.type === 'room_number') {
                    $('#orderingHotelWrap').removeClass('d-none');
                    $('#orderingRoomWrap').removeClass('d-none');
                    orderingResetUI('Select a property and room type to load room numbers.');
                } else {
                    $('#orderingHotelWrap').addClass('d-none');
                    $('#orderingRoomWrap').addClass('d-none');
                    Ordering.hotelId = '';
                    Ordering.roomId = '';
                    orderingResetUI();
                }
            });

            $('#ordering_hotel_id').on('change', function() {
                Ordering.hotelId = $(this).val();
                Ordering.page = 1;

                if (Ordering.type === 'room') {
                    orderingFetchList();
                } else if (Ordering.type === 'room_number') {
                    orderingLoadRoomsForHotel(Ordering.hotelId);
                    orderingResetUI('Select a room type to load room numbers.');
                }
            });

            $('#ordering_room_id').on('change', function() {
                Ordering.roomId = $(this).val();
                Ordering.page = 1;
                if (Ordering.type === 'room_number') {
                    orderingFetchList();
                }
            });

            $('#orderingPrevBtn').on('click', function() {
                if (Ordering.page <= 1 || Ordering.isBusy) return;
                Ordering.page -= 1;
                orderingFetchList();
            });
            $('#orderingNextBtn').on('click', function() {
                if (Ordering.page >= Ordering.lastPage || Ordering.isBusy) return;
                Ordering.page += 1;
                orderingFetchList();
            });
        });

        function loadSettings(hotelId, roomId) {
            $.ajax({
                url: '{{ route("settings.get") }}',
                type: 'GET',
                data: {
                    hotel_id: hotelId,
                    room_id: roomId
                },
                success: function(response) {
                    if (response.success && response.data) {
                        const data = response.data;
                        $('#advance_booking_days').val(data.advance_booking_days || '');
                        $('#apply_dynamic_price').val(data.apply_dynamic_price || '0');
                        $('#dynamicPriceToggle').toggleClass('active', data.apply_dynamic_price == 1);
                        $('#common_price').val(data.common_price || '');
                        $('#gst_percentage').val(data.gst_percentage || '');
                        $('#january').val(data.january || '');
                        $('#february').val(data.february || '');
                        $('#march').val(data.march || '');
                        $('#april').val(data.april || '');
                        $('#may').val(data.may || '');
                        $('#june').val(data.june || '');
                        $('#july').val(data.july || '');
                        $('#august').val(data.august || '');
                        $('#september').val(data.september || '');
                        $('#october').val(data.october || '');
                        $('#november').val(data.november || '');
                        $('#december').val(data.december || '');
                        
                        // Update disable state
                        if (data.apply_dynamic_price == 1) {
                            $("input.month").removeClass("disable");
                            $(".bl").addClass("op");
                        } else {
                            $("input.month").addClass("disable");
                            $(".bl").removeClass("op");
                        }
                    } else {
                        // Reset form fields if no settings found (keep hotel and room selection)
                        resetFormFields();
                    }
                },
                error: function() {
                    console.error('Error loading settings');
                }
            });
        }

        function saveSettings() {
            const hotelId = $('#hotel_id').val();
            const roomId = $('#room_id').val();
            
            if (!hotelId || !roomId) {
                if (typeof swal === 'function') {
                    swal("Error!", 'Please select Property and Room Type', "error");
                }
                return;
            }

            // Disable button and show loader
            const saveBtn = $('#saveSettingsBtn');
            saveBtn.css('opacity', '0.5');
            saveBtn.css('cursor', 'not-allowed');
            saveBtn.css('pointer-events', 'none');
            saveBtn.find('.label-default').addClass('d-none');
            saveBtn.find('.label-loading').removeClass('d-none');

            const formData = $('#priceSettingsForm').serialize();
            
            $.ajax({
                url: '{{ route("settings.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Re-enable button and hide loader
                    saveBtn.css('opacity', '1');
                    saveBtn.css('cursor', 'pointer');
                    saveBtn.css('pointer-events', 'auto');
                    saveBtn.find('.label-default').removeClass('d-none');
                    saveBtn.find('.label-loading').addClass('d-none');

                    if (response.success) {
                        if (typeof swal === 'function') {
                            swal("Success!", response.message || 'Settings saved successfully', "success");
                        }
                    } else {
                        if (typeof swal === 'function') {
                            swal("Error!", response.message || 'Error saving settings', "error");
                        }
                    }
                },
                error: function(xhr) {
                    // Re-enable button and hide loader
                    saveBtn.css('opacity', '1');
                    saveBtn.css('cursor', 'pointer');
                    saveBtn.css('pointer-events', 'auto');
                    saveBtn.find('.label-default').removeClass('d-none');
                    saveBtn.find('.label-loading').addClass('d-none');

                    const response = xhr.responseJSON;
                    if (typeof swal === 'function') {
                        swal("Error!", response?.message || 'Error saving settings', "error");
                    }
                }
            });
        }

        function resetFormFields() {
            // Reset all form fields except hotel and room selection
            $('#advance_booking_days').val('');
            $('#apply_dynamic_price').val('0');
            $('#dynamicPriceToggle').removeClass('active');
            $('#common_price').val('');
            $('#gst_percentage').val('');
            $('input.month').val('');
            $("input.month").addClass("disable");
            $(".bl").removeClass("op");
        }

        function resetForm() {
            $('#priceSettingsForm')[0].reset();
            $('#hotel_id').val('');
            $('#room_id').html('<option value="">Select Room Type</option>');
            resetFormFields();
        }

        function applyAdvanceBooking() {
            // This function can be used for immediate application if needed
            // For now, it's handled in saveSettings
        }

        function applyCommonPrice() {
            const commonPrice = $('#common_price').val();
            if (commonPrice) {
                $('input.month').val(commonPrice);
            }
        }

        function applyGST() {
            // GST application logic can be added here if needed
        }

        function applyEqualPrice() {
            const equalPrice = $('#equalPriceInput').val();
            if (equalPrice) {
                $('input.month').val(equalPrice);
                $('#customprice').modal('hide');
            }
        }

        // ========== Booking Settings Functions ==========
        $(document).ready(function() {
            // Load booking settings on page load
            loadBookingSettings();
        });

        function loadBookingSettings() {
            $.ajax({
                url: '{{ route("settings.getBookingSettings") }}',
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        $('#room_assignment_type').val(response.data.room_assignment_type || 'assign_manually');
                    }
                },
                error: function() {
                    console.error('Error loading booking settings');
                    // Set default value on error
                    $('#room_assignment_type').val('assign_manually');
                }
            });
        }

        function saveBookingSettings() {
            const roomAssignmentType = $('#room_assignment_type').val();
            
            if (!roomAssignmentType) {
                if (typeof swal === 'function') {
                    swal("Error!", 'Please select Room Assignment Type', "error");
                }
                return;
            }

            // Disable button and show loader
            const saveBtn = $('#saveBookingSettingsBtn');
            saveBtn.css('opacity', '0.5');
            saveBtn.css('cursor', 'not-allowed');
            saveBtn.css('pointer-events', 'none');
            saveBtn.find('.label-default').addClass('d-none');
            saveBtn.find('.label-loading').removeClass('d-none');

            const token = $('meta[name="csrf-token"]').attr('content');
            
            $.ajax({
                url: '{{ route("settings.saveBookingSettings") }}',
                type: 'POST',
                data: {
                    _token: token,
                    room_assignment_type: roomAssignmentType
                },
                success: function(response) {
                    // Re-enable button and hide loader
                    saveBtn.css('opacity', '1');
                    saveBtn.css('cursor', 'pointer');
                    saveBtn.css('pointer-events', 'auto');
                    saveBtn.find('.label-default').removeClass('d-none');
                    saveBtn.find('.label-loading').addClass('d-none');

                    if (response.success) {
                        if (typeof swal === 'function') {
                            swal("Success!", response.message || 'Booking settings saved successfully', "success");
                        }
                    } else {
                        if (typeof swal === 'function') {
                            swal("Error!", response.message || 'Error saving settings', "error");
                        }
                    }
                },
                error: function(xhr) {
                    // Re-enable button and hide loader
                    saveBtn.css('opacity', '1');
                    saveBtn.css('cursor', 'pointer');
                    saveBtn.css('pointer-events', 'auto');
                    saveBtn.find('.label-default').removeClass('d-none');
                    saveBtn.find('.label-loading').addClass('d-none');

                    const response = xhr.responseJSON;
                    if (typeof swal === 'function') {
                        swal("Error!", response?.message || 'Error saving settings', "error");
                    }
                }
            });
        }

        function resetBookingSettings() {
            // Reset to previously loaded value
            loadBookingSettings();
        }
    </script>
@endsection

