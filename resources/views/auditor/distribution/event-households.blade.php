{{-- resources/views/admin/distribution/event-households.blade.php --}}
{{-- This page is loaded via fetch() into the modal. Only the .modal-body div is extracted. --}}
<div class="modal-body" id="modalBody">

    {{-- Event Meta --}}
    <div style="display:flex; align-items:center; gap:20px; padding:12px 16px; background:#EAF0FA; border:1px solid #C7D9F3; border-radius:3px; margin-bottom:16px; flex-wrap:wrap;">
        <div>
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:2px;">Event Date</div>
            <div style="font-size:13px; font-weight:600; color:#122D5A;">
                {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
            </div>
        </div>
        <div style="width:1px; height:32px; background:#C7D9F3;"></div>
        <div>
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:2px;">Relief Type</div>
            <div style="font-size:13px; font-weight:600; color:#122D5A;">{{ $event->relief_type ?? '—' }}</div>
        </div>
        <div style="width:1px; height:32px; background:#C7D9F3;"></div>
        <div>
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:2px;">Status</div>
            @php $s = strtolower($event->status); @endphp
            @if($s === 'ongoing')
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;background:#DCFCE7;color:#16A34A;">● Ongoing</span>
            @elseif($s === 'upcoming')
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;background:#EAF0FA;color:#1B3F7A;">● Upcoming</span>
            @elseif($s === 'completed')
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;background:#F0F2F5;color:#5A6372;">● Completed</span>
            @else
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;text-transform:uppercase;background:#FEF2F2;color:#C0392B;">● {{ ucfirst($s) }}</span>
            @endif
        </div>
    </div>

    {{-- Stats row --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-bottom:16px;">
        <div style="background:#fff; border:1px solid #DEE2E8; border-top:3px solid #1B3F7A; padding:12px 14px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:4px;">Total Distributed</div>
            <div style="font-size:22px; font-weight:700; color:#1B3F7A;">{{ $households->count() }}</div>
        </div>
        <div style="background:#fff; border:1px solid #DEE2E8; border-top:3px solid #16A34A; padding:12px 14px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:4px;">With QR Code</div>
            <div style="font-size:22px; font-weight:700; color:#16A34A;">{{ $households->filter(fn($h) => $h->qrCode)->count() }}</div>
        </div>
        <div style="background:#fff; border:1px solid #DEE2E8; border-top:3px solid #D97706; padding:12px 14px;">
            <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#9AA3B0; margin-bottom:4px;">Missing QR</div>
            <div style="font-size:22px; font-weight:700; color:#D97706;">{{ $households->filter(fn($h) => !$h->qrCode)->count() }}</div>
        </div>
    </div>

    {{-- Search --}}
    <div style="background:#F7F8FA; border:1px solid #DEE2E8; padding:12px 14px; margin-bottom:14px;">
        <div style="display:grid; grid-template-columns:1fr auto; gap:8px; align-items:end;">
            <div>
                <div style="font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#5A6372; margin-bottom:5px;">Search Households</div>
                <input type="text" id="modalSearchInput"
                    placeholder="Name, barangay, serial code…"
                    style="width:100%; padding:8px 10px; border:1px solid #DEE2E8; border-radius:3px; font-family:'Open Sans',sans-serif; font-size:12px; color:#2C3340; outline:none;">
            </div>
            <button type="button" id="modalSearchBtn"
                    style="padding:8px 16px; background:#1B3F7A; color:#fff; border:none; border-radius:3px; font-family:'Open Sans',sans-serif; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap;">
                Search
            </button>
        </div>
    </div>

    {{-- Table --}}
    @if($households->isEmpty())
        <div style="text-align:center; padding:40px; color:#9AA3B0;">
            <div style="font-size:14px; font-weight:600; color:#5A6372; margin-bottom:4px;">No households found</div>
            <div style="font-size:12px;">No distributions recorded for this event yet.</div>
        </div>
    @else
        <div style="border:1px solid #DEE2E8; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:12px;">
                <thead>
                    <tr>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">#</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">Serial Code</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">Household Head</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">Barangay</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">Distributed By</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:left; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">Date & Time</th>
                        <th style="padding:9px 12px; background:#F7F8FA; text-align:center; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:#5A6372; border-bottom:1px solid #DEE2E8;">QR</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($households as $i => $household)
                        <tr style="border-bottom:1px solid #F0F2F5; transition:background 0.1s;" onmouseover="this.style.background='#EAF0FA'" onmouseout="this.style.background=''">
                            <td style="padding:9px 12px; color:#9AA3B0; font-size:11px;">{{ $loop->iteration }}</td>
                            <td style="padding:9px 12px;">
                                <code style="background:#F0F2F5; padding:2px 6px; border-radius:3px; font-size:11px; color:#2C3340; font-family:monospace;">
                                    {{ $household->serial_code ?? '—' }}
                                </code>
                            </td>
                            <td style="padding:9px 12px; font-weight:600; color:#122D5A;">
                                {{ $household->household_head_name ?? 'N/A' }}
                            </td>
                            <td style="padding:9px 12px; color:#5A6372; font-size:12px;">
                                {{ $household->barangay ?? 'N/A' }}
                            </td>
                            <td style="padding:9px 12px; color:#5A6372; font-size:12px;">
                                {{ $household->distributionLog?->staff?->name ?? '—' }}
                            </td>
                            <td style="padding:9px 12px; color:#5A6372; font-size:11px; white-space:nowrap;">
                                {{ $household->distributionLog?->distributed_at?->format('M d, Y H:i') ?? '—' }}
                            </td>
                            <td style="padding:9px 12px; text-align:center;">
                                @if($household->qrCode)
                                    <span style="display:inline-flex;align-items:center;gap:3px;background:#DCFCE7;color:#16A34A;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">✓ OK</span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:3px;background:#FEF2F2;color:#C0392B;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;">✗ None</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination inside modal --}}
            @if(false)
                <div style="padding:12px 16px; border-top:1px solid #DEE2E8; background:#F7F8FA; display:flex; align-items:center; justify-content:space-between; font-size:11px; color:#5A6372;">
                    <span>Showing {{ $households->firstItem() }}–{{ $households->lastItem() }} of {{ $households->total() }}</span>
                    <div>{{ $households->withQueryString()->links() }}</div>
                </div>
            @endif
        </div>
    @endif
