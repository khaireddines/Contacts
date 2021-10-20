<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('contacts');
});
/** Authentication Routes */
Route::get('getClientCode', [ContactController::class, 'getClientCode'])->name('getClientCode');
Route::get('ApiAccessTokens', [ContactController::class, 'storeAccessTokens']);
/** Authentication Routes */

/** api Routes */
Route::get('contacts', [ContactController::class, 'allContacts']);
Route::post('contacts', [ContactController::class, 'createContact']);
Route::patch('contacts/{contactId}', [ContactController::class, 'updateContact']);
Route::delete('contacts/{contactId}', [ContactController::class, 'deleteContact']);

