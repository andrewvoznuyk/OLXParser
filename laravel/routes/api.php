<?php

use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\CheckProductUrl;
use Illuminate\Support\Facades\Route;

Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->middleware(CheckProductUrl::class);
