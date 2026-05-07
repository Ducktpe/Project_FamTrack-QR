<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $superAdminIds = User::where('role', 'super_admin')->pluck('id');

        $query = AuditLog::with('user')->latest('created_at');

        // When filtering by severity, include ALL users so super_admin
        // high-severity actions are visible to the auditor.
        if (!$request->filled('severity')) {
            $query->where(function ($q) use ($superAdminIds) {
                $q->whereNotIn('user_id', $superAdminIds)
                  ->orWhereNull('user_id');
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('user_name',      'like', "%{$s}%")
                  ->orWhere('action',       'like', "%{$s}%")
                  ->orWhere('description',  'like', "%{$s}%")
                  ->orWhere('affected_name','like', "%{$s}%")
                  ->orWhere('model',        'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $cat = $request->category;

            // 'auth', 'household', 'distribution' are real DB category values — filter directly.
            // 'qr' and 'general' are derived from action names in the blade (not stored in DB),
            // so we filter by action keywords instead.
            if ($cat === 'qr') {
                $query->where(function ($q) {
                    $q->where('action', 'like', '%qr%')
                      ->orWhere('action', 'like', '%scan%')
                      ->orWhere('action', 'like', '%serial%');
                });
            } elseif ($cat === 'general') {
                // General = everything that doesn't match the other four types
                $query->where(function ($q) {
                    $q->where('category', 'general')
                      ->orWhere(function ($q2) {
                          // Fallback: action doesn't match any known keyword group
                          $q2->whereNotIn('category', ['auth', 'household', 'distribution'])
                             ->where('action', 'not like', '%qr%')
                             ->where('action', 'not like', '%scan%')
                             ->where('action', 'not like', '%serial%')
                             ->where('action', 'not like', '%login%')
                             ->where('action', 'not like', '%logout%')
                             ->where('action', 'not like', '%register%')
                             ->where('action', 'not like', '%password%')
                             ->where('action', 'not like', '%household%')
                             ->where('action', 'not like', '%member%')
                             ->where('action', 'not like', '%family%')
                             ->where('action', 'not like', '%distribution%')
                             ->where('action', 'not like', '%event%')
                             ->where('action', 'not like', '%ayuda%')
                             ->where('action', 'not like', '%relief%')
                             ->where('action', 'not like', '%distributed%');
                      });
                });
            } else {
                // auth / household / distribution — stored directly in category column
                $query->where('category', $cat);
            }
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50)->withQueryString();

        // Summary counts — super_admin excluded, null user_id (System) kept
        $base = AuditLog::where(function ($q) use ($superAdminIds) {
            $q->whereNotIn('user_id', $superAdminIds)
              ->orWhereNull('user_id');
        });

        $totalLogs         = (clone $base)->count();
        $authCount         = (clone $base)->where('category', 'auth')->count();
        $householdCount    = (clone $base)->where('category', 'household')->count();
        $distributionCount = (clone $base)->where('category', 'distribution')->count();
        $highSeverityCount = (clone $base)->where('severity', 'high')->count();

        return view('auditor.audit-trail', compact(
            'logs',
            'totalLogs',
            'authCount',
            'householdCount',
            'distributionCount',
            'highSeverityCount'
        ));
    }
}