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
    return view('welcome');
})->name('home');

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

    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->name('dashboard');
    
    // Residents list
    Route::get('/residents', [\App\Http\Controllers\Admin\ResidentController::class, 'index'])
        ->name('residents.index');

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

    Route::get('/events/{event}', [\App\Http\Controllers\Admin\AdminDistributionEventController::class, 'show'])
        ->name('events.show');

    Route::post('/events/quick-store', function(\Illuminate\Http\Request $request) {
        $request->validate([
            'event_name'        => 'required|string|max:255',
            'relief_type'       => ['required', 'array', 'min:1'],
            'relief_type.*'     => ['required', 'string', 'max:255'],
            'target_barangay'   => 'required|array|min:1',
            'target_barangay.*' => 'required|string|max:100',
            'started_at'        => 'required|date',
            'ended_at'          => 'required|date|after:started_at',
        ]);

        // Item name labels and units — keyed to match blade name attributes
        $itemMeta = [
            // Dignity Kit
            'feminine_hygiene_wash' => ['name' => 'Feminine Hygiene Wash',           'unit' => 'btl'],
            'sanitary_pads'         => ['name' => 'Sanitary Pads / Napkins',          'unit' => 'pack'],
            'tissue_wipes'          => ['name' => 'Tissue / Wipes',                   'unit' => 'pack'],
            'underwear'             => ['name' => 'Underwear',                         'unit' => 'pcs'],
            // First Aid Kit
            'alcohol'               => ['name' => 'Alcohol',                           'unit' => 'btl'],
            'bandaid'               => ['name' => 'Band-aid',                          'unit' => 'box'],
            'bandage'               => ['name' => 'Bandage',                           'unit' => 'roll'],
            'betadine'              => ['name' => 'Betadine',                          'unit' => 'btl'],
            'elastic_bandage'       => ['name' => 'Elastic Bandage',                  'unit' => 'roll'],
            'emergency_medicine'    => ['name' => 'Emergency Medicine / CAMPOLAS',    'unit' => 'pcs'],
            'gauze_pad'             => ['name' => 'Gauze Pad',                         'unit' => 'pcs'],
            'gauze_roll'            => ['name' => 'Gauze Roll',                        'unit' => 'roll'],
            'medical_tape'          => ['name' => 'Medical Tape',                      'unit' => 'roll'],
            // Food Pack
            'canned_goods'          => ['name' => 'Canned Goods (Corned Beef / Sardines)', 'unit' => 'cans'],
            'coffee'                => ['name' => 'Coffee',                            'unit' => 'pack'],
            'instant_noodles'       => ['name' => 'Instant Noodles',                  'unit' => 'pcs'],
            'rice'                  => ['name' => 'Rice',                              'unit' => 'kg'],
            // Hygiene Kit
            'bar_soap'              => ['name' => 'Bar Soap',                          'unit' => 'bars'],
            'bucket'                => ['name' => 'Bucket',                            'unit' => 'pcs'],
            'deodorant'             => ['name' => 'Deodorant',                         'unit' => 'pcs'],
            'dipper'                => ['name' => 'Dipper (Tabo)',                     'unit' => 'pcs'],
            'shampoo'               => ['name' => 'Shampoo',                           'unit' => 'btl'],
            'toothbrush'            => ['name' => 'Toothbrush',                        'unit' => 'pcs'],
            'toothpaste'            => ['name' => 'Toothpaste',                        'unit' => 'tube'],
            'towel'                 => ['name' => 'Towel / Face Towel',               'unit' => 'pcs'],
        ];

        // Build relief_items — only include checked items
        $reliefItems = [];
        foreach ($request->input('items', []) as $key => $data) {
            if (!empty($data['included'])) {
                $reliefItems[$key] = [
                    'name' => $itemMeta[$key]['name'] ?? $key,
                    'qty'  => isset($data['qty']) && $data['qty'] !== '' ? (float) $data['qty'] : 1,
                    'unit' => $itemMeta[$key]['unit'] ?? '',
                ];
            }
        }

        // Handle cash aid separately
        if ($request->filled('cash_amount')) {
            $reliefItems['cash_aid'] = [
                'name' => 'Cash Aid',
                'qty'  => (float) $request->cash_amount,
                'unit' => 'PHP',
            ];
        }

        $event = \App\Models\DistributionEvent::create([
            'event_name'      => $request->event_name,
            'relief_type'     => $request->relief_type,      // array → JSON cast
            'relief_items'    => !empty($reliefItems) ? $reliefItems : null,
            'target_barangay' => $request->target_barangay,  // array → JSON cast
            'event_date'      => $request->event_date ?? now()->toDateString(),
            'description'     => $request->goods_detail,
            'status'          => 'upcoming',
            'started_at'      => $request->started_at,
            'ended_at'        => $request->ended_at,
            'created_by'      => auth()->id(),
        ]);

        \App\Models\AuditLog::log('created_distribution_event', [
            'model'         => 'DistributionEvent',
            'record_id'     => $event->id,
            'affected_name' => $event->event_name,
            'description'   => "Created distribution event \"{$event->event_name}\"",
            'new_values'    => $event->toArray(),
        ]);

        return redirect()->route('admin.distribution.logs')
            ->with('success', 'Event created successfully!');
    })->name('events.quick-store');

    // Distribution Logs (Admin)
    Route::get('/distribution/logs', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'index'])
        ->name('distribution.logs');

    Route::get('/distribution/events', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'eventsList'])
        ->name('distribution.events.list');

    Route::get('/distribution/events/{event}/households', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'eventHouseholds'])
        ->name('distribution.events.households');

    Route::get('/distribution/events/{event}/export-csv', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'exportEventCsv'])
        ->name('distribution.events.export.csv');

    Route::get('/distribution/events/{event}/export-pdf', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'exportEventPdf'])
        ->name('distribution.events.export.pdf');
    
    Route::get('/distribution/events/{event}/export-xlsx', [\App\Http\Controllers\Admin\AdminDistributionLogController::class, 'exportEventXlsx'])
        ->name('distribution.events.export.xlsx');
    
    Route::post('/distribution/events/{event}/start', [\App\Http\Controllers\Admin\AdminDistributionEventController::class, 'start'])
        ->name('distribution.events.start');

    Route::post('/distribution/events/{event}/end', [\App\Http\Controllers\Admin\AdminDistributionEventController::class, 'end'])
        ->name('distribution.events.end');

    Route::post('/distribution/events/{event}/cancel', [\App\Http\Controllers\Admin\AdminDistributionEventController::class, 'cancel'])
        ->name('distribution.events.cancel');

    // Audit Trail
    Route::get('/audit-trail', [\App\Http\Controllers\AuditTrailController::class, 'index'])
        ->name('audit.trail');
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
    Route::get('/scan/history', [\App\Http\Controllers\Staff\StaffScanController::class, 'scanHistory'])
        ->name('scan.history');

    Route::get('/active-event', function () {
        $activeEvents = \App\Models\DistributionEvent::ongoing()
            ->with('creator')
            ->latest('started_at')
            ->get();

        $activeEvents->each(function ($event) {
            $event->recent_scans = $event->logs()
                ->where('distributed_by', auth()->id())
                ->with('household')
                ->latest()
                ->take(20)
                ->get();

            $event->scan_count = $event->logs()->count();
        });

        return view('staff.active-event', compact('activeEvents'));
    })->name('active-event');
});
// ── AUDITOR Routes ───────────────────────────────────────────
Route::middleware(['auth', 'role:auditor'])->prefix('auditor')->name('auditor.')->group(function () {
    Route::get('/dashboard', function () {
        return view('auditor.dashboard');
    })->name('dashboard');

    Route::get('/family-profiles', [\App\Http\Controllers\Auditor\AuditorController::class, 'familyProfiles'])
        ->name('family-profiles');

    // Auditor: distribution logs (read-only)
    Route::get('/distribution/logs', [\App\Http\Controllers\Auditor\AuditorDistributionLogController::class, 'index'])
        ->name('distribution.logs');

    Route::get('/distribution/events', [\App\Http\Controllers\Auditor\AuditorDistributionLogController::class, 'eventsList'])
        ->name('distribution.events.list');

    Route::get('/distribution/events/{event}/households', [\App\Http\Controllers\Auditor\AuditorDistributionLogController::class, 'eventHouseholds'])
        ->name('distribution.events.households');

    Route::get('/households', [\App\Http\Controllers\Auditor\AuditorHouseholdController::class, 'index'])
    ->name('households.index');

    Route::get('/households/{household}', [\App\Http\Controllers\Auditor\AuditorHouseholdController::class, 'show'])
    ->name('households.show');

    // Audit Trail (read-only)
    Route::get('/audit-trail', [\App\Http\Controllers\Auditor\AuditTrailController::class, 'index'])
        ->name('audit.trail');
});