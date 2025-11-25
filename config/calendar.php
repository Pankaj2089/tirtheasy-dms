<?php

return [
    'rooms' => [
        [
            'hostelName' => 'Heritage Block',
            'title' => '4 ICR Villa',
            'summary' => [
                'defaultCount' => 4,
                'defaultPrice' => 18000,
                'tooltip' => 'Available villas / base rate',
                'overrides' => [
                    '2025-03-01' => ['count' => 3, 'price' => 18500],
                    '2025-03-02' => ['count' => 2, 'price' => 18250],
                    '2025-03-03' => ['count' => 1, 'price' => 19000],
                ],
            ],
            'rooms' => [
                ['id' => 101, 'number' => '1109'],
                ['id' => 102, 'number' => '1201'],
                ['id' => 103, 'number' => '1203'],
            ],
        ],
        [
            'hostelName' => 'Heritage Block',
            'title' => 'Garden Suite',
            'summary' => [
                'defaultCount' => 2,
                'defaultPrice' => 15000,
                'tooltip' => 'Suites remaining / rack rate',
                'overrides' => [
                    '2025-03-02' => ['count' => 0, 'price' => 16000],
                    '2025-03-05' => ['count' => 2, 'price' => 14800],
                ],
            ],
            'rooms' => [
                ['id' => 201, 'number' => '1210'],
                ['id' => 202, 'number' => '1302'],
            ],
        ],
        [
            'hostelName' => 'Premium Suites',
            'title' => 'ICR Deluxe',
            'summary' => [
                'defaultCount' => 3,
                'defaultPrice' => 22000,
                'tooltip' => 'Deluxe allocation / nightly rate',
                'overrides' => [
                    '2025-03-03' => ['count' => 4, 'price' => 21500],
                    '2025-03-04' => ['count' => 1, 'price' => 23000],
                ],
            ],
            'rooms' => [
                ['id' => 301, 'number' => '2101'],
                ['id' => 302, 'number' => '2102'],
            ],
        ],
    ],

    'bookings' => [
        [
            'id' => 'BK-001',
            'room_id' => 101,
            'guest_name' => 'Mr. John Doe',
            'label' => 'Mr. John Doe',
            'status' => 'reserved',
            'source' => 'Website',
            'start_date' => '2025-11-24',
            'end_date' => '2025-11-24',
            'is_blocked' => false,
        ],
        [
            'id' => 'BK-002',
            'room_id' => 102,
            'guest_name' => 'John Doe & Group',
            'label' => 'John Doe & Group',
            'status' => 'group',
            'source' => 'Agent OTA',
            'start_date' => '2025-11-26',
            'end_date' => '2025-11-26',
            'is_blocked' => true,
        ],
        [
            'id' => 'BK-003',
            'room_id' => 1,
            'guest_name' => 'Maintenance Block',
            'label' => 'Maintain blk.',
            'status' => 'maintenance',
            'start_date' => '2025-11-25',
            'end_date' => '2025-11-26',
            'is_blocked' => true,
        ],
        [
            'id' => 'BK-004',
            'room_id' => 301,
            'guest_name' => 'Mr. John Doe',
            'label' => 'Mr. John Doe',
            'status' => 'reserved',
            'source' => 'OTA',
            'start_date' => '2025-11-27',
            'end_date' => '2025-11-28',
            'is_blocked' => false,
        ],
    ],
];

