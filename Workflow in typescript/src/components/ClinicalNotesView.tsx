import React, { useState } from 'react';
import {
  FileText,
  Printer,
  CheckCircle,
  AlertTriangle,
  Pill,
  Plus,
  Trash2,
  Search,
  Clock,
  ChevronDown,
  Edit2,
  Save,
  X,
  Sparkles,
} from 'lucide-react';
import confetti from 'canvas-confetti';
import { useClinic } from '../context/ClinicContext';
import { ICD10_DATABASE, COMMON_MEDICATIONS } from '../data/mockData';

export const ClinicalNotesView: React.FC = () => {
  const {
    activePatient,
    vitals,
    updateVitals,
    soapNotes,
    updateSoapNotes,
    prescriptions,
    addPrescription,
    removePrescription,
    pastVisits,
    finishVisit,
    setIsPrintRxModalOpen,
    showToast,
  } = useClinic();

  // Medication add row state
  const [newMedName, setNewMedName] = useState('');
  const [newMedDosage, setNewMedDosage] = useState('');
  const [newMedFreq, setNewMedFreq] = useState('BID (Twice a day)');

  // ICD10 search state
  const [icdSearch, setIcdSearch] = useState('');
  const [showIcdSuggestions, setShowIcdSuggestions] = useState(false);

  // Vitals edit state
  const [isEditingVitals, setIsEditingVitals] = useState(false);
  const [tempVitals, setTempVitals] = useState({ ...vitals });

  const handleAddMedication = () => {
    if (!newMedName.trim()) {
      showToast('Medication Required', 'Please specify the medication compound name.', 'warning');
      return;
    }
    addPrescription({
      medicationName: newMedName.trim(),
      dosage: newMedDosage.trim() || '500mg',
      frequency: newMedFreq,
      duration: '7 days',
    });
    setNewMedName('');
    setNewMedDosage('');
  };

  const handleSelectMedicationTemplate = (medName: string, defaultDosage: string) => {
    setNewMedName(medName);
    setNewMedDosage(defaultDosage);
  };

  const handleAddDiagnosisCode = (codeItem: { code: string; label: string }) => {
    if (!soapNotes.assessmentCodes.some((c) => c.code === codeItem.code)) {
      updateSoapNotes({
        assessmentCodes: [...soapNotes.assessmentCodes, codeItem],
      });
      showToast('Diagnosis Affixed', `${codeItem.code} — ${codeItem.label}`);
    }
    setIcdSearch('');
    setShowIcdSuggestions(false);
  };

  const handleRemoveDiagnosisCode = (code: string) => {
    updateSoapNotes({
      assessmentCodes: soapNotes.assessmentCodes.filter((c) => c.code !== code),
    });
  };

  const handleFinishVisitWithCelebration = () => {
    try {
      confetti({
        particleCount: 70,
        spread: 50,
        origin: { y: 0.6 },
      });
    } catch {
      // ignore
    }
    finishVisit();
  };

  const saveVitalsEdit = () => {
    updateVitals(tempVitals);
    setIsEditingVitals(false);
    showToast('Telemetry Updated', 'Patient biometric vitals saved to clinical archive.');
  };

  const filteredIcdCodes = ICD10_DATABASE.filter(
    (item) =>
      item.code.toLowerCase().includes(icdSearch.toLowerCase()) ||
      item.label.toLowerCase().includes(icdSearch.toLowerCase())
  );

  return (
    <div id="clinical-notes-view" className="p-8 lg:p-10 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Top Patient Dossier Banner with Artistic Flair */}
      <div className="bg-white border border-black/20 p-8 shadow-2xs flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative overflow-hidden">
        <div className="absolute -top-6 -right-6 text-[110px] font-serif italic leading-none opacity-5 select-none pointer-events-none">
          Rx
        </div>

        {/* Left: Patient Profile Details */}
        <div className="flex items-start sm:items-center gap-5 z-10">
          <img
            src={
              activePatient.avatar ||
              'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
            }
            alt={activePatient.firstName}
            referrerPolicy="no-referrer"
            className="w-18 h-18 object-cover border-2 border-black shrink-0"
          />

          <div className="space-y-1.5">
            <div className="flex flex-wrap items-center gap-3">
              <span className="font-mono text-[9px] uppercase tracking-[0.3em] text-[#888] block">
                PATIENT DOSSIER
              </span>
              <span className="font-mono text-[10px] bg-[#F5F5F0] border border-black/20 px-2 py-0.5 text-black">
                MRN #{activePatient.mrn}
              </span>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
                {activePatient.firstName} {activePatient.lastName}
              </h1>

              {/* Allergy Warning Badge */}
              {activePatient.clinicalOverview.knownAllergies &&
                activePatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none' &&
                activePatient.clinicalOverview.knownAllergies.toLowerCase() !== 'none reported' && (
                  <span className="inline-flex items-center gap-1.5 px-3 py-1 border border-rose-600 bg-rose-50 text-rose-900 font-mono text-xs uppercase tracking-wider">
                    <AlertTriangle className="w-3.5 h-3.5 text-rose-700" />
                    <span>ALLERGY: {activePatient.clinicalOverview.knownAllergies}</span>
                  </span>
                )}
            </div>

            <p className="text-xs font-mono text-[#666] flex flex-wrap items-center gap-2">
              <span>DOB: {activePatient.dob} ({activePatient.age} YRS)</span>
              <span>•</span>
              <span>GENDER: {activePatient.gender.toUpperCase()}</span>
              <span>•</span>
              <span>BLOOD: {activePatient.clinicalOverview.bloodGroup || 'O+'}</span>
              <span>•</span>
              <span>INSURANCE: {activePatient.insurance.provider}</span>
            </p>
          </div>
        </div>

        {/* Right: Actions (Print Prescription & Finish Visit) */}
        <div className="flex items-center gap-3 z-10">
          <button
            id="btn-print-prescription"
            onClick={() => setIsPrintRxModalOpen(true)}
            className="px-5 py-3.5 border border-black bg-white hover:bg-[#F5F5F0] text-[#1C1C1C] text-xs font-mono font-bold uppercase tracking-[0.2em] flex items-center gap-2 transition-all cursor-pointer shadow-2xs"
          >
            <Printer className="w-4 h-4 text-black" />
            <span>Generate Rx</span>
          </button>

          <button
            id="btn-finish-visit"
            onClick={handleFinishVisitWithCelebration}
            className="px-6 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-mono font-bold uppercase tracking-[0.2em] border border-black flex items-center gap-2 transition-all cursor-pointer shadow-2xs"
          >
            <CheckCircle className="w-4 h-4 stroke-[2.5]" />
            <span>Conclude Encounter</span>
          </button>
        </div>
      </div>

      {/* Vitals Matrix Banner (Artistic Architectural Readouts) */}
      <div className="bg-white border border-black/20 p-6 shadow-2xs relative">
        <div className="flex items-center justify-between pb-3 mb-4 border-b border-black/10">
          <div className="flex items-center gap-2">
            <span className="w-1.5 h-1.5 bg-black"></span>
            <span className="text-[9px] font-mono font-bold uppercase tracking-[0.3em] text-[#777]">
              PHYSIOLOGICAL TELEMETRY
            </span>
          </div>
          <button
            onClick={() => {
              if (isEditingVitals) {
                saveVitalsEdit();
              } else {
                setTempVitals({ ...vitals });
                setIsEditingVitals(true);
              }
            }}
            className="text-xs font-mono uppercase tracking-wider text-black hover:underline flex items-center gap-1.5 cursor-pointer font-bold"
          >
            {isEditingVitals ? (
              <>
                <Save className="w-3.5 h-3.5" /> [ SAVE BIOMETRICS ]
              </>
            ) : (
              <>
                <Edit2 className="w-3.5 h-3.5" /> [ CALIBRATE VITALS ]
              </>
            )}
          </button>
        </div>

        {isEditingVitals ? (
          <div className="grid grid-cols-2 md:grid-cols-4 gap-4 pt-1">
            <div>
              <label className="text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider block mb-1">
                Blood Pressure (mmHg)
              </label>
              <input
                type="text"
                value={tempVitals.bloodPressure}
                onChange={(e) => setTempVitals({ ...tempVitals, bloodPressure: e.target.value })}
                className="w-full px-3 py-2 border border-black text-sm font-mono font-bold text-black focus:outline-none"
                placeholder="120/80"
              />
            </div>
            <div>
              <label className="text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider block mb-1">
                Heart Rate (BPM)
              </label>
              <input
                type="number"
                value={tempVitals.heartRate}
                onChange={(e) => setTempVitals({ ...tempVitals, heartRate: Number(e.target.value) })}
                className="w-full px-3 py-2 border border-black text-sm font-mono font-bold text-black focus:outline-none"
                placeholder="72"
              />
            </div>
            <div>
              <label className="text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider block mb-1">
                Core Temp (°F)
              </label>
              <input
                type="number"
                step="0.1"
                value={tempVitals.temperature}
                onChange={(e) => setTempVitals({ ...tempVitals, temperature: Number(e.target.value) })}
                className="w-full px-3 py-2 border border-black text-sm font-mono font-bold text-black focus:outline-none"
                placeholder="98.6"
              />
            </div>
            <div>
              <label className="text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider block mb-1">
                Weight (LBS)
              </label>
              <input
                type="number"
                value={tempVitals.weight}
                onChange={(e) => setTempVitals({ ...tempVitals, weight: Number(e.target.value) })}
                className="w-full px-3 py-2 border border-black text-sm font-mono font-bold text-black focus:outline-none"
                placeholder="145"
              />
            </div>
          </div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-black/10">
            <div className="py-2 px-4 text-left">
              <span className="block text-[10px] font-mono font-semibold text-[#888] uppercase tracking-[0.2em]">
                01 / Blood Pressure
              </span>
              <div className="flex items-baseline gap-1.5 mt-1">
                <span className="text-3xl font-serif italic text-[#1C1C1C]">{vitals.bloodPressure}</span>
                <span className="text-[10px] font-mono text-[#888]">mmHg</span>
              </div>
            </div>

            <div className="py-2 px-4 text-left">
              <span className="block text-[10px] font-mono font-semibold text-[#888] uppercase tracking-[0.2em]">
                02 / Pulse Rate
              </span>
              <div className="flex items-baseline gap-1.5 mt-1">
                <span className="text-3xl font-serif italic text-[#1C1C1C]">{vitals.heartRate}</span>
                <span className="text-[10px] font-mono text-[#888]">bpm</span>
              </div>
            </div>

            <div className="py-2 px-4 text-left">
              <span className="block text-[10px] font-mono font-semibold text-[#888] uppercase tracking-[0.2em]">
                03 / Temperature
              </span>
              <div className="flex items-baseline gap-1.5 mt-1">
                <span className="text-3xl font-serif italic text-[#1C1C1C]">{vitals.temperature}</span>
                <span className="text-[10px] font-mono text-[#888]">°F</span>
              </div>
            </div>

            <div className="py-2 px-4 text-left">
              <span className="block text-[10px] font-mono font-semibold text-[#888] uppercase tracking-[0.2em]">
                04 / Body Mass
              </span>
              <div className="flex items-baseline gap-1.5 mt-1">
                <span className="text-3xl font-serif italic text-[#1C1C1C]">{vitals.weight}</span>
                <span className="text-[10px] font-mono text-[#888]">lbs</span>
              </div>
            </div>
          </div>
        )}
      </div>

      {/* Main 2-Column Clinical Layout */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Left Column: SOAP Notes + Prescription Builder (8 cols) */}
        <div className="lg:col-span-8 space-y-8">
          {/* Card 1: Clinical Notes (SOAP) */}
          <div id="soap-notes-card" className="bg-white border border-black/20 p-8 shadow-2xs">
            <div className="mb-6 pb-4 border-b border-black/10 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <FileText className="w-4 h-4 text-black" />
                <h2 className="font-serif italic text-2xl text-[#1C1C1C]">SOAP Clinical Documentation</h2>
              </div>
              <span className="text-[10px] font-mono uppercase tracking-[0.25em] text-[#888]">
                DIAGNOSTIC PROTOCOL
              </span>
            </div>

            <div className="space-y-6">
              {/* Subjective & Objective Row */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {/* Subjective */}
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-[#555]">
                      01 / SUBJECTIVE (SYMPTOMATOLOGY)
                    </span>
                  </div>
                  <textarea
                    id="input-soap-subjective"
                    rows={4}
                    value={soapNotes.subjective}
                    onChange={(e) => updateSoapNotes({ subjective: e.target.value })}
                    placeholder="Patient describes chief complaints, onset, and duration..."
                    className="w-full p-4 border border-black/20 focus:border-black text-xs text-[#1C1C1C] font-sans leading-relaxed bg-[#FDFCFB] focus:outline-none transition-colors"
                  ></textarea>
                </div>

                {/* Objective */}
                <div className="space-y-2">
                  <div className="flex items-center justify-between">
                    <span className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-[#555]">
                      02 / OBJECTIVE (EXAM & SIGNS)
                    </span>
                  </div>
                  <textarea
                    id="input-soap-objective"
                    rows={4}
                    value={soapNotes.objective}
                    onChange={(e) => updateSoapNotes({ objective: e.target.value })}
                    placeholder="Physical examination notes, auscultation, inspection findings..."
                    className="w-full p-4 border border-black/20 focus:border-black text-xs text-[#1C1C1C] font-sans leading-relaxed bg-[#FDFCFB] focus:outline-none transition-colors"
                  ></textarea>
                </div>
              </div>

              {/* Assessment (Diagnosis) */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-[#555]">
                    03 / ASSESSMENT (ICD-10 NOSOLOGY)
                  </span>
                  <span className="text-[10px] font-mono text-[#888]">SELECT OR SEARCH</span>
                </div>

                <div className="relative">
                  <div className="flex items-center gap-3 px-4 py-3 border border-black/20 focus-within:border-black bg-[#FDFCFB]">
                    <Search className="w-3.5 h-3.5 text-[#666] shrink-0" />
                    <input
                      id="input-icd-search"
                      type="text"
                      placeholder="Lookup ICD-10 nosology code or diagnostic label..."
                      value={icdSearch}
                      onChange={(e) => {
                        setIcdSearch(e.target.value);
                        setShowIcdSuggestions(true);
                      }}
                      onFocus={() => setShowIcdSuggestions(true)}
                      className="w-full border-none outline-none text-xs font-mono text-black placeholder-[#999] bg-transparent"
                    />
                  </div>

                  {/* ICD-10 Auto-Suggestions Dropdown */}
                  {showIcdSuggestions && icdSearch.trim() && (
                    <div className="absolute top-full left-0 right-0 mt-1 bg-white border border-black p-2 z-30 max-h-48 overflow-y-auto shadow-xl space-y-1">
                      {filteredIcdCodes.length > 0 ? (
                        filteredIcdCodes.map((item) => (
                          <div
                            key={item.code}
                            onClick={() => handleAddDiagnosisCode(item)}
                            className="p-2.5 text-xs hover:bg-[#F5F5F0] border border-transparent hover:border-black/20 cursor-pointer flex items-center justify-between transition-colors"
                          >
                            <span className="font-mono font-bold text-black">{item.code}</span>
                            <span className="text-[#555] font-sans">{item.label}</span>
                          </div>
                        ))
                      ) : (
                        <div className="p-3 text-xs font-mono text-[#888] text-center">No cataloged ICD-10 classification</div>
                      )}
                    </div>
                  )}
                </div>

                {/* Selected Diagnosis Pills with Artistic Styling */}
                <div className="flex flex-wrap items-center gap-2 pt-1">
                  {soapNotes.assessmentCodes.map((diag) => (
                    <span
                      key={diag.code}
                      className="inline-flex items-center gap-2 px-3 py-1.5 border border-black bg-[#F5F5F0] text-xs font-mono text-black"
                    >
                      <span className="font-bold">{diag.code}</span>
                      <span>—</span>
                      <span className="font-sans">{diag.label}</span>
                      <button
                        onClick={() => handleRemoveDiagnosisCode(diag.code)}
                        className="p-0.5 hover:bg-black hover:text-white transition-colors cursor-pointer"
                        title="Remove diagnosis"
                      >
                        <X className="w-3 h-3" />
                      </button>
                    </span>
                  ))}
                </div>
              </div>

              {/* Plan (Treatment & Follow-up) */}
              <div className="space-y-2">
                <div className="flex items-center justify-between">
                  <span className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-[#555]">
                    04 / PLAN (REGIMEN & FOLLOW-UP)
                  </span>
                </div>
                <textarea
                  id="input-soap-plan"
                  rows={3}
                  value={soapNotes.plan}
                  onChange={(e) => updateSoapNotes({ plan: e.target.value })}
                  placeholder="Prescribed clinical actions, lifestyle modifications, laboratory follow-up schedule..."
                  className="w-full p-4 border border-black/20 focus:border-black text-xs text-[#1C1C1C] font-sans leading-relaxed bg-[#FDFCFB] focus:outline-none transition-colors"
                ></textarea>
              </div>
            </div>
          </div>

          {/* Card 2: Prescription Builder (Tabular Architectural Design) */}
          <div id="prescription-builder-card" className="bg-white border border-black/20 p-8 shadow-2xs">
            <div className="mb-6 pb-4 border-b border-black/10 flex items-center justify-between">
              <div className="flex items-center gap-3">
                <Pill className="w-4 h-4 text-black" />
                <h2 className="font-serif italic text-2xl text-[#1C1C1C]">Apothecary & Prescription Builder</h2>
              </div>
              <button
                onClick={handleAddMedication}
                className="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-black hover:underline flex items-center gap-1.5 cursor-pointer"
              >
                <Plus className="w-3.5 h-3.5" />
                <span>[ APPEND ITEM ]</span>
              </button>
            </div>

            {/* List of Active Prescriptions */}
            <div className="space-y-3">
              {prescriptions.map((rx, idx) => (
                <div
                  key={rx.id}
                  className="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-4 border border-black/15 bg-[#FDFCFB] shadow-2xs"
                >
                  <div className="sm:col-span-1 text-[10px] font-mono text-[#888]">
                    #{idx + 1}
                  </div>

                  <div className="sm:col-span-4">
                    <span className="block text-[9px] font-mono font-bold text-[#888] uppercase tracking-wider">
                      PHARMA COMPOUND
                    </span>
                    <p className="text-xs font-serif italic text-black font-semibold mt-0.5">
                      {rx.medicationName}
                    </p>
                  </div>

                  <div className="sm:col-span-3">
                    <span className="block text-[9px] font-mono font-bold text-[#888] uppercase tracking-wider">
                      DOSAGE
                    </span>
                    <p className="text-xs font-mono text-[#444] mt-0.5">
                      {rx.dosage}
                    </p>
                  </div>

                  <div className="sm:col-span-3">
                    <span className="block text-[9px] font-mono font-bold text-[#888] uppercase tracking-wider">
                      SCHEDULE
                    </span>
                    <p className="text-xs font-mono text-[#444] mt-0.5">
                      {rx.frequency}
                    </p>
                  </div>

                  <div className="sm:col-span-1 flex justify-end">
                    <button
                      onClick={() => removePrescription(rx.id)}
                      className="p-1.5 border border-black/10 hover:border-black hover:bg-black hover:text-white transition-colors cursor-pointer"
                      title="Remove item"
                    >
                      <Trash2 className="w-3.5 h-3.5" />
                    </button>
                  </div>
                </div>
              ))}

              {/* Add New Medication Row */}
              <div className="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-4 border border-black/30 border-dashed bg-[#F5F5F0]">
                <div className="sm:col-span-4">
                  <span className="block text-[9px] font-mono font-bold text-[#666] uppercase tracking-wider mb-1">
                    Compound Name
                  </span>
                  <input
                    id="input-rx-med-name"
                    type="text"
                    placeholder="e.g. Amoxicillin..."
                    value={newMedName}
                    onChange={(e) => setNewMedName(e.target.value)}
                    className="w-full px-3 py-2 border border-black/20 bg-white text-xs font-sans text-black focus:border-black focus:outline-none"
                  />
                </div>

                <div className="sm:col-span-3">
                  <span className="block text-[9px] font-mono font-bold text-[#666] uppercase tracking-wider mb-1">
                    Dosage Unit
                  </span>
                  <input
                    id="input-rx-dosage"
                    type="text"
                    placeholder="e.g. 500mg"
                    value={newMedDosage}
                    onChange={(e) => setNewMedDosage(e.target.value)}
                    className="w-full px-3 py-2 border border-black/20 bg-white text-xs font-sans text-black focus:border-black focus:outline-none"
                  />
                </div>

                <div className="sm:col-span-4">
                  <span className="block text-[9px] font-mono font-bold text-[#666] uppercase tracking-wider mb-1">
                    Frequency Regimen
                  </span>
                  <div className="relative">
                    <select
                      id="select-rx-frequency"
                      value={newMedFreq}
                      onChange={(e) => setNewMedFreq(e.target.value)}
                      className="w-full px-3 py-2 pr-7 border border-black/20 bg-white text-xs font-mono text-black appearance-none focus:border-black focus:outline-none"
                    >
                      <option value="QD (Once daily)">QD (Once daily)</option>
                      <option value="BID (Twice a day)">BID (Twice a day)</option>
                      <option value="TID (Three times daily)">TID (Three times daily)</option>
                      <option value="QID (Four times daily)">QID (Four times daily)</option>
                      <option value="PRN (As needed)">PRN (As needed)</option>
                      <option value="QHS (At bedtime)">QHS (At bedtime)</option>
                    </select>
                    <ChevronDown className="w-3.5 h-3.5 text-black absolute right-2.5 top-2.5 pointer-events-none" />
                  </div>
                </div>

                <div className="sm:col-span-1 flex justify-end">
                  <button
                    id="btn-rx-add-confirm"
                    onClick={handleAddMedication}
                    className="p-2 bg-black text-white hover:bg-neutral-800 transition-colors border border-black cursor-pointer shadow-xs"
                    title="Add Medication Item"
                  >
                    <Plus className="w-4 h-4 stroke-[2.5]" />
                  </button>
                </div>
              </div>

              {/* Quick Template Chips */}
              <div className="pt-2 flex flex-wrap items-center gap-2">
                <span className="text-[10px] font-mono font-semibold text-[#888] uppercase tracking-wider mr-1">
                  Common Formularies:
                </span>
                {COMMON_MEDICATIONS.slice(0, 5).map((med) => (
                  <button
                    key={med.name}
                    type="button"
                    onClick={() => handleSelectMedicationTemplate(med.name, med.defaultDosage)}
                    className="text-[10px] font-mono px-2.5 py-1 border border-black/15 bg-white hover:bg-black hover:text-white transition-all text-[#444] cursor-pointer"
                  >
                    + {med.name} ({med.defaultDosage})
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>

        {/* Right Column: Historical Visits Archive (4 cols) */}
        <div className="lg:col-span-4">
          <div id="visit-history-card" className="bg-white border border-black/20 p-8 shadow-2xs sticky top-28">
            <div className="mb-6 pb-4 border-b border-black/10 flex items-center justify-between">
              <div>
                <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                  LONGITUDINAL
                </span>
                <h2 className="font-serif italic text-xl text-[#1C1C1C]">Prior Clinical Encounters</h2>
              </div>
              <Clock className="w-4 h-4 text-[#666]" />
            </div>

            {/* Timeline */}
            <div className="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-[1px] before:bg-black/20">
              {pastVisits.map((visit, idx) => (
                <div key={visit.id} className="relative group">
                  {/* Timeline node dot */}
                  <span
                    className={`absolute -left-6 top-1.5 w-3 h-3 border border-black ${
                      idx === 0 ? 'bg-black' : 'bg-white'
                    }`}
                  ></span>

                  <div className="bg-[#FDFCFB] border border-black/10 p-3.5 space-y-1">
                    <span className="text-[10px] font-mono font-bold text-black uppercase tracking-wider">
                      {visit.date}
                    </span>
                    <h3 className="font-serif italic text-sm text-[#1C1C1C] font-semibold">{visit.title}</h3>
                    <p className="text-xs text-[#666] leading-relaxed font-sans">{visit.summary}</p>
                    <div className="pt-2 mt-2 border-t border-black/5 flex items-center justify-between text-[10px] font-mono text-[#888]">
                      <span>{visit.doctorName}</span>
                      <span>RECORDED</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            <button
              onClick={() => showToast('Historical Dossier', 'All prior consultation records pulled from cold storage.', 'info')}
              className="w-full mt-6 py-2.5 text-center text-[10px] font-mono font-bold text-black hover:bg-black hover:text-white border border-black/20 uppercase tracking-[0.2em] transition-all cursor-pointer"
            >
              ARCHIVE RECORDS [ ALL ] →
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};
