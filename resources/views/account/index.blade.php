@php
$siteUrl = env('APP_URL');
@endphp

@extends('layout.default')

@section('content')
    @include('account.header')

    <div class="wrapper">
        <div class="calendar-shell d-flex flex-column flex-lg-row">
            <aside class="calendar-shell__sidebar d-none d-lg-block">
                @include('element.sidebar')
            </aside>

            <main class="calendar-shell__body flex-grow-1">
                <div class="main-dash top-report mob-device p-0 mx-md-4 overflow-hidden" style="overflow-y: auto; max-height: calc(100vh - 80px);">

                    <!-- Account card -->
                    <div class="account-card overflow-hidden mb-4 pb-0">
                        <div class="row g-3 align-items-start">
                            <div class="col-md-8 col-7 ps-md-4 mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white bg-opacity-10 rounded-3 p-2 d-flex align-items-center justify-content-center">
                                        <span class="fw-bold fs-4">🏨</span>
                                    </div>
                                    <div>
                                        <div class="small opacity-75 mb-1">Account Name</div>
                                        <h4 class="mb-0">{{ $accountInfo['account_name'] }}</h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-5 pe-md-4 pe-3 text-md-end">
                                <div class="status-pill">
                                    <span class="dot"></span>
                                    <span>Account Status: <strong class="ms-1">{{ $accountInfo['account_status'] }}</strong></span>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="row g-3 px-md-5 mx-0 account-meta pb-3 justify-content-center">
                                    <div class="col-md-4 d-flex col-4 flex-devide">
                                        <div class="pe-md-3">
                                            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M5 14.2864C3.14864 15.1031 2 16.2412 2 17.5C2 19.9853 6.47715 22 12 22C17.5228 22 22 19.9853 22 17.5C22 16.2412 20.8514 15.1031 19 14.2864M18 8C18 12.0637 13.5 14 12 17C10.5 14 6 12.0637 6 8C6 4.68629 8.68629 2 12 2C15.3137 2 18 4.68629 18 8ZM13 8C13 8.55228 12.5523 9 12 9C11.4477 9 11 8.55228 11 8C11 7.44772 11.4477 7 12 7C12.5523 7 13 7.44772 13 8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="ps-1">
                                            <span>Location</span>
                                            <strong>{{ $accountInfo['location'] ?: 'N/A' }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex col-4 flex-devide">
                                        <div class="pe-md-3">
                                            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.0497 6C15.0264 6.19057 15.924 6.66826 16.6277 7.37194C17.3314 8.07561 17.8091 8.97326 17.9997 9.95M14.0497 2C16.0789 2.22544 17.9713 3.13417 19.4159 4.57701C20.8606 6.01984 21.7717 7.91101 21.9997 9.94M10.2266 13.8631C9.02506 12.6615 8.07627 11.3028 7.38028 9.85323C7.32041 9.72854 7.29048 9.66619 7.26748 9.5873C7.18576 9.30695 7.24446 8.96269 7.41447 8.72526C7.46231 8.65845 7.51947 8.60129 7.63378 8.48698C7.98338 8.13737 8.15819 7.96257 8.27247 7.78679C8.70347 7.1239 8.70347 6.26932 8.27247 5.60643C8.15819 5.43065 7.98338 5.25585 7.63378 4.90624L7.43891 4.71137C6.90747 4.17993 6.64174 3.91421 6.35636 3.76987C5.7888 3.4828 5.11854 3.4828 4.55098 3.76987C4.2656 3.91421 3.99987 4.17993 3.46843 4.71137L3.3108 4.86901C2.78117 5.39863 2.51636 5.66344 2.31411 6.02348C2.08969 6.42298 1.92833 7.04347 1.9297 7.5017C1.93092 7.91464 2.01103 8.19687 2.17124 8.76131C3.03221 11.7947 4.65668 14.6571 7.04466 17.045C9.43264 19.433 12.295 21.0575 15.3284 21.9185C15.8928 22.0787 16.1751 22.1588 16.588 22.16C17.0462 22.1614 17.6667 22 18.0662 21.7756C18.4263 21.5733 18.6911 21.3085 19.2207 20.7789L19.3783 20.6213C19.9098 20.0898 20.1755 19.8241 20.3198 19.5387C20.6069 18.9712 20.6069 18.3009 20.3198 17.7333C20.1755 17.448 19.9098 17.1822 19.3783 16.6508L19.1835 16.4559C18.8339 16.1063 18.6591 15.9315 18.4833 15.8172C17.8204 15.3862 16.9658 15.3862 16.3029 15.8172C16.1271 15.9315 15.9523 16.1063 15.6027 16.4559C15.4884 16.5702 15.4313 16.6274 15.3644 16.6752C15.127 16.8453 14.7828 16.904 14.5024 16.8222C14.4235 16.7992 14.3612 16.7693 14.2365 16.7094C12.7869 16.0134 11.4282 15.0646 10.2266 13.8631Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="ps-1">
                                            <span>Contact</span>
                                            <strong>{{ $accountInfo['contact'] }}</strong>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex col-4 flex-devide">
                                        <div class="pe-md-3">
                                            <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M21.5 18L14.8571 12M9.14286 12L2.50003 18M2 7L10.1649 12.7154C10.8261 13.1783 11.1567 13.4097 11.5163 13.4993C11.8339 13.5785 12.1661 13.5785 12.4837 13.4993C12.8433 13.4097 13.1739 13.1783 13.8351 12.7154L22 7M6.8 20H17.2C18.8802 20 19.7202 20 20.362 19.673C20.9265 19.3854 21.3854 18.9265 21.673 18.362C22 17.7202 22 16.8802 22 15.2V8.8C22 7.11984 22 6.27976 21.673 5.63803C21.3854 5.07354 20.9265 4.6146 20.362 4.32698C19.7202 4 18.8802 4 17.2 4H6.8C5.11984 4 4.27976 4 3.63803 4.32698C3.07354 4.6146 2.6146 5.07354 2.32698 5.63803C2 6.27976 2 7.11984 2 8.8V15.2C2 16.8802 2 17.7202 2.32698 18.362C2.6146 18.9265 3.07354 19.3854 3.63803 19.673C4.27976 20 5.11984 20 6.8 20Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                        <div class="ps-1">
                                            <span>Email</span>
                                            <strong>{{ $accountInfo['email'] }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Access credentials -->
                    <div class="mb-4 px-4">
                        <h5 class="mb-3">Access Credentials</h5>
                    </div>

                    <!-- Stats row -->
                    <div class="row g-3 mb-4 px-4">
                        <div class="col-md-3 col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">Total Properties</div>
                                <div class="stat-value">{{ $accountInfo['total_properties'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">Total Rooms</div>
                                <div class="stat-value">{{ $accountInfo['total_rooms'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">User ID</div>
                                <div class="stat-value">{{ $accountInfo['user_id'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="stat-card h-100">
                                <div class="stat-label">Property Code</div>
                                <div class="stat-value">{{ $accountInfo['property_code'] }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Properties header -->
                    <div class="d-flex align-items-center justify-content-between mt-3 mb-3 px-4">
                        <h5 class="mb-0">Properties Overview</h5>
                    </div>

                    <!-- Properties cards -->
                    <div class="row g-3 mb-4 px-4">
                        @forelse($properties as $property)
                            <div class="col-md-4 col-sm-6">
                                <div class="property-card">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="{{$siteUrl}}public/assets/img/buid-ic.svg" alt="">
                                            <div class="name ms-3">
                                                <div class="property-title">{{ $property['title'] }}</div>
                                                <div class="d-flex align-items-center gap-1 small {{ $property['status'] == 'active' ? 'text-success' : 'text-danger' }}">
                                                    <span class="rounded-circle" style="width:6px;height:6px;background:{{ $property['status'] == 'active' ? '#35d27d' : '#dc3545' }};"></span>
                                                    <span>{{ $property['status'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-sm btn-outline-light border-0 text-muted">⋮</button>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mt-3">
                                        <div class="property-meta mb-2">
                                            <img src="{{$siteUrl}}public/assets/img/Group.svg" alt=""> &nbsp; Total Rooms
                                        </div>
                                        <div class="fw-semibold mb-2">{{ $property['total_rooms'] }}</div>
                                    </div>
                                    <div class="property-meta d-flex justify-content-between">
                                        <span><img src="{{$siteUrl}}public/assets/img/tick.svg" alt=""> &nbsp; Occupancy</span>
                                        <span class="fw-semibold">{{ $property['occupancy_rate'] }}%</span>
                                    </div>
                                    <div class="occupancy-bar-bg">
                                        <div class="occupancy-bar-fill" style="width:{{ $property['occupancy_rate'] }}%;"></div>
                                    </div>
                                    <div class="pt-2 text-center">
                                        <a href="#" class="view-link">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info text-center">
                                    No properties found. Please add a property to get started.
                                </div>
                            </div>
                        @endforelse
                    </div>

                </div>
            </main>
        </div>
    </div>

    @include('element.footer')

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".profile").click(function () {
                $(".profile-menus").toggleClass("show");
            });
        });
    </script>
@endsection

