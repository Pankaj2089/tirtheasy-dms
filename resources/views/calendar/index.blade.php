@php
$siteUrl = env('APP_URL');
@endphp
@extends('layout.calendar')
@section('content')
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
                                        <input type="date" placeholder="04, October, 2025" class="border-0">
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
                                            class="badge bg-secondary">80</span></button>
                                    <button class="bg-transparent">Vacant <span
                                            class="badge bg-secondary">34</span></button>
                                    <button class="bg-transparent">Occupied <span
                                            class="badge bg-secondary">40</span></button>
                                    <button class="bg-transparent">Reserved <span
                                            class="badge bg-secondary">07</span></button>
                                    <button class="bg-transparent">Blocked <span
                                            class="badge bg-secondary">09</span></button>
                                    <button class="bg-transparent">Due Date <span
                                            class="badge bg-secondary">09</span></button>
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
                        <div class="d-flex align-items-center position-relative">
                            <button class="bg-transpaent border-0 arrow-slide left" id="moveBtn"><img
                                    src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt=""></button>
                            <div class="date-columns">
                                <div class="date-column" id="box">
                                    <div class="date-cell selected">
                                        <div class="day">MON</div>
                                        <div class="date">01</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">02</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">03</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">04</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">05</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">05</div>
                                        <div class="month">OCT</div>
                                    </div>
                                </div>
                                <div class="date-column" id="box">
                                    <div class="date-cell">
                                        <div class="day">MON</div>
                                        <div class="date">06</div>
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
                            <button class="bg-transpaent border-0 arrow-slide right" id="moveBtn2"><img
                                    src="{{$siteUrl}}public/assets/img/circle-arrow.svg" alt=""></button>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Room Type Filter & Info -->
            <div class="border-bottom room-cat">
                <div class="container-fluid px-3">
                    <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                        <div class="room-label-header">Room Category Name Here</div>
                    </div>
                </div>
            </div>

            <!-- Room Rows -->
            <div class="rooms-container">
                <!-- 4 ICR Villa Section -->
                <div class="room-category">
                    <div class="room-row">
                        <div class="room-name bg-white title">4 ICR Villa</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="price-label"><span  data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip">0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span  data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip">0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span  data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip">0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Villa 01 -->
                    <div class="room-row">
                        <div class="room-name bg-light ">1109</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip" style="width:calc(200% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group"  data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> John doe & Gro...
                                </div>
                            </div>
                            <div class="grid-cell"></div>

                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest payment-pending"  data-bs-toggle="tooltip" data-bs-placement="top" title="Hello Tooltip" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>

                    <!-- Villa 01 Second Row -->
                    <div class="room-row">
                        <div class="room-name bg-light ">1201</div>
                        <div class="room-grid">
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance"
                                    style="width:calc(200% - 5px);" data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintenance block

                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> Mr. john grp...
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>
                </div>



                <div class="room-category">
                    <div class="room-row">
                        <div class="room-name bg-white title">4 ICR Villa</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Villa 01 -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="booking-block booking-guest payment-pending" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe

                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> John Grp...
                                </div>
                            </div>
                            <div class="grid-cell"></div>

                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest payment-pending" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe

                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>

                    <!-- Villa 01 Second Row -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> Mr. john doe &...
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>
                </div>
                <div class="border-bottom room-cat ">
                    <div class="container-fluid px-3">
                        <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                            <div class="room-label-header">Room Category Name 2</div>
                        </div>
                    </div>
                </div>
                <div class="room-category">
                    <div class="room-row">
                        <div class="room-name bg-white title">4 ICR Villa</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Villa 01 -->
                    <div class="room-row">
                        <div class="room-name bg-light ">1109</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> John doe & Gro...
                                </div>
                            </div>
                            <div class="grid-cell"></div>

                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>

                    <!-- Villa 01 Second Row -->
                    <div class="room-row">
                        <div class="room-name bg-light ">1201</div>
                        <div class="room-grid">
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt="">Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-due" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> Mr. john doe &...
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>
                </div>

                <div class="border-bottom room-cat ">
                    <div class="container-fluid px-3">
                        <div class="d-flex align-items-center justify-content-center flex-wrap gap-2">
                            <div class="room-label-header">Room Category Name 3</div>
                        </div>
                    </div>
                </div>

                <div class="room-category">
                    <div class="room-row">
                        <div class="room-name bg-white title">4 ICR Villa</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Villa 01 -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> John doe & Gro...
                                </div>
                            </div>
                            <div class="grid-cell"></div>

                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintain blk..
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>

                    <!-- Villa 01 Second Row -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintenance block
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-due" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> Mr. john doe &...
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>
                </div>

                <div class="room-category">
                    <div class="room-row">
                        <div class="room-name bg-white title">4 ICR Villa</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span> <span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><span
                                        class="bg-info text-white">1</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                            <div class="grid-cell">
                                <div class="price-label"><span>0</span><br>18000.00</div>
                            </div>
                        </div>
                    </div>

                    <!-- Villa 01 -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-group" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> John doe & Gro...
                                </div>
                            </div>
                            <div class="grid-cell"></div>

                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintenance block
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>

                    <!-- Villa 01 Second Row -->
                    <div class="room-row">
                        <div class="room-name bg-light ">V1101</div>
                        <div class="room-grid">
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-maintenance" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/maintain.svg" alt=""> Maintenance block
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                            <div class="grid-cell">
                                <div class="booking-block booking-due" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/users.svg" alt=""> Mr. john doe &...
                                </div>
                            </div>
                            <div class="grid-cell">
                                <div class="booking-block booking-guest" style="width: calc(100% - 5px);"
                                    data-bs-toggle="modal" data-bs-target="#booking-pop">
                                    <img src="{{$siteUrl}}public/assets/img/user.svg" alt=""> Mr. john doe
                                </div>
                            </div>
                            <div class="grid-cell"></div>
                        </div>
                    </div>
                </div>



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
                    <form class="bl-reserve-form position-relative">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-4">
                                <div class="">
                                    <label for="check-In" class="form-label">Check-In</label>
                                    <div class="time">
                                        <input type="date" class="form-control" id="Check-In">
                                        <input type="time" class="form-control" id="Check-In">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label for="check-out" class="form-label">Check-Out</label>
                                    <div class="time">
                                        <input type="date" class="form-control" id="Check-In">
                                        <input type="time" class="form-control" id="Check-In">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-center gap-3">
                                <div class="d-flex flex-column w-50">
                                    <label for="check-out" class="form-label">Rooms</label>
                                    <select name="" id="" class="form-control">
                                        <option value="Select">Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                    </select>
                                </div>
                                <div class="d-flex flex-column w-50">
                                    <label for="check-out" class="form-label">Reservation Type</label>
                                    <select name="" id="" class="form-control">
                                        <option value="Select">Confirm</option>
                                        <option value="Inquiry">Inquiry</option>
                                        <option value="Hold">Hold</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-12">
                                <h5 class="mt-3">Room Details</h5>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Room Type</label>
                                <select name="" id="" class="form-control" class="w-100">
                                    <option value="Normal">Normal</option>
                                    <option value="Mint">Mint</option>
                                    <option value="Deluxe">Deluxe</option>
                                    <option value="Non Deluxe">Non Deluxe</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Rate Type</label>
                                <select name="" id="" class="form-control" class="w-100">
                                    <option value="Select">Select</option>
                                    <!-- <option value="Inquiry">Inquiry</option>
                            <option value="Hold">Hold</option> -->
                                </select>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Number of Room</label>
                                <select name="" id="" class="form-control" class="w-100">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Adult</label>
                                <input type="number" class="form-control" class="w-100" placeholder="Ex. 2">
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Child</label>
                                <input type="number" class="form-control" class="w-100" placeholder="Ex. 2">
                            </div>
                            <div class="col-md-2 d-flex flex-column">
                                <label for="check-out" class="form-label">Price</label>
                                <input type="number" class="form-control" class="w-100" placeholder="Rs. 5000">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 add-rom text-center mx-auto">
                                <button type="submit" class="theme-btn-text text-white w-100 h-auto">+ Add
                                    Room</button>
                            </div>
                        </div>

                        <div class="row align-items-center mb-3">
                            <div class="col-md-12">
                                <h5 class="mt-3">Guest Information</h5>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label for="check-out" class="form-label">Name</label>
                                <input type="text" class="form-control" class="w-100"
                                    placeholder="Shanker Sharma">
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label for="check-out" class="form-label">Gender</label>
                                <select name="" id="" class="form-control" class="w-100">
                                    <option value="Select">Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label for="check-out" class="form-label">Email</label>
                                <input type="email" class="form-control" class="w-100"
                                    placeholder="sample@tirtheasy.com">
                            </div>
                            <div class="col-md-3 d-flex flex-column">
                                <label for="check-out" class="form-label">Mobile</label>
                                <input type="number" class="form-control" class="w-100"
                                    placeholder="+91 0123456789">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 add-rom">
                                <button type="submit" class="theme-outline-btn">Confirm</button>
                            </div>
                        </div>


                        <div
                            class="row justify-content-between align-items-center position-absolute reservation-footer w-100">
                            <hr>
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
                                    <p class="mb-0 text-info">Balance</p>
                                    <p class="mb-0 text-info">Rs. 1,48,680</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex">
                                    <a href=""
                                        class="theme-btn text-white ms-auto text-center text-decoration-none">Payment</a>
                                </div>
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
$(document).ready(function () {
    $(".drop-one").click(function () {
        $(".availability-footer").toggleClass("slide-box");
    });

    $(".drop-one").click(function () {
        $(this).toggleClass("rotate");
    });

    $(".date-columns .date-column").click(function () {
        $(".date-column").removeClass("active");
        $(this).addClass("active");
    });
});
</script>

<script>
$(document).ready(function () {
    $("#moveBtn").click(function () {
        $("#box").animate({ marginLeft: "+=10%" }, 400);
    });

    $("#moveBtn2").click(function () {
        $("#box").animate({ marginLeft: "-=10%" }, 400);
    });
    $(".humberger-ic").click(function () {
        $(".profile-sidebar").toggleClass("active");
    });

    $(".close-profile").click(function () {
        $(".profile-sidebar").removeClass("active");
    });
});
</script>
@endsection