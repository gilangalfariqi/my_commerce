<?php

use App\Http\Controllers\Webhook\MidtransWebhookController;
use Illuminate\Support\Facades\Route;

// Webhook routing. Middleware and prefix are applied in bootstrap/app.php
Route::post('/webhook/midtrans', [MidtransWebhookController::class, 'handle'])->name('webhook.midtrans');
