<?php

return [
    'hourly_rates' => [
        'lo' => (int) env('BIDA_HOURLY_RATE_LO', 50000),
        'phang' => (int) env('BIDA_HOURLY_RATE_PHANG', 50000),
        'lo_vip' => (int) env('BIDA_HOURLY_RATE_LO_VIP', 80000),
        'phang_vip' => (int) env('BIDA_HOURLY_RATE_PHANG_VIP', 80000),
    ],
];
