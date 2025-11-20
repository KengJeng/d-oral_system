import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class PatientRegisterScreen extends StatefulWidget {
  const PatientRegisterScreen({super.key});

  @override
  State<PatientRegisterScreen> createState() => _PatientRegisterScreenState();
}

class _PatientRegisterScreenState extends State<PatientRegisterScreen> {
  final _formKey = GlobalKey<FormState>();

  final TextEditingController _firstNameCtrl = TextEditingController();
  final TextEditingController _middleNameCtrl = TextEditingController();
  final TextEditingController _lastNameCtrl = TextEditingController();
  final TextEditingController _contactNoCtrl = TextEditingController();
  final TextEditingController _addressCtrl = TextEditingController();
  final TextEditingController _emailCtrl = TextEditingController();
  final TextEditingController _passwordCtrl = TextEditingController();
  final TextEditingController _passwordConfirmCtrl = TextEditingController();

  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  String? _sex; // Male / Female
  String? _alertMessage;
  bool _alertSuccess = false;
  bool _loading = false;

  // Adjust for your environment (emulator/device/prod)
  final String apiBase = 'http://localhost:8000/api';

  Future<void> _register() async {
    // Client-side validation (similar to JS version)
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      _loading = true;
      _alertMessage = null;
    });

    final body = {
      'first_name': _firstNameCtrl.text.trim(),
      'middle_name': _middleNameCtrl.text.trim(),
      'last_name': _lastNameCtrl.text.trim(),
      'sex': _sex,
      'contact_no': _contactNoCtrl.text.trim(),
      'address': _addressCtrl.text.trim(),
      'email': _emailCtrl.text.trim(),
      'password': _passwordCtrl.text,
      'password_confirmation': _passwordConfirmCtrl.text,
    };

    try {
      final response = await http.post(
        Uri.parse('$apiBase/patient/register'),
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode(body),
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 || response.statusCode == 201) {
        // Success: show alert, store token+patient, then go to dashboard
        setState(() {
          _alertSuccess = true;
          _alertMessage = 'Registration successful! Redirecting...';
        });

        if (data['token'] != null) {
          await _storage.write(key: 'patient_token', value: data['token']);
        }
        if (data['patient'] != null) {
          await _storage.write(
            key: 'patient',
            value: jsonEncode(data['patient']),
          );
        }

        Future.delayed(const Duration(seconds: 1), () {
          if (!mounted) return;
          Navigator.pushReplacementNamed(context, '/dashboard');
        });
      } else {
        // Handle validation errors from Laravel (data.errors) or message
        String errorMessage = 'Registration failed. Please check your information.';
        if (data['errors'] != null) {
          final errors = (data['errors'] as Map<String, dynamic>)
              .values
              .expand((e) => (e as List))
              .join(', ');
          if (errors.isNotEmpty) errorMessage = errors;
        } else if (data['message'] != null) {
          errorMessage = data['message'];
        }

        setState(() {
          _alertSuccess = false;
          _alertMessage = errorMessage;
        });
      }
    } catch (e) {
      setState(() {
        _alertSuccess = false;
        _alertMessage = 'An error occurred. Please try again.';
      });
    } finally {
      setState(() {
        _loading = false;
      });
    }
  }

  String? _validateNotEmpty(String? value, String label) {
    if (value == null || value.trim().isEmpty) {
      return '$label is required.';
    }
    return null;
  }

  String? _validateContact(String? value) {
    if (value == null || value.trim().isEmpty) {
      return 'Contact number is required.';
    }
    final trimmed = value.trim();
    if (!RegExp(r'^\d{11}$').hasMatch(trimmed)) {
      return 'Contact number must be exactly 11 digits.';
    }
    return null;
  }

  String? _validatePassword(String? value) {
    if (value == null || value.isEmpty) {
      return 'Password is required.';
    }
    if (value.length < 8) {
      return 'Password must be at least 8 characters.';
    }
    return null;
  }

  String? _validateConfirmPassword(String? value) {
    if (value == null || value.isEmpty) {
      return 'Confirm password is required.';
    }
    if (value != _passwordCtrl.text) {
      return 'Passwords do not match.';
    }
    return null;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      // Tailwind-like gradient background: from-blue-50 to-blue-100
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xffeff6ff), Color(0xffdbeafe)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 700),
              child: Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(22),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withOpacity(0.1),
                      blurRadius: 20,
                      offset: const Offset(0, 10),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Header / Logo
                    Column(
                      children: [
                        Container(
                          width: 80,
                          height: 80,
                          decoration: const BoxDecoration(
                            color: Color(0xff2563eb),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.medical_information_rounded,
                            size: 38,
                            color: Colors.white,
                          ),
                        ),
                        const SizedBox(height: 12),
                        const Text(
                          'D-ORALS',
                          style: TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.bold,
                            color: Color(0xff111827),
                          ),
                        ),
                        const Text(
                          'Patient Registration',
                          style: TextStyle(
                            fontSize: 15,
                            color: Color(0xff4b5563),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),

                    // Alert
                    if (_alertMessage != null)
                      Container(
                        margin: const EdgeInsets.only(bottom: 16),
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

                    // Form
                    Form(
                      key: _formKey,
                      child: Column(
                        children: [
                          // Personal Information (grid 2x2 on larger screens)
                          LayoutBuilder(
                            builder: (context, constraints) {
                              final isWide = constraints.maxWidth > 500;
                              return Flex(
                                direction:
                                    isWide ? Axis.horizontal : Axis.vertical,
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: TextFormField(
                                        controller: _firstNameCtrl,
                                        decoration: const InputDecoration(
                                          labelText: 'First Name *',
                                          border: OutlineInputBorder(),
                                        ),
                                        validator: (v) =>
                                            _validateNotEmpty(v, 'First name'),
                                      ),
                                    ),
                                  ),
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: TextFormField(
                                        controller: _middleNameCtrl,
                                        decoration: const InputDecoration(
                                          labelText: 'Middle Name',
                                          border: OutlineInputBorder(),
                                        ),
                                      ),
                                    ),
                                  ),
                                ],
                              );
                            },
                          ),
                          LayoutBuilder(
                            builder: (context, constraints) {
                              final isWide = constraints.maxWidth > 500;
                              return Flex(
                                direction:
                                    isWide ? Axis.horizontal : Axis.vertical,
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: TextFormField(
                                        controller: _lastNameCtrl,
                                        decoration: const InputDecoration(
                                          labelText: 'Last Name *',
                                          border: OutlineInputBorder(),
                                        ),
                                        validator: (v) =>
                                            _validateNotEmpty(v, 'Last name'),
                                      ),
                                    ),
                                  ),
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: DropdownButtonFormField<String>(
                                        value: _sex,
                                        decoration: const InputDecoration(
                                          labelText: 'Sex *',
                                          border: OutlineInputBorder(),
                                        ),
                                        items: const [
                                          DropdownMenuItem(
                                            value: 'Male',
                                            child: Text('Male'),
                                          ),
                                          DropdownMenuItem(
                                            value: 'Female',
                                            child: Text('Female'),
                                          ),
                                        ],
                                        onChanged: (value) {
                                          setState(() {
                                            _sex = value;
                                          });
                                        },
                                        validator: (value) => value == null
                                            ? 'Sex is required.'
                                            : null,
                                      ),
                                    ),
                                  ),
                                ],
                              );
                            },
                          ),

                          const SizedBox(height: 8),

                          // Contact Number
                          TextFormField(
                            controller: _contactNoCtrl,
                            decoration: const InputDecoration(
                              labelText: 'Contact Number *',
                              border: OutlineInputBorder(),
                            ),
                            keyboardType: TextInputType.phone,
                            validator: _validateContact,
                          ),
                          const SizedBox(height: 12),

                          // Address
                          TextFormField(
                            controller: _addressCtrl,
                            decoration: const InputDecoration(
                              labelText: 'Address *',
                              border: OutlineInputBorder(),
                            ),
                            maxLines: 2,
                            validator: (v) =>
                                _validateNotEmpty(v, 'Address'),
                          ),
                          const SizedBox(height: 12),

                          // Email
                          TextFormField(
                            controller: _emailCtrl,
                            decoration: const InputDecoration(
                              labelText: 'Email Address *',
                              border: OutlineInputBorder(),
                            ),
                            keyboardType: TextInputType.emailAddress,
                            validator: (v) {
                              final base = _validateNotEmpty(v, 'Email');
                              if (base != null) return base;
                              if (!v!.contains('@')) {
                                return 'Email is invalid.';
                              }
                              return null;
                            },
                          ),
                          const SizedBox(height: 12),

                          // Password + Confirm (grid 2x1)
                          LayoutBuilder(
                            builder: (context, constraints) {
                              final isWide = constraints.maxWidth > 500;
                              return Flex(
                                direction:
                                    isWide ? Axis.horizontal : Axis.vertical,
                                mainAxisAlignment:
                                    MainAxisAlignment.spaceBetween,
                                children: [
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: TextFormField(
                                        controller: _passwordCtrl,
                                        obscureText: true,
                                        decoration: const InputDecoration(
                                          labelText: 'Password *',
                                          hintText: 'Min. 8 characters',
                                          border: OutlineInputBorder(),
                                        ),
                                        validator: _validatePassword,
                                      ),
                                    ),
                                  ),
                                  Flexible(
                                    flex: 1,
                                    child: Padding(
                                      padding: const EdgeInsets.all(4.0),
                                      child: TextFormField(
                                        controller: _passwordConfirmCtrl,
                                        obscureText: true,
                                        decoration: const InputDecoration(
                                          labelText: 'Confirm Password *',
                                          border: OutlineInputBorder(),
                                        ),
                                        validator: _validateConfirmPassword,
                                      ),
                                    ),
                                  ),
                                ],
                              );
                            },
                          ),
                          const SizedBox(height: 16),

                          // Register button
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                              onPressed: _loading ? null : _register,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xff2563eb),
                                padding:
                                    const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(12),
                                ),
                              ),
                              child: _loading
                                  ? const CircularProgressIndicator(
                                      color: Colors.white,
                                    )
                                  : const Text(
                                      'Register',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.bold,
                                        fontSize: 16,
                                      ),
                                    ),
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // Link to login
                    Center(
                      child: GestureDetector(
                        onTap: () {
                          Navigator.pushReplacementNamed(context, '/login');
                        },
                        child: const Text(
                          'Already have an account? Login here',
                          style: TextStyle(
                            color: Color(0xff2563eb),
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    )
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
