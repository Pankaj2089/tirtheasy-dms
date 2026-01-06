@php
$data = $data ?? [];
$revenue = $data['revenue'] ?? [
    'occupancy' => 0,
    'pickup' => 0,
    'cancellations' => 0,
    'alos' => 0,
    'avgLeadTime' => 0,
    'adr' => 0,
    'revenue' => 0,
    'revpar' => 0,
    'revpac' => 0
];
@endphp

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            Occupancy
            <i>i</i>
        </div>
        <div class="metric-value" id="occupancy">{{ number_format($revenue['occupancy'], 2) }}%</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            Pick Up
            <i>i</i>
        </div>
        <div class="metric-value" id="pickup">{{ $revenue['pickup'] }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            Cancellations
            <i>i</i>
        </div>
        <div class="metric-value" id="cancellations">{{ $revenue['cancellations'] }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            ALOS
            <i>i</i>
        </div>
        <div class="metric-value" id="alos">{{ number_format($revenue['alos'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            Avg. Lead Time
            <i>i</i>
        </div>
        <div class="metric-value" id="avgLeadTime">{{ number_format($revenue['avgLeadTime'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            ADR
            <i>i</i>
        </div>
        <div class="metric-value" id="adr">Rs {{ number_format($revenue['adr'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            Revenue
            <i>i</i>
        </div>
        <div class="metric-value" id="revenue">Rs {{ number_format($revenue['revenue'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            RevPAR
            <i>i</i>
        </div>
        <div class="metric-value" id="revpar">Rs {{ number_format($revenue['revpar'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

<div class="col-6 col-sm-6 col-md-4 col-lg-3">
    <div class="metric-card text-center">
        <div class="metric-title text-center mb-3">
            RevPAC
            <i>i</i>
        </div>
        <div class="metric-value" id="revpac">Rs {{ number_format($revenue['revpac'], 2) }}</div>
        <div class="metric-sub">(N/A)</div>
    </div>
</div>

