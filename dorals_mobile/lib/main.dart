import 'package:flutter/material.dart';

import 'screens/patient_login_screen.dart';
import 'screens/patient_register_screen.dart';
import 'screens/patient_dashboard.dart';

void main() {
  runApp(const DOralApp());
}

class DOralApp extends StatelessWidget {
  const DOralApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'D-ORALS',
      theme: ThemeData(
        useMaterial3: true,
        colorSchemeSeed: Colors.blue,
        fontFamily: 'Roboto',
      ),
      initialRoute: '/login',
      routes: {
        '/login': (context) => const PatientLoginScreen(),
        '/register': (context) => const PatientRegisterScreen(),
        '/dashboard': (context) => const DashboardScreen(),
        
      },
    );
  }
}
