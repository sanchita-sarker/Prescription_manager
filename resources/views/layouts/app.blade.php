<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">

        <!-- ✅ Header Navigation -->
        <nav class="bg-blue-600 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    
                    <!-- Logo / App Name -->
                    <div class="text-white font-bold text-lg">
                        Prescription Manager
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex space-x-6">
                        <a href="{{ route('dashboard') }}" 
                           class="no-underline px-3 py-2 rounded-md text-sm font-medium transition 
                                  {{ request()->routeIs('dashboard') ? 'bg-blue-700 text-white' : 'text-gray-200 hover:bg-indigo-500 hover:text-white' }}">
                            Upload Medicine
                        </a>

                        <a href="{{ route('reminders') }}" 
                           class="no-underline px-3 py-2 rounded-md text-sm font-medium transition 
                                  {{ request()->routeIs('reminders') ? 'bg-blue-700 text-white' : 'text-gray-200 hover:bg-indigo-500 hover:text-white' }}">
                            Reminders
                        </a>

                        <a href="{{ route('medical-history') }}" 
                           class="no-underline px-3 py-2 rounded-md text-sm font-medium transition 
                                  {{ request()->routeIs('medical-history') ? 'bg-blue-700 text-white' : 'text-gray-200 hover:bg-indigo-500 hover:text-white' }}">
                            Medical History
                        </a>

                        <a href="{{ route('appointments') }}" 
                           class="no-underline px-3 py-2 rounded-md text-sm font-medium transition 
                                  {{ request()->routeIs('appointments') ? 'bg-blue-700 text-white' : 'text-gray-200 hover:bg-indigo-500 hover:text-white' }}">
                            Appointments
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="p-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
