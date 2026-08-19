import React from 'react';
import {
  Users,
  UserPlus,
  Receipt,
  PackageX,
  Calendar,
  AlertTriangle,
  Clock,
  CheckCircle2,
  FileText,
  CalendarX,
  AlertCircle,
  ArrowUpRight,
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
    const patientObj = patients.find((p) => p.id === apt.patientId) || {
      id: apt.patientId,
      mrn: apt.patientMrn,
      firstName: apt.patientName.split(' ')[0] || 'Patient',
      lastName: apt.patientName.split(' ')[1] || '',
      dob: '1985-04-12',
      age: 38,
      gender: 'Female' as const,
      phone: '(555) 234-5678',
      email: 'patient@example.com',
      address: '742 Evergreen Terrace, Springfield, OR',
      emergencyContact: { name: 'Contact', relationship: 'Spouse', phone: '(555) 987-6543' },
      insurance: { provider: 'Blue Cross Blue Shield', policyNumber: 'BCBS-9842109', groupNumber: 'GRP-44120' },
      clinicalOverview: { knownAllergies: 'Penicillin Allergy', bloodGroup: 'O+' },
      initials: apt.patientInitials,
      registrationDate: '2023-01-15',
    };
    startEncounterForPatient(patientObj);
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'Arrived':
        return (
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 border border-black/20 bg-white text-[10px] font-mono uppercase tracking-wider text-[#1C1C1C]">
            <span className="w-1.5 h-1.5 bg-[#1C1C1C]"></span>
            Arrived
          </span>
        );
      case 'In Progress':
        return (
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 border border-amber-500/40 bg-amber-50 text-[10px] font-mono uppercase tracking-wider text-amber-900">
            <span className="w-1.5 h-1.5 bg-amber-600 animate-pulse"></span>
            In Progress
          </span>
        );
      case 'Waiting':
        return (
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 border border-rose-400/40 bg-rose-50 text-[10px] font-mono uppercase tracking-wider text-rose-900">
            <span className="w-1.5 h-1.5 bg-rose-600"></span>
            Waiting
          </span>
        );
      case 'Scheduled':
      default:
        return (
          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 border border-black/10 bg-[#F5F5F0] text-[10px] font-mono uppercase tracking-wider text-[#555]">
            <span className="w-1.5 h-1.5 bg-[#888]"></span>
            Scheduled
          </span>
        );
    }
  };

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'patient_registered':
        return (
          <div className="w-7 h-7 border border-black flex items-center justify-center bg-black text-white shrink-0">
            <UserPlus className="w-3.5 h-3.5" />
          </div>
        );
      case 'visit_completed':
        return (
          <div className="w-7 h-7 border border-black/20 flex items-center justify-center bg-[#F5F5F0] text-black shrink-0">
            <CheckCircle2 className="w-3.5 h-3.5" />
          </div>
        );
      case 'lab_received':
        return (
          <div className="w-7 h-7 border border-amber-300 flex items-center justify-center bg-amber-50 text-amber-900 shrink-0">
            <FileText className="w-3.5 h-3.5" />
          </div>
        );
      case 'appointment_cancelled':
        return (
          <div className="w-7 h-7 border border-rose-300 flex items-center justify-center bg-rose-50 text-rose-900 shrink-0">
            <CalendarX className="w-3.5 h-3.5" />
          </div>
        );
      default:
        return (
          <div className="w-7 h-7 border border-black/20 flex items-center justify-center bg-white text-black shrink-0">
            <Clock className="w-3.5 h-3.5" />
          </div>
        );
    }
  };

  return (
    <div id="dashboard-view" className="p-8 lg:p-10 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Editorial Hero Header */}
      <div className="border border-black/15 bg-white p-8 relative overflow-hidden flex flex-col md:flex-row md:items-end justify-between gap-6 shadow-2xs">
        <div className="absolute top-2 right-6 text-[140px] font-serif italic leading-none opacity-5 select-none pointer-events-none">
          CC
        </div>
        <div className="z-10 max-w-2xl">
          <div className="flex items-center gap-2 mb-2">
            <span className="w-2 h-2 bg-black"></span>
            <span className="text-[10px] font-mono font-bold tracking-[0.35em] uppercase text-[#777]">
              OPERATIONS ARCHIVE • ACTIVE ROSTER
            </span>
          </div>
          <h2 className="text-3xl sm:text-4xl font-serif italic text-[#1C1C1C] tracking-tight leading-tight">
            Consultation Matrix & Overview
          </h2>
          <p className="text-xs text-[#666] tracking-wide mt-2">
            Welcome, <span className="font-semibold text-black">{currentDoctor.name}</span> ({currentDoctor.specialty}). All triage queues and laboratory telemetry are synchronized.
          </p>
        </div>

        {/* Date Selector Badge */}
        <div className="z-10 flex items-center gap-3 bg-[#FDFCFB] border border-black/20 px-4 py-2 text-xs font-mono text-[#1C1C1C]">
          <Calendar className="w-3.5 h-3.5 text-[#555]" />
          <span className="uppercase tracking-widest font-semibold">OCTOBER 24, 2026</span>
        </div>
      </div>

      {/* 4 Artistic Metric Cards Grid (Theme Palette: Stark White, Pitch Black, Warm Stone) */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Card 01: Today's Appointments (Stark White) */}
        <div
          id="metric-todays-appointments"
          onClick={() => setActiveTab('calendar')}
          className="bg-white border border-black/20 p-6 hover:border-black transition-all cursor-pointer flex flex-col justify-between group shadow-2xs"
        >
          <div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-mono text-[#888] font-bold">01 / QUEUE</span>
              <span className="text-[10px] font-mono border border-black/20 px-2 py-0.5 uppercase tracking-wider bg-[#F5F5F0]">
                +12% CAP
              </span>
            </div>
            <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#555] mb-2">
              Today's Encounters
            </h3>
            <div className="flex items-baseline gap-2">
              <span className="text-4xl font-serif italic text-[#1C1C1C] leading-none">42</span>
              <span className="text-sm font-mono text-[#888]">/ 48 Target</span>
            </div>
          </div>
          <div className="mt-6 pt-4 border-t border-black/10">
            <div className="w-full bg-[#E5E5E5] h-1.5 overflow-hidden">
              <div
                className="bg-[#1C1C1C] h-full transition-all duration-500"
                style={{ width: `${(42 / 48) * 100}%` }}
              ></div>
            </div>
            <span className="text-[10px] font-mono text-[#777] mt-2 block tracking-wider uppercase">
              87.5% Completed or in-room
            </span>
          </div>
        </div>

        {/* Card 02: New Patients Intake (Inverted Black Block from Theme) */}
        <div
          id="metric-new-patients"
          onClick={() => setActiveTab('patients')}
          className="bg-[#1C1C1C] text-white border border-black p-6 hover:bg-black transition-all cursor-pointer flex flex-col justify-between group shadow-2xs"
        >
          <div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-mono text-white/60 font-bold">02 / INTAKE</span>
              <span className="text-[10px] font-mono border border-white/30 px-2 py-0.5 uppercase tracking-wider bg-white/10">
                +5% WK
              </span>
            </div>
            <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-white/80 mb-2">
              New Patient Intakes
            </h3>
            <div className="flex items-baseline gap-2">
              <span className="text-4xl font-serif italic text-white leading-none">18</span>
              <span className="text-xs font-mono text-white/50">Verified MRNs</span>
            </div>
          </div>
          <div className="mt-6 pt-4 border-t border-white/15 flex items-center justify-between text-[10px] font-mono text-white/70">
            <span className="tracking-wider uppercase">Registry expansion</span>
            <ArrowUpRight className="w-3.5 h-3.5 text-white/60" />
          </div>
        </div>

        {/* Card 03: Pending Ledger (Warm Stone Tone #F5F5F0 from Theme) */}
        <div
          id="metric-pending-billing"
          onClick={() => setActiveTab('billing')}
          className="bg-[#F5F5F0] border border-black/20 p-6 hover:border-black transition-all cursor-pointer flex flex-col justify-between group shadow-2xs"
        >
          <div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-mono text-[#777] font-bold">03 / LEDGER</span>
              <span className="text-[10px] font-mono border border-amber-600/30 px-2 py-0.5 uppercase tracking-wider bg-amber-100 text-amber-900">
                AUDIT
              </span>
            </div>
            <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#555] mb-2">
              Pending Balances
            </h3>
            <div className="flex items-baseline gap-2">
              <span className="text-4xl font-serif italic text-[#1C1C1C] leading-none">$4,250</span>
            </div>
          </div>
          <div className="mt-6 pt-4 border-t border-black/10 flex items-center gap-1.5 text-[10px] font-mono text-amber-900">
            <AlertTriangle className="w-3 h-3 text-amber-700 shrink-0" />
            <span className="uppercase tracking-wider">12 Invoices pending claim</span>
          </div>
        </div>

        {/* Card 04: Critical Apothecary / Low Stock */}
        <div
          id="metric-low-stock"
          onClick={() => setActiveTab('inventory')}
          className="bg-white border border-rose-300 p-6 hover:border-rose-700 transition-all cursor-pointer flex flex-col justify-between group shadow-2xs"
        >
          <div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-xs font-mono text-rose-800 font-bold">04 / APOTHECARY</span>
              <span className="text-[10px] font-mono border border-rose-300 px-2 py-0.5 uppercase tracking-wider bg-rose-50 text-rose-900">
                CRITICAL
              </span>
            </div>
            <h3 className="text-xs font-bold uppercase tracking-[0.2em] text-[#555] mb-2">
              Stock Reorders
            </h3>
            <div className="flex items-baseline gap-2">
              <span className="text-4xl font-serif italic text-rose-900 leading-none">03</span>
              <span className="text-xs font-mono text-rose-700">Formularies</span>
            </div>
          </div>
          <div className="mt-6 pt-4 border-t border-rose-200 flex items-center justify-between text-[10px] font-mono text-rose-900">
            <span className="tracking-wider uppercase">Amoxicillin & Epinephrine</span>
            <AlertCircle className="w-3.5 h-3.5 text-rose-700" />
          </div>
        </div>
      </div>

      {/* Main Content: Upcoming Appointments + Right Column */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Left Column: Upcoming Appointments (8 cols) */}
        <div className="lg:col-span-8 bg-white border border-black/20 shadow-2xs">
          {/* Header */}
          <div className="px-6 py-5 border-b border-black/15 flex items-center justify-between">
            <div className="flex items-center gap-3">
              <span className="w-2 h-2 bg-black"></span>
              <div>
                <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                  TIMELINE
                </span>
                <h3 className="font-serif italic text-xl text-[#1C1C1C]">Today's Scheduled Consultations</h3>
              </div>
            </div>
            <button
              id="btn-view-all-appointments"
              onClick={() => setActiveTab('calendar')}
              className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] border border-black/20 hover:border-black px-3 py-1.5 bg-[#F5F5F0] hover:bg-black hover:text-white transition-all cursor-pointer"
            >
              FULL CALENDAR →
            </button>
          </div>

          {/* Table */}
          <div className="overflow-x-auto">
            <table id="upcoming-appointments-table" className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-black/15 text-[10px] font-mono font-semibold text-[#777] uppercase tracking-[0.2em] bg-[#FDFCFB]">
                  <th className="py-3 px-6">Time Slot</th>
                  <th className="py-3 px-6">Patient & MRN</th>
                  <th className="py-3 px-6">Practitioner</th>
                  <th className="py-3 px-6">Encounter Type</th>
                  <th className="py-3 px-6 text-right">Status</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-black/10 text-xs">
                {appointments.slice(0, 5).map((apt) => (
                  <tr
                    key={apt.id}
                    onClick={() => handlePatientRowClick(apt)}
                    className="hover:bg-[#F5F5F0] transition-colors cursor-pointer group"
                  >
                    {/* Time */}
                    <td className="py-4 px-6 font-mono font-semibold text-[#1C1C1C] whitespace-nowrap">
                      <div className="flex items-center gap-2">
                        {apt.isUrgent && (
                          <AlertCircle className="w-3.5 h-3.5 text-rose-600 shrink-0" title="Urgent Case" />
                        )}
                        <span>{apt.time}</span>
                      </div>
                    </td>

                    {/* Patient */}
                    <td className="py-4 px-6">
                      <div className="flex items-center gap-3">
                        {apt.patientAvatar ? (
                          <img
                            src={apt.patientAvatar}
                            alt={apt.patientName}
                            referrerPolicy="no-referrer"
                            className="w-8 h-8 object-cover border border-black"
                          />
                        ) : (
                          <div className="w-8 h-8 border border-black bg-[#F5F5F0] text-black font-mono font-bold text-xs flex items-center justify-center shrink-0">
                            {apt.patientInitials}
                          </div>
                        )}
                        <div>
                          <p className="font-serif italic text-sm text-[#1C1C1C] group-hover:underline">
                            {apt.patientName}
                          </p>
                          <p className="text-[10px] font-mono text-[#888]">MRN {apt.patientMrn}</p>
                        </div>
                      </div>
                    </td>

                    {/* Doctor */}
                    <td className="py-4 px-6 font-sans text-[#444] whitespace-nowrap">
                      {apt.doctorName}
                    </td>

                    {/* Type */}
                    <td className="py-4 px-6 text-[#555] font-mono text-[11px] whitespace-nowrap">
                      {apt.type}
                    </td>

                    {/* Status Badge */}
                    <td className="py-4 px-6 text-right whitespace-nowrap">
                      {getStatusBadge(apt.status)}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Right Column: Quick Actions & Recent Activity (4 cols) */}
        <div className="lg:col-span-4 space-y-6">
          {/* Quick Actions Card */}
          <div id="quick-actions-card" className="bg-white border border-black/20 p-6 shadow-2xs">
            <div className="mb-4 pb-3 border-b border-black/10">
              <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                FACILITATION
              </span>
              <h3 className="font-serif italic text-lg text-[#1C1C1C]">Desk Actions</h3>
            </div>

            <div className="space-y-2.5">
              <button
                id="btn-quick-register-patient"
                onClick={() => setActiveTab('register-patient')}
                className="w-full bg-[#1C1C1C] hover:bg-black text-white font-mono text-xs uppercase tracking-[0.2em] py-3.5 px-4 border border-black flex items-center justify-center gap-2 transition-all cursor-pointer"
              >
                <UserPlus className="w-3.5 h-3.5" />
                <span>Register New Patient</span>
              </button>

              <button
                id="btn-quick-book-appointment"
                onClick={() => setIsBookModalOpen(true)}
                className="w-full bg-white hover:bg-[#F5F5F0] text-[#1C1C1C] border border-black font-mono text-xs uppercase tracking-[0.2em] py-3.5 px-4 flex items-center justify-center gap-2 transition-all cursor-pointer"
              >
                <Calendar className="w-3.5 h-3.5" />
                <span>Schedule Consultation</span>
              </button>
            </div>
          </div>

          {/* Recent Activity Card */}
          <div id="recent-activity-card" className="bg-white border border-black/20 p-6 shadow-2xs">
            <div className="mb-4 pb-3 border-b border-black/10 flex items-center justify-between">
              <div>
                <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                  TELEMETRY
                </span>
                <h3 className="font-serif italic text-lg text-[#1C1C1C]">Activity Stream</h3>
              </div>
              <span className="text-[10px] font-mono text-[#888]">LIVE</span>
            </div>

            <div className="space-y-3">
              {activities.slice(0, 4).map((act) => (
                <div key={act.id} className="flex items-start gap-3 text-xs p-2.5 bg-[#FDFCFB] border border-black/10">
                  {getActivityIcon(act.type)}
                  <div className="flex-1 min-w-0">
                    <p className="font-serif italic text-sm text-[#1C1C1C] leading-snug">{act.title}</p>
                    <p className="text-[11px] text-[#666] mt-0.5 truncate">{act.detail}</p>
                    <span className="text-[9px] font-mono text-[#999] mt-1 block">RECORDED LOG</span>
                  </div>
                </div>
              ))}
            </div>

            <button
              id="btn-load-more-activity"
              onClick={() => showToast('Audit Ledger', 'Clinical telemetry records up to date.', 'info')}
              className="w-full mt-5 py-2.5 text-center text-[10px] font-mono font-bold text-[#1C1C1C] hover:bg-black hover:text-white border border-black/20 tracking-[0.2em] uppercase transition-all cursor-pointer"
            >
              EXPAND AUDIT LOG →
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
