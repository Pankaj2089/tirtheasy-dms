<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceSettings extends Model
{
    protected $table = 'price_settings';

    protected $fillable = [
        'vendor_id',
        'hotel_id',
        'room_id',
        'advance_booking_days',
        'apply_dynamic_price',
        'common_price',
        'gst_percentage',
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
    ];

    protected $casts = [
        'apply_dynamic_price' => 'boolean',
        'advance_booking_days' => 'integer',
        'common_price' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'january' => 'decimal:2',
        'february' => 'decimal:2',
        'march' => 'decimal:2',
        'april' => 'decimal:2',
        'may' => 'decimal:2',
        'june' => 'decimal:2',
        'july' => 'decimal:2',
        'august' => 'decimal:2',
        'september' => 'decimal:2',
        'october' => 'decimal:2',
        'november' => 'decimal:2',
        'december' => 'decimal:2',
    ];
}

