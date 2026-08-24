import React from 'react';
import {
  Users,
  UserPlus,
  Receipt,
  Package,
  Calendar,
  AlertTriangle,
  Clock,
  CheckCircle2,
  FileText,
  CalendarX,
  AlertCircle,
  Plus,
  ChevronRight,
  TrendingUp,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';
import { Appointment, Patient } from '../types';

export const DashboardView: React.FC = () => {
  const {
    currentDoctor,
    appointments,
    patients,
    activities,
    setActiveTab,
    setIsBookModalOpen,
    startEncounterForPatient,
    showToast,
  } = useClinic();

  const handlePatientRowClick = (apt: Appointment) => {
    if (!apt) return;
    const patientObj = (patients || []).find((p) => p.id === apt.patientId) || {
      id: apt.patientId || 'pat-1',
      mrn: apt.patientMrn || '#10001',
      firstName: apt.patientName?.split(' ')?.[0] || 'Patient',
      lastName: apt.patientName?.split(' ')?.[1] || '',
      dob: '1985-04-12',
      age: 38,
      gender: 'Female' as const,
      phone: '(555) 234-5678',
      email: 'patient@example.com',
      address: '742 Evergreen Terrace, Springfield, OR',
      emergencyContact: { name: 'Contact', relationship: 'Spouse', phone: '(555) 987-6543' },
      insurance: { provider: 'Blue Cross Blue Shield', policyNumber: 'BCBS-9842109', groupNumber: 'GRP-44120' },
      clinicalOverview: { knownAllergies: 'Penicillin Allergy', bloodGroup: 'O+' },
      initials: apt.patientInitials || 'P',
      registrationDate: '2023-01-15',
    };
    startEncounterForPatient(patientObj);
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'Arrived':
        return (
          <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-800">
            <span className="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
            Arrived
          </span>
        );
      case 'In Progress':
        return (
          <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
            <span className="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
            In Progress
          </span>
        );
      case 'Waiting':
        return (
          <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
            <span className="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
            Waiting
          </span>
        );
      case 'Scheduled':
      default:
        return (
          <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100/70 text-blue-800">
            <span className="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
            Scheduled
          </span>
        );
    }
  };

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'patient_registered':
        return (
          <div className="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
            <UserPlus className="w-3.5 h-3.5" />
          </div>
        );
      case 'visit_completed':
        return (
          <div className="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center shrink-0 mt-0.5">
            <CheckCircle2 className="w-3.5 h-3.5" />
          </div>
        );
      case 'lab_received':
        return (
          <div className="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
            <FileText className="w-3.5 h-3.5" />
          </div>
        );
      case 'appointment_cancelled':
        return (
          <div className="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 mt-0.5">
            <CalendarX className="w-3.5 h-3.5" />
          </div>
        );
      default:
        return (
          <div className="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0 mt-0.5">
            <Clock className="w-3.5 h-3.5" />
          </div>
        );
    }
  };

  return (
    <div id="dashboard-view" className="p-8 max-w-[1600px] mx-auto space-y-6 animate-in fade-in duration-200 font-sans">
      {/* Welcome Banner */}
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <p className="text-sm font-medium text-slate-600">
            Welcome back, <span className="font-semibold text-slate-900">{currentDoctor?.name || 'Dr. Sarah Jenkins'}</span>. Here is today's summary.
          </p>
        </div>
        <div className="flex items-center gap-2">
          <div className="inline-flex items-center gap-2 px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 shadow-xs">
            <Calendar className="w-3.5 h-3.5 text-slate-400" />
            <span>October 24, 2023</span>
          </div>
        </div>
      </div>

      {/* 4 Metric Cards Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Card 1: Today's Appointments */}
        <div
          id="metric-todays-appointments"
          onClick={() => setActiveTab('calendar')}
          className="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-blue-300 transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-start justify-between">
            <div className="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white">
              <Users className="w-5 h-5" />
            </div>
            <span className="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
              +12%
            </span>
          </div>
          <div className="mt-4">
            <p className="text-xs font-medium text-slate-500">Today's Appointments</p>
            <div className="flex items-baseline gap-1 mt-1">
              <span className="text-3xl font-bold text-slate-900">42</span>
              <span className="text-xs font-medium text-slate-400">/ 48</span>
            </div>
            <div className="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
              <div className="bg-blue-600 h-full rounded-full" style={{ width: '87.5%' }}></div>
            </div>
          </div>
        </div>

        {/* Card 2: New Patients (Week) */}
        <div
          id="metric-new-patients"
          onClick={() => setActiveTab('patients')}
          className="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-start justify-between">
            <div className="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white">
              <UserPlus className="w-5 h-5" />
            </div>
            <span className="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
              +5%
            </span>
          </div>
          <div className="mt-4">
            <p className="text-xs font-medium text-slate-500">New Patients (Week)</p>
            <span className="text-3xl font-bold text-slate-900 mt-1 block">18</span>
          </div>
        </div>

        {/* Card 3: Pending Billing */}
        <div
          id="metric-pending-billing"
          onClick={() => setActiveTab('billing')}
          className="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-start justify-between">
            <div className="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white">
              <Receipt className="w-5 h-5" />
            </div>
          </div>
          <div className="mt-4">
            <p className="text-xs font-medium text-slate-500">Pending Billing</p>
            <span className="text-3xl font-bold text-slate-900 mt-1 block">$4,250</span>
            <p className="text-xs text-amber-600 font-semibold mt-1 flex items-center gap-1">
              <AlertTriangle className="w-3.5 h-3.5" />
              <span>12 invoices overdue</span>
            </p>
          </div>
        </div>

        {/* Card 4: Low Stock Alerts */}
        <div
          id="metric-low-stock"
          onClick={() => setActiveTab('inventory')}
          className="bg-white p-5 rounded-2xl border border-red-200 shadow-xs hover:border-red-400 transition-all cursor-pointer flex flex-col justify-between"
        >
          <div className="flex items-start justify-between">
            <div className="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
              <Package className="w-5 h-5" />
            </div>
            <span className="w-2 h-2 rounded-full bg-red-500"></span>
          </div>
          <div className="mt-4">
            <p className="text-xs font-medium text-slate-500">Low Stock Alerts</p>
            <span className="text-3xl font-bold text-red-600 mt-1 block">3</span>
            <p className="text-xs text-red-600 font-semibold mt-1">Items need reorder</p>
          </div>
        </div>
      </div>

      {/* Main Grid: Upcoming Appointments + Quick Actions & Recent Activity */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Upcoming Appointments Table */}
        <div className="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-base font-bold text-slate-900 flex items-center gap-2">
              <Calendar className="w-5 h-5 text-blue-600" />
              <span>Upcoming Appointments</span>
            </h2>
            <button
              id="btn-view-all-appointments"
              onClick={() => setActiveTab('calendar')}
              className="text-xs font-bold text-blue-600 hover:text-blue-800 transition uppercase tracking-wider cursor-pointer"
            >
              VIEW ALL
            </button>
          </div>

          <div className="overflow-x-auto">
            <table id="upcoming-appointments-table" className="w-full text-left text-xs">
              <thead>
                <tr className="border-b border-slate-200 text-slate-400 font-semibold uppercase text-[10px] tracking-wider">
                  <th className="py-3 px-3">Time</th>
                  <th className="py-3 px-3">Patient</th>
                  <th className="py-3 px-3">Doctor</th>
                  <th className="py-3 px-3">Type</th>
                  <th className="py-3 px-3 text-right">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100 font-medium">
                {(appointments || []).slice(0, 5).map((apt) => (
                  <tr
                    key={apt.id}
                    onClick={() => handlePatientRowClick(apt)}
                    className="hover:bg-slate-50/80 transition-colors cursor-pointer"
                  >
                    <td className="py-3 px-3 text-slate-900 font-semibold whitespace-nowrap">
                      {apt.isUrgent && <span className="text-red-500 font-bold mr-1">!</span>}
                      {apt.time}
                    </td>

                    <td className="py-3 px-3">
                      <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-full bg-blue-100 text-blue-800 text-xs font-bold flex items-center justify-center shrink-0">
                          {apt.patientInitials}
                        </div>
                        <div>
                          <div className="font-bold text-slate-900">{apt.patientName}</div>
                          <div className="text-[10px] text-slate-400 font-normal">MRN: {apt.patientMrn}</div>
                        </div>
                      </div>
                    </td>

                    <td className="py-3 px-3 text-slate-600 whitespace-nowrap">{apt.doctorName}</td>
                    <td className="py-3 px-3 text-slate-600 whitespace-nowrap">{apt.type}</td>
                    <td className="py-3 px-3 text-right whitespace-nowrap">{getStatusBadge(apt.status)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Right Column Stack */}
        <div className="space-y-6">
          {/* Quick Actions Card */}
          <div id="quick-actions-card" className="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 className="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
              <span className="material-symbols-outlined text-blue-600 text-lg">bolt</span>
              <span>Quick Actions</span>
            </h2>
            <div className="space-y-3">
              <button
                id="btn-quick-register-patient"
                onClick={() => setActiveTab('register-patient')}
                className="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-[#0f2d71] hover:bg-[#0a1f50] text-white font-semibold text-xs rounded-xl shadow-xs transition cursor-pointer"
              >
                <UserPlus className="w-4 h-4" />
                <span>Register New Patient</span>
              </button>

              <button
                id="btn-quick-book-appointment"
                onClick={() => setIsBookModalOpen(true)}
                className="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-white border border-blue-200 text-blue-900 hover:bg-blue-50 font-semibold text-xs rounded-xl transition cursor-pointer"
              >
                <Calendar className="w-4 h-4" />
                <span>Book Appointment</span>
              </button>
            </div>
          </div>

          {/* Recent Activity Card */}
          <div id="recent-activity-card" className="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
            <h2 className="text-base font-bold text-slate-900 flex items-center gap-2 mb-4">
              <Clock className="w-4 h-4 text-blue-600" />
              <span>Recent Activity</span>
            </h2>

            <div className="space-y-4">
              {(activities || []).slice(0, 4).map((act) => (
                <div key={act.id} className="flex items-start gap-3">
                  {getActivityIcon(act.type)}
                  <div>
                    <p className="text-xs font-bold text-slate-900">
                      {act.title}: <span className="font-normal text-slate-700">{act.detail}</span>
                    </p>
                    <p className="text-[10px] text-slate-400 mt-0.5">Recently updated</p>
                  </div>
                </div>
              ))}
            </div>

            <button
              id="btn-load-more-activity"
              onClick={() => showToast('Audit Ledger', 'Clinical telemetry records up to date.', 'info')}
              className="w-full mt-4 py-2 text-center text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-wider border-t border-slate-100 pt-3 cursor-pointer"
            >
              LOAD MORE
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
