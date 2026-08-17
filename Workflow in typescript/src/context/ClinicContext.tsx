import React, { createContext, useContext, useState, useEffect } from 'react';
import {
  Patient,
  Doctor,
  Appointment,
  QueueItem,
  ActivityItem,
  Invoice,
  InventoryItem,
  NavTab,
  SOAPNotes,
  Vitals,
  PrescriptionItem,
  PastVisit,
  AppointmentStatus,
} from '../types';
import {
  DOCTORS,
  INITIAL_PATIENTS,
  INITIAL_APPOINTMENTS,
  INITIAL_QUEUE,
  INITIAL_ACTIVITIES,
  INITIAL_INVOICES,
  INITIAL_INVENTORY,
  INITIAL_VITALS,
  INITIAL_SOAP,
  INITIAL_PRESCRIPTIONS,
  INITIAL_PAST_VISITS,
} from '../data/mockData';

interface ToastMessage {
  id: string;
  type: 'success' | 'info' | 'warning' | 'error';
  title: string;
  message: string;
}

interface ClinicContextType {
  activeTab: NavTab;
  setActiveTab: (tab: NavTab) => void;
  currentDoctor: Doctor;
  setCurrentDoctor: (doc: Doctor) => void;
  doctors: Doctor[];
  
  // Patients
  patients: Patient[];
  activePatient: Patient;
  setActivePatient: (patient: Patient) => void;
  addPatient: (patientData: Omit<Patient, 'id' | 'mrn' | 'registrationDate' | 'initials' | 'age'>) => Patient;
  updatePatient: (id: string, updates: Partial<Patient>) => void;
  
  // Appointments
  appointments: Appointment[];
  addAppointment: (appointmentData: Omit<Appointment, 'id'>) => void;
  updateAppointmentStatus: (id: string, status: AppointmentStatus, room?: string) => void;
  
  // Queue
  queue: QueueItem[];
  checkInPatient: (queueId: string) => void;
  assignPatientToRoom: (queueId: string, room: string) => void;
  completeQueueItem: (queueId: string) => void;
  
  // Clinical Encounter (SOAP)
  vitals: Vitals;
  updateVitals: (updates: Partial<Vitals>) => void;
  soapNotes: SOAPNotes;
  updateSoapNotes: (updates: Partial<SOAPNotes>) => void;
  prescriptions: PrescriptionItem[];
  addPrescription: (rx: Omit<PrescriptionItem, 'id'>) => void;
  removePrescription: (id: string) => void;
  pastVisits: PastVisit[];
  finishVisit: () => void;
  startEncounterForPatient: (patient: Patient) => void;

  // Activities
  activities: ActivityItem[];
  addActivity: (title: string, detail: string, type?: ActivityItem['type'], badgeType?: ActivityItem['badgeType']) => void;

  // Billing & Inventory
  invoices: Invoice[];
  markInvoicePaid: (id: string) => void;
  addInvoice: (inv: Omit<Invoice, 'id' | 'invoiceNumber'>) => void;
  inventory: InventoryItem[];
  restockItem: (id: string, amount: number) => void;

  // Modals & UI Controls
  isBookModalOpen: boolean;
  setIsBookModalOpen: (open: boolean) => void;
  isPrintRxModalOpen: boolean;
  setIsPrintRxModalOpen: (open: boolean) => void;
  isSearchModalOpen: boolean;
  setIsSearchModalOpen: (open: boolean) => void;
  searchQuery: string;
  setSearchQuery: (q: string) => void;

  // Toasts
  toasts: ToastMessage[];
  showToast: (title: string, message: string, type?: ToastMessage['type']) => void;
  removeToast: (id: string) => void;
}

const ClinicContext = createContext<ClinicContextType | undefined>(undefined);

export const ClinicProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [activeTab, setActiveTab] = useState<NavTab>('dashboard');
  const [doctors] = useState<Doctor[]>(DOCTORS);
  const [currentDoctor, setCurrentDoctor] = useState<Doctor>(DOCTORS[0]);

  // Patients state
  const [patients, setPatients] = useState<Patient[]>(() => {
    const saved = localStorage.getItem('clinicflow_patients');
    return saved ? JSON.parse(saved) : INITIAL_PATIENTS;
  });
  const [activePatient, setActivePatient] = useState<Patient>(INITIAL_PATIENTS[0]);

  // Appointments state
  const [appointments, setAppointments] = useState<Appointment[]>(() => {
    const saved = localStorage.getItem('clinicflow_appointments');
    return saved ? JSON.parse(saved) : INITIAL_APPOINTMENTS;
  });

  // Queue state
  const [queue, setQueue] = useState<QueueItem[]>(() => {
    const saved = localStorage.getItem('clinicflow_queue');
    return saved ? JSON.parse(saved) : INITIAL_QUEUE;
  });

  // Clinical Encounter State
  const [vitals, setVitals] = useState<Vitals>(INITIAL_VITALS);
  const [soapNotes, setSoapNotes] = useState<SOAPNotes>(INITIAL_SOAP);
  const [prescriptions, setPrescriptions] = useState<PrescriptionItem[]>(INITIAL_PRESCRIPTIONS);
  const [pastVisits, setPastVisits] = useState<PastVisit[]>(INITIAL_PAST_VISITS);

  // Activities state
  const [activities, setActivities] = useState<ActivityItem[]>(INITIAL_ACTIVITIES);

  // Billing & Inventory
  const [invoices, setInvoices] = useState<Invoice[]>(INITIAL_INVOICES);
  const [inventory, setInventory] = useState<InventoryItem[]>(INITIAL_INVENTORY);

  // Modals & Search
  const [isBookModalOpen, setIsBookModalOpen] = useState(false);
  const [isPrintRxModalOpen, setIsPrintRxModalOpen] = useState(false);
  const [isSearchModalOpen, setIsSearchModalOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  // Toasts
  const [toasts, setToasts] = useState<ToastMessage[]>([]);

  // Local storage persistence
  useEffect(() => {
    localStorage.setItem('clinicflow_patients', JSON.stringify(patients));
  }, [patients]);

  useEffect(() => {
    localStorage.setItem('clinicflow_appointments', JSON.stringify(appointments));
  }, [appointments]);

  useEffect(() => {
    localStorage.setItem('clinicflow_queue', JSON.stringify(queue));
  }, [queue]);

  const showToast = (title: string, message: string, type: ToastMessage['type'] = 'success') => {
    const id = Math.random().toString(36).substring(2, 9);
    setToasts((prev) => [...prev, { id, title, message, type }]);
    setTimeout(() => {
      removeToast(id);
    }, 4000);
  };

  const removeToast = (id: string) => {
    setToasts((prev) => prev.filter((t) => t.id !== id));
  };

  const addActivity = (
    title: string,
    detail: string,
    type: ActivityItem['type'] = 'appointment_booked',
    badgeType: ActivityItem['badgeType'] = 'blue'
  ) => {
    const newAct: ActivityItem = {
      id: `act-${Date.now()}`,
      type,
      title,
      detail,
      timestamp: 'Just now',
      badgeType,
    };
    setActivities((prev) => [newAct, ...prev]);
  };

  const addPatient = (patientData: Omit<Patient, 'id' | 'mrn' | 'registrationDate' | 'initials' | 'age'>): Patient => {
    const birthYear = new Date(patientData.dob).getFullYear() || 1990;
    const age = new Date().getFullYear() - birthYear;
    const mrnNumber = Math.floor(10000 + Math.random() * 90000);
    const mrn = `#${mrnNumber}`;
    const initials = `${patientData.firstName[0] || ''}${patientData.lastName[0] || ''}`.toUpperCase();

    const newPatient: Patient = {
      ...patientData,
      id: `pat-${Date.now()}`,
      mrn,
      age: isNaN(age) ? 30 : age,
      initials,
      registrationDate: new Date().toISOString().split('T')[0],
    };

    setPatients((prev) => [newPatient, ...prev]);
    addActivity(`New Patient Registered: ${newPatient.firstName} ${newPatient.lastName}`, 'Just now • via Portal', 'patient_registered', 'emerald');
    showToast('Patient Registered', `${newPatient.firstName} ${newPatient.lastName} (${newPatient.mrn}) was registered successfully.`);
    return newPatient;
  };

  const updatePatient = (id: string, updates: Partial<Patient>) => {
    setPatients((prev) =>
      prev.map((p) => {
        if (p.id === id) {
          const updated = { ...p, ...updates };
          if (activePatient.id === id) {
            setActivePatient(updated);
          }
          return updated;
        }
        return p;
      })
    );
    showToast('Patient Updated', 'Patient records successfully updated.');
  };

  const addAppointment = (appointmentData: Omit<Appointment, 'id'>) => {
    const newAppt: Appointment = {
      ...appointmentData,
      id: `apt-${Date.now()}`,
    };
    setAppointments((prev) => [newAppt, ...prev]);

    // Also add to queue if scheduled for today
    const newQueueItem: QueueItem = {
      id: `q-${Date.now()}`,
      patientId: newAppt.patientId,
      patientName: newAppt.patientName,
      mrn: newAppt.patientMrn,
      time: newAppt.time,
      doctorName: newAppt.doctorName,
      status: 'Waiting',
      checkInTime: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };
    setQueue((prev) => [newQueueItem, ...prev]);

    addActivity(
      `Appointment Booked: ${newAppt.patientName}`,
      `For ${newAppt.time} with ${newAppt.doctorName}`,
      'appointment_booked',
      'blue'
    );
    showToast('Appointment Scheduled', `Appointment for ${newAppt.patientName} on ${newAppt.date} at ${newAppt.time} booked.`);
  };

  const updateAppointmentStatus = (id: string, status: AppointmentStatus, room?: string) => {
    setAppointments((prev) =>
      prev.map((apt) => (apt.id === id ? { ...apt, status, room: room || apt.room } : apt))
    );
    showToast('Status Updated', `Appointment status changed to "${status}".`, 'info');
  };

  const checkInPatient = (queueId: string) => {
    setQueue((prev) =>
      prev.map((item) => {
        if (item.id === queueId) {
          return {
            ...item,
            status: 'In Room',
            room: 'Room 2',
            checkInTime: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
          };
        }
        return item;
      })
    );
    showToast('Patient Checked In', 'Patient moved to In Room (Room 2).', 'info');
  };

  const assignPatientToRoom = (queueId: string, room: string) => {
    setQueue((prev) =>
      prev.map((item) => (item.id === queueId ? { ...item, status: 'In Room', room } : item))
    );
    showToast('Room Assigned', `Patient directed to ${room}.`);
  };

  const completeQueueItem = (queueId: string) => {
    const item = queue.find((q) => q.id === queueId);
    setQueue((prev) => prev.filter((q) => q.id !== queueId));
    if (item) {
      addActivity(`Visit Completed: ${item.patientName}`, `Just now • ${item.doctorName}`, 'visit_completed', 'blue');
      showToast('Queue Updated', `${item.patientName}'s visit has completed.`);
    }
  };

  const updateVitals = (updates: Partial<Vitals>) => {
    setVitals((prev) => ({ ...prev, ...updates }));
  };

  const updateSoapNotes = (updates: Partial<SOAPNotes>) => {
    setSoapNotes((prev) => ({ ...prev, ...updates }));
  };

  const addPrescription = (rx: Omit<PrescriptionItem, 'id'>) => {
    const newRx: PrescriptionItem = {
      ...rx,
      id: `rx-${Date.now()}`,
    };
    setPrescriptions((prev) => [...prev, newRx]);
    showToast('Medication Added', `${rx.medicationName} ${rx.dosage} (${rx.frequency}) added to prescription.`);
  };

  const removePrescription = (id: string) => {
    setPrescriptions((prev) => prev.filter((rx) => rx.id !== id));
    showToast('Medication Removed', 'Prescription line removed.', 'info');
  };

  const startEncounterForPatient = (patient: Patient) => {
    setActivePatient(patient);
    // customize mock vitals slightly for realism
    setVitals({
      bloodPressure: patient.id === 'pat-1' ? '120/80' : '118/76',
      heartRate: 72,
      temperature: 98.6,
      weight: patient.gender === 'Female' ? 145 : 175,
      height: 66,
      bmi: 23.4,
      oxygenSat: 99,
    });
    // set initial SOAP if patient has known condition
    setSoapNotes({
      subjective: `Patient reports for clinical evaluation. Chief concern: ${patient.clinicalOverview.chronicConditions || 'General checkup and consultation'}.`,
      objective: `Physical exam: Vitals stable. Alert and oriented x4. Heart regular rate and rhythm. Lungs clear to auscultation bilaterally.`,
      assessmentCodes: patient.id === 'pat-1' 
        ? [{ code: 'J01.90', label: 'Acute sinusitis, unspecified' }]
        : [{ code: 'I10', label: 'Essential (primary) hypertension' }],
      plan: `Continue prescribed regimen. Follow up in 3 months or PRN. Prescribed necessary maintenance medications.`,
    });
    setActiveTab('clinical-encounter');
  };

  const finishVisit = () => {
    // Record visit in past visits
    const newPastVisit: PastVisit = {
      id: `pv-${Date.now()}`,
      date: new Date().toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
      title: soapNotes.assessmentCodes[0]?.label || 'Clinical Consultation',
      summary: `${soapNotes.plan.slice(0, 80)}...`,
      doctorName: currentDoctor.name,
      vitals: { ...vitals },
      prescriptions: [...prescriptions],
    };

    setPastVisits((prev) => [newPastVisit, ...prev]);
    addActivity(`Visit Completed: ${activePatient.firstName} ${activePatient.lastName}`, `Just now • ${currentDoctor.name}`, 'visit_completed', 'blue');
    
    // Complete in queue if exists
    setQueue((prev) => prev.filter((q) => q.patientId !== activePatient.id && q.patientName !== `${activePatient.firstName} ${activePatient.lastName}`));

    showToast('Visit Completed!', `Encounter for ${activePatient.firstName} ${activePatient.lastName} has been finalized and archived.`);
  };

  const markInvoicePaid = (id: string) => {
    setInvoices((prev) =>
      prev.map((inv) => (inv.id === id ? { ...inv, status: 'Paid', patientOwed: 0 } : inv))
    );
    showToast('Invoice Paid', 'Payment confirmed and receipt generated.');
  };

  const addInvoice = (inv: Omit<Invoice, 'id' | 'invoiceNumber'>) => {
    const invNumber = `INV-2023-${Math.floor(1000 + Math.random() * 9000)}`;
    const newInv: Invoice = {
      ...inv,
      id: `inv-${Date.now()}`,
      invoiceNumber: invNumber,
    };
    setInvoices((prev) => [newInv, ...prev]);
    showToast('Invoice Created', `Invoice ${invNumber} generated for ${newInv.patientName}.`);
  };

  const restockItem = (id: string, amount: number) => {
    setInventory((prev) =>
      prev.map((item) => {
        if (item.id === id) {
          const newStock = item.currentStock + amount;
          return {
            ...item,
            currentStock: newStock,
            status: newStock <= item.minThreshold ? 'Low Stock' : 'In Stock',
            lastRestocked: new Date().toISOString().split('T')[0],
          };
        }
        return item;
      })
    );
    showToast('Inventory Restocked', `Added ${amount} units to stock.`);
  };

  return (
    <ClinicContext.Provider
      value={{
        activeTab,
        setActiveTab,
        currentDoctor,
        setCurrentDoctor,
        doctors,
        patients,
        activePatient,
        setActivePatient,
        addPatient,
        updatePatient,
        appointments,
        addAppointment,
        updateAppointmentStatus,
        queue,
        checkInPatient,
        assignPatientToRoom,
        completeQueueItem,
        vitals,
        updateVitals,
        soapNotes,
        updateSoapNotes,
        prescriptions,
        addPrescription,
        removePrescription,
        pastVisits,
        finishVisit,
        startEncounterForPatient,
        activities,
        addActivity,
        invoices,
        markInvoicePaid,
        addInvoice,
        inventory,
        restockItem,
        isBookModalOpen,
        setIsBookModalOpen,
        isPrintRxModalOpen,
        setIsPrintRxModalOpen,
        isSearchModalOpen,
        setIsSearchModalOpen,
        searchQuery,
        setSearchQuery,
        toasts,
        showToast,
        removeToast,
      }}
    >
      {children}
    </ClinicContext.Provider>
  );
};

export const useClinic = () => {
  const context = useContext(ClinicContext);
  if (!context) {
    throw new Error('useClinic must be used within a ClinicProvider');
  }
  return context;
};
