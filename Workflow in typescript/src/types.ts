export type NavTab = 
  | 'dashboard' 
  | 'patients' 
  | 'calendar' 
  | 'clinical-encounter' 
  | 'billing' 
  | 'inventory' 
  | 'settings' 
  | 'register-patient';

export type AppointmentStatus = 'Scheduled' | 'Arrived' | 'Waiting' | 'In Progress' | 'Completed' | 'Cancelled';

export type AppointmentType = 'Consultation' | 'Follow-up' | 'Routine Check' | 'Urgent Care' | 'Lab Results' | 'Physical Exam';

export interface Doctor {
  id: string;
  name: string;
  specialty: string;
  color: string; // for calendar badges
  dotColorClass: string;
  avatar: string;
}

export interface Patient {
  id: string;
  mrn: string;
  firstName: string;
  lastName: string;
  dob: string;
  age: number;
  gender: 'Female' | 'Male' | 'Other';
  phone: string;
  email: string;
  address: string;
  emergencyContact: {
    name: string;
    relationship: string;
    phone: string;
  };
  insurance: {
    provider: string;
    policyNumber: string;
    groupNumber: string;
  };
  clinicalOverview: {
    knownAllergies: string;
    bloodGroup: string;
    chronicConditions?: string;
    notes?: string;
  };
  avatar?: string;
  initials: string;
  registrationDate: string;
}

export interface Appointment {
  id: string;
  patientId: string;
  patientName: string;
  patientMrn: string;
  patientAvatar?: string;
  patientInitials: string;
  doctorId: string;
  doctorName: string;
  date: string; // YYYY-MM-DD
  time: string; // e.g. "09:00 AM"
  endTime?: string;
  timeSlot: string; // e.g. "09:00 - 09:45"
  type: AppointmentType;
  status: AppointmentStatus;
  room?: string;
  notes?: string;
  isUrgent?: boolean;
}

export interface QueueItem {
  id: string;
  patientId: string;
  patientName: string;
  mrn: string;
  time: string;
  doctorName: string;
  status: 'Waiting' | 'In Room' | 'Completed';
  room?: string;
  checkInTime?: string;
}

export interface Vitals {
  bloodPressure: string; // e.g. "120/80"
  heartRate: number; // e.g. 72
  temperature: number; // e.g. 98.6
  weight: number; // e.g. 145
  height?: number; // inches
  bmi?: number;
  oxygenSat?: number;
}

export interface PrescriptionItem {
  id: string;
  medicationName: string;
  dosage: string;
  frequency: string;
  duration?: string;
  instructions?: string;
}

export interface SOAPNotes {
  subjective: string;
  objective: string;
  assessmentCodes: Array<{ code: string; label: string }>;
  plan: string;
}

export interface PastVisit {
  id: string;
  date: string;
  title: string;
  summary: string;
  doctorName: string;
  vitals?: Partial<Vitals>;
  prescriptions?: PrescriptionItem[];
}

export interface ActivityItem {
  id: string;
  type: 'patient_registered' | 'visit_completed' | 'lab_received' | 'appointment_cancelled' | 'appointment_booked';
  title: string;
  detail: string;
  timestamp: string;
  badgeType: 'emerald' | 'blue' | 'amber' | 'rose' | 'purple';
}

export interface Invoice {
  id: string;
  invoiceNumber: string;
  patientName: string;
  patientMrn: string;
  serviceDate: string;
  dueDate: string;
  amount: number;
  status: 'Paid' | 'Pending' | 'Overdue';
  insuranceCovered: number;
  patientOwed: number;
  services: string[];
}

export interface InventoryItem {
  id: string;
  name: string;
  sku: string;
  category: 'Pharmaceuticals' | 'Supplies' | 'PPE' | 'Diagnostic';
  currentStock: number;
  minThreshold: number;
  unit: string;
  status: 'In Stock' | 'Low Stock' | 'Out of Stock';
  lastRestocked: string;
}
