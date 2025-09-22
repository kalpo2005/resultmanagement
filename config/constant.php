<?php

return [

// ============================ For the api url ===================

//  'api_base_url' => env('API_BASE_URL', 'http://127.0.0.1:8000/api'),  // local
//  'api_base_url' => env('API_BASE_URL', 'http://10.120.215.10:8000/api'),  // local
 'api_base_url' => env('API_BASE_URL', 'https://result.studymotion.in/api'),  // Live



    'allowed_login_roles' => ['administrate', 'manager'], // only these roles can log in
    'jwt_ttl' => 1, // in minutes (1 hour)
];
