<!DOCTYPE html>
<html>
<head>
    <title>Household Management - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #0038A8; }
        .tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #0038A8; }
        .tab { padding: 10px 20px; text-decoration: none; color: #333; border: none; background: none; cursor: pointer; }
        .tab.active { border-bottom: 3px solid #CE1126; font-weight: bold; color: #0038A8; }
        .tab .count { background: #CE1126; color: white; padding: 2px 8px; border-radius: 10px; font-size: 12px; margin-left: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #0038A8; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 2px; border: none; cursor: pointer; }
        .btn-primary { background-color: #0038A8; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-card { flex: 1; padding: 20px; border-radius: 8px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-card.pending { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.approved { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .stat-number { font-size: 36px; font-weight: bold; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <h1>Household Management</h1>

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

    <div class="stats">
        <div class="stat-card pending">
            <div class="stat-number">{{ $pendingCount }}</div>
            <div class="stat-label">⏳ Pending Approval</div>
        </div>
        <div class="stat-card approved">
            <div class="stat-number">{{ $approvedCount }}</div>
            <div class="stat-label">✓ Approved Households</div>
        </div>
    </div>

    <div class="tabs">
        <a href="{{ route('admin.households.index', ['filter' => 'all']) }}" 
           class="tab {{ $filter === 'all' ? 'active' : '' }}">
            All Households
        </a>
        <a href="{{ route('admin.households.index', ['filter' => 'pending']) }}" 
           class="tab {{ $filter === 'pending' ? 'active' : '' }}">
            Pending <span class="count">{{ $pendingCount }}</span>
        </a>
        <a href="{{ route('admin.households.index', ['filter' => 'approved']) }}" 
           class="tab {{ $filter === 'approved' ? 'active' : '' }}">
            Approved
        </a>
    </div>

    <a href="{{ route('admin.dashboard') }}" class="btn btn-warning">← Back to Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>Household Head</th>
                <th>Address</th>
                <th>Members</th>
                <th>Encoded By</th>
                <th>Status</th>
                <th>Serial Code</th>
                <th>Date Registered</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($households as $household)
                <tr>
                    <td>
                        <strong>{{ $household->household_head_name }}</strong><br>
                        <small>{{ $household->sex }}, {{ $household->age }} years old</small>
                    </td>
                    <td>
                        {{ $household->street_purok }}, {{ $household->barangay }}<br>
                        <small>{{ $household->municipality }}, {{ $household->province }}</small>
                    </td>
                    <td>{{ $household->total_members }} person(s)</td>
                    <td>{{ $household->encoder->name }}</td>
                    <td>
                        @if($household->isApproved())
                            <span class="badge badge-success">✓ Approved</span>
                        @else
                            <span class="badge badge-warning">⏳ Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($household->serial_code)
                            <strong style="color: #0038A8;">{{ $household->serial_code }}</strong>
                        @else
                            <em style="color: #999;">Not assigned</em>
                        @endif
                    </td>
                    <td>{{ $household->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('admin.households.show', $household) }}" class="btn btn-primary">View</a>
                        
                        @if(!$household->isApproved())
                            <form method="POST" action="{{ route('admin.households.approve', $household) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Approve this household and generate serial code?')">
                                    ✓ Approve
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px;">
                        No households found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $households->appends(['filter' => $filter])->links() }}
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 30px;">
        @csrf
        <button type="submit" class="btn btn-warning">Logout</button>
    </form>
</body>
</html>