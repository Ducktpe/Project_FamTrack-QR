<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class AuditLoginListener
{
    public function handleLogin(Login $event): void
    {
        AuditLog::create([
            'user_id'       => $event->user->id,
            'user_name'     => $event->user->name,
            'action'        => 'login',
            'category'      => 'auth',
            'severity'      => 'medium',
            'model'         => 'User',
            'record_id'     => $event->user->id,
            'affected_name' => $event->user->name,
            'description'   => "{$event->user->name} logged in",
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        AuditLog::create([
            'user_id'       => $event->user->id,
            'user_name'     => $event->user->name,
            'action'        => 'logout',
            'category'      => 'auth',
            'severity'      => 'low',
            'model'         => 'User',
            'record_id'     => $event->user->id,
            'affected_name' => $event->user->name,
            'description'   => "{$event->user->name} logged out",
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
        ]);
    }
}