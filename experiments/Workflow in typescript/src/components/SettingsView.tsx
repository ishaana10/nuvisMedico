import React, { useState } from 'react';
import { Building2, Bell, Save } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const SettingsView: React.FC = () => {
  const { showToast } = useClinic();
  const [clinicName, setClinicName] = useState('City Clinic - Metropolitan Health Center');
  const [clinicAddress, setClinicAddress] = useState('100 Hospital Way, Suite 400, Springfield, OR 97477');
  const [clinicPhone, setClinicPhone] = useState('(555) 019-2834');
  const [emailAlerts, setEmailAlerts] = useState(true);
  const [smsReminders, setSmsReminders] = useState(true);

  const handleSave = (e: React.FormEvent) => {
    e.preventDefault();
    showToast('Settings Saved', 'Clinic configuration and preferences updated successfully.');
  };

  return (
    <div id="settings-view" className="p-8 lg:p-10 max-w-4xl mx-auto space-y-8 animate-in fade-in duration-200">
      <div className="border-b border-slate-200 pb-5">
        <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
          System Configuration & Settings
        </h1>
        <p className="text-xs text-slate-500 mt-0.5">
          Configure clinic identity, notifications, automated SMS, and operational preferences.
        </p>
      </div>

      <form onSubmit={handleSave} className="space-y-6">
        {/* Practice Information */}
        <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs space-y-6">
          <div className="flex items-center gap-3 border-b border-slate-100 pb-4 text-slate-900">
            <Building2 className="w-5 h-5 text-blue-600" />
            <h2 className="font-bold text-base">Practice Identity</h2>
          </div>

          <div className="space-y-4 text-xs font-medium">
            <div>
              <label className="block text-xs font-bold text-slate-700 mb-1">
                Clinic Name
              </label>
              <input
                type="text"
                value={clinicName}
                onChange={(e) => setClinicName(e.target.value)}
                className="w-full p-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
              />
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-700 mb-1">
                Clinic Address
              </label>
              <input
                type="text"
                value={clinicAddress}
                onChange={(e) => setClinicAddress(e.target.value)}
                className="w-full p-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
              />
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-700 mb-1">
                Phone Line
              </label>
              <input
                type="text"
                value={clinicPhone}
                onChange={(e) => setClinicPhone(e.target.value)}
                className="w-full p-2.5 border border-slate-200 rounded-xl focus:border-blue-600 bg-slate-50/50 text-xs text-slate-900 focus:bg-white focus:outline-none transition-all"
              />
            </div>
          </div>
        </div>

        {/* Notifications & Patient Automation */}
        <div className="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-2xs space-y-6">
          <div className="flex items-center gap-3 border-b border-slate-100 pb-4 text-slate-900">
            <Bell className="w-5 h-5 text-blue-600" />
            <h2 className="font-bold text-base">Automated Notifications</h2>
          </div>

          <div className="space-y-3 text-xs">
            <label className="flex items-center justify-between p-4 bg-slate-50/50 rounded-xl border border-slate-200/60 cursor-pointer hover:bg-slate-100/50 transition-colors">
              <div>
                <span className="font-bold text-slate-900 text-xs block">
                  Automated Appointment SMS Reminders
                </span>
                <p className="text-xs text-slate-500 mt-0.5">
                  Send 24-hour reminder alert to confirmed patients
                </p>
              </div>
              <input
                type="checkbox"
                checked={smsReminders}
                onChange={(e) => setSmsReminders(e.target.checked)}
                className="w-4 h-4 accent-blue-600 rounded cursor-pointer"
              />
            </label>

            <label className="flex items-center justify-between p-4 bg-slate-50/50 rounded-xl border border-slate-200/60 cursor-pointer hover:bg-slate-100/50 transition-colors">
              <div>
                <span className="font-bold text-slate-900 text-xs block">
                  Critical Pathology Alert Notifications
                </span>
                <p className="text-xs text-slate-500 mt-0.5">
                  Notify attending physician immediately upon critical out-of-range diagnostics
                </p>
              </div>
              <input
                type="checkbox"
                checked={emailAlerts}
                onChange={(e) => setEmailAlerts(e.target.checked)}
                className="w-4 h-4 accent-blue-600 rounded cursor-pointer"
              />
            </label>
          </div>
        </div>

        {/* Save Button */}
        <div className="flex justify-end">
          <button
            type="submit"
            className="px-6 py-3 bg-[#0f2d71] hover:bg-[#0c245a] text-white font-semibold text-xs rounded-xl shadow-sm flex items-center gap-2 transition-all cursor-pointer"
          >
            <Save className="w-4 h-4" />
            <span>Save Settings</span>
          </button>
        </div>
      </form>
    </div>
  );
};
