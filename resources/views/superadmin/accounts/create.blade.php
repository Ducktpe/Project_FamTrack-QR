@extends('superadmin.superadmin')

@section('title', 'Send Account Invite')

@push('styles')
<style>
/* ── Info step list ── */
.step-list { list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px; }
.step-item { display:flex; align-items:flex-start; gap:12px; font-size:13px; color:var(--gray-600); }
.step-num {
    width:22px; height:22px; border-radius:50%; background:var(--blue); color:#fff;
    font-size:10px; font-weight:800; display:flex; align-items:center; justify-content:center;
    flex-shrink:0; margin-top:1px;
}

/* ── Email preview box ── */
.email-preview {
    background: var(--blue-pale); border: 1px solid var(--blue-pale2);
    border-left: 4px solid var(--blue); border-radius: 0 6px 6px 0;
    padding: 12px 16px; margin-top: 4px;
}
.email-preview .ep-label {
    font-size: 10px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1px; color: var(--blue-light); margin-bottom: 5px;
}
.email-preview .ep-value {
    font-family: 'Courier New', monospace; font-size: 14px;
    font-weight: 700; color: var(--blue-dark);
    word-break: break-all;
}
.email-preview .ep-note {
    font-size: 11px; color: var(--blue-light); margin-top: 4px;
}

/* ── Role card (privilege preview) ── */
.role-card-label {
    font-size: 12px; font-weight: 700; padding: 6px 12px;
    border-radius: 5px; margin-bottom: 14px; display: inline-block;
}
.role-priv-list { list-style:none; padding:0; margin:0; }
.role-priv-item {
    display:flex; align-items:flex-start; gap:9px;
    padding:7px 0; border-bottom:1px solid var(--gray-200);
    font-size:13px; color:var(--gray-700);
}
.role-priv-item:last-child { border-bottom:none; }
.rp-dot {
    width:7px; height:7px; border-radius:50%;
    background:var(--green); flex-shrink:0; margin-top:4px;
}
.role-empty {
    text-align:center; padding:24px 0;
    color:var(--gray-400); font-size:13px; font-style:italic;
}

/* ── Create page two-col layout ── */
.create-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 18px;
    align-items: start;
}
.role-preview-panel {
    position: sticky;
    top: calc(var(--topbar-h) + var(--header-h) + 16px);
}

/* ── Mobile ── */
@media (max-width: 900px) {
    .create-layout { grid-template-columns: 1fr; }
    .role-preview-panel { position: static; }
}
@media (max-width: 640px) {
    .step-item { font-size: 12px; }
    .email-preview .ep-value { font-size: 12px; }
}
</style>
@endpush

@section('content')

{{-- ══ PAGE TITLE ══ --}}
<div class="page-titlebar">
    <div>
        <div class="page-breadcrumb">
            Super Admin <span class="bc-sep">›</span>
            <a href="{{ route('superadmin.accounts.index') }}" class="bc-link" style="text-decoration:none;">Account Management</a>
            <span class="bc-sep">›</span>
            <span class="bc-link">Send Invite</span>
        </div>
        <div class="page-h1">Send Account Invite</div>
        <div class="page-sub">Enter the recipient's Gmail and assign their role. The system generates their login email and sends a 24-hour setup link.</div>
    </div>
    <div class="page-date-badge">
        <span class="day">{{ now()->format('l') }}</span>
        <span class="full-date">{{ now()->format('F j, Y') }}</span>
    </div>
</div>

<div class="create-layout">

    {{-- ══ FORM PANEL ══ --}}
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot yellow"></span>
                Invite Details
            </div>
            <span class="super-only-badge">Super Admin Only</span>
        </div>

        <form method="POST" action="{{ route('superadmin.accounts.store') }}" style="padding:22px;">
            @csrf

            {{-- Gmail --}}
            <div class="form-group full" style="margin-bottom:18px;">
                <label for="personal_email">
                    Recipient's Gmail Address <span class="req">*</span>
                </label>
                <input type="email" id="personal_email" name="personal_email"
                       value="{{ old('personal_email') }}"
                       placeholder="e.g. juandelacruz@gmail.com" required/>
                <span style="font-size:11px; color:var(--gray-400); margin-top:4px; display:block;">
                    The invite and setup link will be sent to this address.
                </span>
                @error('personal_email')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group full" style="margin-bottom:18px;">
                <label for="role">Assign Role <span class="req">*</span></label>
                <select id="role" name="role" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role…</option>
                    <option value="admin"   {{ old('role')=='admin'   ? 'selected':'' }}>Administrator</option>
                    <option value="encoder" {{ old('role')=='encoder' ? 'selected':'' }}>Encoder</option>
                    <option value="staff"   {{ old('role')=='staff'   ? 'selected':'' }}>Staff</option>
                    <option value="auditor" {{ old('role')=='auditor' ? 'selected':'' }}>Auditor</option>
                </select>
                @error('role')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            {{-- Generated email preview --}}
            <div id="emailPreviewBox" style="display:none; margin-bottom:20px;">
                <div class="email-preview">
                    <div class="ep-label">Generated Login Email (preview)</div>
                    <div class="ep-value" id="emailPreviewValue"></div>
                    <div class="ep-note">This will appear in the invite email. The user logs in with this address.</div>
                </div>
            </div>

            {{-- What happens next info box --}}
            <div style="background:var(--gray-50); border:1px solid var(--gray-200); border-radius:6px; padding:16px 18px; margin-bottom:24px;">
                <div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:1px; color:var(--gray-400); margin-bottom:12px;">
                    What happens after sending
                </div>
                <ul class="step-list">
                    <li class="step-item"><span class="step-num">1</span>System generates a unique login email (e.g. <em>adminA001@barangay.gov.ph</em>)</li>
                    <li class="step-item"><span class="step-num">2</span>Invite sent to their Gmail with a <strong>24-hour setup link</strong></li>
                    <li class="step-item"><span class="step-num">3</span>User enters their full name and creates their own password</li>
                    <li class="step-item"><span class="step-num">4</span>Account activates — they log in with the generated email</li>
                </ul>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-cancel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back
                </a>
                <button type="submit" class="btn btn-super">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Send Invite
                </button>
            </div>
        </form>
    </div>

    {{-- ══ ROLE PREVIEW PANEL ══ --}}
    <div class="panel role-preview-panel">
        <div class="panel-header">
            <div class="panel-title">
                <span class="panel-dot orange"></span>
                Role Privileges
            </div>
        </div>
        <div style="padding:18px;" id="rolePreviewBody">
            <p class="role-empty">Select a role to preview its privileges.</p>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
const rolePreviews = {
    admin: {
        label: 'Administrator', color: '#1e40af', bg: '#dbeafe',
        prefix: 'admin',
        items: [
            'Manage households and residents',
            'Approve or reject household records',
            'Create and manage distribution events',
            'Generate and download QR codes',
            'View distribution logs and export reports',
            'View audit trail logs',
        ]
    },
    encoder: {
        label: 'Encoder', color: '#166534', bg: '#dcfce7',
        prefix: 'encoder',
        items: [
            'Encode new household records',
            'Edit existing household records',
            'View assigned household data',
        ]
    },
    staff: {
        label: 'Staff', color: '#92400e', bg: '#fef3c7',
        prefix: 'staff',
        items: [
            'Scan QR codes during relief distribution',
            'View active distribution events',
            'View scan history',
        ]
    },
    auditor: {
        label: 'Auditor', color: '#1e3a8a', bg: '#eff6ff',
        prefix: 'auditor',
        items: [
            'View household and family profiles (read-only)',
            'View distribution logs',
            'View full audit trail',
        ]
    },
};

const roleCounts = {
    admin:   {{ \App\Models\User::where('role','admin')->whereNotNull('account_code')->count() }},
    encoder: {{ \App\Models\User::where('role','encoder')->whereNotNull('account_code')->count() }},
    staff:   {{ \App\Models\User::where('role','staff')->whereNotNull('account_code')->count() }},
    auditor: {{ \App\Models\User::where('role','auditor')->whereNotNull('account_code')->count() }},
};

function getNextCode(count) {
    return String.fromCharCode(65 + Math.floor(count / 999)) + String((count % 999) + 1).padStart(3, '0');
}

document.getElementById('role').addEventListener('change', function () {
    const role = this.value;
    const p    = rolePreviews[role];
    const body = document.getElementById('rolePreviewBody');
    const box  = document.getElementById('emailPreviewBox');

    if (!p) {
        box.style.display = 'none';
        body.innerHTML = '<p class="role-empty">Select a role.</p>';
        return;
    }

    // Show email preview
    box.style.display = 'block';
    document.getElementById('emailPreviewValue').textContent =
        p.prefix + getNextCode(roleCounts[role]).toLowerCase() + '@barangay.gov.ph';

    // Build role card
    const items = p.items.map(i =>
        `<li class="role-priv-item"><span class="rp-dot"></span>${i}</li>`
    ).join('');

    body.innerHTML = `
        <div class="role-card-label" style="background:${p.bg}; color:${p.color};">${p.label}</div>
        <ul class="role-priv-list">${items}</ul>
    `;
});
</script>
@endpush