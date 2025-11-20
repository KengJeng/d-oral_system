import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class PatientLoginScreen extends StatefulWidget {
  const PatientLoginScreen({super.key});

  @override
  State<PatientLoginScreen> createState() => _PatientLoginScreenState();
}

class _PatientLoginScreenState extends State<PatientLoginScreen> {
  final TextEditingController emailCtrl = TextEditingController();
  final TextEditingController passwordCtrl = TextEditingController();
  final _storage = const FlutterSecureStorage();
  final _formKey = GlobalKey<FormState>();

  String? alertMessage;
  bool isSuccess = false;
  bool loading = false;

  final String apiBase = "http://localhost:8000/api"; // Adjust for production

  Future<void> login() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      loading = true;
      alertMessage = null;
    });

    final response = await http.post(
      Uri.parse("$apiBase/patient/login"),
      headers: {
        "Content-Type": "application/json",
      },
      body: jsonEncode({
        "email": emailCtrl.text.trim(),
        "password": passwordCtrl.text.trim(),
      }),
    );

    final data = jsonDecode(response.body);

    if (response.statusCode == 200) {
      // Success
      setState(() {
        isSuccess = true;
        alertMessage = "Login successful!";
      });

      // Store token and patient data (replaces localStorage)
      await _storage.write(key: "patient_token", value: data["token"]);
      await _storage.write(
          key: "patient", value: jsonEncode(data["patient"]));

      // Navigate after short delay
      Future.delayed(const Duration(seconds: 1), () {
        Navigator.pushReplacementNamed(context, "/dashboard");
      });
    } else {
      // Error
      setState(() {
        isSuccess = false;
        alertMessage = data["message"] ?? "Login failed.";
      });
    }

    setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xffe0f2fe),
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(24),
          child: Container(
            width: 380,
            padding: const EdgeInsets.all(28),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(22),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withOpacity(0.1),
                  blurRadius: 20,
                  offset: const Offset(0, 8),
                )
              ],
            ),
            child: Column(
              children: [
                // Logo
                Container(
                  width: 90,
                  height: 90,
                  decoration: const BoxDecoration(
                    color: Color(0xff2563eb),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.medical_information_rounded,
                    size: 45,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 15),
                const Text(
                  "D-ORALS",
                  style: TextStyle(
                    fontSize: 30,
                    fontWeight: FontWeight.bold,
                    color: Color(0xff1e293b),
                  ),
                ),
                const Text(
                  "Patient Login",
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.black54,
                  ),
                ),
                const SizedBox(height: 20),

                // Alert box
                if (alertMessage != null)
                  Container(
                    padding: const EdgeInsets.all(12),
                    margin: const EdgeInsets.only(bottom: 16),
                    decoration: BoxDecoration(
                      color: isSuccess
                          ? Colors.green.shade100
                          : Colors.red.shade100,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      alertMessage!,
                      style: TextStyle(
                        color:
                            isSuccess ? Colors.green.shade800 : Colors.red.shade700,
                      ),
                    ),
                  ),

                // Form
                Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      // Email
                      TextFormField(
                        controller: emailCtrl,
                        validator: (v) =>
                            v == null || v.isEmpty ? "Email is required" : null,
                        decoration: InputDecoration(
                          labelText: "Email Address",
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Password
                      TextFormField(
                        controller: passwordCtrl,
                        obscureText: true,
                        validator: (v) => v == null || v.isEmpty
                            ? "Password is required"
                            : null,
                        decoration: InputDecoration(
                          labelText: "Password",
                          filled: true,
                          fillColor: Colors.grey.shade50,
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                      ),

                      const SizedBox(height: 25),

                      // Login button
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: loading ? null : login,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xff2563eb),
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(12),
                            ),
                          ),
                          child: loading
                              ? const CircularProgressIndicator(
                                  color: Colors.white,
                                )
                              : const Text(
                                  "Login",
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

                const SizedBox(height: 20),

                // Links
                Column(
                  children: [
                    GestureDetector(
                      onTap: () => Navigator.pushNamed(context, "/register"),
                      child: const Text(
                        "Don't have an account? Register here",
                        style: TextStyle(
                          color: Color(0xff2563eb),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    const SizedBox(height: 6),
                    GestureDetector(
                      onTap: () {}, // or redirect to admin login
                      child: const Text(
                        "Admin Login",
                        style: TextStyle(
                          color: Color(0xff2563eb),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ],
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
