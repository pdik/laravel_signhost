### Pdik laravel_signhost package
A simple package to use signhost for your laravel app.

###### Step 1.
`composer require pdik/laravel_signhost`

###### Step 2.
`php artisan vendor:publish --tag laravel_signhost-config`

###### Step 3.
Fill in app_key, secret_key, user_token. 
in app/config/laravel_signhost-config.php

###### step 4
Use the Trait `Hassignature` for the model you wanted to be able to have a signuture
