import React, { useState } from 'react';
import {
  Calendar as CalendarIcon,
  ChevronLeft,
  ChevronRight,
  Printer,
  Plus,
  Clock,
  User,
  AlertTriangle,
  ChevronDown,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const CalendarView: React.FC = () => {
  const {
    queue,
    checkInPatient,
    doctors,
    setIsBookModalOpen,
    startEncounterForPatient,
    patients,
  } = useClinic();

  // Mini calendar state
  const [selectedDay, setSelectedDay] = useState<number>(14);
  const [viewMode, setViewMode] = useState<'Day' | 'Week' | 'Month'>('Week');

  // Filter by provider state
  const [allProviders, setAllProviders] = useState(true);
  const [selectedDoctors, setSelectedDoctors] = useState<string[]>(['doc-1', 'doc-2']);

  const toggleDoctorFilter = (docId: string) => {
    if (selectedDoctors.includes(docId)) {
      const next = selectedDoctors.filter((id) => id !== docId);
      setSelectedDoctors(next);
      if (next.length < doctors.length) setAllProviders(false);
    } else {
      const next = [...selectedDoctors, docId];
      setSelectedDoctors(next);
      if (next.length === doctors.length) setAllProviders(true);
    }
  };

  const toggleAllProviders = () => {
    if (allProviders) {
      setAllProviders(false);
      setSelectedDoctors([]);
    } else {
      setAllProviders(true);
      setSelectedDoctors(doctors.map((d) => d.id));
    }
  };

  // Calendar days representation
  const miniCalendarDays = [
    { day: 29, currentMonth: false },
    { day: 30, currentMonth: false },
    { day: 31, currentMonth: false },
    { day: 1, currentMonth: true },
    { day: 2, currentMonth: true },
    { day: 3, currentMonth: true },
    { day: 4, currentMonth: true },
    { day: 5, currentMonth: true },
    { day: 6, currentMonth: true },
    { day: 7, currentMonth: true },
    { day: 8, currentMonth: true },
    { day: 9, currentMonth: true },
    { day: 10, currentMonth: true },
    { day: 11, currentMonth: true },
    { day: 12, currentMonth: true },
    { day: 13, currentMonth: true, isHighlighted: true },
    { day: 14, currentMonth: true, isHighlighted: true },
    { day: 15, currentMonth: true },
    { day: 16, currentMonth: true },
    { day: 17, currentMonth: true },
    { day: 18, currentMonth: true },
  ];

  const handleStartConsultationFromQueue = (patientId: string, patientName: string) => {
    const patientObj = patients.find((p) => p.id === patientId || `${p.firstName} ${p.lastName}` === patientName);
    if (patientObj) {
      startEncounterForPatient(patientObj);
    } else {
      startEncounterForPatient({
        id: patientId,
        mrn: 'MRN-' + Math.floor(100000 + Math.random() * 900000),
        firstName: patientName.split(' ')[0] || 'Patient',
        lastName: patientName.split(' ')[1] || '',
        dob: '1990-01-01',
        age: 34,
        gender: 'Female',
        phone: '(555) 000-0000',
        email: 'patient@example.com',
        address: '123 Main St',
        emergencyContact: { name: 'Contact', relationship: 'Family', phone: '(555) 111-2222' },
        insurance: { provider: 'Insurance Co', policyNumber: 'POL-123', groupNumber: 'GRP-1' },
        clinicalOverview: { knownAllergies: 'None', bloodGroup: 'O+' },
        initials: patientName.slice(0, 2).toUpperCase(),
        registrationDate: '2023-10-01',
      });
    }
  };

  return (
    <div id="calendar-view" className="p-8 lg:p-10 max-w-[1700px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Top Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
            Appointment Grid & Operations
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Real-time appointment calendar, practitioner allocation, and triage queue.
          </p>
        </div>

        <div className="flex items-center gap-3">
          <button
            id="btn-print-calendar"
            onClick={() => window.print()}
            className="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-2 transition-all cursor-pointer shadow-2xs"
          >
            <Printer className="w-4 h-4 text-slate-500" />
            <span>Print Schedule</span>
          </button>

          <button
            id="btn-calendar-book-appointment"
            onClick={() => setIsBookModalOpen(true)}
            className="px-5 py-2 bg-[#0f2d71] hover:bg-[#0c245a] text-white text-xs font-semibold rounded-xl flex items-center gap-2 transition-all cursor-pointer shadow-sm"
          >
            <Plus className="w-4 h-4" />
            <span>Schedule Slot</span>
          </button>
        </div>
      </div>

      {/* 3 Column Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Left Column: Mini Calendar & Filter by Provider (3 cols) */}
        <div className="lg:col-span-3 space-y-6">
          {/* Mini Calendar Card */}
          <div id="mini-calendar-widget" className="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs">
            <div className="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
              <h2 className="font-bold text-sm text-slate-900">November 2026</h2>
              <div className="flex items-center gap-1 text-slate-600">
                <button className="p-1 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                  <ChevronLeft className="w-4 h-4" />
                </button>
                <button className="p-1 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                  <ChevronRight className="w-4 h-4" />
                </button>
              </div>
            </div>

            {/* Weekday headers */}
            <div className="grid grid-cols-7 text-center text-[10px] font-semibold text-slate-400 mb-2 uppercase tracking-wider">
              <span>Su</span>
              <span>Mo</span>
              <span>Tu</span>
              <span>We</span>
              <span>Th</span>
              <span>Fr</span>
              <span>Sa</span>
            </div>

            {/* Days grid */}
            <div className="grid grid-cols-7 gap-1 text-center text-xs font-medium">
              {miniCalendarDays.map((item, idx) => {
                const isSelected = item.day === selectedDay && item.currentMonth;
                return (
                  <button
                    key={idx}
                    onClick={() => item.currentMonth && setSelectedDay(item.day)}
                    className={`h-7 w-7 mx-auto rounded-full flex items-center justify-center transition-all cursor-pointer ${
                      isSelected
                        ? 'bg-blue-600 text-white font-bold'
                        : item.isHighlighted
                        ? 'bg-blue-50 text-blue-700 font-bold'
                        : item.currentMonth
                        ? 'text-slate-700 hover:bg-slate-100'
                        : 'text-slate-300'
                    }`}
                  >
                    {item.day}
                  </button>
                );
              })}
            </div>
          </div>

          {/* Filter by Provider Card */}
          <div id="filter-provider-card" className="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs">
            <div className="mb-4 pb-3 border-b border-slate-100">
              <h2 className="font-bold text-sm text-slate-900">Attending Physicians</h2>
            </div>

            <div className="space-y-2 text-xs">
              {/* All Providers Checkbox */}
              <label className="flex items-center gap-3 cursor-pointer select-none group p-1.5 rounded-lg hover:bg-slate-50">
                <input
                  type="checkbox"
                  checked={allProviders}
                  onChange={toggleAllProviders}
                  className="w-4 h-4 accent-blue-600 rounded cursor-pointer"
                />
                <span className="font-bold text-slate-900 text-xs">
                  All Practitioners
                </span>
              </label>

              {/* Provider items */}
              {doctors.map((doc) => {
                const isChecked = allProviders || selectedDoctors.includes(doc.id);
                return (
                  <label key={doc.id} className="flex items-center gap-3 cursor-pointer select-none group p-1.5 rounded-lg hover:bg-slate-50 transition-colors">
                    <input
                      type="checkbox"
                      checked={isChecked}
                      onChange={() => toggleDoctorFilter(doc.id)}
                      className="w-4 h-4 accent-blue-600 rounded cursor-pointer"
                    />
                    <div className="flex items-center gap-2">
                      <span className="w-2 h-2 rounded-full bg-blue-600"></span>
                      <span className="text-slate-700 group-hover:text-slate-900 text-xs font-medium">
                        {doc.name}
                      </span>
                    </div>
                  </label>
                );
              })}
            </div>
          </div>
        </div>

        {/* Center Column: Week Calendar Schedule Grid (6 cols) */}
        <div className="lg:col-span-6 bg-white border border-black/20 shadow-2xs flex flex-col overflow-hidden min-h-[620px]">
          {/* Calendar Top Toolbar */}
          <div className="p-4 border-b border-black/15 flex items-center justify-between bg-[#FDFCFB]">
            <div className="flex items-center gap-3">
              <button
                onClick={() => setSelectedDay(14)}
                className="px-3 py-1 border border-black/20 bg-white hover:bg-black hover:text-white text-[10px] font-mono uppercase tracking-wider transition-colors"
              >
                Today
              </button>
              <div className="flex items-center gap-1">
                <button className="p-1 hover:bg-black/5 border border-black/10 text-black">
                  <ChevronLeft className="w-3.5 h-3.5" />
                </button>
                <button className="p-1 hover:bg-black/5 border border-black/10 text-black">
                  <ChevronRight className="w-3.5 h-3.5" />
                </button>
              </div>
              <span className="font-serif italic text-base text-[#1C1C1C]">Nov 13 – Nov 17, 2026</span>
            </div>

            {/* View selector dropdown */}
            <div className="relative">
              <select
                value={viewMode}
                onChange={(e) => setViewMode(e.target.value as any)}
                className="text-[10px] font-mono font-bold uppercase tracking-wider text-black bg-white border border-black/20 px-3 py-1.5 pr-7 focus:outline-none appearance-none cursor-pointer"
              >
                <option value="Day">Day</option>
                <option value="Week">Week</option>
                <option value="Month">Month</option>
              </select>
              <ChevronDown className="w-3 h-3 text-black absolute right-2 top-2.5 pointer-events-none" />
            </div>
          </div>

          {/* Schedule Grid */}
          <div className="flex-1 flex flex-col overflow-y-auto">
            {/* Days Header */}
            <div className="grid grid-cols-4 border-b border-black/15 bg-[#F5F5F0] text-center py-2.5 text-xs font-mono">
              <div className="w-16"></div>
              <div className="text-[#555]">
                <span className="block text-[10px] uppercase text-[#888]">Mon</span>
                <span className="font-serif italic text-base text-black font-semibold">13</span>
              </div>
              <div className="text-black bg-white border-x border-black/15 py-1">
                <span className="block text-[10px] uppercase font-bold text-black">Tue</span>
                <span className="font-serif italic text-base text-black font-bold">14</span>
              </div>
              <div className="text-[#555]">
                <span className="block text-[10px] uppercase text-[#888]">Wed</span>
                <span className="font-serif italic text-base text-black font-semibold">15</span>
              </div>
            </div>

            {/* Time Grid with Appointment Cards */}
            <div className="flex-1 relative divide-y divide-black/10 text-xs">
              {/* 8 AM Slot */}
              <div className="grid grid-cols-4 min-h-[95px] group hover:bg-[#FDFCFB] transition-colors">
                <div className="w-16 text-right pr-3 pt-2 font-mono text-[10px] text-[#888]">08:00</div>
                <div className="border-l border-black/10 p-1 relative"></div>
                <div className="border-l border-black/10 p-1.5 relative">
                  {/* Jane Cooper Appointment Card */}
                  <div
                    onClick={() => handleStartConsultationFromQueue('pat-7', 'Jane Cooper')}
                    className="bg-[#FDFCFB] border border-black p-3 hover:bg-[#F5F5F0] transition-all cursor-pointer text-left shadow-2xs"
                  >
                    <div className="flex items-center gap-1.5 text-[10px] font-mono text-[#666] mb-1">
                      <Clock className="w-3 h-3 text-black" />
                      <span>08:30 — 09:30</span>
                    </div>
                    <p className="font-serif italic text-sm text-black font-semibold">Jane Cooper</p>
                    <p className="text-[10px] font-mono text-[#666] mt-0.5">Dr. Chen • Follow-up</p>
                  </div>
                </div>
                <div className="border-l border-black/10 p-1 relative"></div>
              </div>

              {/* 9 AM Slot */}
              <div className="grid grid-cols-4 min-h-[105px] group hover:bg-[#FDFCFB] transition-colors">
                <div className="w-16 text-right pr-3 pt-2 font-mono text-[10px] text-[#888]">09:00</div>
                <div className="border-l border-black/10 p-1.5 relative">
                  {/* Robert Fox Appointment Card */}
                  <div
                    onClick={() => handleStartConsultationFromQueue('pat-10', 'Robert Fox')}
                    className="bg-white border-l-4 border-l-black border border-black/20 p-2.5 hover:border-black transition-all cursor-pointer text-left"
                  >
                    <div className="flex items-center gap-1.5 text-[10px] font-mono text-[#666] mb-1">
                      <Clock className="w-3 h-3 text-black" />
                      <span>09:00 — 09:45</span>
                    </div>
                    <p className="font-serif italic text-sm text-black font-semibold">Robert Fox</p>
                    <p className="text-[10px] font-mono text-[#666] mt-0.5">Dr. Jenkins • Checkup</p>
                  </div>
                </div>
                <div className="border-l border-black/10 p-1.5 relative">
                  {/* Wade Warren Appointment (Room 2) */}
                  <div
                    onClick={() => handleStartConsultationFromQueue('pat-8', 'Wade Warren')}
                    className="bg-black text-white border border-black p-2.5 hover:bg-neutral-900 transition-all cursor-pointer text-left shadow-2xs"
                  >
                    <p className="font-mono text-[10px] text-white/70">09:15 — 10:00</p>
                    <p className="font-serif italic text-sm text-white font-semibold">Wade Warren</p>
                    <p className="text-[10px] font-mono text-emerald-400">Dr. Jenkins • ROOM 02</p>
                  </div>
                </div>
                <div className="border-l border-black/10 p-1 relative"></div>
              </div>

              {/* 10 AM Slot */}
              <div className="grid grid-cols-4 min-h-[95px] group hover:bg-[#FDFCFB] transition-colors">
                <div className="w-16 text-right pr-3 pt-2 font-mono text-[10px] text-[#888]">10:00</div>
                <div className="border-l border-black/10 p-1 relative"></div>
                <div className="border-l border-black/10 p-1.5 relative">
                  {/* Esther Howard Appointment Card */}
                  <div
                    onClick={() => handleStartConsultationFromQueue('pat-9', 'Esther Howard')}
                    className="bg-rose-50 border border-rose-400 p-2.5 hover:border-rose-700 transition-all cursor-pointer text-left"
                  >
                    <div className="flex items-center gap-1.5 text-rose-900 font-mono text-[10px] mb-1">
                      <AlertTriangle className="w-3 h-3 text-rose-700" />
                      <span>10:00 — 11:00</span>
                    </div>
                    <p className="font-serif italic text-sm text-rose-950 font-semibold">Esther Howard</p>
                    <p className="text-[10px] font-mono text-rose-800 mt-0.5">Dr. Jenkins • Acute Care</p>
                  </div>
                </div>
                <div className="border-l border-black/10 p-1 relative"></div>
              </div>

              {/* 11 AM Slot */}
              <div className="grid grid-cols-4 min-h-[85px] group hover:bg-[#FDFCFB] transition-colors">
                <div className="w-16 text-right pr-3 pt-2 font-mono text-[10px] text-[#888]">11:00</div>
                <div className="border-l border-black/10 p-1 relative"></div>
                <div className="border-l border-black/10 p-1 relative"></div>
                <div className="border-l border-black/10 p-1 relative"></div>
              </div>
            </div>
          </div>
        </div>

        {/* Right Column: Today's Queue (3 cols) */}
        <div className="lg:col-span-3 space-y-4">
          <div className="bg-white border border-black/20 p-6 shadow-2xs">
            {/* Header */}
            <div className="flex items-center justify-between mb-5 pb-3 border-b border-black/10">
              <div>
                <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                  ACTIVE QUEUE
                </span>
                <h2 className="font-serif italic text-base text-[#1C1C1C]">Waiting Room Triage</h2>
              </div>
              <span className="bg-black text-white text-[10px] font-mono font-bold px-2 py-0.5 border border-black">
                12 ACTIVE
              </span>
            </div>

            {/* Queue Cards */}
            <div className="space-y-4">
              {/* Card 1: Jane Cooper */}
              <div className="border border-black/15 p-4 bg-[#FDFCFB] shadow-2xs">
                <div className="flex items-start justify-between">
                  <div>
                    <h3 className="font-serif italic text-base text-black font-semibold">Jane Cooper</h3>
                    <p className="text-[10px] font-mono text-[#888]">MRN #884920</p>
                  </div>
                  <span className="text-[9px] font-mono uppercase tracking-wider bg-[#F5F5F0] border border-black/15 px-2 py-0.5 text-[#555]">
                    WAITING
                  </span>
                </div>

                <div className="flex items-center gap-1.5 text-[10px] font-mono text-[#666] mt-2.5">
                  <Clock className="w-3 h-3 text-black" />
                  <span>08:30 AM • Dr. Chen</span>
                </div>

                <button
                  id="btn-checkin-jane"
                  onClick={() => checkInPatient('q-1')}
                  className="w-full mt-3 py-2 bg-white hover:bg-black hover:text-white text-black border border-black text-[10px] font-mono font-bold uppercase tracking-[0.2em] transition-all cursor-pointer"
                >
                  Verify Intake
                </button>
              </div>

              {/* Card 2: Wade Warren (Room 2 Active) */}
              <div className="border-2 border-black p-4 bg-black text-white shadow-2xs">
                <div className="flex items-start justify-between">
                  <div>
                    <h3 className="font-serif italic text-base text-white font-semibold">Wade Warren</h3>
                    <p className="text-[10px] font-mono text-white/60">MRN #112934</p>
                  </div>
                  <span className="text-[9px] font-mono uppercase tracking-wider bg-emerald-500 text-black font-bold px-2 py-0.5">
                    ROOM 02
                  </span>
                </div>

                <div className="flex items-center gap-1.5 text-[10px] font-mono text-white/80 mt-2.5">
                  <Clock className="w-3 h-3 text-emerald-400" />
                  <span>09:15 AM • Dr. Jenkins</span>
                </div>

                <button
                  onClick={() => handleStartConsultationFromQueue('pat-8', 'Wade Warren')}
                  className="w-full mt-3 py-2 bg-white text-black hover:bg-[#F5F5F0] border border-white text-[10px] font-mono font-bold uppercase tracking-[0.2em] transition-all cursor-pointer"
                >
                  Enter Consultation
                </button>
              </div>

              {/* Card 3: Esther Howard */}
              <div className="border border-black/15 p-4 bg-[#FDFCFB] shadow-2xs">
                <div className="flex items-start justify-between">
                  <div>
                    <h3 className="font-serif italic text-base text-black font-semibold">Esther Howard</h3>
                    <p className="text-[10px] font-mono text-[#888]">MRN #440219</p>
                  </div>
                  <span className="text-[10px] font-mono text-[#666]">10:00 AM</span>
                </div>

                <div className="flex items-center gap-1.5 text-[10px] font-mono text-[#666] mt-2.5">
                  <Clock className="w-3 h-3 text-black" />
                  <span>Dr. Jenkins • Acute Care</span>
                </div>

                <button
                  onClick={() => handleStartConsultationFromQueue('pat-9', 'Esther Howard')}
                  className="w-full mt-3 py-2 bg-[#F5F5F0] hover:bg-black hover:text-white text-black border border-black/20 text-[10px] font-mono font-bold uppercase tracking-[0.2em] transition-all cursor-pointer"
                >
                  Call to Chamber
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
