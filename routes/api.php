<?php

use App\Http\Controllers\Api\WhatsAppWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// WhatsApp Webhook Routes
Route::prefix('webhooks/whatsapp')->group(function () {
    Route::get('/', [WhatsAppWebhookController::class, 'verify'])->withoutMiddleware(['throttle:api']);
    Route::post('/', [WhatsAppWebhookController::class, 'handle'])->withoutMiddleware(['throttle:api']);
});

// Authenticated API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Patient endpoints
    Route::prefix('patient')->group(function () {
        Route::get('/appointments', function () {
            return auth()->user()->patient->appointments()->with(['clinic', 'queueEntry'])->latest()->get();
        });
        
        Route::get('/prescriptions', function () {
            return auth()->user()->patient->prescriptions()->with(['doctor', 'medicines.medicine'])->latest()->get();
        });
        
        Route::get('/queue/{appointmentId}', function ($appointmentId) {
            $appointment = auth()->user()->patient->appointments()->findOrFail($appointmentId);
            
            return $appointment->queueEntry()->with('clinic')->first();
        });
    });
    
    // Pharmacy endpoints
    Route::prefix('pharmacies')->middleware('throttle:api')->group(function () {
        Route::get('/nearby', function (Request $request) {
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 5); // km
            
            // Simple distance calculation (Haversine formula can be added later)
            return \App\Models\Pharmacy::query()
                ->where('approval_status', 'approved')
                ->where('is_active', true)
                ->get();
        });
        
        Route::post('/{pharmacy}/request-delivery', function (\App\Models\Pharmacy $pharmacy, Request $request) {
            // Implementation for delivery request
            return response()->json(['message' => 'Delivery request received']);
        });
    });
    
    // AI endpoints for clinics
    Route::prefix('ai')->middleware(['throttle:ai'])->group(function () {
        Route::post('/transcribe', function (Request $request) {
            // Implementation for audio transcription
            return response()->json(['message' => 'Transcription started']);
        });
        
        Route::post('/extract-notes', function (Request $request) {
            // Implementation for note extraction
            return response()->json(['message' => 'Extraction completed']);
        });
    });
});
