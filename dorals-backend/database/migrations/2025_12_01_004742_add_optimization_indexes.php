<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Appointments optimization
        Schema::table('appointments', function (Blueprint $table) {
            // Index on queue_number for todayQueue and ordering
            $table->index('queue_number', 'idx_appointments_queue_number');

            // Separate index on status so queries filtering only by status can use it
            $table->index('status', 'idx_appointments_status');
        });

        // 2. Notifications optimization
        Schema::table('notifications', function (Blueprint $table) {
            // Combined index for "notifications for an appointment and sent or not"
            $table->index(
                ['appointment_id', 'is_sent'],
                'idx_notifications_appointment_is_sent'
            );

            // Combined index for "unsent notifications in queue order"
            $table->index(
                ['is_sent', 'queue_number'],
                'idx_notifications_is_sent_queue'
            );
        });

        // 3. Patients optimization (for searching by name)
        Schema::table('patients', function (Blueprint $table) {
            $table->index(
                ['last_name', 'first_name'],
                'idx_patients_name'
            );
        });

        // 4. Services optimization (search by service name)
        Schema::table('services', function (Blueprint $table) {
            $table->index('name', 'idx_services_name');
        });
    }

    public function down(): void
    {
        // Rollback for appointments
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_queue_number');
            $table->dropIndex('idx_appointments_status');
        });

        // Rollback for notifications
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_appointment_is_sent');
            $table->dropIndex('idx_notifications_is_sent_queue');
        });

        // Rollback for patients
        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_name');
        });

        // Rollback for services
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('idx_services_name');
        });
    }
};
