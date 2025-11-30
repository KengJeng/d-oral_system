// initialize variables and constants
       
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
            setInterval(loadDashboard, 40000);
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
                loadPatientDemographics(),

            ]);
            renderPredictiveCharts();

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

        async function loadAppointmentsForecast() {
            try {
                const res = await fetch(`${API_BASE}/analytics/appointments/forecast`, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`
                    }
                });

                if (!res.ok) {
                    console.error('Forecast API error:', res.status);
                    return;
                }

                const data = await res.json();

                const histLabels = data.historical?.labels || [];
                const histValues = data.historical?.values || [];
                const forecastLabels = data.forecast?.labels || [];
                const forecastValues = data.forecast?.values || [];

                const ctx = document.getElementById('appointmentsForecastChart')?.getContext('2d');
                if (!ctx) return;

                if (appointmentsForecastChart) {
                    appointmentsForecastChart.destroy();
                }

                // Combine labels so chart shows historical + forecast nicely
// Number of days for historical data (limit it to 14 days)
const historicalDataLength = 14;  // Adjust this to the desired length of historical data

// Total length for the forecast (e.g., 30 days forecast)
const forecastDataLength = 30;  // Adjust the forecast period to the desired length

// Extend the period for forecast (for the next month or the desired period)
const extendedLabels = [
    ...histLabels.slice(-historicalDataLength),  // Use only the last 'historicalDataLength' days of historical data
    ...forecastLabels.slice(0, forecastDataLength)  // Extend forecast to the desired length (e.g., 30 days)
];

// Update historical data (only the last 'historicalDataLength' days)
const extendedHistorical = [
    ...histValues.slice(-historicalDataLength),  // Only the last 'historicalDataLength' values
    ...Array(forecastDataLength).fill(null)  // Fill the remaining with null for forecast period
];

// Update forecast data (extend to the full forecast period)
const extendedForecast = [
    ...Array(historicalDataLength).fill(null),  // Pad with null for the historical period
    ...forecastValues.slice(0, forecastDataLength)  // Get forecast data for the full extended period
];

// Create the chart with the extended data
appointmentsForecastChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: extendedLabels,  // Updated labels to include historical and forecast period
        datasets: [
            {
                label: 'Actual Appointments',
                data: extendedHistorical,  
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true
            },
            {
                label: 'Forecast',
                data: extendedForecast,  
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.05)',
                borderWidth: 3,
                borderDash: [6, 4],  
                tension: 0.3,
                fill: false
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Appointments'
                }
            },
            x: {
                ticks: {
                    maxRotation: 0,
                    autoSkip: true,
                    maxTicksLimit: 10
                }
            }
        }
    }
});


            } catch (e) {
                console.error('Error loading appointments forecast:', e);
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
                    const activeClass = i === currentPatientsPage ?
                        'bg-blue-500 text-white' :
                        'bg-white text-gray-700 hover:bg-gray-50';
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

        function renderPredictiveCharts() {
    const chartsData = window.reportChartsData || {};

    const historicalLabels   = chartsData.historicalLabels || [];
    const historicalValues   = chartsData.historicalValues || [];
    const forecastLabels     = chartsData.forecastLabels || [];
    const forecastValues     = chartsData.forecastValues || [];
    const combinedLabels     = chartsData.combinedLabels || [];
    const combinedHistorical = chartsData.combinedHistorical || [];
    const combinedForecast   = chartsData.combinedForecast || [];
    const serviceNames       = chartsData.serviceNames || [];
    const serviceCounts      = chartsData.serviceCounts || [];

    // 1. Descriptive Chart
const descCanvas = document.getElementById('descriptiveAppointmentsChart');
if (descCanvas) {
    const ctx = descCanvas.getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: historicalLabels,
            datasets: [{
                label: 'Completed Appointments',
                data: historicalValues,
                borderWidth: 2,
                tension: 0.3,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                pointRadius: 3,
                pointBackgroundColor: '#3b82f6',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,   // <- REQUIRED FIX
            scales: {
                x: {
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12
                    }
                },
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
}


    // 2. Forecast Chart
    const forecastCanvas = document.getElementById('forecastAppointmentsChart');
    if (forecastCanvas) {
        const ctx = forecastCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: forecastLabels,
                datasets: [{
                    label: 'Forecasted Appointments',
                    data: forecastValues,
                    borderWidth: 2,
                    tension: 0.3,
                    borderDash: [6, 4],
                    fill: false,
                }]
            }
        });
    }

    // 3. Combined Chart
    const combinedCanvas = document.getElementById('combinedForecastChart');
if (combinedCanvas) {
    const ctx = combinedCanvas.getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: combinedLabels,
            datasets: [
                {
                    label: 'Historical',
                    data: combinedHistorical,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                },
                {
                    label: 'Forecast',
                    data: combinedForecast,
                    borderWidth: 2,
                    tension: 0.3,
                    borderDash: [6, 4],
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false 
        }
    });
}


    // 4. Service Forecast
    const serviceCanvas = document.getElementById('serviceForecastChart');
    if (serviceCanvas) {
        const ctx = serviceCanvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: serviceNames,
                datasets: [{
                    label: 'Forecasted Service Count',
                    data: serviceCounts,
                    borderWidth: 1
                }]
            }
        });
    }


}

function generateSmartSchedulingRecommendation() {
    if (!window.prescriptiveReco) return;

    const p = window.prescriptiveReco;

    const msg =
        `Peak congestion expected at ${p.peak_window}. ` +
        `Average expected load: ${p.avg_daily_load} patients/day. ` +
        `No-show risk: ${p.no_show_risk}%. ` +
        `Recommended day for scheduling: ${p.suggested_day}.`;

    const box = document.getElementById("smartPrescriptiveSuggestion");
    if (box) box.textContent = msg;
}



loadReportsAnalytics().then(() => {
    generateSmartSchedulingRecommendation();
});
