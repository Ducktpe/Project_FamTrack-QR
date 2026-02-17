<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay RBI System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Header -->
    <header class="bg-blue-700 text-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold tracking-wide">Barangay RBI System</h1>
            <div>
                <a href="{{ route('login') }}" class="bg-white text-blue-700 font-semibold px-5 py-2 rounded-lg shadow hover:bg-gray-200 transition duration-300">
                    Admin Login
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="container mx-auto px-6 py-16 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">Welcome to the Barangay RBI System</h2>
        <p class="text-gray-600 max-w-xl mx-auto mb-12">
            Securely register households, generate QR codes, and manage ayuda distribution efficiently.
        </p>
    </section>

    <!-- Quick Actions -->
    <section class="container mx-auto px-6 py-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Register Household -->
            <a href="{{ route('households.create') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition transform duration-300">
                <div class="text-blue-700 text-5xl mb-4">📝</div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Register Household</h3>
                <p class="text-gray-500">Add a new family and record all members.</p>
            </a>

            <!-- View Households -->
            <a href="{{ route('households.index') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition transform duration-300">
                <div class="text-blue-700 text-5xl mb-4">👨‍👩‍👧</div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">View Households</h3>
                <p class="text-gray-500">Search and manage all households in the barangay.</p>
            </a>

            <!-- Generate QR Code -->
            <a href="{{ route('qr.generate') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition transform duration-300">
                <div class="text-blue-700 text-5xl mb-4">🔳</div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Generate QR Code</h3>
                <p class="text-gray-500">Generate unique QR codes for ayuda claiming.</p>
            </a>

            <!-- Ayuda Distribution -->
            <a href="{{ route('ayuda.index') }}" class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl hover:scale-105 transition transform duration-300">
                <div class="text-blue-700 text-5xl mb-4">🎁</div>
                <h3 class="text-xl font-semibold mb-2 text-gray-800">Ayuda Distribution</h3>
                <p class="text-gray-500">Scan QR codes and mark ayuda as claimed.</p>
            </a>

        </div>
    </section>

    <!-- Dashboard Summary -->
    <section class="container mx-auto px-6 py-16">
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Dashboard Summary</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 text-center">
            <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition duration-300">
                <h4 class="font-bold text-lg mb-2">🏠 Total Households</h4>
                <p class="text-3xl font-semibold text-blue-700">{{ $totalHouseholds ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition duration-300">
                <h4 class="font-bold text-lg mb-2">👨‍👩‍👧 Total Families</h4>
                <p class="text-3xl font-semibold text-blue-700">{{ $totalFamilies ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition duration-300">
                <h4 class="font-bold text-lg mb-2">👥 Total Members</h4>
                <p class="text-3xl font-semibold text-blue-700">{{ $totalMembers ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition duration-300">
                <h4 class="font-bold text-lg mb-2">🎁 Ayuda Claimed</h4>
                <p class="text-3xl font-semibold text-blue-700">{{ $ayudaClaimed ?? 0 }}</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-700 text-white mt-12">
        <div class="container mx-auto px-6 py-6 text-center text-sm">
            &copy; 2026 Barangay Name. All rights reserved. | Contact: 0912-345-6789
        </div>
    </footer>

</body>
</html>
