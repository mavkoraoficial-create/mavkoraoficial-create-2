<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;

// Public Area Routes
Route::get('/', function () {
    return view('public.index');
})->name('public.home');

Route::get('/portfolio', function () {
    return view('public.portfolio');
})->name('public.portfolio');

// Formulario de cotización del modal. Limitado por IP para frenar el spam.
Route::post('/cotizacion', [LeadController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('public.quote');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Area Routes
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Leads: los que llegan del formulario web y los que captura el bot.
    Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [AdminLeadController::class, 'update'])->name('leads.update');

    // Conversaciones de WhatsApp.
    Route::get('/conversaciones', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversaciones/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::put('/conversaciones/{conversation}', [ConversationController::class, 'update'])->name('conversations.update');

    // Reuniones agendadas por el bot.
    Route::get('/citas', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::put('/citas/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
});
