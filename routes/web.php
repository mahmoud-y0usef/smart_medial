<?php

use App\Http\Controllers\QueueTrackingController;
use App\Livewire\Form;
use Illuminate\Support\Facades\Route;

Route::get('form', Form::class);

Route::redirect('login-redirect', 'login')->name('login');

// Patient Welcome Page - WhatsApp Booking
Route::get('/', function () {
    return view('patient-welcome');
})->name('home');

Route::get('/book', function () {
    return view('patient-welcome');
})->name('patient.book');

// Queue tracking
Route::get('/queue/track/{appointment}', [QueueTrackingController::class, 'track'])
    ->name('queue.track');

Route::get('/queue/status/{appointment}', [QueueTrackingController::class, 'status'])
    ->name('queue.status');
