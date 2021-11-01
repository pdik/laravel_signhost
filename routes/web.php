<?php

use Illuminate\Support\Facades\Route;


use pdik\signhost\Http\Controllers\Signature;

Route::prefix('documents')->group(function () {
    /**
     * User Routes
     */
    Route::group(['middleware' => ['permission:documents.view']], function () {
        /**
         * Show signed document
         */
        Route::get('view/{id}', [Signature::class, 'show'])
            ->name('document.show');

        Route::get('sign/{id}', [Signature::class, 'sign'])
            ->middleware(['permission:documents.sign'])
            ->name('document.sign');
    });

});
