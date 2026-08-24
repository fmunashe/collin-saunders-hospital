<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'HMS') }} - Hospital Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#006838', 600: '#00542d', 700: '#003d20', 800: '#002d18', 900: '#001f10' },
                        accent: { 400: '#fde047', 500: '#FDB913', 600: '#d49b00' },
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-primary-500 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <img src="/images/logo.png" alt="Logo" class="h-10">
                    <span class="text-white text-lg font-semibold hidden sm:block">Hospital Management System</span>
                </div>
                <a href="/nova" class="inline-flex items-center px-4 py-2 bg-accent-500 text-primary-700 font-semibold text-sm rounded-md hover:bg-accent-400 transition-colors">
                    Staff Portal
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary-500 to-primary-700 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 800 600">
                <circle cx="400" cy="300" r="250" stroke="white" stroke-width="0.5"/>
                <circle cx="400" cy="300" r="180" stroke="white" stroke-width="0.5"/>
                <path d="M370 200 h60 v70 h70 v60 h-70 v70 h-60 v-70 h-70 v-60 h70 z" stroke="white" stroke-width="1" fill="none"/>
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight">
                    Quality Healthcare,<br>
                    <span class="text-accent-500">Simplified.</span>
                </h1>
                <p class="mt-6 text-lg md:text-xl text-green-100 max-w-2xl mx-auto">
                    A comprehensive hospital management system designed to streamline patient care, pharmacy operations, billing, and administrative workflows.
                </p>
                <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/nova" class="px-8 py-3 bg-accent-500 text-primary-700 font-semibold rounded-md hover:bg-accent-400 transition-colors text-lg">
                        Access Dashboard
                    </a>
                    <a href="#services" class="px-8 py-3 border-2 border-white text-white font-semibold rounded-md hover:bg-white hover:text-primary-500 transition-colors text-lg">
                        Our Services
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Hospital Services</h2>
                <p class="mt-4 text-lg text-gray-600">Comprehensive care across all departments</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Outpatient -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Outpatient Care</h3>
                    <p class="mt-3 text-gray-600">Walk-in consultations, follow-up visits, and specialist referrals with efficient queue management.</p>
                </div>
                <!-- Inpatient -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Inpatient Admissions</h3>
                    <p class="mt-3 text-gray-600">Ward management, bed allocation, and comprehensive inpatient care tracking from admission to discharge.</p>
                </div>
                <!-- Pharmacy -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Pharmacy</h3>
                    <p class="mt-3 text-gray-600">Prescription management, medication dispensing, stock control, and expiry tracking.</p>
                </div>
                <!-- Laboratory -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Laboratory</h3>
                    <p class="mt-3 text-gray-600">Diagnostic testing, sample processing, and results management by qualified lab technicians.</p>
                </div>
                <!-- Radiology -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Radiology</h3>
                    <p class="mt-3 text-gray-600">X-ray, ultrasound, and imaging services with digital reporting by certified radiographers.</p>
                </div>
                <!-- Billing -->
                <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-md transition-shadow border border-gray-100">
                    <div class="w-12 h-12 bg-primary-50 rounded-lg flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900">Billing & Medical Aid</h3>
                    <p class="mt-3 text-gray-600">Cash and medical aid billing, invoice generation, tariff management, and payment tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-primary-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-accent-500">24/7</div>
                    <div class="mt-2 text-green-100">Emergency Care</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-accent-500">10+</div>
                    <div class="mt-2 text-green-100">Departments</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-accent-500">50+</div>
                    <div class="mt-2 text-green-100">Medical Staff</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-accent-500">100+</div>
                    <div class="mt-2 text-green-100">Bed Capacity</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Why Our System</h2>
                <p class="mt-4 text-lg text-gray-600">Built for modern healthcare delivery</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Patient Registration</h3>
                        <p class="mt-1 text-gray-600">Quick registration for staff and non-staff patients with medical aid integration.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Role-Based Access</h3>
                        <p class="mt-1 text-gray-600">Doctors, nurses, pharmacists, lab techs, radiographers — each sees only what they need.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Prescription Tracking</h3>
                        <p class="mt-1 text-gray-600">From doctor's order to pharmacy dispensing — full lifecycle tracking.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-accent-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-700" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Medical Aid Claims</h3>
                        <p class="mt-1 text-gray-600">Integrated medical aid processing with tariff codes and claim submission.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <img src="/images/logo.png" alt="Logo" class="h-10 brightness-200">
                    <p class="mt-4 text-sm">Providing quality healthcare services with modern technology and compassionate care.</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="/nova" class="hover:text-white transition-colors">Staff Portal</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm">
                        <li>Emergency: 911</li>
                        <li>Reception: +27 (0) 32 439 4000</li>
                        <li>info@hospital.co.za</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-10 pt-8 text-center text-sm">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
