import React from 'react';
import { ClinicProvider, useClinic } from './context/ClinicContext';
import { Sidebar } from './components/Sidebar';
import { TopNavbar } from './components/TopNavbar';
import { DashboardView } from './components/DashboardView';
import { RegisterPatientView } from './components/RegisterPatientView';
import { CalendarView } from './components/CalendarView';
import { ClinicalNotesView } from './components/ClinicalNotesView';
import { PatientsView } from './components/PatientsView';
import { BillingView } from './components/BillingView';
import { InventoryView } from './components/InventoryView';
import { SettingsView } from './components/SettingsView';
import { BookAppointmentModal } from './components/BookAppointmentModal';
import { PrintPrescriptionModal } from './components/PrintPrescriptionModal';
import { SearchModal } from './components/SearchModal';
import { Toast } from './components/Toast';

const MainLayout: React.FC = () => {
  const { activeTab } = useClinic();

  const renderActiveView = () => {
    switch (activeTab) {
      case 'dashboard':
        return <DashboardView />;
      case 'register-patient':
        return <RegisterPatientView />;
      case 'calendar':
        return <CalendarView />;
      case 'clinical-encounter':
        return <ClinicalNotesView />;
      case 'patients':
        return <PatientsView />;
      case 'billing':
        return <BillingView />;
      case 'inventory':
        return <InventoryView />;
      case 'settings':
        return <SettingsView />;
      default:
        return <DashboardView />;
    }
  };

  return (
    <div className="min-h-screen bg-[#FDFCFB] flex font-sans text-[#1C1C1C] antialiased selection:bg-[#1C1C1C] selection:text-white">
      {/* Fixed Left Sidebar */}
      <Sidebar />

      {/* Main Content Area */}
      <div className="flex-1 flex flex-col min-w-0 bg-[#FDFCFB]">
        {/* Top Navbar */}
        <TopNavbar />

        {/* Dynamic Page Content */}
        <main className="flex-1 pb-16 overflow-y-auto">
          {renderActiveView()}
        </main>
      </div>

      {/* Modals & Popovers */}
      <BookAppointmentModal />
      <PrintPrescriptionModal />
      <SearchModal />
      <Toast />
    </div>
  );
};

export default function App() {
  return (
    <ClinicProvider>
      <MainLayout />
    </ClinicProvider>
  );
}
