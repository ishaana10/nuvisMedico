import React, { useState } from 'react';
import { X, Calendar, AlertCircle, Check } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const BookAppointmentModal: React.FC = () => {
  const {
    isBookModalOpen,
    setIsBookModalOpen,
    patients,
    doctors,
    bookAppointment,
  } = useClinic();

  const [patientId, setPatientId] = useState(patients[0]?.id || '');
  const [doctorId, setDoctorId] = useState(doctors[0]?.id || '');
  const [date, setDate] = useState('2026-11-14');
  const [time, setTime] = useState('10:00 AM');
  const [type, setType] = useState('Consultation');
  const [isUrgent, setIsUrgent] = useState(false);
  const [notes, setNotes] = useState('');

  if (!isBookModalOpen) return null;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const pat = patients.find((p) => p.id === patientId) || patients[0];
    const doc = doctors.find((d) => d.id === doctorId) || doctors[0];

    bookAppointment({
      patientId: pat.id,
      patientName: `${pat.firstName} ${pat.lastName}`,
      patientMrn: pat.mrn,
      patientAvatar: pat.avatar,
      patientInitials: pat.initials,
      doctorId: doc.id,
      doctorName: doc.name,
      time,
      date,
      type,
      status: 'Scheduled',
      isUrgent,
      notes,
    });

    setIsBookModalOpen(false);
  };

  return (
    <div
      id="book-appointment-modal"
      className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-in fade-in duration-150"
    >
      <div className="bg-white border-2 border-black max-w-lg w-full p-8 shadow-2xl space-y-6 animate-in zoom-in-95 duration-150">
        {/* Modal Header */}
        <div className="flex items-center justify-between pb-4 border-b border-black/15">
          <div>
            <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
              CALENDAR DISPATCH
            </span>
            <h2 className="text-2xl font-serif italic text-black">Schedule Clinical Session</h2>
          </div>
          <button
            onClick={() => setIsBookModalOpen(false)}
            className="p-1.5 text-black hover:bg-black hover:text-white border border-black/20 transition-colors cursor-pointer"
          >
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit} className="space-y-5 text-xs font-mono">
          {/* Patient Selector */}
          <div>
            <label className="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-1.5">
              Select Patient Dossier
            </label>
            <select
              value={patientId}
              onChange={(e) => setPatientId(e.target.value)}
              className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none cursor-pointer"
            >
              {patients.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.firstName} {p.lastName} — MRN #{p.mrn} ({p.gender}, {p.age}Y)
                </option>
              ))}
            </select>
          </div>

          {/* Attending Doctor */}
          <div>
            <label className="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-1.5">
              Attending Practitioner
            </label>
            <select
              value={doctorId}
              onChange={(e) => setDoctorId(e.target.value)}
              className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none cursor-pointer"
            >
              {doctors.map((d) => (
                <option key={d.id} value={d.id}>
                  {d.name} — {d.specialty}
                </option>
              ))}
            </select>
          </div>

          {/* Date & Time */}
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-1.5">
                Session Date
              </label>
              <input
                type="date"
                value={date}
                onChange={(e) => setDate(e.target.value)}
                className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none"
              />
            </div>
            <div>
              <label className="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-1.5">
                Time Slot
              </label>
              <select
                value={time}
                onChange={(e) => setTime(e.target.value)}
                className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none cursor-pointer"
              >
                <option value="08:30 AM">08:30 AM</option>
                <option value="09:00 AM">09:00 AM</option>
                <option value="09:30 AM">09:30 AM</option>
                <option value="10:00 AM">10:00 AM</option>
                <option value="10:30 AM">10:30 AM</option>
                <option value="11:00 AM">11:00 AM</option>
                <option value="11:30 AM">11:30 AM</option>
                <option value="01:30 PM">01:30 PM</option>
                <option value="02:00 PM">02:00 PM</option>
                <option value="03:00 PM">03:00 PM</option>
                <option value="04:00 PM">04:00 PM</option>
              </select>
            </div>
          </div>

          {/* Appointment Type */}
          <div>
            <label className="block text-[10px] font-bold uppercase tracking-wider text-[#555] mb-1.5">
              Consultation Protocol
            </label>
            <select
              value={type}
              onChange={(e) => setType(e.target.value)}
              className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none cursor-pointer"
            >
              <option value="Consultation">Standard Clinical Consultation</option>
              <option value="Follow-up">Longitudinal Follow-up</option>
              <option value="Routine Check">Preventative Routine Check</option>
              <option value="Urgent Care">Urgent Acute Triage</option>
              <option value="Lab Results">Diagnostic Pathology Review</option>
              <option value="Annual Physical">Comprehensive Annual Examination</option>
            </select>
          </div>

          {/* Urgent Checkbox */}
          <label className="flex items-center gap-3 p-3.5 bg-rose-50 border border-rose-400 cursor-pointer">
            <input
              type="checkbox"
              checked={isUrgent}
              onChange={(e) => setIsUrgent(e.target.checked)}
              className="w-4 h-4 accent-rose-700 cursor-pointer"
            />
            <div className="flex items-center gap-2 text-rose-900 font-bold text-xs uppercase tracking-wider">
              <AlertCircle className="w-4 h-4 text-rose-700" />
              <span>Priority / Stat Triage Case</span>
            </div>
          </label>

          {/* Actions */}
          <div className="flex items-center justify-end gap-3 pt-3 border-t border-black/10">
            <button
              type="button"
              onClick={() => setIsBookModalOpen(false)}
              className="px-4 py-2.5 border border-black/20 hover:border-black uppercase tracking-wider text-xs font-bold transition-colors cursor-pointer"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-6 py-2.5 bg-black hover:bg-neutral-800 text-white font-bold uppercase tracking-[0.2em] text-xs border border-black flex items-center gap-2 shadow-2xs transition-all cursor-pointer"
            >
              <Check className="w-4 h-4 stroke-[2.5]" />
              <span>Confirm Reservation</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};
