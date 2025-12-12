@php
$siteUrl = env('APP_URL');
@endphp
@extends('layout.calendar')
@section('calendar-content')

<style>
    .reservation-sec {
        padding: 20px;
        height: calc(100vh - 80px);
        overflow-y: auto;
    }
    .reserve-box {
        border: 1px solid #ddd;
        border-radius: 8px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .box-top h5 {
        font-size: 14px;
        font-weight: 600;
        margin: 0;
    }
    .box-top p {
        font-size: 12px;
        color: #666;
        margin: 0;
    }
    .date p {
        font-size: 12px;
        margin: 0;
        font-weight: 500;
    }
    .date p span {
        font-size: 18px;
        font-weight: 700;
    }
    .box-detail ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .box-detail ul li {
        margin-bottom: 8px;
    }
    .box-detail ul li a {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: #333;
        font-size: 12px;
    }
    .box-conclusion ul {
        list-style: none;
        padding: 0;
        margin: 15px 0 0 0;
        border-top: 1px solid #eee;
        padding-top: 15px;
    }
    .box-conclusion ul li {
        margin-bottom: 8px;
        font-size: 13px;
    }
    .badge {
        background: #CD484B;
        color: white;
        border-radius: 12px;
        padding: 2px 8px;
        font-size: 11px;
        margin-left: 5px;
    }
    .assign-room-link {
        cursor: pointer;
        color: #CD484B !important;
    }
    .assign-room-link:hover {
        text-decoration: underline !important;
    }
    .room-assigned {
        color: #28a745 !important;
    }
</style>

<div class="reservation-sec">
    <div class="reservation-inner position-relative">
        <div class="row justify-content-between bg-white shadow-sm pe-3 top-bar">
            <div class="col-md-6">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="Arrivals-tab" data-bs-toggle="tab"
                            data-bs-target="#Arrivals" type="button" role="tab" aria-controls="Arrivals"
                            aria-selected="true">Arrivals
                            @if(count($arrivals) > 0)
                                <div class="badge">{{ count($arrivals) }}</div>
                            @endif
                        </button>
                        <button class="nav-link" id="Departures-tab" data-bs-toggle="tab"
                            data-bs-target="#Departures" type="button" role="tab" aria-controls="Departures"
                            aria-selected="false">Departures
                            @if(count($departures) > 0)
                                <div class="badge">{{ count($departures) }}</div>
                            @endif
                        </button>
                        <button class="nav-link" id="House-tab" data-bs-toggle="tab" data-bs-target="#House"
                            type="button" role="tab" aria-controls="House" aria-selected="false">In House
                            @if(count($inHouse) > 0)
                                <div class="badge">{{ count($inHouse) }}</div>
                            @endif
                        </button>
                    </div>
                </nav>
            </div>
            <div class="col-md-3 d-flex align-items-center justify-content-end gap-3">
                <div class="time w-75 d-flex align-items-center gap-2">
                    <input type="date" class="form-control border-top-0 border-bottom-0 rounded-0" id="Check-In" value="{{ $selectedFilterDate ?? date('Y-m-d') }}" data-filter-date>
                    @if(request()->has('filter_date'))
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearDateFilter" title="Clear date filter to show all future bookings">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    @endif
                </div>
                <div class="d-flex align-items-center">
                    <a class="user-button"><img src="{{$siteUrl}}public/assets/img/user.svg" alt=""></a>
                    <a class="user-button fill"><img src="{{$siteUrl}}public/assets/img/users.svg" alt=""></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tab-content" id="nav-tabContent">
                    <!-- Arrivals Tab -->
                    <div class="tab-pane fade active show" id="Arrivals" role="tabpanel" aria-labelledby="Arrivals-tab">
                        <div class="row mt-3">
                            @forelse($arrivals as $booking)
                                @include('reservation.booking-card', ['booking' => $booking])
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted mt-4">No arrivals found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Departures Tab -->
                    <div class="tab-pane fade" id="Departures" role="tabpanel" aria-labelledby="Departures-tab">
                        <div class="row mt-3">
                            @forelse($departures as $booking)
                                @include('reservation.booking-card', ['booking' => $booking])
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted mt-4">No departures found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- In House Tab -->
                    <div class="tab-pane fade" id="House" role="tabpanel" aria-labelledby="House-tab">
                        <div class="row mt-3">
                            @forelse($inHouse as $booking)
                                @include('reservation.booking-card', ['booking' => $booking])
                            @empty
                                <div class="col-12">
                                    <p class="text-center text-muted mt-4">No in-house guests found</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Room Modal -->
<div class="modal fade" id="assign" tabindex="-1" aria-labelledby="assignLabel" aria-hidden="true">
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
                    <button class="bg-transpaent border-0 arrow-slide left" id="assignModalPrevBtn"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt=""></button>
                    <div class="date-columns" id="assignModalDateColumns"></div>
                    <button class="bg-transpaent border-0 arrow-slide right" id="assignModalNextBtn"><img src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt=""></button>
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
                <div class="accordion" id="accordionExample"></div>
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

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $(".user-button").click(function(){
            $(".user-button.fill").removeClass("fill");
            $(this).addClass("fill");
        });
        
        // Handle date filter change - reload page with filter parameter
        $('#Check-In').on('change', function() {
            const selectedDate = $(this).val();
            const url = new URL(window.location.href);
            
            if (selectedDate) {
                url.searchParams.set('filter_date', selectedDate);
            } else {
                url.searchParams.delete('filter_date');
            }
            
            window.location.href = url.toString();
        });
        
        // Handle clear date filter button
        $('#clearDateFilter').on('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.delete('filter_date');
            window.location.href = url.toString();
        });
        
        // Handle assign room link clicks - directly open room selector
        $(document).on('click', '.assign-room-link', function(e) {
            e.preventDefault();
            const bookingId = $(this).data('booking-id');
            const orderId = $(this).data('booking-order-id');
            const rooms = $(this).data('booking-rooms') || 1;
            
            // Extract numeric ID from 'ORD-123' format if needed
            let numericBookingId = bookingId;
            if (typeof bookingId === 'string' && bookingId.startsWith('ORD-')) {
                numericBookingId = bookingId.replace(/^ORD-/, '');
            }
            
            // Open room selector directly
            openRoomSelectorForBooking({
                id: numericBookingId,
                order_id: orderId || numericBookingId,
                rooms: rooms
            });
        });
    });
    
    // Room assignment functionality
    const assignEndpoints = {
        available: @json(route('assign.rooms.available')),
        assign: @json(route('assign.rooms.assign'))
    };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let assignRoomSelectModalInstance = null;
    
    function toISO(date) {
        if (!date) return null;
        const d = date instanceof Date ? date : new Date(date);
        if (isNaN(d.getTime())) return null;
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }
    
    async function openRoomSelectorForBooking(booking) {
        const modal = document.getElementById('assignRoomSelectModal');
        if (!modal) {
            if (typeof swal !== 'undefined') {
                swal('Error', 'Room selection modal not found.', 'error');
            } else {
                alert('Room selection modal not found.');
            }
            return;
        }
        
        const modalBodyEl = modal.querySelector('#assignRoomSelectBody');
        if (!modalBodyEl) {
            if (typeof swal !== 'undefined') {
                swal('Error', 'Modal body not found.', 'error');
            } else {
                alert('Modal body not found.');
            }
            return;
        }
        
        modalBodyEl.innerHTML = '<p class="text-muted small mb-0">Loading available rooms...</p>';
        
        if (!assignRoomSelectModalInstance && window.bootstrap && window.bootstrap.Modal) {
            assignRoomSelectModalInstance = new window.bootstrap.Modal(modal);
        }
        if (assignRoomSelectModalInstance) {
            assignRoomSelectModalInstance.show();
        }
        
        // Extract numeric ID from 'ORD-123' format (same as calendar page)
        const bookingIdStr = booking.id.toString();
        const numericId = bookingIdStr.replace(/^ORD-/, '');
        const orderId = parseInt(numericId);
        
        if (!orderId || isNaN(orderId)) {
            const errorMsg = 'Invalid booking ID format.';
            if (typeof swal !== 'undefined') {
                swal('Error', errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        const today = new Date();
        const params = new URLSearchParams({
            booking_id: orderId,
            date: toISO(today)
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
            
            const requiredRooms = parseInt(booking.rooms) || 1;
            
            const container = document.createElement('div');
            container.innerHTML = `
                <div class="mb-3">
                    <p class="small text-muted mb-2">Select exactly <strong>${requiredRooms}</strong> room(s) (Booking requires ${requiredRooms} room${requiredRooms > 1 ? 's' : ''}):</p>
                    <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                        ${rooms.map((room) => {
                            return `
                                <label class="list-group-item d-flex align-items-center" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input me-2 room-checkbox" value="${room.id}" data-room-label="${room.label}">
                                    ${room.label}
                                </label>
                            `;
                        }).join('')}
                    </div>
                    <p class="small text-info mt-2 mb-0" id="room-selection-status">
                        Selected: <strong>0</strong> / ${requiredRooms}
                    </p>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm" data-cancel-selection>Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" data-save-selection>Save Selection</button>
                </div>
            `;
            
            modalBodyEl.innerHTML = '';
            modalBodyEl.appendChild(container);
            
            // Function to update checkbox states
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
            
            // Add change event listeners
            container.querySelectorAll('input.room-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    updateCheckboxStates();
                });
            });
            
            updateCheckboxStates();
            
            // Handle cancel
            container.querySelector('[data-cancel-selection]')?.addEventListener('click', () => {
                if (assignRoomSelectModalInstance) {
                    assignRoomSelectModalInstance.hide();
                }
            });
            
            // Handle save
            container.querySelector('[data-save-selection]')?.addEventListener('click', async () => {
                const checkedBoxes = container.querySelectorAll('input.room-checkbox:checked');
                const selectedRooms = Array.from(checkedBoxes).map(cb => ({
                    id: parseInt(cb.value),
                    label: cb.dataset.roomLabel
                }));
                
                if (selectedRooms.length === 0) {
                    if (typeof swal !== 'undefined') {
                        swal('Please select at least one room.', '', 'warning');
                    } else {
                        alert('Please select at least one room.');
                    }
                    return;
                }
                
                if (selectedRooms.length !== requiredRooms) {
                    const message = `Please select exactly ${requiredRooms} room(s). You have selected ${selectedRooms.length} room(s).`;
                    if (typeof swal !== 'undefined') {
                        swal(message, '', 'warning');
                    } else {
                        alert(message);
                    }
                    return;
                }
                
                // Assign rooms
                await assignRoomToBooking(booking.id, selectedRooms.map(r => r.id));
            });
        } catch (err) {
            console.error(err);
            modalBodyEl.innerHTML = `<p class="text-danger small mb-0">Error: ${err.message || 'Failed to load rooms.'}</p>`;
        }
    }
    
    async function assignRoomToBooking(bookingId, roomIds) {
        // Extract numeric ID from 'ORD-123' format (same as calendar page)
        const numericId = bookingId.toString().replace(/^ORD-/, '');
        const orderId = parseInt(numericId);
        
        if (!orderId || isNaN(orderId)) {
            const errorMsg = 'Invalid booking ID format.';
            if (typeof swal !== 'undefined') {
                swal('Error', errorMsg, 'error');
            } else {
                alert(errorMsg);
            }
            return;
        }
        
        const formData = new FormData();
        formData.append('booking_id', orderId);
        formData.append('room_number_ids', Array.isArray(roomIds) ? roomIds.join(',') : roomIds);
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
            
            if (assignRoomSelectModalInstance) {
                assignRoomSelectModalInstance.hide();
            }
            
            if (typeof swal !== 'undefined') {
                swal('Success!', 'Room assigned successfully.', 'success').then(() => {
                    location.reload();
                });
            } else {
                alert('Room assigned successfully!');
                location.reload();
            }
        } catch (err) {
            console.error(err);
            if (typeof swal !== 'undefined') {
                swal('Error', err.message || 'Unable to assign room. Please try again.', 'error');
            } else {
                alert('Error: ' + (err.message || 'Unable to assign room. Please try again.'));
            }
        }
    }
</script>
@endsection

