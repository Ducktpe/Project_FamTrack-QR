<!DOCTYPE html>
<html>
<head>
    <title>Household Details - {{ $household->household_head_name }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; max-width: 1000px; }
        h1 { color: #0038A8; }
        h2 { color: #CE1126; border-bottom: 2px solid #0038A8; padding-bottom: 5px; margin-top: 30px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0; }
        .info-item { padding: 10px; background: #f9f9f9; border-left: 4px solid #0038A8; }
        .info-label { font-weight: bold; color: #666; font-size: 12px; }
        .info-value { font-size: 16px; margin-top: 5px; }
        .btn { padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; border: none; cursor: pointer; }
        .btn-primary { background: #0038A8; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        .badge { padding: 8px 15px; border-radius: 4px; font-weight: bold; display: inline-block; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #0038A8; color: white; }
        .qr-card { border: 2px solid #0038A8; padding: 20px; background: white; text-align: center; max-width: 300px; margin: 20px auto; border-radius: 8px; }
        .qr-image { width: 250px; height: 250px; }
        .actions { margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd; }
    </style>
</head>
<body>
    <h1>Household Details</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0;">{{ $household->household_head_name }}</h2>
            <p style="color: #666;">Serial Code: 
                @if($household->serial_code)
                    <strong style="color: #0038A8; font-size: 18px;">{{ $household->serial_code }}</strong>
                @else
                    <em>Not assigned</em>
                @endif
            </p>
        </div>
        <div>
            @if($household->isApproved())
                <span class="badge badge-success">✓ Approved</span>
            @else
                <span class="badge badge-warning">⏳ Pending</span>
            @endif
        </div>
    </div>

    <h2>Household Head Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Full Name</div>
            <div class="info-value">{{ $household->household_head_name }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Sex / Age</div>
            <div class="info-value">{{ $household->sex }}, {{ $household->age }} years old</div>
        </div>
        <div class="info-item">
            <div class="info-label">Birthday</div>
            <div class="info-value">{{ $household->birthday->format('F d, Y') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Civil Status</div>
            <div class="info-value">{{ $household->civil_status }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Contact Number</div>
            <div class="info-value">{{ $household->contact_number ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Listahanan ID</div>
            <div class="info-value">{{ $household->listahanan_id ?? 'Not enrolled' }}</div>
        </div>
    </div>

    <h2>Address</h2>
    <div class="info-item">
        <div class="info-value">
            {{ $household->house_number }} {{ $household->street_purok }}<br>
            {{ $household->barangay }}, {{ $household->municipality }}, {{ $household->province }}
        </div>
    </div>

    <h2>DSWD / Listahanan Flags</h2>
    <div>
        @if($household->is_4ps_beneficiary) <span class="badge badge-success">4Ps Beneficiary</span> @endif
        @if($household->is_pwd) <span class="badge badge-success">Has PWD Member</span> @endif
        @if($household->is_senior) <span class="badge badge-success">Has Senior Citizen</span> @endif
        @if($household->is_solo_parent) <span class="badge badge-success">Has Solo Parent</span> @endif
        @if(!$household->is_4ps_beneficiary && !$household->is_pwd && !$household->is_senior && !$household->is_solo_parent)
            <em style="color: #999;">No special flags</em>
        @endif
    </div>

    <h2>Family Members ({{ $household->members->count() }})</h2>
    @if($household->members->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Relationship</th>
                    <th>Sex / Age</th>
                    <th>Birthday</th>
                    <th>Occupation</th>
                    <th>Flags</th>
                </tr>
            </thead>
            <tbody>
                @foreach($household->members as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td>{{ $member->relationship }}</td>
                        <td>{{ $member->sex }}, {{ $member->age }} y/o</td>
                        <td>{{ $member->birthday->format('M d, Y') }}</td>
                        <td>{{ $member->occupation ?? 'N/A' }}</td>
                        <td>
                            @if($member->is_pwd) <span class="badge badge-success">PWD</span> @endif
                            @if($member->is_student) <span class="badge badge-success">Student</span> @endif
                            @if($member->isSenior()) <span class="badge badge-success">Senior</span> @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #999; font-style: italic;">No additional family members registered.</p>
    @endif

    <h2>QR Code</h2>
    @if($household->qrCode)
        <div class="qr-card">
            <img src="{{ asset('storage/' . $household->qrCode->file_path) }}" alt="QR Code" class="qr-image">
            <p style="margin-top: 15px; font-weight: bold; font-size: 16px;">{{ $household->serial_code }}</p>
            <p style="color: #666; font-size: 12px;">{{ $household->household_head_name }}</p>
            <p style="color: #999; font-size: 11px;">
                Generated: {{ $household->qrCode->generated_at->format('M d, Y') }}<br>
                Reprint Count: {{ $household->qrCode->reprint_count }}
            </p>
            <a href="{{ route('admin.households.qr.download', $household) }}" class="btn btn-primary" style="margin-top: 10px;">
                ⬇ Download QR Code
            </a>
        </div>
    @else
        <div style="text-align: center; padding: 40px; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 8px;">
            <p style="color: #999; margin-bottom: 15px;">QR Code not generated yet</p>
            @if($household->isApproved())
                <form method="POST" action="{{ route('admin.households.qr.generate', $household) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Generate QR code for this household?')">
                        ⚡ Generate QR Code
                    </button>
                </form>
            @else
                <p style="color: #CE1126; font-weight: bold;">Household must be approved first</p>
            @endif
        </div>
    @endif

    <h2>Record Information</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Encoded By</div>
            <div class="info-value">{{ $household->encoder->name }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Approved By</div>
            <div class="info-value">{{ $household->approver->name ?? 'Not yet approved' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Date Registered</div>
            <div class="info-value">{{ $household->created_at->format('F d, Y h:i A') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Last Updated</div>
            <div class="info-value">{{ $household->updated_at->format('F d, Y h:i A') }}</div>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('admin.households.index') }}" class="btn btn-warning">← Back to List</a>

        @if(!$household->isApproved())
            <form method="POST" action="{{ route('admin.households.approve', $household) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this household?')">
                    ✓ Approve Household
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('admin.households.unapprove', $household) }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-warning" onclick="return confirm('Remove approval from this household?')">
                    ✕ Unapprove
                </button>
            </form>
        @endif

        @if(!$household->isApproved())
            <form method="POST" action="{{ route('admin.households.destroy', $household) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this household permanently?')">
                    🗑 Delete
                </button>
            </form>
        @endif
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 30px;">
        @csrf
        <button type="submit" class="btn btn-warning">Logout</button>
    </form>
</body>
</html>