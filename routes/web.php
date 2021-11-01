<?php

use Illuminate\Support\Facades\Route;
use pdik\laravel_signhost\Http\Controllers\Signature;

Route::prefix('signature')->group(function () {
    /**
     * User Routes
     */
    Route::group(['middleware' => ['permission:documents.view']], function () {
        /**
         * Show signed document
         */
        Route::get('view/{id}', [Signature::class, 'show']);
        Route::get('sign/{id}', [Signature::class, 'sign']);
    });

});
