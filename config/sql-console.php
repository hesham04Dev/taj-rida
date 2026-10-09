<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SQL Console Kill Switch
    |--------------------------------------------------------------------------
    | Set SQL_CONSOLE_ENABLED=false in .env to disable this feature entirely,
    | even for the allowed super-admin email.
    */
    'enabled' => env('SQL_CONSOLE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Allowed Super-Admin Email
    |--------------------------------------------------------------------------
    | Only the user with this exact email can access the SQL console.
    | Leave empty to deny everyone (useful as a secondary kill switch).
    */
    'allowed_email' => env('SQL_CONSOLE_ALLOWED_EMAIL', ''),

    /*
    |--------------------------------------------------------------------------
    | Write Mode
    |--------------------------------------------------------------------------
    | When false (default), only SELECT/SHOW/DESCRIBE/EXPLAIN are allowed.
    | Set SQL_CONSOLE_WRITE_MODE=true to also allow INSERT/UPDATE/DELETE/DDL.
    | Use with extreme caution in production.
    */
    'write_mode' => env('SQL_CONSOLE_WRITE_MODE', false),
];
