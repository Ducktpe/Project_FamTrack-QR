@extends('superadmin.superadmin')

@section('title', 'Account Details')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">
            Super Admin &rsaquo;
            <a href="{{ route('superadmin.accounts.index') }}" style="color:var(--blue-acc);text-decoration:none;">Account Management</a>
            &rsaquo; <span>Account Details</span>
        </div>
        <h2 class="page-title">Account Details</h2>
        <p class="page-sub">Full profile information for this system account.</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-ghost">← Back</a>

        @if($user->is_setup_complete)
        <form method="POST" action="{{ route('superadmin.accounts.toggle', $user) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $user->status === 'active' ? 'btn-ghost' : 'btn-primary' }}"
                onclick="return confirm('{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }} this account?')">
                {{ $user->status === 'active' ? '🔒 Deactivate' : '🔓 Activate' }}
            </button>
        </form>
        @endif

        @if(! $user->is_setup_complete)
        <form method="POST" action="{{ route('superadmin.accounts.resend', $user) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary"
                onclick="return confirm('Resend invite to {{ $user->personal_email }}?')">
                📧 Resend Invite
            </button>
        </form>
        @endif

        <form method="POST" action="{{ route('superadmin.accounts.destroy', $user) }}" class="d-inline">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-ghost" style="color:var(--red);border-color:var(--red);"
                onclick="return confirm('Archive account {{ addslashes($user->email) }}?')">
                🗑️ Archive
            </button>
        </form>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:22px;">

    {{-- LEFT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:22px;">

        {{-- ACCOUNT IDENTITY --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot blue"></span> Account Identity</div>
                <span class="badge badge-{{ $user->role }}">
                    <span class="badge-dot"></span>{{ ucfirst($user->role) }}
                </span>
            </div>
            <div style="padding:20px;">

                {{-- Avatar --}}
                <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                    <div style="width:56px;height:56px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--gold);font-size:22px;flex-shrink:0;">
                        {{ strtoupper(substr($user->is_setup_complete ? $user->name : '?', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:var(--navy);">
                            @if($user->is_setup_complete)
                                {{ $user->name }}
                            @else
                                <span style="color:var(--muted);font-style:italic;">Not set up yet</span>
                            @endif
                        </div>
                        <div style="font-size:12px;color:var(--muted);">{{ $user->roleLabel() }}</div>
                    </div>
                </div>

                {{-- Login Email --}}
                <div style="margin-bottom:14px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:4px;">🔑 Login Email</div>
                    <div style="font-family:'Courier New',monospace;font-size:14px;font-weight:700;color:var(--navy);background:#f0f3f9;padding:8px 12px;border-radius:6px;">
                        {{ $user->email }}
                    </div>
                </div>

                {{-- Account Code --}}
                <div style="margin-bottom:14px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:4px;">🏷️ Account Code</div>
                    <div style="font-size:14px;font-weight:600;color:var(--text);">{{ $user->account_code ?? '—' }}</div>
                </div>

                {{-- Personal Gmail --}}
                <div style="margin-bottom:14px;">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:4px;">📧 Personal Gmail</div>
                    <div style="font-size:14px;color:var(--text);">{{ $user->personal_email ?? '—' }}</div>
                </div>

            </div>
        </div>

        {{-- STATUS INFO --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot green"></span> Status Information</div>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Account Status</span>
                    <span class="badge badge-{{ $user->status }}">
                        <span class="badge-dot"></span>{{ ucfirst($user->status) }}
                    </span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Setup Status</span>
                    @if($user->is_setup_complete)
                        <span class="badge badge-active"><span class="badge-dot"></span> Complete</span>
                    @elseif($user->invite_expires_at?->isFuture())
                        <span class="badge badge-pending"><span class="badge-dot"></span> Invite Sent</span>
                    @else
                        <span class="badge badge-inactive"><span class="badge-dot"></span> Invite Expired</span>
                    @endif
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Last Login</span>
                    <span style="font-size:13px;color:var(--text);font-weight:600;">
                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never logged in' }}
                    </span>
                </div>

                @if(! $user->is_setup_complete && $user->invite_expires_at)
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Invite Expires</span>
                    <span style="font-size:13px;color:{{ $user->invite_expires_at->isFuture() ? 'var(--green)' : 'var(--red)' }};font-weight:600;">
                        {{ $user->invite_expires_at->format('M d, Y h:i A') }}
                        ({{ $user->invite_expires_at->diffForHumans() }})
                    </span>
                </div>
                @endif

            </div>
        </div>

    </div>

    {{-- RIGHT COLUMN --}}
    <div style="display:flex;flex-direction:column;gap:22px;">

        {{-- ACCOUNT HISTORY --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot orange"></span> Account History</div>
            </div>
            <div style="padding:20px;display:flex;flex-direction:column;gap:14px;">

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Invited By</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">
                        {{ $user->creator?->name ?? 'System' }}
                    </span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Date Invited</span>
                    <span style="font-size:13px;font-weight:600;color:var(--text);">
                        {{ $user->created_at->format('M d, Y h:i A') }}
                    </span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-bottom:12px;border-bottom:1px solid var(--border);">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Account Created</span>
                    <span style="font-size:13px;color:var(--muted);">
                        {{ $user->created_at->diffForHumans() }}
                    </span>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:var(--muted);font-weight:600;">Last Updated</span>
                    <span style="font-size:13px;color:var(--muted);">
                        {{ $user->updated_at->diffForHumans() }}
                    </span>
                </div>

            </div>
        </div>

        {{-- ROLE PRIVILEGES --}}
        <div class="panel">
            <div class="panel-header">
                <div class="panel-title"><span class="panel-dot purple"></span> Role Privileges</div>
                <span class="badge badge-{{ $user->role }}">{{ $user->roleLabel() }}</span>
            </div>
            <div style="padding:16px 20px;">
                <ul style="margin:0;padding:0;list-style:none;">
                    @foreach($user->rolePrivileges() as $priv)
                    <li style="display:flex;align-items:flex-start;gap:9px;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);">
                        <span style="width:8px;height:8px;border-radius:50%;background:#22a86a;margin-top:4px;flex-shrink:0;display:inline-block;"></span>
                        {{ $priv }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

</div>

{{-- RECENT AUDIT LOGS --}}
<div class="panel" style="margin-top:22px;">
    <div class="panel-header">
        <div class="panel-title"><span class="panel-dot blue"></span> Recent Activity Logs</div>
        <span style="font-size:12px;color:var(--muted);">Last 10 entries</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auditLogs as $log)
                <tr>
                    <td style="font-size:11px;color:var(--muted);white-space:nowrap;">
                        {{ $log->created_at->format('M d, Y') }}<br/>
                        <strong>{{ $log->created_at->format('h:i A') }}</strong>
                    </td>
                    <td>
                        @php
                            $cls = match(true) {
                                str_starts_with($log->action, 'created'),
                                str_starts_with($log->action, 'sent'),
                                str_starts_with($log->action, 'restored') => 'trail-create',
                                str_starts_with($log->action, 'updated'),
                                str_starts_with($log->action, 'toggled')  => 'trail-update',
                                str_starts_with($log->action, 'deleted'),
                                str_starts_with($log->action, 'permanently') => 'trail-delete',
                                str_starts_with($log->action, 'login'),
                                str_starts_with($log->action, 'account') => 'trail-login',
                                default => 'trail-system',
                            };
                        @endphp
                        <span class="trail-type {{ $cls }}">{{ $log->action }}</span>
                    </td>
                    <td style="font-size:12px;max-width:320px;">{{ $log->description }}</td>
                    <td><span class="ip-code">{{ $log->ip_address ?? '—' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-state">No activity logs found for this account.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection