<!DOCTYPE html>
<html>
<head>
    <title>QR Scanner - Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #0038A8; color: white; padding: 15px; text-align: center; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .event-selector { background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .event-selector label { display: block; font-weight: bold; margin-bottom: 10px; }
        .event-selector select { width: 100%; padding: 12px; font-size: 16px; border: 2px solid #ddd; border-radius: 4px; }
        #scanner-container { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        #reader { width: 100%; border-radius: 8px; overflow: hidden; }
        .result-card { background: white; border-radius: 8px; padding: 20px; margin-top: 20px; display: none; }
        .result-card.success { border-left: 6px solid #28a745; }
        .result-card.duplicate { border-left: 6px solid #ffc107; background: #fff3cd; }
        .result-card.error { border-left: 6px solid #dc3545; background: #f8d7da; }
        .result-card h3 { margin-bottom: 15px; }
        .household-info { display: grid; gap: 10px; margin: 15px 0; }
        .info-row { display: flex; justify-content: space-between; padding: 8px; background: #f9f9f9; border-radius: 4px; }
        .info-label { font-weight: bold; color: #666; }
        .btn { display: block; width: 100%; padding: 15px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .btn-success { background: #28a745; color: white; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .alert { padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
        .stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 20px; }
        .stat-card { background: white; padding: 15px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; color: #0038A8; }
        .stat-label { font-size: 14px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📱 QR Code Scanner</h1>
        <p>{{ auth()->user()->name }} - Distribution Staff</p>
    </div>

    <div class="container">
        @if($events->isEmpty())
            <div class="alert alert-warning">
                <strong>No ongoing distribution events.</strong><br>
                Please wait for Admin to activate an event.
            </div>
            <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
        @else
            <div class="event-selector">
                <label for="event_id">Select Distribution Event:</label>
                <select id="event_id" required>
                    <option value="">-- Choose Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">
                            {{ $event->event_name }} ({{ $event->event_date->format('M d, Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="scanner-container" style="display: none;">
                <h3 style="text-align: center; margin-bottom: 15px;">📷 Point camera at QR Code</h3>
                <div id="reader"></div>
                <p style="text-align: center; color: #666; margin-top: 10px; font-size: 14px;">
                    Scanning will start automatically
                </p>
            </div>

            <div id="result-card" class="result-card"></div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="scan-count">0</div>
                    <div class="stat-label">Scanned Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="duplicate-count">0</div>
                    <div class="stat-label">Duplicates Blocked</div>
                </div>
            </div>
        @endif
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const eventSelect = document.getElementById('event_id');
        const scannerContainer = document.getElementById('scanner-container');
        const resultCard = document.getElementById('result-card');
        let html5QrcodeScanner = null;
        let currentHouseholdData = null;
        let scanCount = 0;
        let duplicateCount = 0;

        // Start scanner when event is selected
        eventSelect.addEventListener('change', function() {
            if (this.value) {
                startScanner();
            } else {
                stopScanner();
            }
        });

        function startScanner() {
            scannerContainer.style.display = 'block';
            
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
            }

            html5QrcodeScanner = new Html5QrcodeScanner("reader", {
                fps: 10,
                qrbox: 250,
                rememberLastUsedCamera: true,
            });

            html5QrcodeScanner.render(onScanSuccess, onScanError);
        }

        function stopScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.clear();
                scannerContainer.style.display = 'none';
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Pause scanner while processing
            html5QrcodeScanner.pause(true);

            // Send to server
            processQRCode(decodedText);
        }

        function onScanError(errorMessage) {
            // Ignore scan errors (camera still detecting)
        }

        async function processQRCode(serialCode) {
            const eventId = eventSelect.value;

            try {
                const response = await fetch('{{ route("staff.scan.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        serial_code: serialCode,
                        event_id: eventId,
                    }),
                });

                const data = await response.json();

                if (data.status === 'success') {
                    currentHouseholdData = data.household;
                    showSuccessResult(data);
                } else if (data.status === 'duplicate') {
                    duplicateCount++;
                    document.getElementById('duplicate-count').textContent = duplicateCount;
                    showDuplicateResult(data);
                } else {
                    showErrorResult(data.message);
                }

            } catch (error) {
                showErrorResult('Network error. Please check your connection.');
            }
        }

        function showSuccessResult(data) {
            const badges = [];
            if (data.household.is_4ps) badges.push('<span class="badge badge-success">4Ps</span>');
            if (data.household.is_pwd) badges.push('<span class="badge badge-success">PWD</span>');
            if (data.household.is_senior) badges.push('<span class="badge badge-success">Senior</span>');

            resultCard.className = 'result-card success';
            resultCard.style.display = 'block';
            resultCard.innerHTML = `
                <h3>✓ Household Found</h3>
                <div class="household-info">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span><strong>${data.household.name}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Serial Code:</span>
                        <span><strong>${data.household.serial_code}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span>${data.household.address}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Members:</span>
                        <span>${data.household.members_count} person(s)</span>
                    </div>
                    ${badges.length > 0 ? `<div class="info-row"><span class="info-label">Flags:</span><span>${badges.join(' ')}</span></div>` : ''}
                </div>
                <button class="btn btn-success" onclick="confirmRelease()">✓ CONFIRM RELEASE</button>
                <button class="btn btn-secondary" onclick="resetScanner()">✕ Cancel</button>
            `;
        }

        function showDuplicateResult(data) {
            resultCard.className = 'result-card duplicate';
            resultCard.style.display = 'block';
            resultCard.innerHTML = `
                <h3 style="color: #856404;">⚠ ALREADY RECEIVED</h3>
                <div class="household-info">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span><strong>${data.household.name}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Serial Code:</span>
                        <span><strong>${data.household.serial_code}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Previously Released:</span>
                        <span>${data.previous_release.date}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Released By:</span>
                        <span>${data.previous_release.staff}</span>
                    </div>
                </div>
                <button class="btn btn-secondary" onclick="resetScanner()">← Scan Next</button>
            `;
        }

        function showErrorResult(message) {
            resultCard.className = 'result-card error';
            resultCard.style.display = 'block';
            resultCard.innerHTML = `
                <h3 style="color: #721c24;">✕ Error</h3>
                <p style="color: #721c24; margin: 15px 0;">${message}</p>
                <button class="btn btn-secondary" onclick="resetScanner()">← Try Again</button>
            `;
        }

        async function confirmRelease() {
            const eventId = eventSelect.value;
            const confirmBtn = event.target;
            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Recording...';

            try {
                const response = await fetch('{{ route("staff.scan.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        household_id: currentHouseholdData.id,
                        event_id: eventId,
                    }),
                });

                const data = await response.json();

                if (data.status === 'success') {
                    scanCount++;
                    document.getElementById('scan-count').textContent = scanCount;
                    
                    resultCard.innerHTML = `
                        <h3 style="color: #28a745;">✓ SUCCESS</h3>
                        <p style="font-size: 18px; margin: 20px 0; text-align: center;">
                            <strong>Distribution Recorded!</strong><br>
                            <span style="color: #666;">${data.log.household}</span><br>
                            <small>${data.log.time}</small>
                        </p>
                        <button class="btn btn-success" onclick="resetScanner()">→ Scan Next</button>
                    `;
                } else {
                    showErrorResult(data.message);
                }

            } catch (error) {
                showErrorResult('Failed to record distribution. Please try again.');
            }
        }

        function resetScanner() {
            resultCard.style.display = 'none';
            currentHouseholdData = null;
            if (html5QrcodeScanner) {
                html5QrcodeScanner.resume();
            }
        }
    </script>
</body>
</html>