<?php

return [
    'packages' => [
        //List of packages, this needs to be an array, format is prorperly available in read me/documentation.
    ],

    /*
    * Application configurations
    */

    'old_config' => false,

    // Generates the salt key for encryption.

    'salt_key' => '',

    /*
     * Enviornment: Production or development cycle configuration
     * 'dev' => development, 'prod' => production
     * Encryption doesn't work on development mode
     */
    'app_env' => 'dev',
];
