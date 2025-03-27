<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Route;

// Page d'accueil
Route::get('/', [ProductController::class, 'index'])->name('home');

// Routes pour les produits
Route::resource('products', ProductController::class);

// Route pour le chatbot IA (POST seulement)
Route::post('/products/{product}/ask', [AIController::class, 'askQuestion'])
    ->name('products.ask');