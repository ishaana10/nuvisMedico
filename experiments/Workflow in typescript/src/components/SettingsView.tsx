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
      <div className="border-b border-black/15 pb-6">
        <div className="flex items-center gap-2 mb-1">
          <span className="w-2 h-2 bg-black"></span>
          <span className="text-[10px] font-mono font-bold tracking-[0.3em] uppercase text-[#777]">
            SYSTEM CONFIGURATION
          </span>
        </div>
        <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
          Practice Protocol & Parameters
        </h1>
        <p className="text-xs text-[#666] mt-1 font-sans">
          Configure clinical institutional identity, automated SMS patient outreach, and biometric alerts.
        </p>
      </div>

      <form onSubmit={handleSave} className="space-y-8">
        {/* Practice Information */}
        <div className="bg-white border border-black/20 p-8 shadow-2xs space-y-6">
          <div className="flex items-center justify-between border-b border-black/10 pb-4">
            <div className="flex items-center gap-3 text-black">
              <Building2 className="w-4 h-4" />
              <h2 className="font-serif italic text-xl text-[#1C1C1C]">Institutional Identity</h2>
            </div>
            <span className="text-[10px] font-mono uppercase tracking-[0.25em] text-[#888]">
              01 / PRACTICE
            </span>
          </div>

          <div className="space-y-5 text-xs font-mono">
            <div>
              <label className="block text-[10px] font-bold text-[#555] uppercase tracking-wider mb-1.5">
                Practice Designation
              </label>
              <input
                type="text"
                value={clinicName}
                onChange={(e) => setClinicName(e.target.value)}
                className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label className="block text-[10px] font-bold text-[#555] uppercase tracking-wider mb-1.5">
                Physical Dispensary Address
              </label>
              <input
                type="text"
                value={clinicAddress}
                onChange={(e) => setClinicAddress(e.target.value)}
                className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none transition-colors"
              />
            </div>

            <div>
              <label className="block text-[10px] font-bold text-[#555] uppercase tracking-wider mb-1.5">
                Switchboard Communications Line
              </label>
              <input
                type="text"
                value={clinicPhone}
                onChange={(e) => setClinicPhone(e.target.value)}
                className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono text-black focus:outline-none transition-colors"
              />
            </div>
          </div>
        </div>

        {/* Notifications & Patient Automation */}
        <div className="bg-white border border-black/20 p-8 shadow-2xs space-y-6">
          <div className="flex items-center justify-between border-b border-black/10 pb-4">
            <div className="flex items-center gap-3 text-black">
              <Bell className="w-4 h-4" />
              <h2 className="font-serif italic text-xl text-[#1C1C1C]">Automated Telemetry & Alerts</h2>
            </div>
            <span className="text-[10px] font-mono uppercase tracking-[0.25em] text-[#888]">
              02 / AUTOMATION
            </span>
          </div>

          <div className="space-y-3 font-mono text-xs">
            <label className="flex items-center justify-between p-4 bg-[#FDFCFB] border border-black/10 cursor-pointer hover:border-black transition-colors">
              <div>
                <span className="font-bold text-black uppercase tracking-wider text-[11px] block">
                  Automated Appointment SMS Reminders
                </span>
                <p className="text-[10px] text-[#666] mt-0.5 font-sans">
                  Send 24-hour scheduled triage alert to confirmed patients
                </p>
              </div>
              <input
                type="checkbox"
                checked={smsReminders}
                onChange={(e) => setSmsReminders(e.target.checked)}
                className="w-4 h-4 accent-black cursor-pointer"
              />
            </label>

            <label className="flex items-center justify-between p-4 bg-[#FDFCFB] border border-black/10 cursor-pointer hover:border-black transition-colors">
              <div>
                <span className="font-bold text-black uppercase tracking-wider text-[11px] block">
                  Critical Pathology Alert Stream
                </span>
                <p className="text-[10px] text-[#666] mt-0.5 font-sans">
                  Notify attending physician immediately upon critical out-of-range diagnostics
                </p>
              </div>
              <input
                type="checkbox"
                checked={emailAlerts}
                onChange={(e) => setEmailAlerts(e.target.checked)}
                className="w-4 h-4 accent-black cursor-pointer"
              />
            </label>
          </div>
        </div>

        {/* Save Button */}
        <div className="flex justify-end">
          <button
            type="submit"
            className="px-8 py-3.5 bg-black hover:bg-neutral-800 text-white font-mono font-bold uppercase tracking-[0.2em] text-xs border border-black shadow-xs flex items-center gap-2 transition-all cursor-pointer"
          >
            <Save className="w-4 h-4" />
            <span>Commit Configuration</span>
          </button>
        </div>
      </form>
    </div>
  );
};
