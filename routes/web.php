<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;

// ── Public Routes ────────────────────────────────────────────
Route::get('/', function () {
    // If already logged in, redirect to their dashboard
    if (auth()->check()) {
        return redirect()->route(auth()->user()->role.'.dashboard');    
    }
    return redirect()->route('login');
});

// ── Auth Routes (handled by Breeze in auth.php) ─────────────
require __DIR__.'/auth.php';

// ── Profile Routes (from Breeze) ────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── ADMIN Routes ─────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Household management
    Route::get('/households', [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'index'])
        ->name('households.index');
    
    Route::get('/households/{household}', [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'show'])
        ->name('households.show');
    
    Route::post('/households/{household}/approve', [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'approve'])
        ->name('households.approve');
    
    Route::post('/households/{household}/unapprove', [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'unapprove'])
        ->name('households.unapprove');
    
    Route::delete('/households/{household}', [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'destroy'])
        ->name('households.destroy');
        
    Route::post('/households/{household}/qr-generate', 
        [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'generateQrCode'])
        ->name('households.qr.generate');

    Route::get('/households/{household}/qr-download', 
        [\App\Http\Controllers\Admin\AdminHouseholdController::class, 'downloadQrCode'])
        ->name('households.qr.download');

    Route::get('/events/create-quick', function() {
        return view('admin.events.quick-create');
    })->name('events.quick-create');

    Route::post('/events/quick-store', function(\Illuminate\Http\Request $request) {
        $event = \App\Models\DistributionEvent::create([
            'event_name' => $request->event_name,
            'relief_type' => $request->relief_type,
            'event_date' => now(),
            'status' => 'ongoing',
            'created_by' => auth()->id(),
        ]);
        return redirect()->route('admin.dashboard')->with('success', 'Event created and set to ONGOING!');
    })->name('events.quick-store');
});

// ── ENCODER Routes ───────────────────────────────────────────
Route::middleware(['auth', 'role:encoder'])->prefix('encoder')->name('encoder.')->group(function () {

    Route::get('/dashboard', function () {
        return view('encoder.dashboard');
    })->name('dashboard');

    // Household CRUD routes
    Route::resource('households', \App\Http\Controllers\Encoder\EncoderHouseholdController::class);

});

// ── STAFF Routes ─────────────────────────────────────────────
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    Route::get('/scan', [\App\Http\Controllers\Staff\StaffScanController::class, 'index'])
        ->name('scan');
    Route::post('/scan/process', [\App\Http\Controllers\Staff\StaffScanController::class, 'scan'])
        ->name('scan.process');
    Route::post('/scan/confirm', [\App\Http\Controllers\Staff\StaffScanController::class, 'confirm'])
        ->name('scan.confirm');
});

// ── AUDITOR Routes ───────────────────────────────────────────
Route::middleware(['auth', 'role:auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard', function () {
        return view('auditor.dashboard');
    })->name('dashboard');
});