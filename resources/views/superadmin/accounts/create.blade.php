@extends('superadmin.superadmin')

@section('title', 'Send Account Invite')

@section('content')

<div class="page-header-row">
    <div>
        <div class="breadcrumb">Super Admin &rsaquo; <a href="{{ route('superadmin.accounts.index') }}" style="color:var(--blue-acc);text-decoration:none;">Account Management</a> &rsaquo; <span>Send Invite</span></div>
        <h2 class="page-title">Send Account Invite</h2>
        <p class="page-sub">Enter the recipient's Gmail and assign their role. The system generates their login email and sends a 24-hour setup link.</p>
    </div>
    <div class="today-label">
        <span>{{ now()->format('l') }}</span>
        <strong>{{ now()->format('F j, Y') }}</strong>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:22px;align-items:start;">

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><span class="panel-dot purple"></span> Invite Details</div>
        </div>
        <form method="POST" action="{{ route('superadmin.accounts.store') }}" style="padding:24px;">
            @csrf

            <div class="form-group full" style="margin-bottom:18px;">
                <label for="personal_email">Recipient's Gmail Address <span class="req">*</span></label>
                <input type="email" id="personal_email" name="personal_email"
                       value="{{ old('personal_email') }}"
                       placeholder="e.g. juandelacruz@gmail.com" required/>
                <span style="font-size:11px;color:var(--muted);margin-top:4px;display:block;">The invite + setup link will be sent here.</span>
                @error('personal_email') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group full" style="margin-bottom:18px;">
                <label for="role">Assign Role <span class="req">*</span></label>
                <select id="role" name="role" required>
                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select role…</option>
                    <option value="admin"   {{ old('role')=='admin'   ? 'selected':'' }}>Administrator</option>
                    <option value="encoder" {{ old('role')=='encoder' ? 'selected':'' }}>Encoder</option>
                    <option value="staff"   {{ old('role')=='staff'   ? 'selected':'' }}>Staff</option>
                    <option value="auditor" {{ old('role')=='auditor' ? 'selected':'' }}>Auditor</option>
                </select>
                @error('role') <span class="field-error">{{ $message }}</span> @enderror
            </div>

            {{-- Generated email preview --}}
            <div id="emailPreviewBox" style="display:none;background:#f0f3f9;border:1px solid #dde3ef;border-left:4px solid #2e6ddd;border-radius:0 8px 8px 0;padding:14px 16px;margin-bottom:20px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted);margin-bottom:4px;">🔑 Generated Login Email (preview)</div>
                <div id="emailPreviewValue" style="font-size:15px;font-weight:700;color:var(--navy);font-family:'Courier New',monospace;"></div>
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">Shown in the invite email. User logs in with this address.</div>
            </div>

            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #22a86a;border-radius:0 8px 8px 0;padding:12px 16px;margin-bottom:24px;">
                <p style="margin:0;font-size:12px;color:#14532d;line-height:1.8;">
                    📧 <strong>What happens next:</strong><br/>
                    1. System generates login email (e.g. <em>adminA001@barangay.gov.ph</em>)<br/>
                    2. Invite sent to their Gmail with a <strong>24-hour setup link</strong><br/>
                    3. User enters their full name + creates their own password<br/>
                    4. Account activates — they log in with the generated email
                </p>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('superadmin.accounts.index') }}" class="btn btn-cancel">← Back</a>
                <button type="submit" class="btn btn-super">📧 Send Invite</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title"><span class="panel-dot purple"></span> Role Privileges Preview</div>
        </div>
        <div style="padding:20px;" id="rolePreviewBody">
            <p style="font-size:12px;color:var(--muted);text-align:center;padding:20px 0;">Select a role to preview privileges.</p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const rolePreviews = {
    admin:   { label:'Administrator', color:'#1e40af', bg:'#dbeafe', prefix:'admin',   items:['Manage households and residents','Approve or reject household records','Create and manage distribution events','Generate and download QR codes','View distribution logs and export reports','View audit trail logs'] },
    encoder: { label:'Encoder',        color:'#166534', bg:'#dcfce7', prefix:'encoder', items:['Encode new household records','Edit existing household records','View assigned household data'] },
    staff:   { label:'Staff',          color:'#5b21b6', bg:'#f3e8ff', prefix:'staff',   items:['Scan QR codes during relief distribution','View active distribution events','View scan history'] },
    auditor: { label:'Auditor',        color:'#92400e', bg:'#fef3c7', prefix:'auditor', items:['View household and family profiles (read-only)','View distribution logs','View full audit trail'] },
};
const roleCounts = {
    admin:   {{ \App\Models\User::where('role','admin')->whereNotNull('account_code')->count() }},
    encoder: {{ \App\Models\User::where('role','encoder')->whereNotNull('account_code')->count() }},
    staff:   {{ \App\Models\User::where('role','staff')->whereNotNull('account_code')->count() }},
    auditor: {{ \App\Models\User::where('role','auditor')->whereNotNull('account_code')->count() }},
};
function getNextCode(count) {
    return String.fromCharCode(65 + Math.floor(count / 999)) + String((count % 999) + 1).padStart(3,'0');
}
document.getElementById('role').addEventListener('change', function () {
    const role = this.value, p = rolePreviews[role];
    const body = document.getElementById('rolePreviewBody');
    const box  = document.getElementById('emailPreviewBox');
    if (!p) { box.style.display='none'; body.innerHTML='<p style="font-size:12px;color:var(--muted);text-align:center;padding:20px 0;">Select a role.</p>'; return; }
    box.style.display = 'block';
    document.getElementById('emailPreviewValue').textContent = p.prefix + getNextCode(roleCounts[role]).toLowerCase() + '@barangay.gov.ph';
    let html = `<div style="background:${p.bg};color:${p.color};border-radius:6px;padding:10px 14px;margin-bottom:16px;font-size:13px;font-weight:700;">${p.label}</div><ul style="margin:0;padding:0;list-style:none;">`;
    p.items.forEach(i => { html += `<li style="display:flex;align-items:flex-start;gap:8px;padding:6px 0;border-bottom:1px solid var(--border);font-size:13px;color:var(--text);"><span style="width:8px;height:8px;border-radius:50%;background:#22a86a;margin-top:4px;flex-shrink:0;display:inline-block;"></span>${i}</li>`; });
    body.innerHTML = html + '</ul>';
});
</script>
@endpush