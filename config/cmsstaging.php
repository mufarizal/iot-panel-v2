<?php
return [
    'topic_type_map' => [
        '0002' => 'xyz',
        '0011' => 'xyz',

        '0003' => 'current',
        '0012' => 'current',

        '0004' => 'temp_humidity',
        '0013' => 'temp_humidity',
    ],
    'type_tables' => [
        'xyz' => [
            'table' => 'xyz_readings',
            'fields' => ['x', 'y', 'z'],
        ],
        'current' => [
            'table' => 'current_readings',
            'fields' => ['current'],
        ],
        'temp_humidity' => [
            'table' => 'temp_humidity_readings',
            'fields' => ['temperature', 'humidity'],
        ],
    ],
];
