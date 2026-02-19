<!DOCTYPE html>
<html>
<head>
    <title>My Registered Households</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #0038A8; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 2px; }
        .btn-primary { background-color: #0038A8; color: white; }
        .btn-success { background-color: #28a745; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background-color: #d4edda; color: #155724; }
    </style>
</head>
<body>
    <h1>My Registered Households</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('encoder.households.create') }}" class="btn btn-primary">+ Register New Household</a>
    <a href="{{ route('encoder.dashboard') }}" class="btn btn-warning">← Back to Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>Household Head</th>
                <th>Address</th>
                <th>Members</th>
                <th>Status</th>
                <th>Serial Code</th>
                <th>Registered</th>
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
                    <td>
                        @if($household->isApproved())
                            <span class="badge badge-success">✓ Approved</span>
                        @else
                            <span class="badge badge-warning">⏳ Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($household->serial_code)
                            <strong>{{ $household->serial_code }}</strong>
                        @else
                            <em>Not assigned yet</em>
                        @endif
                    </td>
                    <td>{{ $household->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('encoder.households.show', $household) }}" class="btn btn-primary">View</a>
                        @if(!$household->isApproved())
                            <a href="{{ route('encoder.households.edit', $household) }}" class="btn btn-warning">Edit</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        No households registered yet. <a href="{{ route('encoder.households.create') }}">Register your first household</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $households->links() }}
    </div>

    <form method="POST" action="{{ route('logout') }}" style="margin-top: 30px;">
        @csrf
        <button type="submit" class="btn btn-warning">Logout</button>
    </form>
</body>
</html>