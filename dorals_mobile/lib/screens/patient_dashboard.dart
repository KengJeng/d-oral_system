import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';


class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final _storage = const FlutterSecureStorage();

  // API base
  final String apiBase = 'http://localhost:8000/api'; // change for prod

  // Auth / patient
  String? _token;
  int? _patientId;
  String _patientName = 'Patient';

  // Appointment form
  DateTime? _selectedDate;
  final TextEditingController _notesCtrl = TextEditingController();
  bool _booking = false;

  // Services
  List<dynamic> _services = [];
  bool _loadingServices = false;
  Set<int> _selectedServiceIds = {};

  // Appointments
  List<dynamic> _appointments = [];
  bool _loadingAppointments = false;

  // Alert
  String? _alertMessage;
  bool _alertSuccess = false;

  @override
  void initState() {
    super.initState();
    _initDashboard();
  }

  Future<void> _initDashboard() async {
    await _loadAuth();
    if (_token == null) return;
    await Future.wait([_loadServices(), _loadAppointments()]);
  }

  Future<void> _loadAuth() async {
    final token = await _storage.read(key: 'patient_token');
    final patientJson = await _storage.read(key: 'patient');

    if (token == null || patientJson == null) {
      // Not logged in, redirect to login
      if (mounted) {
        Navigator.pushReplacementNamed(context, '/login');
      }
      return;
    }

    final patient = jsonDecode(patientJson);
    setState(() {
      _token = token;
      _patientId = patient['patient_id'];
      _patientName = patient['first_name'] ?? 'Patient';
    });
  }

  Future<void> _loadServices() async {
    setState(() {
      _loadingServices = true;
    });

    try {
      final res = await http.get(
        Uri.parse('$apiBase/services'),
        headers: {'Accept': 'application/json'},
      );
      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);
        setState(() {
          _services = data is List ? data : [];
        });
      } else {
        _showAlert('Failed to load services', success: false);
      }
    } catch (e) {
      _showAlert('Failed to load services', success: false);
    } finally {
      setState(() {
        _loadingServices = false;
      });
    }
  }

  Future<void> _loadAppointments() async {
    if (_token == null) return;

    setState(() {
      _loadingAppointments = true;
    });

    try {
      final res = await http.get(
        Uri.parse('$apiBase/appointments/my'),
        headers: {
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
      );

      if (res.statusCode == 200) {
        final data = jsonDecode(res.body);

        // Depending on your controller, this may be:
        // - a list directly, or
        // - paginated with data.data
        List<dynamic> items;
        if (data is List) {
          items = data;
        } else if (data is Map && data['data'] is List) {
          items = data['data'];
        } else {
          items = [];
        }

        setState(() {
          _appointments = items;
        });
      } else {
        _showAlert('Failed to load appointments', success: false);
      }
    } catch (e) {
      _showAlert('Failed to load appointments', success: false);
    } finally {
      setState(() {
        _loadingAppointments = false;
      });
    }
  }

  void _toggleService(int serviceId) {
    setState(() {
      if (_selectedServiceIds.contains(serviceId)) {
        _selectedServiceIds.remove(serviceId);
      } else {
        _selectedServiceIds.add(serviceId);
      }
    });
  }

  List<dynamic> get _selectedServices => _services
      .where((s) => _selectedServiceIds.contains(s['service_id']))
      .toList();

  int get _totalDuration {
    return _selectedServices.fold<int>(0, (sum, s) {
      final d = s['duration'];
      if (d == null) return sum;
      return sum + int.tryParse(d.toString())!;
    });
  }

  Future<void> _bookAppointment() async {
    if (_selectedServiceIds.isEmpty) {
      _showAlert('Please select at least one service', success: false);
      return;
    }
    if (_selectedDate == null) {
      _showAlert('Please select a date', success: false);
      return;
    }
    if (_patientId == null || _token == null) {
      _showAlert('You are not logged in.', success: false);
      return;
    }

    // Only allow future dates
    final today = DateTime.now();
    final selected = DateTime(
      _selectedDate!.year,
      _selectedDate!.month,
      _selectedDate!.day,
    );
    final todayOnly = DateTime(today.year, today.month, today.day);
    if (selected.isBefore(todayOnly)) {
      _showAlert('Please select a future date', success: false);
      return;
    }

    setState(() {
      _booking = true;
    });

    final body = {
      'patient_id': _patientId,
      'scheduled_date':
          '${_selectedDate!.year.toString().padLeft(4, '0')}-${_selectedDate!.month.toString().padLeft(2, '0')}-${_selectedDate!.day.toString().padLeft(2, '0')}',
      'service_ids': _selectedServiceIds.toList(),
      'notes': _notesCtrl.text.trim().isEmpty ? null : _notesCtrl.text.trim(),
    };

    try {
      final res = await http.post(
        Uri.parse('$apiBase/appointments'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': 'Bearer $_token',
        },
        body: jsonEncode(body),
      );

      final data = jsonDecode(res.body);

      if (res.statusCode == 200 || res.statusCode == 201) {
  final appointment = data['appointment'] ?? {};
  final queueNumber = appointment['queue_number'] ?? 'TBD';

  _showAlert(
    'Appointment booked successfully! Your queue number is #$queueNumber',
    success: true,
  );

  // Reset form
  setState(() {
    _selectedDate = null;
    _selectedServiceIds.clear();
    _notesCtrl.clear();
  });

  // reload appointments (optional kung History screen na rin maglo-load)
  await _loadAppointments();

  // Redirect to Appointment History page
  if (mounted) {
    Navigator.pushReplacementNamed(context, '/appointment-history');
    // kung gusto mong pwede bumalik sa dashboard, gamitin:
    // Navigator.pushNamed(context, '/appointment-history');
  }
}

         
          else {
        _showAlert(
          data['message'] ?? 'Failed to book appointment',
          success: false,
        );
      }
    } catch (e) {
      _showAlert('An error occurred. Please try again.', success: false);
    } finally {
      setState(() {
        _booking = false;
      });
    }
  }

  void _showAlert(String message, {required bool success}) {
    setState(() {
      _alertMessage = message;
      _alertSuccess = success;
    });
    // auto-hide after a few seconds
    Future.delayed(const Duration(seconds: 5), () {
      if (!mounted) return;
      setState(() {
        _alertMessage = null;
      });
    });
  }

  Future<void> _selectDate() async {
    final now = DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: _selectedDate ?? now,
      firstDate: now,
      lastDate: DateTime(now.year + 1),
    );
    if (picked != null) {
      setState(() {
        _selectedDate = picked;
      });
    }
  }

  Future<void> _logout() async {
    await _storage.delete(key: 'patient_token');
    await _storage.delete(key: 'patient');
    if (mounted) {
      Navigator.pushReplacementNamed(context, '/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xfff9fafb),
      body: Column(
        children: [
          // NAVBAR
          Container(
            color: const Color(0xff2563eb),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            child: SafeArea(
              bottom: false,
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Row(
                    children: const [
                      Icon(
                        Icons.medical_information_rounded,
                        size: 30,
                        color: Colors.white,
                      ),
                      SizedBox(width: 8),
                      Text(
                        'D-ORALS',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                  Row(
                    children: [
                      Text(
                        _patientName,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 14,
                        ),
                      ),
                      const SizedBox(width: 12),
                      ElevatedButton(
                        onPressed: _logout,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: const Color(0xffef4444),
                          padding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 8,
                          ),
                        ),
                        child: const Text(
                          'Logout',
                          style: TextStyle(fontSize: 12, color: Colors.white),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),

          // BODY
          Expanded(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 900),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // Header Card
                      Card(
                        elevation: 2,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Padding(
                          padding: EdgeInsets.all(16.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Book an Appointment',
                                style: TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xff111827),
                                ),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Schedule your dental visit with us',
                                style: TextStyle(
                                  fontSize: 14,
                                  color: Color(0xff6b7280),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Alert
                      if (_alertMessage != null)
                        Container(
                          margin: const EdgeInsets.only(bottom: 12),
                          padding: const EdgeInsets.all(12),
                          decoration: BoxDecoration(
                            color: _alertSuccess
                                ? Colors.green.shade100
                                : Colors.red.shade100,
                            borderRadius: BorderRadius.circular(10),
                          ),
                          child: Text(
                            _alertMessage!,
                            style: TextStyle(
                              color: _alertSuccess
                                  ? Colors.green.shade800
                                  : Colors.red.shade700,
                            ),
                          ),
                        ),

                      // Appointment Form Card
                      Card(
                        elevation: 2,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              // Date Selection
                              const Text(
                                'Appointment Date *',
                                style: TextStyle(
                                  fontWeight: FontWeight.w600,
                                  color: Color(0xff111827),
                                ),
                              ),
                              const SizedBox(height: 8),
                              InkWell(
                                onTap: _selectDate,
                                child: InputDecorator(
                                  decoration: const InputDecoration(
                                    border: OutlineInputBorder(),
                                  ),
                                  child: Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        _selectedDate == null
                                            ? 'Select a future date'
                                            : '${_selectedDate!.year}-${_selectedDate!.month.toString().padLeft(2, '0')}-${_selectedDate!.day.toString().padLeft(2, '0')}',
                                        style: TextStyle(
                                          color: _selectedDate == null
                                              ? Colors.grey.shade500
                                              : Colors.black,
                                        ),
                                      ),
                                      const Icon(
                                        Icons.calendar_today,
                                        size: 18,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'Select a future date',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Color(0xff6b7280),
                                ),
                              ),
                              const SizedBox(height: 16),

                              // Services list
                              const Text(
                                'Select Services *',
                                style: TextStyle(
                                  fontWeight: FontWeight.w600,
                                  color: Color(0xff111827),
                                ),
                              ),
                              const SizedBox(height: 8),
                              Container(
                                constraints: const BoxConstraints(
                                  maxHeight: 320,
                                ),
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(8),
                                  border: Border.all(
                                    color: Colors.grey.shade300,
                                  ),
                                ),
                                child: _loadingServices
                                    ? const Center(
                                        child: Padding(
                                          padding: EdgeInsets.all(12.0),
                                          child: CircularProgressIndicator(),
                                        ),
                                      )
                                    : _services.isEmpty
                                    ? const Center(
                                        child: Padding(
                                          padding: EdgeInsets.all(12.0),
                                          child: Text(
                                            'No services available',
                                            style: TextStyle(
                                              color: Color(0xff6b7280),
                                            ),
                                          ),
                                        ),
                                      )
                                    : ListView.builder(
                                        itemCount: _services.length,
                                        itemBuilder: (context, index) {
                                          final s = _services[index];
                                          final serviceId =
                                              s['service_id'] as int;
                                          final selected = _selectedServiceIds
                                              .contains(serviceId);

                                          return InkWell(
                                            onTap: () =>
                                                _toggleService(serviceId),
                                            child: Padding(
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                    vertical: 4.0,
                                                  ),
                                              child: Row(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.center,
                                                children: [
                                                  Checkbox(
                                                    value: selected,
                                                    onChanged: (_) =>
                                                        _toggleService(
                                                          serviceId,
                                                        ),
                                                  ),
                                                  Expanded(
                                                    child: Column(
                                                      crossAxisAlignment:
                                                          CrossAxisAlignment
                                                              .start,
                                                      children: [
                                                        Text(
                                                          s['name'] ?? '',
                                                          style:
                                                              const TextStyle(
                                                                fontWeight:
                                                                    FontWeight
                                                                        .w600,
                                                                color: Color(
                                                                  0xff111827,
                                                                ),
                                                              ),
                                                        ),
                                                        const SizedBox(
                                                          height: 2,
                                                        ),
                                                        Text(
                                                          '${s['duration'] ?? 0} minutes',
                                                          style:
                                                              const TextStyle(
                                                                fontSize: 12,
                                                                color: Color(
                                                                  0xff6b7280,
                                                                ),
                                                              ),
                                                        ),
                                                      ],
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ),
                                          );
                                        },
                                      ),
                              ),
                              const SizedBox(height: 4),
                              const Text(
                                'Select at least one service',
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Color(0xff6b7280),
                                ),
                              ),

                              const SizedBox(height: 12),

                              // Selected Services Summary
                              if (_selectedServices.isNotEmpty) ...[
                                const Text(
                                  'Selected Services',
                                  style: TextStyle(
                                    fontWeight: FontWeight.w600,
                                    color: Color(0xff111827),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Container(
                                  decoration: BoxDecoration(
                                    color: const Color(0xffeff6ff),
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  padding: const EdgeInsets.all(12),
                                  child: Column(
                                    children: [
                                      ..._selectedServices.map(
                                        (s) => Padding(
                                          padding: const EdgeInsets.symmetric(
                                            vertical: 4.0,
                                          ),
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                s['name'] ?? '',
                                                style: const TextStyle(
                                                  fontWeight: FontWeight.w500,
                                                ),
                                              ),
                                              Text(
                                                '${s['duration'] ?? 0} min',
                                                style: const TextStyle(
                                                  color: Color(0xff6b7280),
                                                  fontSize: 12,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Align(
                                        alignment: Alignment.centerLeft,
                                        child: Text(
                                          'Total estimated time: $_totalDuration minutes',
                                          style: const TextStyle(
                                            fontSize: 12,
                                            color: Color(0xff4b5563),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 12),
                              ],

                              // Notes
                              const Text(
                                'Additional Notes (Optional)',
                                style: TextStyle(
                                  fontWeight: FontWeight.w600,
                                  color: Color(0xff111827),
                                ),
                              ),
                              const SizedBox(height: 8),
                              TextField(
                                controller: _notesCtrl,
                                maxLines: 3,
                                decoration: const InputDecoration(
                                  border: OutlineInputBorder(),
                                  hintText:
                                      'Any specific concerns or requests...',
                                ),
                              ),

                              const SizedBox(height: 16),

                              // Buttons
                              Row(
                                children: [
                                  Expanded(
                                    child: ElevatedButton(
                                      onPressed: _booking
                                          ? null
                                          : _bookAppointment,
                                      style: ElevatedButton.styleFrom(
                                        backgroundColor: const Color(
                                          0xff2563eb,
                                        ),
                                        padding: const EdgeInsets.symmetric(
                                          vertical: 14,
                                        ),
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(
                                            10,
                                          ),
                                        ),
                                      ),
                                      child: _booking
                                          ? const CircularProgressIndicator(
                                              color: Colors.white,
                                            )
                                          : const Text(
                                              'Book Appointment',
                                              style: TextStyle(
                                                color: Colors.white,
                                                fontWeight: FontWeight.bold,
                                              ),
                                            ),
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: OutlinedButton(
                                      onPressed: () {
                                        setState(() {
                                          _selectedDate = null;
                                          _selectedServiceIds.clear();
                                          _notesCtrl.clear();
                                        });
                                      },
                                      style: OutlinedButton.styleFrom(
                                        padding: const EdgeInsets.symmetric(
                                          vertical: 14,
                                        ),
                                        side: BorderSide(
                                          color: Colors.grey.shade400,
                                        ),
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(
                                            10,
                                          ),
                                        ),
                                      ),
                                      child: const Text(
                                        'Cancel',
                                        style: TextStyle(
                                          color: Color(0xff111827),
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ),

                      const SizedBox(height: 16),

                      // My Appointments
                      Card(
                        elevation: 2,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.stretch,
                            children: [
                              const Text(
                                'My Appointments',
                                style: TextStyle(
                                  fontSize: 20,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xff111827),
                                ),
                              ),
                              const SizedBox(height: 8),
                              _loadingAppointments
                                  ? const Padding(
                                      padding: EdgeInsets.all(12.0),
                                      child: Center(
                                        child: CircularProgressIndicator(),
                                      ),
                                    )
                                  : _appointments.isEmpty
                                  ? const Padding(
                                      padding: EdgeInsets.symmetric(
                                        vertical: 20.0,
                                      ),
                                      child: Center(
                                        child: Text(
                                          'No appointments yet',
                                          style: TextStyle(
                                            color: Color(0xff6b7280),
                                          ),
                                        ),
                                      ),
                                    )
                                  : Column(
                                      children: _appointments.map((apt) {
                                        final queueNumber =
                                            apt['queue_number'] ?? 'TBD';
                                        final status =
                                            (apt['status'] ?? 'Unknown')
                                                .toString();
                                        final dateStr =
                                            apt['scheduled_date'] ?? '';
                                        final services =
                                            apt['services'] as List<dynamic>? ??
                                            [];
                                        final serviceNames = services
                                            .map((s) => s['name'] ?? '')
                                            .where(
                                              (name) =>
                                                  name.toString().isNotEmpty,
                                            )
                                            .join(', ');

                                        return Container(
                                          margin: const EdgeInsets.only(top: 8),
                                          padding: const EdgeInsets.all(12.0),
                                          decoration: BoxDecoration(
                                            borderRadius: BorderRadius.circular(
                                              10,
                                            ),
                                            border: Border.all(
                                              color: Colors.grey.shade200,
                                            ),
                                          ),
                                          child: Column(
                                            crossAxisAlignment:
                                                CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                mainAxisAlignment:
                                                    MainAxisAlignment
                                                        .spaceBetween,
                                                children: [
                                                  Column(
                                                    crossAxisAlignment:
                                                        CrossAxisAlignment
                                                            .start,
                                                    children: [
                                                      Text(
                                                        'Queue #$queueNumber',
                                                        style: const TextStyle(
                                                          fontSize: 16,
                                                          fontWeight:
                                                              FontWeight.bold,
                                                        ),
                                                      ),
                                                      const SizedBox(height: 2),
                                                      Text(
                                                        dateStr,
                                                        style: const TextStyle(
                                                          fontSize: 13,
                                                          color: Color(
                                                            0xff6b7280,
                                                          ),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                  _buildStatusChip(status),
                                                ],
                                              ),
                                              const SizedBox(height: 6),
                                              Text(
                                                'Services: ${serviceNames.isEmpty ? 'N/A' : serviceNames}',
                                                style: const TextStyle(
                                                  fontSize: 13,
                                                  color: Color(0xff4b5563),
                                                ),
                                              ),
                                            ],
                                          ),
                                        );
                                      }).toList(),
                                    ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusChip(String status) {
    Color bg;
    Color fg;

    switch (status) {
      case 'Pending':
        bg = const Color(0xfffef3c7);
        fg = const Color(0xff92400e);
        break;
      case 'Confirmed':
        bg = const Color(0xffdbeafe);
        fg = const Color(0xff1d4ed8);
        break;
      case 'Completed':
        bg = const Color(0xffdcfce7);
        fg = const Color(0xff166534);
        break;
      case 'Canceled':
        bg = const Color(0xfffee2e2);
        fg = const Color(0xffb91c1c);
        break;
      case 'No-show':
        bg = const Color(0xffe5e7eb);
        fg = const Color(0xff374151);
        break;
      default:
        bg = const Color(0xffe5e7eb);
        fg = const Color(0xff374151);
        break;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: bg,
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        status,
        style: TextStyle(color: fg, fontSize: 12, fontWeight: FontWeight.w600),
      ),
    );
  }
}
