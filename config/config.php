<?php
return [
    /**
     * Api key
     * Signhost
     */

    'app_key'=> env('SIGNHOST_KEY'),

    /**
     * Secret keys
     */
    'secret_key' => env('SIGNHOST_SECRET'),
    'user_token' => env('SIGNHOST_USER'),

    'api_postback'=> config('app.url','localhost').'/'.config('signhost_postback'),
    'api_endpoint'=> 'https://api.signhost.com/api',

    'signRequestMessage' => '',
    'SendSignRequest' => true,
    'sendSignConfirmation' => true,
    'sendEmailNotifications' => true,
];