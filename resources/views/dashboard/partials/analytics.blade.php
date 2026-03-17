@php
$data = $data ?? [];
$arrivals = $data['arrivals'] ?? ['total' => 0, 'completed' => 0, 'pending' => 0, 'percentage' => 0];
$departures = $data['departures'] ?? ['total' => 0, 'checkedOut' => 0, 'pending' => 0, 'percentage' => 0];
$inHouse = $data['inHouse'] ?? ['adults' => 0, 'children' => 0, 'total' => 0, 'percentage' => 0];
$roomStatus = $data['roomStatus'] ?? ['total' => 0, 'occupied' => 0, 'vacant' => 0, 'complimentary' => 0, 'percentage' => 0];
$housekeeping = $data['housekeeping'] ?? ['clean' => 0, 'assign' => 0, 'dirty' => 0, 'block' => 0];
$activityFeeds = $data['activityFeeds'] ?? [];
@endphp

<!-- Arrival -->
<div class="col-6 col-md-6 col-xl-4">
    <div class="card card-mini p-3 pb-2">
        <div class="d-flex justify-content-start align-items-start">
            <div>
                <img src="{{$siteUrl}}public/assets/img/arival-Icon.svg" alt=""> &nbsp;
                <small class="">Arrival</small>
            </div>
        </div>
        <div class="radial-wrap">
            <svg width="110" height="110">
                <circle class="bg" cx="55" cy="55" r="44"></circle>
                <circle class="progress-arrival" cx="55" cy="55" r="44" stroke-dasharray="276"
                    stroke-dashoffset="{{ 276 - ($arrivals['percentage'] / 100) * 276 }}"></circle>
            </svg>
            <div class="radial-center">
                <strong id="arrivalPercentage">{{ $arrivals['percentage'] }}%</strong>
                <span>Complete</span>
            </div>
        </div>
        <div class="legend">
            <span class="pending"><i></i>Pending (<span id="arrivalPending">{{ $arrivals['pending'] }}</span>)</span>
            <span class="arrival"><i></i>Arrival (<span id="arrivalCompleted">{{ $arrivals['completed'] }}</span>)</span>
        </div>
    </div>
</div>

<!-- Departure -->
<div class="col-6 col-md-6 col-xl-4">
    <div class="card card-mini p-3 pb-2">
        <div class="d-flex justify-content-start align-items-center">
            <img src="{{$siteUrl}}public/assets/img/departure-Icon.svg" alt=""> &nbsp;
            <small class="">Departure</small>
        </div>
        <div class="radial-wrap">
            <svg width="110" height="110">
                <circle class="bg" cx="55" cy="55" r="44"></circle>
                <circle class="progress-departure" cx="55" cy="55" r="44" stroke-dasharray="276"
                    stroke-dashoffset="{{ 276 - ($departures['percentage'] / 100) * 276 }}"></circle>
            </svg>
            <div class="radial-center">
                <strong id="departurePercentage">{{ $departures['percentage'] }}%</strong>
                <span>Complete</span>
            </div>
        </div>
        <div class="legend">
            <span class="pending"><i></i>Pending (<span id="departurePending">{{ $departures['pending'] }}</span>)</span>
            <span class="checked"><i></i>Checked Out (<span id="departureCheckedOut">{{ $departures['checkedOut'] }}</span>)</span>
        </div>
    </div>
</div>

<!-- In House -->
<div class="col-6 col-md-6 col-xl-4">
    <div class="card card-mini p-3 pb-2">
        <div class="d-flex justify-content-start align-items-center">
            <img src="{{$siteUrl}}public/assets/img/house-Icon.svg" alt=""> &nbsp;
            <small class="">In House</small>
        </div>
        <div class="radial-wrap">
            <svg width="110" height="110">
                <circle class="bg" cx="55" cy="55" r="44"></circle>
                <circle class="progress-inhouse" cx="55" cy="55" r="44" stroke-dasharray="276"
                    stroke-dashoffset="{{ 276 - ($inHouse['percentage'] / 100) * 276 }}"></circle>
            </svg>
            <div class="radial-center">
                <strong id="inHousePercentage">{{ $inHouse['percentage'] }}%</strong>
                <span>Complete</span>
            </div>
        </div>
        <div class="legend">
            <span class="adult"><i></i>Adult (<span id="inHouseAdults">{{ $inHouse['adults'] }}</span>)</span>
            <span class="child"><i></i>Child (<span id="inHouseChildren">{{ $inHouse['children'] }}</span>)</span>
        </div>
    </div>
</div>

<!-- Room Status -->
<div class="col-6 col-md-6 col-xl-4">
    <div class="card card-mini p-3 pb-2">
        <div class="d-flex justify-content-start align-items-center">
            <img src="{{$siteUrl}}public/assets/img/room-Icon.svg" alt=""> &nbsp;
            <small class="">Room Status</small>
        </div>
        <div class="radial-wrap">
            <svg width="110" height="110">
                <circle class="bg" cx="55" cy="55" r="44"></circle>
                <circle class="progress-room" cx="55" cy="55" r="44" stroke-dasharray="276"
                    stroke-dashoffset="{{ 276 - ($roomStatus['percentage'] / 100) * 276 }}"></circle>
            </svg>
            <div class="radial-center">
                <strong id="roomPercentage">{{ $roomStatus['percentage'] }}%</strong>
                <span>Complete</span>
            </div>
        </div>
        <div class="legend">
            <span class="comp"><i></i>Complimentary (<span id="roomComplimentary">{{ $roomStatus['complimentary'] }}</span>)</span>
            <span class="vacant"><i></i>Vacant (<span id="roomVacant">{{ $roomStatus['vacant'] }}</span>)</span>
        </div>
    </div>
</div>

<!-- Housekeeping -->
<div class="col-12 col-md-6 col-xl-4">
    <div class="card card-mini p-3 pb-2">
        <div class="hk-card p-0">
            <img src="{{$siteUrl}}public/assets/img/house-Icon.svg" alt=""> &nbsp;
            <small>Housekeeping</small>

            <!-- bars -->
            <div class="hk-chart">
                <div class="hk-bar-wrap">
                    <div class="hk-value" id="hkClean">{{ $housekeeping['clean'] }}</div>
                    <div class="hk-bar clean" style="width: {{ $housekeeping['clean'] > 0 ? ($housekeeping['clean'] / max($housekeeping['clean'], $housekeeping['assign'], $housekeeping['dirty'], $housekeeping['block']) * 100) : 0 }}%"></div>
                    <div class="hk-label">Clean</div>
                </div>
                <div class="hk-bar-wrap">
                    <div class="hk-value" id="hkAssign">{{ $housekeeping['assign'] }}</div>
                    <div class="hk-bar assign" style="width: {{ $housekeeping['assign'] > 0 ? ($housekeeping['assign'] / max($housekeeping['clean'], $housekeeping['assign'], $housekeeping['dirty'], $housekeeping['block']) * 100) : 0 }}%"></div>
                    <div class="hk-label">HKAssign</div>
                </div>
                <div class="hk-bar-wrap">
                    <div class="hk-value" id="hkDirty">{{ $housekeeping['dirty'] }}</div>
                    <div class="hk-bar dirty" style="width: {{ $housekeeping['dirty'] > 0 ? ($housekeeping['dirty'] / max($housekeeping['clean'], $housekeeping['assign'], $housekeeping['dirty'], $housekeeping['block']) * 100) : 0 }}%"></div>
                    <div class="hk-label">Dirty</div>
                </div>
                <div class="hk-bar-wrap">
                    <div class="hk-value" id="hkBlock">{{ $housekeeping['block'] }}</div>
                    <div class="hk-bar block" style="width: {{ $housekeeping['block'] > 0 ? ($housekeeping['block'] / max($housekeeping['clean'], $housekeeping['assign'], $housekeeping['dirty'], $housekeeping['block']) * 100) : 0 }}%"></div>
                    <div class="hk-label">Block</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Feeds -->
<div class="col-12 col-md-6 col-xl-4">
    <div class="card card-mini pb-2 overflow-hidden">
        <div class="d-flex justify-content-between bg-light align-items-center p-3 py-2">
            <div class="left d-flex align-items-center">
                <small class="">Activity Feeds</small>
            </div>
            <div class="right d-flex align-items-center gap-2">
                <select class="form-select border-0 form-select-sm" id="activityFeedFilter" style="width: 150px;">
                    <option value="all">All</option>
                    <option value="check_in">Check In</option>
                    <option value="check_out">Check Out</option>
                    <option value="room_block">Room Block</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                &nbsp;
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M22.7 13.5L20.7005 11.5L18.7 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C15.3019 3 18.1885 4.77814 19.7545 7.42909M12 7V12L15 14"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <hr class="my-0">
        <ul class="p-3" id="activityFeedsList">
            @foreach($activityFeeds as $feed)
            <li class="d-flex justify-content-between align-items-center mb-2">
                <p class="small-text w-75">{{ $feed['message'] }}</p>
                <div class="btn btn-sm text-white {{ $feed['class'] }}">{{ $feed['type'] }}</div>
            </li>
            @endforeach
        </ul>
    </div>
</div>

