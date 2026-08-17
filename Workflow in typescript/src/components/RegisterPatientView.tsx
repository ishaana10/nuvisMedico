import React, { useState } from 'react';
import {
  UserCheck,
  PhoneCall,
  ShieldCheck,
  HeartPulse,
  AlertTriangle,
  ChevronDown,
  Check,
  RotateCcw,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const RegisterPatientView: React.FC = () => {
  const { addPatient, setActiveTab, startEncounterForPatient } = useClinic();

  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    dob: '',
    gender: 'Female' as 'Female' | 'Male' | 'Other',
    phone: '',
    email: '',
    address: '',
    emergencyContactName: '',
    emergencyContactRelationship: '',
    emergencyContactPhone: '',
    insuranceProvider: '',
    insurancePolicyNumber: '',
    insuranceGroupNumber: '',
    knownAllergies: '',
    bloodGroup: 'O+',
    chronicConditions: '',
    clinicalNotes: '',
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
    if (errors[name]) {
      setErrors((prev) => {
        const next = { ...prev };
        delete next[name];
        return next;
      });
    }
  };

  const handleReset = () => {
    setFormData({
      firstName: '',
      lastName: '',
      dob: '',
      gender: 'Female',
      phone: '',
      email: '',
      address: '',
      emergencyContactName: '',
      emergencyContactRelationship: '',
      emergencyContactPhone: '',
      insuranceProvider: '',
      insurancePolicyNumber: '',
      insuranceGroupNumber: '',
      knownAllergies: '',
      bloodGroup: 'O+',
      chronicConditions: '',
      clinicalNotes: '',
    });
    setErrors({});
  };

  const handleSubmit = (e: React.FormEvent, startConsultation = false) => {
    e.preventDefault();
    const newErrors: Record<string, string> = {};
    if (!formData.firstName.trim()) newErrors.firstName = 'First name is required';
    if (!formData.lastName.trim()) newErrors.lastName = 'Last name is required';
    if (!formData.dob) newErrors.dob = 'Date of birth is required';
    if (!formData.phone.trim()) newErrors.phone = 'Phone number is required';

    if (Object.keys(newErrors).length > 0) {
      setErrors(newErrors);
      return;
    }

    const createdPatient = addPatient({
      firstName: formData.firstName.trim(),
      lastName: formData.lastName.trim(),
      dob: formData.dob,
      gender: formData.gender,
      phone: formData.phone.trim(),
      email: formData.email.trim() || `${formData.firstName.toLowerCase()}.${formData.lastName.toLowerCase()}@example.com`,
      address: formData.address.trim() || '123 Health Ave, Springfield',
      emergencyContact: {
        name: formData.emergencyContactName.trim() || 'Not specified',
        relationship: formData.emergencyContactRelationship.trim() || 'Spouse',
        phone: formData.emergencyContactPhone.trim() || formData.phone,
      },
      insurance: {
        provider: formData.insuranceProvider.trim() || 'Self Pay',
        policyNumber: formData.insurancePolicyNumber.trim() || 'N/A',
        groupNumber: formData.insuranceGroupNumber.trim() || 'N/A',
      },
      clinicalOverview: {
        knownAllergies: formData.knownAllergies.trim() || 'None reported',
        bloodGroup: formData.bloodGroup,
        chronicConditions: formData.chronicConditions.trim(),
        notes: formData.clinicalNotes.trim(),
      },
    });

    if (startConsultation) {
      startEncounterForPatient(createdPatient);
    } else {
      setActiveTab('patients');
    }
  };

  return (
    <div id="register-patient-view" className="p-8 lg:p-10 max-w-5xl mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Page Header */}
      <div className="border-b border-black/15 pb-6">
        <div className="flex items-center gap-2 mb-1">
          <span className="w-2 h-2 bg-black"></span>
          <span className="text-[10px] font-mono font-bold tracking-[0.3em] uppercase text-[#777]">
            REGISTRATION FOLIO
          </span>
        </div>
        <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
          New Patient Intake Registration
        </h1>
        <p className="text-xs text-[#666] mt-1 font-sans">
          Record demographic identity, insurance verification, and preliminary allergy alerts.
        </p>
      </div>

      <form onSubmit={(e) => handleSubmit(e, false)} className="space-y-8">
        {/* Section 1: Demographics */}
        <div className="bg-white border border-black/20 p-8 shadow-2xs">
          <div className="mb-6 pb-4 border-b border-black/10 flex items-center justify-between">
            <div className="flex items-center gap-3 text-black">
              <UserCheck className="w-4 h-4" />
              <h2 className="font-serif italic text-xl text-[#1C1C1C]">Demographic Record</h2>
            </div>
            <span className="text-[10px] font-mono uppercase tracking-[0.25em] text-[#888]">
              01 / IDENTITY
            </span>
          </div>

          <div className="space-y-5">
            {/* First Name & Last Name */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  First Legal Name
                </label>
                <input
                  id="input-first-name"
                  type="text"
                  name="firstName"
                  placeholder="e.g. Eleanor"
                  value={formData.firstName}
                  onChange={handleInputChange}
                  className={`w-full px-4 py-3 border ${
                    errors.firstName ? 'border-rose-500 bg-rose-50/30' : 'border-black/20 focus:border-black'
                  } bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors`}
                />
                {errors.firstName && <p className="text-[10px] font-mono text-rose-600 mt-1">{errors.firstName}</p>}
              </div>

              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  Surname / Family Name
                </label>
                <input
                  id="input-last-name"
                  type="text"
                  name="lastName"
                  placeholder="e.g. Vance"
                  value={formData.lastName}
                  onChange={handleInputChange}
                  className={`w-full px-4 py-3 border ${
                    errors.lastName ? 'border-rose-500 bg-rose-50/30' : 'border-black/20 focus:border-black'
                  } bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors`}
                />
                {errors.lastName && <p className="text-[10px] font-mono text-rose-600 mt-1">{errors.lastName}</p>}
              </div>
            </div>

            {/* Date of Birth & Gender */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  Date of Birth
                </label>
                <input
                  id="input-dob"
                  type="date"
                  name="dob"
                  value={formData.dob}
                  onChange={handleInputChange}
                  className={`w-full px-4 py-3 border ${
                    errors.dob ? 'border-rose-500 bg-rose-50/30' : 'border-black/20 focus:border-black'
                  } bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors`}
                />
                {errors.dob && <p className="text-[10px] font-mono text-rose-600 mt-1">{errors.dob}</p>}
              </div>

              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  Biological Gender
                </label>
                <div className="relative">
                  <select
                    id="select-gender"
                    name="gender"
                    value={formData.gender}
                    onChange={handleInputChange}
                    className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black appearance-none focus:outline-none transition-colors cursor-pointer"
                  >
                    <option value="Female">Female</option>
                    <option value="Male">Male</option>
                    <option value="Other">Other / Non-binary</option>
                  </select>
                  <ChevronDown className="w-4 h-4 text-black absolute right-4 top-3.5 pointer-events-none" />
                </div>
              </div>
            </div>

            {/* Phone Number & Email Address */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  Primary Contact Phone
                </label>
                <input
                  id="input-phone"
                  type="tel"
                  name="phone"
                  placeholder="(555) 000-0000"
                  value={formData.phone}
                  onChange={handleInputChange}
                  className={`w-full px-4 py-3 border ${
                    errors.phone ? 'border-rose-500 bg-rose-50/30' : 'border-black/20 focus:border-black'
                  } bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors`}
                />
                {errors.phone && <p className="text-[10px] font-mono text-rose-600 mt-1">{errors.phone}</p>}
              </div>

              <div>
                <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                  Electronic Mail Address
                </label>
                <input
                  id="input-email"
                  type="email"
                  name="email"
                  placeholder="patient@example.com"
                  value={formData.email}
                  onChange={handleInputChange}
                  className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
                />
              </div>
            </div>

            {/* Full Address */}
            <div>
              <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                Physical Residential Address
              </label>
              <input
                id="input-address"
                type="text"
                name="address"
                placeholder="Street address, Suite, City, State, Postal code"
                value={formData.address}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
              />
            </div>
          </div>
        </div>

        {/* Section 2 & 3: Emergency Contact & Insurance Information */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
          {/* Emergency Contact */}
          <div className="bg-white border border-black/20 p-8 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="mb-5 pb-3 border-b border-black/10 flex items-center justify-between">
                <div className="flex items-center gap-2.5 text-black">
                  <PhoneCall className="w-4 h-4" />
                  <h2 className="font-serif italic text-lg text-[#1C1C1C]">Emergency Liaison</h2>
                </div>
                <span className="text-[9px] font-mono uppercase tracking-[0.25em] text-[#888]">
                  02 / CONTACT
                </span>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                    Contact Full Name
                  </label>
                  <input
                    id="input-emergency-name"
                    type="text"
                    name="emergencyContactName"
                    placeholder="Liaison Name"
                    value={formData.emergencyContactName}
                    onChange={handleInputChange}
                    className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                      Relationship
                    </label>
                    <input
                      id="input-emergency-relationship"
                      type="text"
                      name="emergencyContactRelationship"
                      placeholder="e.g. Spouse, Parent"
                      value={formData.emergencyContactRelationship}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                      Phone Number
                    </label>
                    <input
                      id="input-emergency-phone"
                      type="tel"
                      name="emergencyContactPhone"
                      placeholder="(555) 000-0000"
                      value={formData.emergencyContactPhone}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Insurance Information */}
          <div className="bg-white border border-black/20 p-8 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="mb-5 pb-3 border-b border-black/10 flex items-center justify-between">
                <div className="flex items-center gap-2.5 text-black">
                  <ShieldCheck className="w-4 h-4" />
                  <h2 className="font-serif italic text-lg text-[#1C1C1C]">Coverage & Payer</h2>
                </div>
                <span className="text-[9px] font-mono uppercase tracking-[0.25em] text-[#888]">
                  03 / POLICY
                </span>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                    Insurance Underwriter
                  </label>
                  <input
                    id="input-insurance-provider"
                    type="text"
                    name="insuranceProvider"
                    placeholder="e.g. Blue Cross, Aetna, Cigna, Medicare"
                    value={formData.insuranceProvider}
                    onChange={handleInputChange}
                    className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                      Policy Identifier
                    </label>
                    <input
                      id="input-insurance-policy"
                      type="text"
                      name="insurancePolicyNumber"
                      placeholder="POL-984210"
                      value={formData.insurancePolicyNumber}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors"
                    />
                  </div>
                  <div>
                    <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                      Group ID
                    </label>
                    <input
                      id="input-insurance-group"
                      type="text"
                      name="insuranceGroupNumber"
                      placeholder="GRP-4412"
                      value={formData.insuranceGroupNumber}
                      onChange={handleInputChange}
                      className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Section 4: Clinical Overview */}
        <div className="bg-white border border-black/20 p-8 shadow-2xs relative overflow-hidden">
          <div className="mb-6 pb-4 border-b border-black/10 flex items-center justify-between">
            <div className="flex items-center gap-2.5 text-black">
              <HeartPulse className="w-4 h-4" />
              <h2 className="font-serif italic text-xl text-[#1C1C1C]">Preliminary Clinical Overview</h2>
            </div>
            <span className="text-[10px] font-mono uppercase tracking-[0.25em] text-[#888]">
              04 / TRIAGE
            </span>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Known Allergies with Stark Highlight */}
            <div className="md:col-span-2">
              <label className="flex items-center gap-1.5 text-[10px] font-mono font-bold text-rose-700 uppercase tracking-wider mb-1.5">
                <AlertTriangle className="w-3.5 h-3.5" />
                <span>CRITICAL ALLERGIES & ADVERSE REACTIONS</span>
              </label>
              <input
                id="input-known-allergies"
                type="text"
                name="knownAllergies"
                placeholder="e.g. Penicillin Allergy, Peanuts, Sulfa"
                value={formData.knownAllergies}
                onChange={handleInputChange}
                className="w-full px-4 py-3 border border-rose-400 bg-rose-50/30 focus:border-rose-700 text-xs font-mono text-black placeholder-rose-400 focus:outline-none transition-colors"
              />
            </div>

            {/* Blood Group */}
            <div>
              <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
                Blood Classification
              </label>
              <div className="relative">
                <select
                  id="select-blood-group"
                  name="bloodGroup"
                  value={formData.bloodGroup}
                  onChange={handleInputChange}
                  className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black appearance-none focus:outline-none transition-colors cursor-pointer"
                >
                  <option value="A+">A+</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B-">B-</option>
                  <option value="O+">O+</option>
                  <option value="O-">O-</option>
                  <option value="AB+">AB+</option>
                  <option value="AB-">AB-</option>
                </select>
                <ChevronDown className="w-4 h-4 text-black absolute right-4 top-3.5 pointer-events-none" />
              </div>
            </div>
          </div>

          <div className="mt-6">
            <label className="block text-[10px] font-mono font-bold text-[#555] uppercase tracking-wider mb-1.5">
              Preliminary Medical Conditions & Notes
            </label>
            <textarea
              id="input-chronic-conditions"
              name="chronicConditions"
              rows={2}
              placeholder="e.g. Hypertension, prior appendectomy, chronic asthma..."
              value={formData.chronicConditions}
              onChange={handleInputChange}
              className="w-full px-4 py-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
            ></textarea>
          </div>
        </div>

        {/* Form Actions */}
        <div className="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
          <button
            type="button"
            onClick={handleReset}
            className="w-full sm:w-auto px-5 py-3.5 border border-black/20 hover:border-black bg-white text-black text-xs font-mono font-bold uppercase tracking-[0.2em] flex items-center justify-center gap-2 transition-colors cursor-pointer"
          >
            <RotateCcw className="w-3.5 h-3.5" />
            <span>Reset Folio</span>
          </button>

          <button
            type="button"
            onClick={(e) => handleSubmit(e, true)}
            className="w-full sm:w-auto px-6 py-3.5 border border-black bg-white hover:bg-black hover:text-white text-black text-xs font-mono font-bold uppercase tracking-[0.2em] flex items-center justify-center gap-2 transition-all cursor-pointer shadow-2xs"
          >
            <span>Register & Start Encounter</span>
          </button>

          <button
            type="submit"
            id="btn-submit-register-patient"
            className="w-full sm:w-auto px-8 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-mono font-bold uppercase tracking-[0.2em] border border-black flex items-center justify-center gap-2 transition-all cursor-pointer shadow-xs"
          >
            <Check className="w-4 h-4 stroke-[2.5]" />
            <span>Save to Archive</span>
          </button>
        </div>
      </form>
    </div>
  );
};
