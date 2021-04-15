<?php

return [
    'api_endpoint' => env('TESTCENTER_PROD_API_ENDPOINT'),
    'partner_access_token' => env('TESTCENTER_PROD_PARTNER_ACCESS_TOKEN'),
    'partner_secret_key' => env('TESTCENTER_PROD_PARTNER_SECRET_KEY'),
    'client_id' => env('TESTCENTER_CLIENT_ID'),
    'client_url' => env('TESTCENTER_PROD_CLIENT_URL', 'https://app.testcenter.vn'),
];