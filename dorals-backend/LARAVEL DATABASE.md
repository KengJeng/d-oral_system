LARAVEL DATABASE





“**Transactions** were implemented in the Appointment Controller to guarantee atomicity whenever a patient or admin creates, updates, or deletes appointments. These operations involve multiple dependent database writes (appointment table, pivot services table, queue number updates, and audit logs). Using DB::transaction() ensures the system never enters a partial or inconsistent state.”



“**Optimization** was performed by adding indexes on frequently queried columns such as scheduled\_date, status, queue\_number, appointment\_id, is\_sent, and patient/service names.”



“These **indexes** support core use cases: listing appointments by date and status, retrieving today’s queue in order, fetching notifications efficiently, and searching patients and services.”



“A dedicated migration (add\_optimization\_indexes) was implemented to apply and manage these performance optimizations.”



A **login\_history** table records every successful authentication event for both administrators and patients. Each row stores the user’s ID, user type (admin or patient), login timestamp, IP address, and user agent string. The AuthController writes to this table inside the patientLogin and adminLogin methods, using the framework’s request metadata to capture client information. This allows the system to audit access patterns, detect suspicious activity, and support security reviews.





5.1 Restoring the entire database

mysql -u doral\_admin -p dorals\_db < dorals\_backup\_2025\_12\_01.sql





This restores:



schema

tables

data

relationships

indexes

triggers



5.2 Restoring a single table

mysql -u doral\_admin -p dorals\_db < appointments\_backup.sql



5.3 Recovery After Accidentally Dropping a Table



If someone accidentally runs:

DROP TABLE appointments;



You can recover it by:

mysql -u doral\_admin -p dorals\_db < full\_backup.sql



Document this scenario as part of your DRP (Disaster Recovery Plan).

