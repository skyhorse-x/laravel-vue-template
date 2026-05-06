<?php

return [
    'secret' => env('JWT_SECRET'),
    'expire' => env('JWT_EXPIRE', 7200),
    'issuer' => env('APP_URL', 'http://localhost'),
    'algorithm' => 'HS256',
];
