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
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
            Patient Roster & Dossiers
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Registry of registered patients, medical records, and encounter protocols.
          </p>
        </div>

        <button
          id="btn-patients-register-new"
          onClick={() => setActiveTab('register-patient')}
          className="px-5 py-2.5 bg-[#0f2d71] hover:bg-[#0c245a] text-white text-xs font-semibold rounded-xl flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-sm"
        >
          <Plus className="w-4 h-4" />
          <span>New Patient Intake</span>
        </button>
      </div>

      {/* Search & Filter Bar */}
      <div className="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-4">
        <div className="relative flex-1 w-full">
          <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
          <input
            type="text"
            placeholder="Search patients by name, MRN, phone, or email..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-xl focus:border-blue-600 text-xs text-slate-900 placeholder-slate-400 bg-slate-50/50 focus:bg-white focus:outline-none transition-all"
          />
        </div>

        <div className="flex items-center gap-3 w-full md:w-auto">
          <span className="text-xs font-semibold text-slate-500">
            Gender:
          </span>
          <select
            value={genderFilter}
            onChange={(e) => setGenderFilter(e.target.value)}
            className="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-700 focus:border-blue-600 focus:outline-none cursor-pointer"
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
        <div className="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl shadow-2xs overflow-hidden">
          <div className="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
            <div>
              <h2 className="font-bold text-sm text-slate-900">
                Registered Patients ({filteredPatients.length})
              </h2>
            </div>
            <span className="text-xs text-slate-400">
              Select for details
            </span>
          </div>

          <div className="divide-y divide-slate-100 max-h-[640px] overflow-y-auto">
            {filteredPatients.map((patient) => {
              const isSelected = selectedPatient?.id === patient.id;
              return (
                <div
                  key={patient.id}
                  onClick={() => setSelectedPatientId(patient.id)}
                  className={`p-4 flex items-center justify-between hover:bg-slate-50 transition-all cursor-pointer ${
                    isSelected ? 'bg-blue-50/60 border-l-4 border-l-blue-600' : ''
                  }`}
                >
                  <div className="flex items-center gap-3.5">
                    <img
                      src={
                        patient.avatar ||
                        'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                      }
                      alt={patient.firstName}
                      referrerPolicy="no-referrer"
                      className="w-11 h-11 object-cover rounded-full border border-slate-200 shrink-0"
                    />

                    <div>
                      <div className="flex items-center gap-2">
                        <h3 className="text-sm font-bold text-slate-900">
                          {patient.firstName} {patient.lastName}
                        </h3>
                        {patient.clinicalOverview.knownAllergies &&
                          patient.clinicalOverview.knownAllergies.toLowerCase() !== 'none' &&
                          patient.clinicalOverview.knownAllergies.toLowerCase() !== 'none reported' && (
                            <span className="text-[10px] font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                              Allergy
                            </span>
                          )}
                      </div>
                      <p className="text-xs text-slate-500 mt-0.5">
                        MRN #{patient.mrn} • {patient.gender}, {patient.age}y
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-3">
                    <button
                      onClick={(e) => {
                        e.stopPropagation();
                        startEncounterForPatient(patient);
                      }}
                      className="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold rounded-lg flex items-center gap-1.5 transition-all cursor-pointer"
                      title="Start Clinical Consultation"
                    >
                      <Stethoscope className="w-3.5 h-3.5" />
                      <span className="hidden sm:inline">Consult</span>
                    </button>
                    <ChevronRight className="w-4 h-4 text-slate-400" />
                  </div>
                </div>
              );
            })}
          </div>
        </div>

        {/* Selected Patient Detail Panel (5 cols) */}
        <div className="lg:col-span-5">
          {selectedPatient ? (
            <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs space-y-6 sticky top-28">
              {/* Profile Card Header */}
              <div className="flex items-center gap-4 pb-5 border-b border-slate-100">
                <img
                  src={
                    selectedPatient.avatar ||
                    'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                  }
                  alt={selectedPatient.firstName}
                  referrerPolicy="no-referrer"
                  className="w-16 h-16 object-cover rounded-full border-2 border-slate-200 shrink-0"
                />
                <div>
                  <h2 className="text-xl font-bold text-slate-900 tracking-tight">
                    {selectedPatient.firstName} {selectedPatient.lastName}
                  </h2>
                  <p className="text-xs text-slate-500 mt-0.5">
                    MRN #{selectedPatient.mrn} • DOB: {selectedPatient.dob} ({selectedPatient.age}y)
                  </p>
                  <span className="inline-block mt-2 text-[11px] font-semibold bg-slate-100 text-slate-700 px-2.5 py-0.5 rounded-full">
                    Blood Group: {selectedPatient.clinicalOverview.bloodGroup || 'O+'}
                  </span>
                </div>
              </div>

              {/* Start Encounter Primary Button */}
              <button
                onClick={() => startEncounterForPatient(selectedPatient)}
                className="w-full py-3 bg-[#0f2d71] hover:bg-[#0c245a] text-white font-semibold text-xs rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all cursor-pointer"
              >
                <Stethoscope className="w-4 h-4" />
                <span>Begin Clinical Consultation (SOAP)</span>
              </button>

              {/* Contact Information */}
              <div className="space-y-2 text-xs">
                <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                  Contact Information
                </span>
                <div className="flex items-center gap-2.5 text-slate-700">
                  <Phone className="w-4 h-4 text-slate-400" />
                  <span>{selectedPatient.phone}</span>
                </div>
                <div className="flex items-center gap-2.5 text-slate-700">
                  <Mail className="w-4 h-4 text-slate-400" />
                  <span>{selectedPatient.email}</span>
                </div>
                <div className="flex items-center gap-2.5 text-slate-700">
                  <MapPin className="w-4 h-4 text-slate-400" />
                  <span>{selectedPatient.address}</span>
                </div>
              </div>

              {/* Emergency Contact */}
              <div className="p-4 bg-slate-50 rounded-xl border border-slate-200/60 text-xs space-y-1">
                <span className="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                  Emergency Contact
                </span>
                <p className="text-sm font-bold text-slate-900">
                  {selectedPatient.emergencyContact.name} ({selectedPatient.emergencyContact.relationship})
                </p>
                <p className="text-xs text-slate-600">{selectedPatient.emergencyContact.phone}</p>
              </div>

              {/* Insurance */}
              <div className="p-4 bg-blue-50/50 rounded-xl border border-blue-100 text-xs space-y-1">
                <div className="flex items-center gap-1.5 text-blue-900 font-bold text-xs">
                  <Shield className="w-4 h-4 text-blue-600" />
                  <span>Insurance: {selectedPatient.insurance.provider}</span>
                </div>
                <p className="text-xs text-blue-700">
                  Policy #{selectedPatient.insurance.policyNumber} • Group #{selectedPatient.insurance.groupNumber}
                </p>
              </div>

              {/* Clinical Warnings / Allergies */}
              {selectedPatient.clinicalOverview.knownAllergies &&
                selectedPatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none' &&
                selectedPatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none reported' && (
                  <div className="p-4 bg-red-50 rounded-xl border border-red-200 text-xs">
                    <div className="flex items-center gap-1.5 text-red-800 font-bold mb-1">
                      <AlertTriangle className="w-4 h-4 text-red-600" />
                      <span>Critical Allergy Alert</span>
                    </div>
                    <p className="text-red-900 font-semibold">{selectedPatient.clinicalOverview.knownAllergies}</p>
                    {selectedPatient.clinicalOverview.chronicConditions && (
                      <p className="text-red-700 text-xs mt-1">
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
