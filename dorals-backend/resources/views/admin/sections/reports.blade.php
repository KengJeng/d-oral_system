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


        <!-- Combined Historical + Forecast Chart -->

         <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">
                            Forecasted Daily Appointments
                        </h3>
                        <p class="text-xs text-gray-400">
                            Combined historical (solid) and forecast (dashed) trend line.
                        </p>
                    </div>

                    <div class="flex gap-4 text-xs text-gray-500">
                        <div class="flex items-center gap-1">
                            <span class="w-4 h-[3px] bg-blue-500 block rounded"></span> Historical
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="w-4 h-[3px] border border-dashed border-rose-500 block rounded"></span>
                            Forecast
                        </div>
                    </div>
                </div>

                <div class="w-full h-80" >
                    <canvas id="combinedForecastChart" ></canvas>
                </div>
            </div>

            <!-- Appointment Status -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Appointment Status</h3>
            <canvas id="appointmentStatusChart"></canvas>
        </div>

    </div>

    <!-- More Charts -->
    <div class="grid grid-cols-2 gap-6 mb-8">

        <!-- Peak Days -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Appointments by Day of Week</h3>
            <canvas id="peakDaysChart"></canvas>
        </div>

        <!-- Monthly Comparison -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Monthly Comparison</h3>
            <canvas id="monthlyComparisonChart"></canvas>
        </div>

    </div>

                <!-- Forecasted service demand -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">
                            Forecasted Service Demand
                        </h3>
                        <p class="text-xs text-gray-400">
                            Expected service volume for the next forecast window.
                        </p>
                    </div>
                </div>

                <div class="w-full h-72">
                    <canvas id="serviceForecastChart"></canvas>
                </div>
            </div>

    <!-- Patient Demographics -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Patient Demographics</h2>
    </div>

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

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Gender Distribution</h3>
            <canvas id="genderChart"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top Barangays (Top 10)</h3>
            <canvas id="barangayChart"></canvas>
        </div>

    </div>

    <!-- Final Charts -->
    <div class="grid grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Patient Growth</h3>
            <canvas id="patientGrowthChart"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Most Popular Services</h3>
            <canvas id="serviceUtilizationChart"></canvas>
        </div>

    </div>
        {{-- ====================================== --}}
        {{-- SMART APPOINTMENT SCHEDULING (PRESCRIPTIVE) --}}
        {{-- ====================================== --}}
        <div class="mt-12 bg-white border border-gray-200 shadow-sm rounded-2xl p-8">

            <h2 class="text-2xl font-bold text-gray-900 mb-2">
                Smart Appointment Scheduling Recommendations
            </h2>

            <p class="text-gray-500 text-sm mb-6">
                Actionable decisions generated from forecasted volume, historical patterns, and no-show risks.
            </p>

            <!-- MAIN INSIGHT BOX -->
            <div class="bg-blue-50 border border-blue-200 p-6 rounded-xl mb-8">
                <h3 class="font-semibold text-blue-900 text-lg mb-2">System Insight</h3>
                <p class="text-base text-blue-800 leading-relaxed">
                    {{ $prescriptiveReco['message'] ?? '' }}
                </p>
            </div>

            <!-- 2x2 Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <!-- Predicted Load -->
                <div class="p-6 rounded-xl border bg-gray-50">
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">
                        Predicted Daily Load
                    </p>
                    <p class="text-4xl font-bold text-blue-600">
                        {{ $prescriptiveReco['avg_daily_load'] }}
                    </p>
                </div>

                <!-- Recommended Day -->
                <div class="p-6 rounded-xl border bg-emerald-50 border-emerald-200">
                    <p class="text-xs text-emerald-700 uppercase font-semibold mb-1">
                        Recommended Day
                    </p>
                    <p class="text-4xl font-extrabold text-emerald-700">
                        {{ $prescriptiveReco['suggested_day'] }}
                    </p>
                </div>

                <!-- No-Show Risk -->
                <div class="p-6 rounded-xl border bg-gray-50">
                    <p class="text-xs text-gray-500 uppercase font-semibold mb-1">
                        No-Show Risk
                    </p>
                    <p class="text-4xl font-bold text-red-600">
                        {{ $prescriptiveReco['no_show_risk'] }}%
                    </p>
                </div>



                <!-- Worst Day -->
                <div class="p-6 rounded-xl border bg-red-50 border-red-200">
                    <p class="text-xs text-red-700 uppercase font-semibold mb-1">
                        Worst Day
                    </p>
                    <p class="text-4xl font-extrabold text-red-600">
                        {{ $prescriptiveReco['worst_day'] }}
                    </p>
                </div>

            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        Day-of-Week Performance Ranking
    </h3>

    <table class="w-full text-sm">
        <thead>
            <tr class="text-gray-500">
                <th class="py-2 text-left">Rank</th>
                <th class="py-2 text-left">Day</th>
                <th class="py-2 text-center">Avg Load</th>
                <th class="py-2 text-center">No-Show Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($weekdayRanking as $i => $row)
                <tr class="border-t">
                    <td class="py-2 font-bold text-blue-600">{{ $i+1 }}</td>
                    <td class="py-2">{{ $row['day'] }}</td>
                    <td class="py-2 text-center">{{ $row['load'] }}</td>
                    <td class="py-2 text-center">{{ $row['no_show_rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


    </div>


    <script>
        window.prescriptiveReco = @json($prescriptiveReco);
    </script>


</div>
