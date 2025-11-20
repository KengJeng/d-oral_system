<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>D-ORALS Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 h-full w-64 bg-white shadow-xl z-40">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">DENTI-ORAL</h1>
                    <p class="text-xs text-gray-500">Admin Portal</p>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-1">
            <a href="#" onclick="showSection('queue')"
                class="nav-item active flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                    <path fill-rule="evenodd"
                        d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" />
                </svg>
                <span class="font-medium">Appointments & Queue</span>
            </a>

            <a href="#" onclick="showSection('patients')"
                class="nav-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span class="font-medium">View Patients</span>
            </a>

            <a href="#" onclick="showSection('services')"
                class="nav-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
                <span class="font-medium">Manage Services</span>
            </a>

            <a href="#" onclick="showSection('reports')"
                class="nav-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
                <span class="font-medium">Reports & Analytics</span>
            </a>

            <a href="#" onclick="showSection('audit')"
                class="nav-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" />
                </svg>
                <span class="font-medium">Audit Trail</span>
            </a>

            <a href="#" onclick="showSection('settings')"
                class="nav-item flex items-center space-x-3 px-4 py-3 rounded-xl transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" />
                </svg>
                <span class="font-medium">Settings</span>
            </a>
        </nav>

        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100">
            <div
                class="flex items-center justify-between px-4 py-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                        A</div>
                    <div>
                        <p id="sidebarAdminName" class="text-sm font-semibold text-gray-800">Admin</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                </div>
                <button onclick="logout()" class="text-gray-400 hover:text-red-500 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" />
                    </svg>
                </button>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
        <!-- Top Bar -->
        <header class="bg-white/80 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-30">
            <div class="px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 id="pageTitle" class="text-2xl font-bold text-gray-800">Appointments & Queue</h2>
                        <p class="text-sm text-gray-500 mt-1">Manage today's appointments and patient queue</p>
                    </div>
                    <button onclick="loadQueue()"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition flex items-center space-x-2 shadow-lg shadow-blue-500/30">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" />
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- Alert -->
        <div id="alert" class="hidden mx-8 mt-6"></div>

        <!-- Queue Section -->
        <div id="queueSection" class="p-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Today's Queue</p>
                    <p id="todayQueue" class="text-4xl font-bold text-gray-800 mt-2">0</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Completed</p>
                    <p id="completedToday" class="text-4xl font-bold text-gray-800 mt-2">0</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Total Patients</p>
                    <p id="totalPatients" class="text-4xl font-bold text-gray-800 mt-2">0</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 font-medium">Pending</p>
                    <p id="pendingToday" class="text-4xl font-bold text-gray-800 mt-2">0</p>
                </div>
            </div>

            <!-- Queue and Next Patient -->
            <div class="grid grid-cols-3 gap-6 mb-8">
                <!-- Today's Queue List -->
                <div class="col-span-2 bg-white rounded-2xl shadow-sm">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Today's Queue</h3>
                                <p class="text-sm text-gray-500 mt-1">Real-time appointment queue</p>
                            </div>
                            <button onclick="showNewAppointmentModal()"
                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm rounded-xl transition">
                                + New Appointment
                            </button>
                        </div>
                    </div>
                    <div class="p-6 max-h-[600px] overflow-y-auto">
                        <div id="queueList" class="space-y-3">
                            <p class="text-gray-400 text-center py-12">Loading queue...</p>
                        </div>
                    </div>
                </div>

                <!-- Next Patient -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg text-white p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-2">Next Patient</h3>
                        <p class="text-blue-100 text-sm">Ready to be called</p>
                    </div>
                    <div id="nextPatient" class="bg-white/20 backdrop-blur-sm rounded-xl p-6 mb-6">
                        <p class="text-blue-100 text-sm">Loading...</p>
                    </div>
                    <button onclick="callNextPatient()"
                        class="w-full bg-white text-blue-600 hover:bg-blue-50 py-4 rounded-xl font-semibold transition shadow-lg">
                        📢 Call Next Patient
                    </button>
                </div>
            </div>
        </div>

        <!-- Patients Section -->
        <div id="patientsSection" class="hidden p-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="mb-6">
                    <input type="text" id="patientSearch" placeholder="Search patients by name or email..."
                        onkeyup="searchPatients()"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div id="patientsList" class="space-y-3">
                    <p class="text-gray-400 text-center py-12">Loading patients...</p>
                </div>

                <!-- Pagination Controls -->
                <div id="patientsPagination"
                    class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        Showing <span id="patientsFrom">0</span> to <span id="patientsTo">0</span> of <span
                            id="patientsTotal">0</span> patients
                    </div>
                    <div class="flex gap-2">
                        <button onclick="goToPatientsPage('prev')" id="patientsPrevBtn"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Previous
                        </button>
                        <div id="patientsPageNumbers" class="flex gap-2"></div>
                        <button onclick="goToPatientsPage('next')" id="patientsNextBtn"
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div id="servicesSection" class="hidden p-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div id="servicesList" class="space-y-3">
                    <p class="text-gray-400 text-center py-12">Loading services...</p>
                </div>
            </div>
        </div>

        <!-- Reports Section -->
        <div id="reportsSection" class="hidden p-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Total Appointments</p>
                    <p id="reportTotalApts" class="text-3xl font-bold text-blue-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">All time</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Completion Rate</p>
                    <p id="reportCompletionRate" class="text-3xl font-bold text-green-600 mt-2">0%</p>
                    <p class="text-xs text-gray-400 mt-1">Successfully completed</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Cancellation Rate</p>
                    <p id="reportCancelRate" class="text-3xl font-bold text-red-600 mt-2">0%</p>
                    <p class="text-xs text-gray-400 mt-1">Canceled + No-show</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Avg Per Day</p>
                    <p id="reportAvgPerDay" class="text-3xl font-bold text-purple-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">Daily average</p>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <!-- Appointment Trends -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Appointment Trends</h3>
                    <canvas id="appointmentTrendsChart" height="300"></canvas>
                </div>

                <!-- Appointment Status Breakdown -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Appointment Status</h3>
                    <canvas id="appointmentStatusChart" height="300"></canvas>
                </div>
            </div>

            <!-- More Charts -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <!-- Peak Days -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Appointments by Day of Week</h3>
                    <canvas id="peakDaysChart" height="300"></canvas>
                </div>

                <!-- Monthly Comparison -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Monthly Comparison</h3>
                    <canvas id="monthlyComparisonChart" height="300"></canvas>
                </div>
            </div>

            <!-- Patient Demographics Section -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Patient Demographics</h2>
            </div>

            <!-- Demographics Summary Cards -->
            <div class="grid grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Total Patients</p>
                    <p id="demoTotalPatients" class="text-3xl font-bold text-blue-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">Registered</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">New This Month</p>
                    <p id="demoNewThisMonth" class="text-3xl font-bold text-green-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">New registrations</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Active Patients</p>
                    <p id="demoActivePatients" class="text-3xl font-bold text-purple-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">With appointments</p>
                </div>
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <p class="text-sm text-gray-500 font-medium">Avg Visits</p>
                    <p id="demoAvgVisits" class="text-3xl font-bold text-amber-600 mt-2">0</p>
                    <p class="text-xs text-gray-400 mt-1">Per patient</p>
                </div>
            </div>

            <!-- Demographics Charts -->
            <div class="grid grid-cols-2 gap-6 mb-8">
                <!-- Gender Distribution -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Gender Distribution</h3>
                    <canvas id="genderChart" height="300"></canvas>
                </div>

                <!-- Geographic Distribution -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Top Barangays (Top 10)</h3>
                    <canvas id="barangayChart" height="300"></canvas>
                </div>
            </div>

            <!-- Patient Growth & Service Utilization -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Patient Growth -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Patient Growth</h3>
                    <canvas id="patientGrowthChart" height="300"></canvas>
                </div>

                <!-- Service Utilization -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Most Popular Services</h3>
                    <canvas id="serviceUtilizationChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Settings Section -->
        <div id="settingsSection" class="hidden p-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6">System Settings</h3>
                <p class="text-gray-500">Settings configuration coming soon...</p>
            </div>
        </div>
    </div>

    <!-- Audit Trail Section -->
    <div id="auditSection" class="hidden p-8 ml-64">
        <div class="bg-white rounded-2xl shadow-sm">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">System Audit Trail</h3>
                        <p class="text-sm text-gray-500 mt-1">Track all system activities and user actions</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <select id="auditFilter" onchange="filterAuditLogs()"
                            class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Actions</option>
                            <option value="login">Logins</option>
                            <option value="appointment">Appointments</option>
                            <option value="patient">Patient Actions</option>
                            <option value="service">Service Changes</option>
                        </select>
                        <button onclick="loadAuditLogs()"
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Summary Stats -->
                <div class="grid grid-cols-4 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-sm text-blue-600 font-medium">Total Logs Today</p>
                        <p id="auditTodayCount" class="text-2xl font-bold text-blue-700 mt-1">0</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4">
                        <p class="text-sm text-green-600 font-medium">User Actions</p>
                        <p id="auditUserCount" class="text-2xl font-bold text-green-700 mt-1">0</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4">
                        <p class="text-sm text-purple-600 font-medium">System Events</p>
                        <p id="auditSystemCount" class="text-2xl font-bold text-purple-700 mt-1">0</p>
                    </div>
                    <div class="bg-amber-50 rounded-xl p-4">
                        <p class="text-sm text-amber-600 font-medium">Active Users</p>
                        <p id="auditActiveUsers" class="text-2xl font-bold text-amber-700 mt-1">0</p>
                    </div>
                </div>

                <!-- Audit Log Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Timestamp
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">User ID
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            </tr>
                        </thead>
                        <tbody id="auditLogsList">
                            <tr>
                                <td colspan="4" class="text-center py-12 text-gray-400">Loading audit logs...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex items-center justify-between mt-6">
                    <p class="text-sm text-gray-600">Showing <span id="auditShowing">0</span> of <span
                            id="auditTotal">0</span> logs</p>
                    <div class="flex space-x-2">
                        <button onclick="loadAuditLogs(auditCurrentPage - 1)" id="auditPrevBtn"
                            class="px-4 py-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50"
                            disabled>Previous</button>
                        <button onclick="loadAuditLogs(auditCurrentPage + 1)" id="auditNextBtn"
                            class="px-4 py-2 border border-gray-200 rounded-xl hover:bg-gray-50 transition disabled:opacity-50"
                            disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Appointment Modal -->
    <div id="newAppointmentModal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white">
                <h3 class="text-2xl font-bold text-gray-800">New Appointment</h3>
                <button onclick="closeModal('newAppointmentModal')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" />
                    </svg>
                </button>
            </div>
            <form id="newAppointmentForm" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Patient</label>
                    <select id="appointmentPatient" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                    <input type="date" id="appointmentDate" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Services</label>
                    <div id="appointmentServices"
                        class="border border-gray-200 rounded-xl p-4 max-h-60 overflow-y-auto">
                        <p class="text-gray-400">Loading...</p>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white py-4 rounded-xl font-semibold transition shadow-lg shadow-blue-500/30">
                    Create Appointment
                </button>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = 'http://localhost:8000/api';
        let authToken = localStorage.getItem('admin_token');
        let admin = JSON.parse(localStorage.getItem('admin'));
        let allPatients = [];
        let allServices = [];
        let chartInstance = null;

        let currentPatientsPage = 1;
        let totalPatientsPages = 1;
        let totalPatientsCount = 0;
        let searchTimeout = null;

        let auditCurrentPage = 1;
        let auditTotalPages = 1;
        let auditFilter = 'all';

        let appointmentTrendsChart = null;
        let appointmentStatusChart = null;
        let peakDaysChart = null;
        let monthlyComparisonChart = null;
        let genderChart = null;
        let barangayChart = null;
        let patientGrowthChart = null;
        let serviceUtilizationChart = null;

        if (!authToken) window.location.href = '/admin/login';
        if (admin) {
            document.getElementById('sidebarAdminName').textContent = admin.name;
        }

        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('appointmentDate');
        if (dateInput) dateInput.setAttribute('min', today);

        document.addEventListener('DOMContentLoaded', () => {
            loadDashboard();
            setInterval(loadDashboard, 30000);
        });

        function showSection(section) {
            const sections = ['queue', 'patients', 'services', 'reports', 'settings', 'audit'];
            const titles = {
                queue: 'Appointments & Queue',
                patients: 'View Patients',
                services: 'Manage Services',
                reports: 'Reports & Analytics',
                settings: 'Settings',
                audit: 'Audit Trail'
            };

            sections.forEach(s => {
                document.getElementById(s + 'Section').classList.add('hidden');
            });
            document.getElementById(section + 'Section').classList.remove('hidden');
            document.getElementById('pageTitle').textContent = titles[section];

            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.closest('.nav-item').classList.add('active');

            if (section === 'patients') displayPatients();
            if (section === 'services') displayServices();
            if (section === 'reports') loadReportsAnalytics();
            if (section === 'audit') loadAuditLogs();
        }

        async function loadDashboard() {
            await Promise.all([loadStats(), loadQueue(), loadNextPatient()]);
        }

        async function loadStats() {
            try {
                const res = await fetch(`${API_BASE}/dashboard/stats`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const stats = await res.json();
                document.getElementById('todayQueue').textContent = stats.today_queue || 0;
                document.getElementById('completedToday').textContent = stats.completed_today || 0;
                document.getElementById('totalPatients').textContent = stats.total_patients || 0;
                document.getElementById('pendingToday').textContent = stats.pending_today || 0;
            } catch (e) {
                console.error('Error loading stats:', e);
            }
        }

        async function loadQueue() {
            try {
                const res = await fetch(`${API_BASE}/appointments/today-queue`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const apts = await res.json();
                const list = document.getElementById('queueList');
                if (apts.length === 0) {
                    list.innerHTML = '<p class="text-gray-400 text-center py-12">No appointments in queue</p>';
                    return;
                }
                list.innerHTML = apts.map(a => `
            <div class="flex items-center justify-between p-5 border border-gray-100 rounded-xl hover:shadow-md transition bg-white">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <span class="text-white font-bold text-xl">${a.queue_number}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 text-lg">${a.patient.first_name} ${a.patient.last_name}</p>
                        <p class="text-sm text-gray-500">${a.services?.map(s => s.name).join(', ') || 'No services'}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-4 py-2 rounded-xl text-sm font-medium ${getStatusColor(a.status)}">${a.status}</span>
                    ${a.status !== 'Completed' ? `<button onclick="updateStatus(${a.appointment_id}, 'Completed')" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-medium transition shadow-sm">Complete</button>` : ''}
                </div>
            </div>
        `).join('');
            } catch (e) {
                console.error('Error loading queue:', e);
            }
        }

        async function loadNextPatient() {
            try {
                const res = await fetch(`${API_BASE}/queue/next`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const next = await res.json();
                const box = document.getElementById('nextPatient');
                if (next && next.appointment_id) {
                    box.innerHTML = `
                <div class="text-4xl font-bold mb-3">#${next.queue_number}</div>
                <div class="text-lg font-semibold mb-1">${next.patient.first_name} ${next.patient.last_name}</div>
                <div class="text-sm text-blue-100">${next.services?.map(s => s.name).join(', ') || 'No services'}</div>
            `;
                } else {
                    box.innerHTML = '<p class="text-blue-100 text-sm">No patients in queue</p>';
                }
            } catch (e) {
                console.error('Error loading next:', e);
            }
        }

        async function loadReportsAnalytics() {
            await Promise.all([
                loadAppointmentAnalytics(),
                loadPatientDemographics()
            ]);
        }

        async function loadAppointmentAnalytics() {
            try {
                const res = await fetch(`${API_BASE}/analytics/appointments`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await res.json();

                // Update summary cards
                document.getElementById('reportTotalApts').textContent = data.total_appointments || 0;
                document.getElementById('reportCompletionRate').textContent = (data.completion_rate || 0) + '%';
                document.getElementById('reportCancelRate').textContent = (data.cancellation_rate || 0) + '%';
                document.getElementById('reportAvgPerDay').textContent = data.avg_per_day || 0;

                // Appointment Trends Chart (Last 30 days)

                if (appointmentTrendsChart) appointmentTrendsChart.destroy();
                const trendCtx = document.getElementById('appointmentTrendsChart').getContext('2d');
                appointmentTrendsChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: data.trends.labels || [],
                        datasets: [{
                            label: 'Appointments',
                            data: data.trends.values || [],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Appointment Status Chart (Pie)
                if (appointmentStatusChart) appointmentStatusChart.destroy();
                const statusCtx = document.getElementById('appointmentStatusChart').getContext('2d');
                appointmentStatusChart = new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: data.status_breakdown.labels || [],
                        datasets: [{
                            data: data.status_breakdown.values || [],
                            backgroundColor: [
                                'rgb(34, 197, 94)', // Completed - green
                                'rgb(251, 191, 36)', // Pending - yellow
                                'rgb(59, 130, 246)', // Confirmed - blue
                                'rgb(239, 68, 68)', // Canceled - red
                                'rgb(156, 163, 175)' // No-show - gray
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Peak Days Chart (Bar)
                if (peakDaysChart) peakDaysChart.destroy();
                const peakCtx = document.getElementById('peakDaysChart').getContext('2d');
                peakDaysChart = new Chart(peakCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                        datasets: [{
                            label: 'Appointments',
                            data: data.peak_days || [],
                            backgroundColor: 'rgba(59, 130, 246, 0.8)',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Monthly Comparison Chart
                if (monthlyComparisonChart) monthlyComparisonChart.destroy();
                const monthlyCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
                monthlyComparisonChart = new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: data.monthly.labels || [],
                        datasets: [{
                            label: 'Appointments',
                            data: data.monthly.values || [],
                            backgroundColor: 'rgba(168, 85, 247, 0.8)',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

            } catch (e) {
                console.error('Error loading appointment analytics:', e);
            }
        }

        async function loadPatientDemographics() {
            try {
                const res = await fetch(`${API_BASE}/analytics/demographics`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await res.json();

                // Update summary cards
                document.getElementById('demoTotalPatients').textContent = data.total_patients || 0;
                document.getElementById('demoNewThisMonth').textContent = data.new_this_month || 0;
                document.getElementById('demoActivePatients').textContent = data.active_patients || 0;
                document.getElementById('demoAvgVisits').textContent = data.avg_visits || 0;

                // Gender Distribution Chart
                if (genderChart) genderChart.destroy();
                const genderCtx = document.getElementById('genderChart').getContext('2d');
                genderChart = new Chart(genderCtx, {
                    type: 'pie',
                    data: {
                        labels: data.gender.labels || [],
                        datasets: [{
                            data: data.gender.values || [],
                            backgroundColor: [
                                'rgb(59, 130, 246)', // Male - blue
                                'rgb(236, 72, 153)' // Female - pink
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Barangay Distribution Chart
                if (barangayChart) barangayChart.destroy();
                const barangayCtx = document.getElementById('barangayChart').getContext('2d');
                barangayChart = new Chart(barangayCtx, {
                    type: 'bar',
                    data: {
                        labels: data.barangays.labels || [],
                        datasets: [{
                            label: 'Patients',
                            data: data.barangays.values || [],
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Patient Growth Chart
                if (patientGrowthChart) patientGrowthChart.destroy();
                const growthCtx = document.getElementById('patientGrowthChart').getContext('2d');
                patientGrowthChart = new Chart(growthCtx, {
                    type: 'line',
                    data: {
                        labels: data.growth.labels || [],
                        datasets: [{
                            label: 'New Patients',
                            data: data.growth.values || [],
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Service Utilization Chart
                if (serviceUtilizationChart) serviceUtilizationChart.destroy();
                const serviceCtx = document.getElementById('serviceUtilizationChart').getContext('2d');
                serviceUtilizationChart = new Chart(serviceCtx, {
                    type: 'bar',
                    data: {
                        labels: data.services.labels || [],
                        datasets: [{
                            label: 'Times Booked',
                            data: data.services.values || [],
                            backgroundColor: 'rgba(251, 191, 36, 0.8)',
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true
                            }
                        }
                    }
                });

            } catch (e) {
                console.error('Error loading patient demographics:', e);
            }
        }

        async function updateStatus(id, status) {
            try {
                const res = await fetch(`${API_BASE}/appointments/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        status
                    })
                });
                if (res.ok) {
                    showAlert(`Marked as ${status}`, 'success');
                    loadDashboard();
                } else {
                    showAlert('Failed to update', 'error');
                }
            } catch (e) {
                showAlert('Error updating', 'error');
            }
        }

        async function callNextPatient() {
            try {
                const res = await fetch(`${API_BASE}/queue/call-next`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const result = await res.json();
                if (res.ok && result.patient) {
                    showAlert(`Called: ${result.patient.patient.first_name} ${result.patient.patient.last_name}`,
                        'success');
                    loadDashboard();
                } else {
                    showAlert(result.message || 'No patients', 'error');
                }
            } catch (e) {
                showAlert('Error calling patient', 'error');
            }
        }

        async function showNewAppointmentModal() {
            document.getElementById('newAppointmentModal').classList.remove('hidden');
            await loadPatients();
            await loadServices();
        }

        async function loadPatients() {
            try {
                const res = await fetch(`${API_BASE}/patients`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await res.json();
                allPatients = data.data || [];
                const sel = document.getElementById('appointmentPatient');
                sel.innerHTML = '<option value="">Select patient</option>' + allPatients.map(p =>
                    `<option value="${p.patient_id}">${p.first_name} ${p.last_name}</option>`).join('');
            } catch (e) {
                console.error('Error loading patients:', e);
            }
        }

        async function loadServices() {
            try {
                const res = await fetch(`${API_BASE}/services`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                allServices = await res.json();
                const box = document.getElementById('appointmentServices');
                box.innerHTML = allServices.map(s => `
            <label class="flex items-center space-x-3 p-3 hover:bg-gray-50 rounded-lg cursor-pointer transition">
                <input type="checkbox" value="${s.service_id}" class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-700">${s.name} <span class="text-gray-400 text-sm">(${s.duration} min)</span></span>
            </label>
        `).join('');
            } catch (e) {
                console.error('Error loading services:', e);
            }
        }

        document.getElementById('newAppointmentForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const patientId = document.getElementById('appointmentPatient').value;
            const date = document.getElementById('appointmentDate').value;
            const checks = document.querySelectorAll('#appointmentServices input:checked');
            const serviceIds = Array.from(checks).map(c => parseInt(c.value));
            if (!patientId || !date || serviceIds.length === 0) {
                showAlert('Fill all fields', 'error');
                return;
            }
            try {
                const res = await fetch(`${API_BASE}/appointments`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_id: parseInt(patientId),
                        scheduled_date: date,
                        service_ids: serviceIds
                    })
                });
                const data = await res.json();
                if (res.ok) {
                    showAlert(`Created! Queue #${data.appointment.queue_number}`, 'success');
                    closeModal('newAppointmentModal');
                    document.getElementById('newAppointmentForm').reset();
                    loadDashboard();
                } else {
                    showAlert(data.message || 'Failed', 'error');
                }
            } catch (e) {
                showAlert('Error creating', 'error');
            }
        });

async function displayPatients(page = 1) {
    try {
        const res = await fetch(`${API_BASE}/patients?page=${page}`, {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        });
        const data = await res.json();
        
        allPatients = data.data || [];
        currentPatientsPage = data.current_page;
        totalPatientsPages = data.last_page;
        totalPatientsCount = data.total;
        
        const box = document.getElementById('patientsList');
        if (allPatients.length === 0) {
            box.innerHTML = '<p class="text-gray-400 text-center py-12">No patients</p>';
            return;
        }
        
        box.innerHTML = allPatients.map(p => `
            <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition bg-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-bold text-lg text-gray-800">${p.first_name} ${p.middle_name || ''} ${p.last_name}</p>
                        <p class="text-gray-600 text-sm mt-1">${p.email}</p>
                        <p class="text-gray-600 text-sm">${p.contact_no}</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">${p.sex}</span>
                </div>
                <p class="text-gray-500 text-sm mt-3">${p.address}</p>
            </div>
        `).join('');
        
        updatePagination();
        
    } catch (e) {
        console.error('Error loading patients:', e);
    }
}

function updatePagination() {
    // Update info text
    const from = (currentPatientsPage - 1) * 20 + 1;
    const to = Math.min(currentPatientsPage * 20, totalPatientsCount);
    document.getElementById('patientsFrom').textContent = from;
    document.getElementById('patientsTo').textContent = to;
    document.getElementById('patientsTotal').textContent = totalPatientsCount;
    
    // Update buttons
    const prevBtn = document.getElementById('patientsPrevBtn');
    const nextBtn = document.getElementById('patientsNextBtn');
    
    prevBtn.disabled = currentPatientsPage === 1;
    nextBtn.disabled = currentPatientsPage === totalPatientsPages;
    
    // Generate page numbers
    const pageNumbersDiv = document.getElementById('patientsPageNumbers');
    let pageNumbers = '';
    
    for (let i = 1; i <= totalPatientsPages; i++) {
        // Show first page, last page, current page, and pages around current
        if (i === 1 || i === totalPatientsPages || (i >= currentPatientsPage - 1 && i <= currentPatientsPage + 1)) {
            const activeClass = i === currentPatientsPage 
                ? 'bg-blue-500 text-white' 
                : 'bg-white text-gray-700 hover:bg-gray-50';
            pageNumbers += `
                <button onclick="goToPatientsPage(${i})" 
                    class="px-4 py-2 border border-gray-300 rounded-lg ${activeClass}">
                    ${i}
                </button>
            `;
        } else if (i === currentPatientsPage - 2 || i === currentPatientsPage + 2) {
            pageNumbers += '<span class="px-2 py-2">...</span>';
        }
    }
    
    pageNumbersDiv.innerHTML = pageNumbers;
}

function goToPatientsPage(direction) {
    let newPage = currentPatientsPage;
    
    if (direction === 'prev') {
        newPage = Math.max(1, currentPatientsPage - 1);
    } else if (direction === 'next') {
        newPage = Math.min(totalPatientsPages, currentPatientsPage + 1);
    } else {
        newPage = direction; // Direct page number
    }
    
    displayPatients(newPage);
}

// NEW SEARCH FUNCTION - Searches across ALL patients via API
function searchPatients() {
    const search = document.getElementById('patientSearch').value.toLowerCase().trim();
    
    // Clear previous timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Debounce: wait 300ms after user stops typing
    searchTimeout = setTimeout(() => {
        if (search === '') {
            // If search is empty, reload first page
            displayPatients(1);
            document.getElementById('patientsPagination').classList.remove('hidden');
        } else {
            // Search via API
            searchPatientsAPI(search);
        }
    }, 300);
}

async function searchPatientsAPI(searchQuery) {
    try {
        const box = document.getElementById('patientsList');
        box.innerHTML = '<p class="text-gray-400 text-center py-12">Searching...</p>';
        
        // Hide pagination during search
        document.getElementById('patientsPagination').classList.add('hidden');
        
        // Make API call with search parameter
        const res = await fetch(`${API_BASE}/patients?search=${encodeURIComponent(searchQuery)}`, {
            headers: {
                'Authorization': `Bearer ${authToken}`
            }
        });
        const data = await res.json();
        
        const results = data.data || [];
        
        if (results.length === 0) {
            box.innerHTML = '<p class="text-gray-400 text-center py-12">No patients found</p>';
            return;
        }
        
        box.innerHTML = results.map(p => `
            <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition bg-white">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-bold text-lg text-gray-800">${p.first_name} ${p.middle_name || ''} ${p.last_name}</p>
                        <p class="text-gray-600 text-sm mt-1">${p.email}</p>
                        <p class="text-gray-600 text-sm">${p.contact_no}</p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">${p.sex}</span>
                </div>
                <p class="text-gray-500 text-sm mt-3">${p.address}</p>
            </div>
        `).join('');
        
    } catch (e) {
        console.error('Error searching patients:', e);
        document.getElementById('patientsList').innerHTML = 
            '<p class="text-red-500 text-center py-12">Error searching patients</p>';
    }
}

        async function displayServices() {
            try {
                const res = await fetch(`${API_BASE}/services`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                allServices = await res.json();
                const box = document.getElementById('servicesList');
                if (allServices.length === 0) {
                    box.innerHTML = '<p class="text-gray-400 text-center py-12">No services</p>';
                    return;
                }
                box.innerHTML = allServices.map(s => `
            <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition bg-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-bold text-lg text-gray-800">${s.name}</p>
                        <p class="text-gray-500 text-sm mt-1">Duration: ${s.duration} minutes</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editService(${s.service_id})" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm transition">Edit</button>
                        <button onclick="deleteService(${s.service_id})" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm transition">Delete</button>
                    </div>
                </div>
            </div>
        `).join('');
            } catch (e) {
                console.error('Error loading services:', e);
            }
        }

        async function editService(id) {
            const svc = allServices.find(s => s.service_id === id);
            if (!svc) return;
            const name = prompt('Service Name:', svc.name);
            const dur = prompt('Duration (min):', svc.duration);
            if (!name || !dur) return;
            try {
                const res = await fetch(`${API_BASE}/services/${id}`, {
                    method: 'PATCH',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        name,
                        duration: parseInt(dur)
                    })
                });
                if (res.ok) {
                    showAlert('Updated', 'success');
                    displayServices();
                } else {
                    showAlert('Failed', 'error');
                }
            } catch (e) {
                showAlert('Error', 'error');
            }
        }

        async function deleteService(id) {
            if (!confirm('Delete this service?')) return;
            try {
                const res = await fetch(`${API_BASE}/services/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                if (res.ok) {
                    showAlert('Deleted', 'success');
                    displayServices();
                } else {
                    const data = await res.json();
                    showAlert(data.message || 'Failed', 'error');
                }
            } catch (e) {
                showAlert('Error', 'error');
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function getStatusColor(status) {
            const colors = {
                'Pending': 'bg-amber-100 text-amber-700',
                'Confirmed': 'bg-blue-100 text-blue-700',
                'Completed': 'bg-green-100 text-green-700',
                'Canceled': 'bg-red-100 text-red-700',
                'No-show': 'bg-gray-100 text-gray-700'
            };
            return colors[status] || 'bg-gray-100 text-gray-700';
        }

        function showAlert(msg, type) {
            const alert = document.getElementById('alert');
            alert.className =
                `mx-8 mt-6 p-4 rounded-xl ${type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
            alert.textContent = msg;
            alert.classList.remove('hidden');
            setTimeout(() => alert.classList.add('hidden'), 5000);
        }

        function logout() {
            localStorage.removeItem('admin_token');
            localStorage.removeItem('admin');
            window.location.href = '/admin/login';
        }

        document.addEventListener('click', (e) => {
            const modal = document.getElementById('newAppointmentModal');
            if (e.target === modal) closeModal('newAppointmentModal');
        });

        async function loadAuditLogs(page = 1) {
            try {
                auditCurrentPage = page;
                const filter = auditFilter !== 'all' ? `&filter=${auditFilter}` : '';
                const res = await fetch(`${API_BASE}/audit-logs?page=${page}${filter}`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const data = await res.json();

                displayAuditLogs(data);
                updateAuditStats();
            } catch (e) {
                console.error('Error loading audit logs:', e);
                document.getElementById('auditLogsList').innerHTML =
                    '<tr><td colspan="4" class="text-center py-12 text-red-500">Failed to load audit logs</td></tr>';
            }
        }

        function displayAuditLogs(data) {
            const tbody = document.getElementById('auditLogsList');
            const logs = data.data || [];

            if (logs.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center py-12 text-gray-400">No audit logs found</td></tr>';
                return;
            }

            tbody.innerHTML = logs.map(log => {
                const actionType = getActionType(log.action);
                const actionColor = getActionColor(actionType);

                return `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                <td class="px-4 py-4 text-sm text-gray-600">
                    <div>${log.log_date}</div>
                    <div class="text-xs text-gray-400">${log.log_time}</div>
                </td>
                <td class="px-4 py-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-xs font-bold">
                            ${log.user_id}
                        </div>
                        <span class="text-sm text-gray-700">User #${log.user_id}</span>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="text-sm text-gray-800">${log.action}</span>
                </td>
                <td class="px-4 py-4">
                    <span class="px-3 py-1 rounded-lg text-xs font-medium ${actionColor}">
                        ${actionType}
                    </span>
                </td>
            </tr>
        `;
            }).join('');

            // Update pagination
            document.getElementById('auditShowing').textContent = logs.length;
            document.getElementById('auditTotal').textContent = data.total || logs.length;

            auditTotalPages = data.last_page || 1;
            document.getElementById('auditPrevBtn').disabled = auditCurrentPage <= 1;
            document.getElementById('auditNextBtn').disabled = auditCurrentPage >= auditTotalPages;
        }

        function getActionType(action) {
            const actionLower = action.toLowerCase();
            if (actionLower.includes('login') || actionLower.includes('logout')) return 'Authentication';
            if (actionLower.includes('appointment')) return 'Appointment';
            if (actionLower.includes('patient')) return 'Patient';
            if (actionLower.includes('service')) return 'Service';
            if (actionLower.includes('queue')) return 'Queue';
            return 'System';
        }

        function getActionColor(type) {
            const colors = {
                'Authentication': 'bg-blue-100 text-blue-700',
                'Appointment': 'bg-green-100 text-green-700',
                'Patient': 'bg-purple-100 text-purple-700',
                'Service': 'bg-amber-100 text-amber-700',
                'Queue': 'bg-pink-100 text-pink-700',
                'System': 'bg-gray-100 text-gray-700'
            };
            return colors[type] || colors['System'];
        }

        async function updateAuditStats() {
            try {
                const res = await fetch(`${API_BASE}/audit-logs/stats`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });
                const stats = await res.json();

                document.getElementById('auditTodayCount').textContent = stats.today_count || 0;
                document.getElementById('auditUserCount').textContent = stats.user_actions || 0;
                document.getElementById('auditSystemCount').textContent = stats.system_events || 0;
                document.getElementById('auditActiveUsers').textContent = stats.active_users || 0;
            } catch (e) {
                console.error('Error loading audit stats:', e);
            }
        }

        function filterAuditLogs() {
            auditFilter = document.getElementById('auditFilter').value;
            loadAuditLogs(1);
        }
    </script>

    <style>
        .nav-item {
            color: #6b7280;
        }

        .nav-item:hover {
            background: #f3f4f6;
            color: #1f2937;
        }

        .nav-item.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Add to your chart container */
        .chart-container {
            max-height: 400px;
            /* or whatever height you prefer */
            height: 400px;
            width: 100%;
        }

        /* Add to your CSS file */
        #appointmentTrendsChart,
        #appointmentStatusChart,
        #peakDaysChart,
        #monthlyComparisonChart {
            max-height: 300px !important;
        }

        /* Or if they're in containers */
        .chart-container {
            height: 300px;
            position: relative;
            margin-bottom: 20px;
        }
    </style>
</body>

</html>
