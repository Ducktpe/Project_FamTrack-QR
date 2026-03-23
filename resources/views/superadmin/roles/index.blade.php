@extends('superadmin.superadmin')

@section('title', 'Role Permissions')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <span>Role Permissions</span></div>
        <h2 class="page-title">Role Permissions</h2>
        <p class="page-sub">Overview of privileges assigned to each system role.</p>
    </div>
    <div class="today-label">
        <span>{{ now()->format('l') }}</span>
        <strong>{{ now()->format('F j, Y') }}</strong>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:18px;">
    @foreach($roles as $roleKey => $role)
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <span style="font-size:20px;">{{ $role['icon'] }}</span>
                {{ $role['label'] }}
                <span class="badge badge-{{ $roleKey }}" style="margin-left:4px;">
                    <span class="badge-dot"></span>{{ ucfirst($roleKey) }}
                </span>
            </div>
            <div style="font-size:12px;color:var(--muted);">
                {{ $role['count'] }} {{ Str::plural('account', $role['count']) }}
            </div>
        </div>
        <div style="padding:16px 20px;">
            <p style="margin:0 0 10px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);">
                Allowed Actions
            </p>
            <ul style="margin:0;padding:0;list-style:none;">
                @foreach($role['privileges'] as $priv)
                <li style="display:flex;align-items:flex-start;gap:9px;padding:7px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);">
                    <span style="width:8px;height:8px;border-radius:50%;background:#22a86a;margin-top:4px;flex-shrink:0;display:inline-block;"></span>
                    {{ $priv }}
                </li>
                @endforeach
            </ul>
            <div style="margin-top:16px;">
                <a href="{{ route('superadmin.accounts.index') }}?role={{ $roleKey }}"
                   class="btn btn-ghost btn-sm">👤 View {{ $role['label'] }} Accounts →</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection