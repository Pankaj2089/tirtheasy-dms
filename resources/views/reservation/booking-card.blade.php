@php
    $checkIn = \Carbon\Carbon::parse($booking['check_in_date']);
    $checkOut = $booking['check_out_date'] ? \Carbon\Carbon::parse($booking['check_out_date']) : null;
    $checkInMonth = strtoupper($checkIn->format('M'));
    $checkInDay = $checkIn->format('d');
    $checkInTime = $checkIn->format('h:i A');
    
    if ($checkOut) {
        $checkOutMonth = strtoupper($checkOut->format('M'));
        $checkOutDay = $checkOut->format('d');
        $checkOutTime = $checkOut->format('h:i A');
    } else {
        $checkOutMonth = $checkInMonth;
        $checkOutDay = $checkInDay;
        $checkOutTime = $checkInTime;
    }
    
    $formatCurrency = function($amount) {
        return 'Rs. ' . number_format($amount, 2);
    };
    
    $reservationType = ucfirst($booking['reservation_type'] ?? 'confirmed');
    $mealPlan = 'Meal Only'; // You can customize this based on your data
@endphp

<div class="col-md-3 my-2 mb-3">
    <div class="reserve-box" data-booking-id="{{ $booking['id'] }}">
        <div class="box-top d-flex align-items-center justify-content-between">
            <div class="media d-flex">
                <img src="{{$siteUrl}}public/assets/img/cal-icon.svg" alt="">
                <div class="media-body ms-3">
                    <h5>{{ $booking['guest_name'] }} 
                        @if($booking['payment_status'] === 'PAID' || $booking['payment_status'] === 'SUCCESS')
                            <img src="{{$siteUrl}}public/assets/img/tick.svg" alt="">
                        @endif
                    </h5>
                    <p class="mb-0">{{ $booking['reservation_number'] }}</p>
                </div>
            </div>
            <button class="bg-transparent border-0"><img src="{{$siteUrl}}public/assets/img/box-option.svg" alt=""></button>
        </div>

        <div class="box-detail d-flex align-items-center">
            <div class="date">
                <p>{{ $checkInMonth }} <span>{{ $checkInDay }}</span></p>
                <p>{{ $checkInTime }}</p>
                <hr>
                <p>{{ $checkOutMonth }} <span>{{ $checkOutDay }}</span></p>
                <p>{{ $checkOutTime }}</p>
            </div>
            <ul>
                <li>
                    <a href="">
                        <img src="{{$siteUrl}}public/assets/img/night.svg" alt="">
                        <span>{{ $booking['nights'] }} Night Stay</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <img src="{{$siteUrl}}public/assets/img/bed.svg" alt="">
                        <span>{{ $booking['rooms'] }} Rooms / {{ $mealPlan }}</span>
                    </a>
                </li>
                <li>
                    <a href="">
                        <img src="{{$siteUrl}}public/assets/img/Family.svg" alt="">
                        <span>{{ $booking['adults'] }} Adult{{ $booking['adults'] != 1 ? 's' : '' }}{{ $booking['children'] > 0 ? ', ' . $booking['children'] . ' Child' . ($booking['children'] != 1 ? 'ren' : '') : '' }}</span>
                    </a>
                </li>
                <li>
                    <span style="display: flex; align-items: center; gap: 8px;">
                        <img src="{{$siteUrl}}public/assets/img/tick.svg" alt="">
                        <span class="reservation-type-text">{{ $reservationType }} Reservation</span>
                    </span>
                </li>
                @if(!$booking['has_room_assigned'])
                <li>
                    <a href="#" class="assign-room-link" 
                       data-booking-id="{{ $booking['id'] }}" 
                       data-booking-order-id="{{ $booking['order_id'] }}" 
                       data-booking-rooms="{{ $booking['rooms'] }}">
                        <img src="{{$siteUrl}}public/assets/img/tick.svg" alt="">
                        <span>Assign Room</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        <div class="box-conclusion">
            <ul>
                <li class="d-flex align-items-center justify-content-between">
                    <span>Total</span>
                    <span>{{ $formatCurrency($booking['total']) }}</span>
                </li>
                <li class="d-flex align-items-center justify-content-between">
                    <span>Paid</span>
                    <span>{{ $formatCurrency($booking['paid']) }}</span>
                </li>
                <li class="d-flex align-items-center justify-content-between">
                    <span>Balance</span>
                    <span class="text-danger">{{ $formatCurrency($booking['balance']) }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>

