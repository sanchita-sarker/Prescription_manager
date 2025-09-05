<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescription Manager</title>
    @vite('resources/css/app.css') <!-- if using Vite for Tailwind -->
    @livewireStyles
</head>
<body class="bg-gray-100 font-sans">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <!-- Logo / Brand -->
                <div class="flex-shrink-0">
                    <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">Prescription Manager</a>
                </div>

                <!-- Navigation Links -->
                <nav class="space-x-4">
                    <a href="{{ route('upload-medicine') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Upload Medicine</a>
                    <a href="{{ route('reminder') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Reminder</a>
                    <a href="{{ route('medical-history') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Medical History</a>
                    <a href="{{ route('appointments') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Appointments</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto p-4">
        {{ $slot ?? null }}
        @yield('content')
    </main>

    @livewireScripts
</body>
</html>
