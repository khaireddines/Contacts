<?php

use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::prefix('v1')->middleware([])->group(function ( )
{
    /** Authentication Routes */
    Route::get('getClientCode',[ContactController::class,'getClientCode']);
    Route::get('ApiAccessTokens',[ContactController::class,'storeAccessTokens']);
    /** Authentication Routes */

    /** api Routes */
    Route::get('contacts',[ContactController::class,'allContacts']);
    Route::post('contacts',[ContactController::class,'createContact']);
    Route::patch('contacts/{contactId}',[ContactController::class,'updateContact']);
    Route::delete('contacts/{contactId}',[ContactController::class,'deleteContact']);
});
