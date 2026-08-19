import React, { useState } from 'react';
import {
  Users,
  Search,
  Plus,
  Stethoscope,
  Phone,
  Mail,
  MapPin,
  Shield,
  AlertTriangle,
  ChevronRight,
  Filter,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const PatientsView: React.FC = () => {
  const { patients, setActiveTab, startEncounterForPatient } = useClinic();
  const [search, setSearch] = useState('');
  const [genderFilter, setGenderFilter] = useState('All');
  const [selectedPatientId, setSelectedPatientId] = useState<string>(patients[0]?.id || '');

  const filteredPatients = patients.filter((p) => {
    const matchesSearch =
      `${p.firstName} ${p.lastName}`.toLowerCase().includes(search.toLowerCase()) ||
      p.mrn.toLowerCase().includes(search.toLowerCase()) ||
      p.phone.includes(search) ||
      p.email.toLowerCase().includes(search.toLowerCase());
    const matchesGender = genderFilter === 'All' || p.gender === genderFilter;
    return matchesSearch && matchesGender;
  });

  const selectedPatient = patients.find((p) => p.id === selectedPatientId) || patients[0];

  return (
    <div id="patients-view" className="p-8 lg:p-10 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Top Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-black/15 pb-6">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="w-2 h-2 bg-black"></span>
            <span className="text-[10px] font-mono font-bold tracking-[0.3em] uppercase text-[#777]">
              CLINICAL ARCHIVES
            </span>
          </div>
          <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
            Patient Roster & Dossiers
          </h1>
          <p className="text-xs text-[#666] mt-1 font-sans">
            Curated registry of registered patients, longitudinal medical records, and encounter protocols.
          </p>
        </div>

        <button
          id="btn-patients-register-new"
          onClick={() => setActiveTab('register-patient')}
          className="px-6 py-3 bg-black hover:bg-neutral-800 text-white text-xs font-mono font-bold uppercase tracking-[0.2em] border border-black flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-xs"
        >
          <Plus className="w-3.5 h-3.5 stroke-[2.5]" />
          <span>New Patient Intake</span>
        </button>
      </div>

      {/* Search & Filter Bar */}
      <div className="bg-white border border-black/20 p-4 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div className="relative flex-1 w-full">
          <Search className="w-3.5 h-3.5 text-black absolute left-3.5 top-3.5" />
          <input
            type="text"
            placeholder="Search patient catalogue by name, MRN, phone, or email..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-black/20 focus:border-black text-xs font-mono text-black placeholder-[#888] bg-[#FDFCFB] focus:outline-none transition-colors"
          />
        </div>

        <div className="flex items-center gap-3 w-full md:w-auto">
          <span className="text-[10px] font-mono font-bold uppercase tracking-wider text-[#777]">
            FILTER GENDER:
          </span>
          <select
            value={genderFilter}
            onChange={(e) => setGenderFilter(e.target.value)}
            className="px-3 py-2 bg-white border border-black/20 text-xs font-mono text-black focus:border-black focus:outline-none cursor-pointer"
          >
            <option value="All">All Classifications</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
            <option value="Other">Other / Non-binary</option>
          </select>
        </div>
      </div>

      {/* 2-Column Directory & Patient Detail Preview */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Patient Table / List (7 cols) */}
        <div className="lg:col-span-7 bg-white border border-black/20 shadow-2xs overflow-hidden">
          <div className="px-6 py-4 border-b border-black/10 flex items-center justify-between bg-[#FDFCFB]">
            <div>
              <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                INDEX
              </span>
              <h2 className="font-serif italic text-base text-[#1C1C1C]">
                Registered Individuals ({filteredPatients.length})
              </h2>
            </div>
            <span className="text-[10px] font-mono uppercase tracking-wider text-[#888]">
              SELECT FOR DOSSIER
            </span>
          </div>

          <div className="divide-y divide-black/10 max-h-[640px] overflow-y-auto">
            {filteredPatients.map((patient) => {
              const isSelected = selectedPatient?.id === patient.id;
              return (
                <div
                  key={patient.id}
                  onClick={() => setSelectedPatientId(patient.id)}
                  className={`p-4 flex items-center justify-between hover:bg-[#F5F5F0] transition-colors cursor-pointer ${
                    isSelected ? 'bg-[#F5F5F0] border-l-4 border-l-black' : ''
                  }`}
                >
                  <div className="flex items-center gap-4">
                    <img
                      src={
                        patient.avatar ||
                        'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                      }
                      alt={patient.firstName}
                      referrerPolicy="no-referrer"
                      className="w-12 h-12 object-cover border border-black shrink-0"
                    />

                    <div>
                      <div className="flex items-center gap-2">
                        <h3 className="font-serif italic text-base text-[#1C1C1C] font-semibold">
                          {patient.firstName} {patient.lastName}
                        </h3>
                        {patient.clinicalOverview.knownAllergies &&
                          patient.clinicalOverview.knownAllergies.toLowerCase() !== 'none' &&
                          patient.clinicalOverview.knownAllergies.toLowerCase() !== 'none reported' && (
                            <span className="text-[9px] font-mono uppercase bg-rose-50 border border-rose-400 text-rose-900 px-1.5 py-0.5">
                              ALLERGY
                            </span>
                          )}
                      </div>
                      <p className="text-[11px] font-mono text-[#666] mt-0.5">
                        MRN #{patient.mrn} • {patient.gender.toUpperCase()}, {patient.age}Y
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3">
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        startEncounterForPatient(patient);
                      }}
                      className="px-3.5 py-1.5 bg-white border border-black hover:bg-black hover:text-white text-[10px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5 transition-all cursor-pointer shadow-2xs"
                      title="Start Clinical Consultation"
                    >
                      <Stethoscope className="w-3 h-3" />
                      <span className="hidden sm:inline">Consult</span>
                    </button>
                    <ChevronRight className="w-4 h-4 text-black" />
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Selected Patient Detail Panel (5 cols) */}
        <div className="lg:col-span-5">
          {selectedPatient ? (
            <div className="bg-white border border-black/20 p-8 shadow-2xs space-y-6 sticky top-28">
              {/* Profile Card Header */}
              <div className="flex items-center gap-4 pb-5 border-b border-black/10">
                <img
                  src={
                    selectedPatient.avatar ||
                    'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                  }
                  alt={selectedPatient.firstName}
                  referrerPolicy="no-referrer"
                  className="w-18 h-18 object-cover border-2 border-black shrink-0"
                />
                <div>
                  <span className="text-[9px] font-mono uppercase tracking-[0.25em] text-[#888] block">
                    PATIENT DOSSIER
                  </span>
                  <h2 className="text-2xl font-serif italic text-[#1C1C1C] tracking-tight">
                    {selectedPatient.firstName} {selectedPatient.lastName}
                  </h2>
                  <p className="text-xs font-mono text-[#666] mt-0.5">
                    MRN #{selectedPatient.mrn} • DOB: {selectedPatient.dob} ({selectedPatient.age}Y)
                  </p>
                  <span className="inline-block mt-1.5 text-[10px] font-mono uppercase border border-black/20 bg-[#F5F5F0] text-black px-2 py-0.5">
                    BLOOD GROUP: {selectedPatient.clinicalOverview.bloodGroup || 'O+'}
                  </span>
                </div>
              </div>

              {/* Start Encounter Primary Button */}
              <button
                onClick={() => startEncounterForPatient(selectedPatient)}
                className="w-full py-3.5 bg-black hover:bg-neutral-800 text-white font-mono font-bold uppercase tracking-[0.2em] text-xs border border-black flex items-center justify-center gap-2 shadow-xs transition-all cursor-pointer"
              >
                <Stethoscope className="w-4 h-4" />
                <span>Begin Clinical Consultation (SOAP)</span>
              </button>

              {/* Contact Information */}
              <div className="space-y-2 text-xs font-mono">
                <span className="text-[10px] font-mono font-bold text-[#888] uppercase tracking-wider block">
                  COMMUNICATION CHANNELS
                </span>
                <div className="flex items-center gap-2.5 text-black">
                  <Phone className="w-3.5 h-3.5 text-[#666]" />
                  <span>{selectedPatient.phone}</span>
                </div>
                <div className="flex items-center gap-2.5 text-black">
                  <Mail className="w-3.5 h-3.5 text-[#666]" />
                  <span>{selectedPatient.email}</span>
                </div>
                <div className="flex items-center gap-2.5 text-black">
                  <MapPin className="w-3.5 h-3.5 text-[#666]" />
                  <span className="font-sans text-xs">{selectedPatient.address}</span>
                </div>
              </div>

              {/* Emergency Contact */}
              <div className="p-4 bg-[#FDFCFB] border border-black/15 text-xs space-y-1">
                <span className="text-[9px] font-mono font-bold text-[#777] uppercase tracking-wider block">
                  EMERGENCY LIAISON
                </span>
                <p className="font-serif italic text-sm text-black font-semibold">
                  {selectedPatient.emergencyContact.name} ({selectedPatient.emergencyContact.relationship})
                </p>
                <p className="font-mono text-xs text-[#555]">{selectedPatient.emergencyContact.phone}</p>
              </div>

              {/* Insurance */}
              <div className="p-4 bg-[#F5F5F0] border border-black/20 text-xs space-y-1">
                <div className="flex items-center gap-1.5 text-black font-bold font-mono text-[11px] uppercase tracking-wider">
                  <Shield className="w-3.5 h-3.5" />
                  <span>COVERAGE: {selectedPatient.insurance.provider}</span>
                </div>
                <p className="font-mono text-xs text-[#444]">
                  POLICY #{selectedPatient.insurance.policyNumber} • GRP #{selectedPatient.insurance.groupNumber}
                </p>
              </div>

              {/* Clinical Warnings / Allergies */}
              {selectedPatient.clinicalOverview.knownAllergies &&
                selectedPatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none' &&
                selectedPatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none reported' && (
                  <div className="p-4 bg-rose-50 border border-rose-400 text-xs">
                    <div className="flex items-center gap-1.5 text-rose-900 font-mono font-bold uppercase tracking-wider mb-1">
                      <AlertTriangle className="w-3.5 h-3.5 text-rose-700" />
                      <span>CRITICAL ALLERGY ALERT</span>
                    </div>
                    <p className="text-rose-950 font-mono font-semibold">{selectedPatient.clinicalOverview.knownAllergies}</p>
                    {selectedPatient.clinicalOverview.chronicConditions && (
                      <p className="text-rose-800 text-xs mt-1 font-sans">
                        Conditions: {selectedPatient.clinicalOverview.chronicConditions}
                      </p>
                    )}
                  </div>
                )}
            </div>
          ) : null}
        </div>
      </div>
    </div>
  );
};
