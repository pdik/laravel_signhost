<?php
use Illuminate\Support\Facades\Route;
use Pdik\laravel_signhost\Http\Controllers\Signature;
/**
 * Show signed document
 */
Route::get('/customers/documents/{id}', [Signature::class, 'show']);