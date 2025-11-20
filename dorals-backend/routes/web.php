<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect root to patient login
Route::get('/', function () {
    return redirect('/patient/login');
});

// Patient Routes
Route::prefix('patient')->group(function () {
    Route::get('/login', function () {
        return view('patient.login');
    })->name('patient.login');

    Route::get('/register', function () {
        return view('patient.register');
    })->name('patient.register');

    Route::get('/dashboard', function () {
        return view('patient.appointment');
    })->name('patient.dashboard');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/login', function () {
        return view('admin.login');
    })->name('admin.login');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
