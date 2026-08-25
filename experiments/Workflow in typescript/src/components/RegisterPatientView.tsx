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
      <div className="border-b border-slate-200 pb-5">
        <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
          New Patient Registration
        </h1>
        <p className="text-xs text-slate-500 mt-0.5">
          Record demographic identity, insurance details, and preliminary allergy information.
        </p>
      </div>

      <form onSubmit={(e) => handleSubmit(e, false)} className="space-y-6">
        {/* Section 1: Demographics */}
        <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs">
          <div className="mb-6 pb-4 border-b border-slate-100 flex items-center gap-2.5 text-slate-900">
            <UserCheck className="w-5 h-5 text-blue-600" />
            <h2 className="font-bold text-base">Demographics</h2>
          </div>

          <div className="space-y-4">
            {/* First Name & Last Name */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  First Name
                </label>
                <input
                  id="input-first-name"
                  type="text"
                  name="firstName"
                  placeholder="e.g. Eleanor"
                  value={formData.firstName}
                  onChange={handleInputChange}
                  className={`w-full px-3.5 py-2.5 border ${
                    errors.firstName ? 'border-red-500 bg-red-50/50' : 'border-slate-200 focus:border-blue-600'
                  } bg-slate-50/50 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none transition-all`}
                />
                {errors.firstName && <p className="text-xs text-red-600 mt-1">{errors.firstName}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  Last Name
                </label>
                <input
                  id="input-last-name"
                  type="text"
                  name="lastName"
                  placeholder="e.g. Vance"
                  value={formData.lastName}
                  onChange={handleInputChange}
                  className={`w-full px-3.5 py-2.5 border ${
                    errors.lastName ? 'border-red-500 bg-red-50/50' : 'border-slate-200 focus:border-blue-600'
                  } bg-slate-50/50 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none transition-all`}
                />
                {errors.lastName && <p className="text-xs text-red-600 mt-1">{errors.lastName}</p>}
              </div>
            </div>

            {/* Date of Birth & Gender */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  Date of Birth
                </label>
                <input
                  id="input-dob"
                  type="date"
                  name="dob"
                  value={formData.dob}
                  onChange={handleInputChange}
                  className={`w-full px-3.5 py-2.5 border ${
                    errors.dob ? 'border-red-500 bg-red-50/50' : 'border-slate-200 focus:border-blue-600'
                  } bg-slate-50/50 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none transition-all`}
                />
                {errors.dob && <p className="text-xs text-red-600 mt-1">{errors.dob}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  Gender
                </label>
                <div className="relative">
                  <select
                    id="select-gender"
                    name="gender"
                    value={formData.gender}
                    onChange={handleInputChange}
                    className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 appearance-none focus:bg-white focus:outline-none transition-all cursor-pointer"
                  >
                    <option value="Female">Female</option>
                    <option value="Male">Male</option>
                    <option value="Other">Other / Non-binary</option>
                  </select>
                  <ChevronDown className="w-4 h-4 text-slate-400 absolute right-3.5 top-3 pointer-events-none" />
                </div>
              </div>
            </div>

            {/* Phone Number & Email Address */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  Phone Number
                </label>
                <input
                  id="input-phone"
                  type="tel"
                  name="phone"
                  placeholder="(555) 000-0000"
                  value={formData.phone}
                  onChange={handleInputChange}
                  className={`w-full px-3.5 py-2.5 border ${
                    errors.phone ? 'border-red-500 bg-red-50/50' : 'border-slate-200 focus:border-blue-600'
                  } bg-slate-50/50 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none transition-all`}
                />
                {errors.phone && <p className="text-xs text-red-600 mt-1">{errors.phone}</p>}
              </div>

              <div>
                <label className="block text-xs font-bold text-slate-700 mb-1">
                  Email Address
                </label>
                <input
                  id="input-email"
                  type="email"
                  name="email"
                  placeholder="patient@example.com"
                  value={formData.email}
                  onChange={handleInputChange}
                  className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                />
              </div>
            </div>

            {/* Full Address */}
            <div>
              <label className="block text-xs font-bold text-slate-700 mb-1">
                Residential Address
              </label>
              <input
                id="input-address"
                type="text"
                name="address"
                placeholder="Street address, Suite, City, State, Postal code"
                value={formData.address}
                onChange={handleInputChange}
                className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
              />
            </div>
          </div>
        </div>

        {/* Section 2 & 3: Emergency Contact & Insurance Information */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Emergency Contact */}
          <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="mb-5 pb-3 border-b border-slate-100 flex items-center gap-2.5 text-slate-900">
                <PhoneCall className="w-5 h-5 text-blue-600" />
                <h2 className="font-bold text-base">Emergency Contact</h2>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 mb-1">
                    Contact Name
                  </label>
                  <input
                    id="input-emergency-name"
                    type="text"
                    name="emergencyContactName"
                    placeholder="Full Name"
                    value={formData.emergencyContactName}
                    onChange={handleInputChange}
                    className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">
                      Relationship
                    </label>
                    <input
                      id="input-emergency-relationship"
                      type="text"
                      name="emergencyContactRelationship"
                      placeholder="e.g. Spouse"
                      value={formData.emergencyContactRelationship}
                      onChange={handleInputChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">
                      Phone Number
                    </label>
                    <input
                      id="input-emergency-phone"
                      type="tel"
                      name="emergencyContactPhone"
                      placeholder="(555) 000-0000"
                      value={formData.emergencyContactPhone}
                      onChange={handleInputChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Insurance Information */}
          <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs flex flex-col justify-between">
            <div>
              <div className="mb-5 pb-3 border-b border-slate-100 flex items-center gap-2.5 text-slate-900">
                <ShieldCheck className="w-5 h-5 text-blue-600" />
                <h2 className="font-bold text-base">Insurance Coverage</h2>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-xs font-bold text-slate-700 mb-1">
                    Insurance Provider
                  </label>
                  <input
                    id="input-insurance-provider"
                    type="text"
                    name="insuranceProvider"
                    placeholder="e.g. Blue Cross, Aetna, Medicare"
                    value={formData.insuranceProvider}
                    onChange={handleInputChange}
                    className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                  />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">
                      Policy Number
                    </label>
                    <input
                      id="input-insurance-policy"
                      type="text"
                      name="insurancePolicyNumber"
                      placeholder="POL-984210"
                      value={formData.insurancePolicyNumber}
                      onChange={handleInputChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-bold text-slate-700 mb-1">
                      Group ID
                    </label>
                    <input
                      id="input-insurance-group"
                      type="text"
                      name="insuranceGroupNumber"
                      placeholder="GRP-4412"
                      value={formData.insuranceGroupNumber}
                      onChange={handleInputChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Section 4: Clinical Overview */}
        <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs relative overflow-hidden">
          <div className="mb-6 pb-4 border-b border-slate-100 flex items-center gap-2.5 text-slate-900">
            <HeartPulse className="w-5 h-5 text-blue-600" />
            <h2 className="font-bold text-base">Clinical Overview</h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {/* Known Allergies */}
            <div className="md:col-span-2">
              <label className="flex items-center gap-1.5 text-xs font-bold text-red-700 mb-1">
                <AlertTriangle className="w-4 h-4 text-red-600" />
                <span>Known Allergies</span>
              </label>
              <input
                id="input-known-allergies"
                type="text"
                name="knownAllergies"
                placeholder="e.g. Penicillin, Peanuts, Sulfa"
                value={formData.knownAllergies}
                onChange={handleInputChange}
                className="w-full px-3.5 py-2.5 border border-red-200 bg-red-50/40 rounded-xl focus:border-red-500 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
              />
            </div>

            {/* Blood Group */}
            <div>
              <label className="block text-xs font-bold text-slate-700 mb-1">
                Blood Group
              </label>
              <div className="relative">
                <select
                  id="select-blood-group"
                  name="bloodGroup"
                  value={formData.bloodGroup}
                  onChange={handleInputChange}
                  className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 appearance-none focus:bg-white focus:outline-none transition-all cursor-pointer"
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
                <ChevronDown className="w-4 h-4 text-slate-400 absolute right-3.5 top-3 pointer-events-none" />
              </div>
            </div>
          </div>

          <div className="mt-4">
            <label className="block text-xs font-bold text-slate-700 mb-1">
              Medical History / Chronic Conditions
            </label>
            <textarea
              id="input-chronic-conditions"
              name="chronicConditions"
              rows={2}
              placeholder="e.g. Hypertension, prior surgeries, asthma..."
              value={formData.chronicConditions}
              onChange={handleInputChange}
              className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
            ></textarea>
          </div>
        </div>

        {/* Form Actions */}
        <div className="flex flex-col sm:flex-row items-center justify-end gap-3 pt-2">
          <button
            type="button"
            onClick={handleReset}
            className="w-full sm:w-auto px-5 py-2.5 border border-slate-200 hover:bg-slate-50 rounded-xl text-slate-700 text-xs font-semibold flex items-center justify-center gap-2 transition-all cursor-pointer"
          >
            <RotateCcw className="w-4 h-4" />
            <span>Reset Form</span>
          </button>

          <button
            type="button"
            onClick={(e) => handleSubmit(e, true)}
            className="w-full sm:w-auto px-5 py-2.5 bg-blue-50 text-blue-800 hover:bg-blue-100 rounded-xl text-xs font-semibold flex items-center justify-center gap-2 transition-all cursor-pointer"
          >
            <span>Register & Start Encounter</span>
          </button>

          <button
            type="submit"
            id="btn-submit-register-patient"
            className="w-full sm:w-auto px-6 py-2.5 bg-[#0f2d71] hover:bg-[#0c245a] text-white text-xs font-semibold rounded-xl flex items-center justify-center gap-2 transition-all cursor-pointer shadow-sm"
          >
            <Check className="w-4 h-4" />
            <span>Save Patient</span>
          </button>
        </div>
      </form>
    </div>
  );
};
