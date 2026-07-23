<?php

return [
    'name' => env('RESTAURANT_NAME', 'LOS TRONCOS RESTO BAR'),
    'address' => env('RESTAURANT_ADDRESS', 'Av. San Martin xxxx'),
    'city' => env('RESTAURANT_CITY', 'Puerto Rico - Misiones'),
    'phone' => env('RESTAURANT_PHONE', '3743-611895'),
    'instagram' => env('RESTAURANT_INSTAGRAM', '@lostroncosrestobar'),
    'whatsapp' => env('RESTAURANT_WHATSAPP', '3743-611895'),

    'ticket' => [
        'width' => (int) env('THERMAL_TICKET_WIDTH', 26),
        'printer' => env('THERMAL_PRINTER_NAME'),
    ],
];
