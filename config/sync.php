<?php

return [
    'api_token' => env('SYNC_API_TOKEN', ''),
    'allowed_ips' => env('SYNC_ALLOWED_IPS', ''),       // Comma-separated, kosong = semua boleh
    'max_batch_size' => (int) env('SYNC_MAX_BATCH_SIZE', 100),
    'rate_limit' => (int) env('SYNC_RATE_LIMIT', 60),   // Request per menit per IP
];
