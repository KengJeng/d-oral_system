import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class AppointmentHistoryScreen extends StatefulWidget {
  const AppointmentHistoryScreen({super.key});

  @override
  State<AppointmentHistoryScreen> createState() => _AppointmentHistoryScreenState();
}

class _AppointmentHistoryScreenState extends State<AppointmentHistoryScreen> {
  final _storage = const FlutterSecureStorage();
  final String apiBase = 'http://localhost:8000/api';

  String? _token;
  bool _loading = false;
  List<dynamic> _appointments = [];

  @override
  void initState() {
    super.initState();
    _init();
  }

  Future<void> _init() async {
    final token = await _storage.read(key: 'patient_token');
    if (!mounted) return;

    if (token == null) {
      Navigator.pushReplacementNamed(context, '/login');
      return;
    }

    setState(() => _token = token);
    await _loadAppointments();
  }

  Future<void> _loadAppointments() async {
    if (_token == null) return;

    setState(() => _loading = true);

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
        // optional: show snackbar or alert
      }
    } finally {
      if (mounted) {
        setState(() => _loading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Appointment History'),
        backgroundColor: const Color(0xff2563eb),
      ),
      backgroundColor: const Color(0xfff9fafb),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: _loading
            ? const Center(child: CircularProgressIndicator())
            : _appointments.isEmpty
                ? const Center(
                    child: Text(
                      'No appointments found.',
                      style: TextStyle(color: Color(0xff6b7280)),
                    ),
                  )
                : ListView.builder(
                    itemCount: _appointments.length,
                    itemBuilder: (context, index) {
                      final apt = _appointments[index];
                      final queueNumber = apt['queue_number'] ?? 'TBD';
                      final status = (apt['status'] ?? 'Unknown').toString();
                      final dateStr = apt['scheduled_date'] ?? '';
                      final services =
                          apt['services'] as List<dynamic>? ?? [];
                      final serviceNames = services
                          .map((s) => s['name'] ?? '')
                          .where((name) => name.toString().isNotEmpty)
                          .join(', ');

                      return Container(
                        margin: const EdgeInsets.only(bottom: 8),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(color: Colors.grey.shade200),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment:
                                  MainAxisAlignment.spaceBetween,
                              children: [
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      'Queue #$queueNumber',
                                      style: const TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                                    const SizedBox(height: 2),
                                    Text(
                                      dateStr,
                                      style: const TextStyle(
                                        fontSize: 13,
                                        color: Color(0xff6b7280),
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
                    },
                  ),
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
