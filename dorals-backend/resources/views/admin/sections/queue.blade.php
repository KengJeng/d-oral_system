<!-- Queue Section -->
<div id="queueSection" class="p-8">

    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
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
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
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
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
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
                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm text-gray-500 font-medium">Pending</p>
            <p id="pendingToday" class="text-4xl font-bold text-gray-800 mt-2">0</p>
        </div>
    </div>

    <!-- Queue List + Next Patient -->
    <div class="grid grid-cols-3 gap-6 mb-8">

        <!-- Queue List -->
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
