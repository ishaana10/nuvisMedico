<?php
/**
 * Database Initialization & Seed Script
 */

require_once __DIR__ . '/../config/database.php';

$pdo = getDB();

echo "Initializing database tables...\n";

$isSqlite = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite');

if ($isSqlite) {
    $queries = [
        "CREATE TABLE IF NOT EXISTS doctors (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            specialty TEXT NOT NULL,
            email TEXT UNIQUE,
            password_hash TEXT,
            role TEXT DEFAULT 'Doctor',
            color TEXT DEFAULT '#10B981',
            dot_color_class TEXT DEFAULT 'bg-emerald-500',
            avatar TEXT
        );",
        "CREATE TABLE IF NOT EXISTS clinic_settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT
        );",
        "CREATE TABLE IF NOT EXISTS patients (
            id TEXT PRIMARY KEY,
            mrn TEXT UNIQUE NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            dob TEXT NOT NULL,
            age INTEGER NOT NULL,
            gender TEXT NOT NULL,
            phone TEXT,
            email TEXT,
            address TEXT,
            emergency_contact_name TEXT,
            emergency_contact_relationship TEXT,
            emergency_contact_phone TEXT,
            insurance_provider TEXT,
            insurance_policy_number TEXT,
            insurance_group_number TEXT,
            known_allergies TEXT,
            blood_group TEXT,
            chronic_conditions TEXT,
            clinical_notes TEXT,
            avatar TEXT,
            initials TEXT,
            registration_date TEXT NOT NULL
        );",
        "CREATE TABLE IF NOT EXISTS appointments (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            patient_name TEXT NOT NULL,
            patient_mrn TEXT NOT NULL,
            patient_avatar TEXT,
            patient_initials TEXT,
            doctor_id TEXT NOT NULL,
            doctor_name TEXT NOT NULL,
            appointment_date TEXT NOT NULL,
            time TEXT NOT NULL,
            end_time TEXT,
            time_slot TEXT NOT NULL,
            type TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Scheduled',
            room TEXT,
            notes TEXT,
            is_urgent INTEGER DEFAULT 0
        );",
        "CREATE TABLE IF NOT EXISTS queue (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            patient_name TEXT NOT NULL,
            mrn TEXT NOT NULL,
            time TEXT NOT NULL,
            doctor_name TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Waiting',
            room TEXT,
            check_in_time TEXT
        );",
        "CREATE TABLE IF NOT EXISTS vitals (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            blood_pressure TEXT DEFAULT '120/80',
            heart_rate INTEGER DEFAULT 72,
            temperature REAL DEFAULT 98.6,
            weight INTEGER DEFAULT 145,
            height INTEGER DEFAULT 66,
            bmi REAL DEFAULT 23.4,
            oxygen_sat INTEGER DEFAULT 99,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS soap_notes (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            subjective TEXT,
            objective TEXT,
            assessment_codes TEXT,
            plan TEXT,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS prescriptions (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            medication_name TEXT NOT NULL,
            dosage TEXT NOT NULL,
            frequency TEXT NOT NULL,
            duration TEXT,
            instructions TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS past_visits (
            id TEXT PRIMARY KEY,
            patient_id TEXT NOT NULL,
            visit_date TEXT NOT NULL,
            title TEXT NOT NULL,
            summary TEXT,
            doctor_name TEXT NOT NULL,
            vitals TEXT,
            prescriptions TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS activities (
            id TEXT PRIMARY KEY,
            type TEXT NOT NULL,
            title TEXT NOT NULL,
            detail TEXT NOT NULL,
            timestamp TEXT NOT NULL,
            badge_type TEXT DEFAULT 'blue',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );",
        "CREATE TABLE IF NOT EXISTS invoices (
            id TEXT PRIMARY KEY,
            invoice_number TEXT UNIQUE NOT NULL,
            patient_name TEXT NOT NULL,
            patient_mrn TEXT NOT NULL,
            service_date TEXT NOT NULL,
            due_date TEXT NOT NULL,
            amount REAL NOT NULL,
            status TEXT NOT NULL DEFAULT 'Pending',
            insurance_covered REAL NOT NULL DEFAULT 0.00,
            patient_owed REAL NOT NULL DEFAULT 0.00,
            services TEXT
        );",
        "CREATE TABLE IF NOT EXISTS inventory (
            id TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            sku TEXT UNIQUE NOT NULL,
            category TEXT NOT NULL,
            current_stock INTEGER NOT NULL DEFAULT 0,
            min_threshold INTEGER NOT NULL DEFAULT 10,
            unit TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'In Stock',
            last_restocked TEXT NOT NULL
        );",
        "CREATE TABLE IF NOT EXISTS medical_certificates (
            id TEXT PRIMARY KEY,
            certificate_number TEXT UNIQUE NOT NULL,
            patient_id TEXT NOT NULL,
            visit_id TEXT,
            issue_date TEXT NOT NULL,
            diagnosis TEXT NOT NULL,
            fitness_status TEXT NOT NULL,
            fit_status_details TEXT,
            recommendations TEXT,
            doctor_name TEXT NOT NULL,
            prc_number TEXT,
            ptr_number TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );"
    ];

    foreach ($queries as $q) {
        $pdo->exec($q);
    }
} else {
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($sql);
}

echo "Seeding default data...\n";

// Clear existing tables
$tables = ['doctors', 'clinic_settings', 'patients', 'appointments', 'queue', 'vitals', 'soap_notes', 'prescriptions', 'past_visits', 'activities', 'invoices', 'inventory', 'medical_certificates'];
foreach ($tables as $t) {
    $pdo->exec("DELETE FROM $t");
}

// Default Doctors with Passwords (Password: "password")
$defaultPasswordHash = password_hash('password', PASSWORD_DEFAULT);

$doctors = [
    ['doc-1', 'Dr. Sarah Jenkins', 'Internal Medicine', 'admin@clinicflow.com', $defaultPasswordHash, 'Administrator', '#10B981', 'bg-emerald-500', 'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&q=80&w=200'],
    ['doc-2', 'Dr. Marcus Chen', 'Cardiology & Family Practice', 'mchen@clinicflow.com', $defaultPasswordHash, 'Doctor', '#F59E0B', 'bg-amber-500', 'https://images.unsplash.com/photo-1622253692010-333f2da6031d?auto=format&fit=crop&q=80&w=200'],
    ['doc-3', 'Dr. Emily Thorne', 'Pediatrics', 'ethorne@clinicflow.com', $defaultPasswordHash, 'Doctor', '#6366F1', 'bg-indigo-500', 'https://images.unsplash.com/photo-1594824813581-22e6900f68ff?auto=format&fit=crop&q=80&w=200'],
    ['doc-4', 'Dr. A. Patel', 'General Practice', 'apatel@clinicflow.com', $defaultPasswordHash, 'Doctor', '#3B82F6', 'bg-blue-500', 'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&q=80&w=200']
];
$stmt = $pdo->prepare("INSERT INTO doctors (id, name, specialty, email, password_hash, role, color, dot_color_class, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($doctors as $doc) {
    $stmt->execute($doc);
}

// Default Clinic Settings
$defaultSettings = [
    'clinic_name' => 'ClinicFlow Medical Center',
    'clinic_subtitle' => 'Integrated Primary & Specialist Healthcare',
    'clinic_address' => '100 Healthcare Way, Suite 400, Springfield, OR 97477',
    'clinic_phone' => '(555) 019-2831',
    'clinic_email' => 'contact@clinicflow.com',
    'clinic_dea' => 'FC9823019',
    'clinic_npi' => '1092830192',
    'rx_header_title' => 'OFFICIAL MEDICAL PRESCRIPTION',
    'rx_disclaimer' => 'Notice: This prescription is valid for 30 days from date of issue unless specified otherwise.',
    'rx_footer_note' => 'Substitution Permitted unless DAW (Dispense As Written) is indicated.',
    'invoice_header_title' => 'MEDICAL SERVICES INVOICE',
    'invoice_tax_id' => '93-1029384',
    'invoice_payment_terms' => 'Net 30 Days. Please remit payment promptly.',
    'invoice_footer_note' => 'Thank you for choosing ClinicFlow Medical Center for your care.',
    'receipt_header_title' => 'OFFICIAL PAYMENT RECEIPT',
    'receipt_thank_you_msg' => 'Thank you for your payment. Your account balance for this invoice is cleared.',
    'doc_prc_no' => 'PRC-0098412',
    'doc_ptr_no' => 'PTR-8842109'
];
$stmt = $pdo->prepare("INSERT INTO clinic_settings (setting_key, setting_value) VALUES (?, ?)");
foreach ($defaultSettings as $key => $val) {
    $stmt->execute([$key, $val]);
}

// Patients
$patients = [
    ['pat-1', '#98234-A', 'Sarah', 'Jenkins', '1985-04-12', 38, 'Female', '(555) 234-5678', 'sarah.j.patient@example.com', '742 Evergreen Terrace, Suite 4B, Springfield, OR', 'Michael Jenkins', 'Spouse', '(555) 987-6543', 'Blue Cross Blue Shield', 'BCBS-9842109', 'GRP-44120', 'Penicillin Allergy, Peanuts', 'O+', 'Mild Hypertension (controlled), Seasonal Rhinitis', 'Patient experiences urticaria and mild bronchospasm with beta-lactams.', 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200', 'SJ', '2023-01-15'],
    ['pat-2', '#48291', 'Robert', 'Johnson', '1976-08-22', 47, 'Male', '(555) 345-6789', 'robert.johnson@example.com', '128 Meadow Lane, Maplewood, NJ', 'Patricia Johnson', 'Spouse', '(555) 345-6780', 'Aetna Health', 'AET-771928', 'GRP-99381', 'Sulfa Drugs', 'A+', 'Type 2 Diabetes', 'Monitors blood glucose daily.', null, 'RJ', '2022-11-04'],
    ['pat-3', '#55102', 'Elena', 'Rodriguez', '1992-11-30', 31, 'Female', '(555) 456-7890', 'elena.rodriguez@example.com', '512 Sunset Blvd, Los Angeles, CA', 'Carlos Rodriguez', 'Brother', '(555) 456-7891', 'UnitedHealthcare', 'UHC-339182', 'GRP-10492', 'None reported', 'B+', 'None', 'Routine health maintenance.', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200', 'ER', '2023-05-18'],
    ['pat-4', '#22941', 'Arthur', 'Smith', '1965-03-14', 58, 'Male', '(555) 567-8901', 'arthur.smith@example.com', '88 Oakridge Drive, Dallas, TX', 'Mary Smith', 'Daughter', '(555) 567-8902', 'Cigna Health', 'CIG-904128', 'GRP-88192', 'Aspirin, NSAIDs', 'AB+', 'Asthma', 'Requires albuterol rescue inhaler.', null, 'AS', '2021-09-12'],
    ['pat-5', '#88210', 'Marcus', 'Williams', '1989-07-19', 34, 'Male', '(555) 678-9012', 'marcus.williams@example.com', '304 Pine Valley Rd, Atlanta, GA', 'Tanya Williams', 'Spouse', '(555) 678-9013', 'Humana', 'HUM-662810', 'GRP-33182', 'Latex', 'O-', 'None', 'No chronic issues.', null, 'MW', '2023-08-01'],
    ['pat-6', '#10293', 'Linda', 'Jones', '1959-12-05', 64, 'Female', '(555) 789-0123', 'linda.jones@example.com', '910 Birch Lane, Denver, CO', 'David Jones', 'Son', '(555) 789-0124', 'Medicare Part B', 'MED-1129384', 'GRP-10029', 'Codeine', 'A-', 'Osteoarthritis, Hyperlipidemia', 'Routine lipid panel review scheduled.', null, 'LJ', '2020-04-10'],
    ['pat-7', '884920', 'Jane', 'Cooper', '1990-06-15', 33, 'Female', '(555) 890-1234', 'jane.cooper@example.com', '445 Hilltop Way, Seattle, WA', 'Tom Cooper', 'Spouse', '(555) 890-1235', 'Kaiser Permanente', 'KP-882910', 'GRP-55421', 'None', 'B-', 'Migraines', 'Follow-up for episodic migraine prevention.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200', 'JC', '2023-03-21'],
    ['pat-8', '112934', 'Wade', 'Warren', '1982-09-28', 41, 'Male', '(555) 901-2345', 'wade.warren@example.com', '772 River Road, Chicago, IL', 'Sarah Warren', 'Sister', '(555) 901-2346', 'Blue Cross Blue Shield', 'BCBS-112938', 'GRP-44120', 'Penicillin', 'O+', 'Hypertension', 'In Room 2 for follow up.', null, 'WW', '2022-02-14'],
    ['pat-9', '440219', 'Esther', 'Howard', '1974-02-10', 49, 'Female', '(555) 012-3456', 'esther.howard@example.com', '613 Elm Street, Austin, TX', 'Brian Howard', 'Spouse', '(555) 012-3457', 'Aetna', 'AET-440219', 'GRP-99381', 'Iodine contrast dye', 'AB-', 'Acute sinusitis, recurrent', 'Urgent care visit for facial pressure and fever.', null, 'EH', '2023-09-05'],
    ['pat-10', '#66190', 'Robert', 'Fox', '1987-10-11', 36, 'Male', '(555) 123-4560', 'robert.fox@example.com', '223 Willow Creek Rd, Portland, OR', 'Jessica Fox', 'Spouse', '(555) 123-4561', 'Cigna Health', 'CIG-661902', 'GRP-88192', 'None', 'A+', 'None', 'Annual routine wellness checkup.', null, 'RF', '2023-06-11']
];
$stmt = $pdo->prepare("INSERT INTO patients (id, mrn, first_name, last_name, dob, age, gender, phone, email, address, emergency_contact_name, emergency_contact_relationship, emergency_contact_phone, insurance_provider, insurance_policy_number, insurance_group_number, known_allergies, blood_group, chronic_conditions, clinical_notes, avatar, initials, registration_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($patients as $p) {
    $stmt->execute($p);
}

// Appointments
$appointments = [
    ['apt-1', 'pat-2', 'Robert Johnson', '#48291', null, 'RJ', 'doc-1', 'Dr. S. Jenkins', '2023-10-24', '09:00 AM', null, '09:00 - 09:45', 'Follow-up', 'Arrived', 'Room 1', null, 0],
    ['apt-2', 'pat-3', 'Elena Rodriguez', '#55102', 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=200', 'ER', 'doc-2', 'Dr. M. Chen', '2023-10-24', '09:30 AM', null, '09:30 - 10:15', 'Consultation', 'In Progress', 'Room 3', null, 0],
    ['apt-3', 'pat-4', 'Arthur Smith', '#22941', null, 'AS', 'doc-1', 'Dr. S. Jenkins', '2023-10-24', '10:00 AM', null, '10:00 - 10:45', 'Urgent Care', 'Waiting', null, null, 1],
    ['apt-4', 'pat-5', 'Marcus Williams', '#88210', null, 'MW', 'doc-4', 'Dr. A. Patel', '2023-10-24', '10:45 AM', null, '10:45 - 11:30', 'Routine Check', 'Scheduled', null, null, 0],
    ['apt-5', 'pat-6', 'Linda Jones', '#10293', null, 'LJ', 'doc-1', 'Dr. S. Jenkins', '2023-10-24', '11:30 AM', null, '11:30 - 12:00', 'Lab Results', 'Scheduled', null, null, 0],
    ['apt-cal-1', 'pat-10', 'Robert Fox', '#66190', null, 'RF', 'doc-1', 'Dr. Jenkins', '2023-11-13', '09:00 AM', '09:45 AM', '9:00 - 9:45', 'Routine Check', 'Scheduled', null, 'Checkup', 0],
    ['apt-cal-2', 'pat-7', 'Jane Cooper', '884920', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200', 'JC', 'doc-2', 'Dr. Chen', '2023-11-14', '08:30 AM', '09:30 AM', '8:30 - 9:30', 'Follow-up', 'Waiting', null, 'Follow-up', 0],
    ['apt-cal-3', 'pat-9', 'Esther Howard', '440219', null, 'EH', 'doc-1', 'Dr. Jenkins', '2023-11-14', '10:00 AM', '11:00 AM', '10:00 - 11:00', 'Urgent Care', 'Waiting', null, 'Acute care', 1],
    ['apt-cal-4', 'pat-8', 'Wade Warren', '112934', null, 'WW', 'doc-1', 'Dr. Jenkins', '2023-11-14', '09:15 AM', '10:00 AM', '9:15 - 10:00', 'Follow-up', 'In Progress', 'Room 2', null, 0]
];
$stmt = $pdo->prepare("INSERT INTO appointments (id, patient_id, patient_name, patient_mrn, patient_avatar, patient_initials, doctor_id, doctor_name, appointment_date, time, end_time, time_slot, type, status, room, notes, is_urgent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($appointments as $apt) {
    $stmt->execute($apt);
}

// Queue
$queue = [
    ['q-1', 'pat-7', 'Jane Cooper', '884920', '8:30 AM', 'Dr. Chen', 'Waiting', null, '08:22 AM'],
    ['q-2', 'pat-8', 'Wade Warren', '112934', '9:15 AM', 'Dr. Jenkins', 'In Room', 'Room 2', '09:05 AM'],
    ['q-3', 'pat-9', 'Esther Howard', '440219', '10:00 AM', 'Dr. Jenkins', 'Waiting', null, '09:48 AM'],
    ['q-4', 'pat-2', 'Robert Johnson', '#48291', '10:30 AM', 'Dr. Jenkins', 'Waiting', null, '10:15 AM']
];
$stmt = $pdo->prepare("INSERT INTO queue (id, patient_id, patient_name, mrn, time, doctor_name, status, room, check_in_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($queue as $q) {
    $stmt->execute($q);
}

// Vitals & SOAP for Sarah Jenkins (pat-1)
$stmt = $pdo->prepare("INSERT INTO vitals (id, patient_id, blood_pressure, heart_rate, temperature, weight, height, bmi, oxygen_sat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['v-1', 'pat-1', '120/80', 72, 98.6, 145, 66, 23.4, 99]);

$stmt = $pdo->prepare("INSERT INTO soap_notes (id, patient_id, subjective, objective, assessment_codes, plan) VALUES (?, ?, ?, ?, ?, ?)");
$assessmentCodes = json_encode([['code' => 'J01.90', 'label' => 'Acute sinusitis, unspecified']]);
$stmt->execute([
    's-1',
    'pat-1',
    'Patient reports 4-day history of worsening nasal congestion, facial pressure over maxillary sinuses, and purulent nasal discharge. Mild headache rated 4/10. No shortness of breath.',
    'Physical exam reveals erythema of bilateral nasal mucosa with mucopurulent drainage. Tenderness on palpation over bilateral maxillary sinuses. Oropharynx clear without exudate. TMs pearly gray bilateral.',
    $assessmentCodes,
    'Advised rest and hydration. Prescribed Amoxicillin 500mg PO BID for 7 days. Normal saline nasal rinses BID. Return to clinic if symptoms fail to improve in 5 days or if high fevers develop.'
]);

// Prescriptions
$prescriptions = [
    ['rx-1', 'pat-1', 'Amoxicillin', '500mg', 'BID (Twice a day)', '7 days', 'Take with food and full glass of water.']
];
$stmt = $pdo->prepare("INSERT INTO prescriptions (id, patient_id, medication_name, dosage, frequency, duration, instructions) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($prescriptions as $rx) {
    $stmt->execute($rx);
}

// Past Visits
$pastVisits = [
    ['pv-1', 'pat-1', 'Oct 12, 2023', 'Follow-up: Hypertension', 'BP stable. Refilled Lisinopril. Advised dietary changes.', 'Dr. Sarah Jenkins', json_encode(['bloodPressure' => '122/82', 'heartRate' => 70, 'weight' => 146]), json_encode([['id' => 'rx-prev-1', 'medicationName' => 'Lisinopril', 'dosage' => '10mg', 'frequency' => 'QD (Once daily)']])],
    ['pv-2', 'pat-1', 'Jun 05, 2023', 'Annual Physical', 'General checkup. Labs ordered. Flu shot administered.', 'Dr. Sarah Jenkins', json_encode(['bloodPressure' => '118/78', 'heartRate' => 74, 'weight' => 144]), null],
    ['pv-3', 'pat-1', 'Jan 22, 2023', 'Urgent Care: Sprain', 'Right ankle sprain. X-ray negative. Prescribed rest, ice, ibuprofen.', 'Dr. Marcus Chen', json_encode(['bloodPressure' => '124/80', 'heartRate' => 76, 'weight' => 145]), null]
];
$stmt = $pdo->prepare("INSERT INTO past_visits (id, patient_id, visit_date, title, summary, doctor_name, vitals, prescriptions) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($pastVisits as $pv) {
    $stmt->execute($pv);
}

// Activities
$activities = [
    ['act-1', 'patient_registered', 'New Patient Registered: David Kim', '10 mins ago • via Portal', '10 mins ago', 'emerald'],
    ['act-2', 'visit_completed', 'Visit Completed: Sarah Connor', '45 mins ago • Dr. Jenkins', '45 mins ago', 'blue'],
    ['act-3', 'lab_received', 'Lab Results Received: James Wilson', '2 hours ago • Blood Panel', '2 hours ago', 'amber'],
    ['act-4', 'appointment_cancelled', 'Appointment Cancelled: Emily Davis', '3 hours ago • Patient requested', '3 hours ago', 'rose']
];
$stmt = $pdo->prepare("INSERT INTO activities (id, type, title, detail, timestamp, badge_type) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($activities as $act) {
    $stmt->execute($act);
}

// Invoices
$invoices = [
    ['inv-1', 'INV-2023-8821', 'Robert Johnson', '#48291', '2023-10-24', '2023-11-24', 250.00, 'Pending', 200.00, 50.00, json_encode(['Follow-up Consultation', 'Point of Care Blood Glucose'])],
    ['inv-2', 'INV-2023-8819', 'Elena Rodriguez', '#55102', '2023-10-24', '2023-11-24', 320.00, 'Pending', 270.00, 50.00, json_encode(['Specialist Consultation', 'ECG Interpretation'])],
    ['inv-3', 'INV-2023-8790', 'Arthur Smith', '#22941', '2023-10-18', '2023-11-01', 680.00, 'Overdue', 450.00, 230.00, json_encode(['Urgent Care Evaluation', 'Nebulizer Treatment', 'Spirometry'])],
    ['inv-4', 'INV-2023-8765', 'Linda Jones', '#10293', '2023-10-10', '2023-10-31', 1450.00, 'Overdue', 1100.00, 350.00, json_encode(['Comprehensive Metabolic Panel', 'Lipid Profile', 'X-Ray Knee Bilateral'])],
    ['inv-5', 'INV-2023-8720', 'Sarah Jenkins', '#98234-A', '2023-10-12', '2023-11-12', 1550.00, 'Overdue', 1200.00, 350.00, json_encode(['Chronic Care Management', 'Follow-up Evaluation'])],
    ['inv-6', 'INV-2023-8650', 'Wade Warren', '112934', '2023-09-28', '2023-10-28', 420.00, 'Paid', 380.00, 40.00, json_encode(['Cardiology Assessment', 'Routine Blood Draw'])]
];
$stmt = $pdo->prepare("INSERT INTO invoices (id, invoice_number, patient_name, patient_mrn, service_date, due_date, amount, status, insurance_covered, patient_owed, services) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($invoices as $inv) {
    $stmt->execute($inv);
}

// Inventory
$inventory = [
    ['inv-item-1', 'Amoxicillin 500mg Capsules', 'MED-AMOX-500', 'Pharmaceuticals', 14, 50, 'Bottles (100ct)', 'Low Stock', '2023-09-15'],
    ['inv-item-2', 'Sterile Syringes 5ml (Luer Lock)', 'SUP-SYR-005', 'Supplies', 25, 100, 'Boxes (100ct)', 'Low Stock', '2023-09-20'],
    ['inv-item-3', 'Nitrile Exam Gloves (Medium)', 'PPE-GLV-MED', 'PPE', 8, 40, 'Boxes (200ct)', 'Low Stock', '2023-10-01'],
    ['inv-item-4', 'Rapid Influenza A+B Antigen Kits', 'DIA-FLU-AB', 'Diagnostic', 65, 30, 'Test Kits', 'In Stock', '2023-10-18'],
    ['inv-item-5', 'Lisinopril 10mg Tablets', 'MED-LIS-010', 'Pharmaceuticals', 120, 40, 'Bottles (100ct)', 'In Stock', '2023-10-10'],
    ['inv-item-6', 'Sterile Gauze Pads 4x4', 'SUP-GAU-4X4', 'Supplies', 180, 50, 'Boxes (50ct)', 'In Stock', '2023-10-05']
];
$stmt = $pdo->prepare("INSERT INTO inventory (id, name, sku, category, current_stock, min_threshold, unit, status, last_restocked) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($inventory as $item) {
    $stmt->execute($item);
}

echo "Database successfully initialized and seeded!\n";
