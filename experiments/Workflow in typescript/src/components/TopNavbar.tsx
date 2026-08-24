import React, { useState } from 'react';
import { Search, Bell, HelpCircle, ChevronDown, Check } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const TopNavbar: React.FC = () => {
  const {
    currentDoctor,
    setCurrentDoctor,
    doctors,
    setIsSearchModalOpen,
    searchQuery,
    setSearchQuery,
    showToast,
  } = useClinic();

  const [isDoctorMenuOpen, setIsDoctorMenuOpen] = useState(false);
  const [notificationsOpen, setNotificationsOpen] = useState(false);

  return (
    <header
      id="top-navbar"
      className="h-16 bg-white border-b border-slate-200/80 px-8 flex items-center justify-between sticky top-0 z-10 select-none font-sans"
    >
      {/* Brand & Global Search Bar */}
      <div className="flex items-center gap-6 flex-1 max-w-2xl">
        <span className="font-bold text-blue-600 text-lg tracking-tight hidden md:inline">
          ClinicFlow
        </span>

        <div className="relative flex-1">
          <div
            id="global-search-container"
            onClick={() => setIsSearchModalOpen(true)}
            className="flex items-center gap-2.5 bg-slate-100/70 border border-slate-200/80 hover:border-slate-300 transition-all px-3.5 py-2 text-slate-500 rounded-full cursor-pointer text-xs w-full group"
          >
            <Search className="w-4 h-4 text-slate-400 group-hover:text-slate-600 shrink-0 transition-colors" />
            <input
              id="top-search-input"
              type="text"
              placeholder="Search patients, doctors..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onFocus={() => setIsSearchModalOpen(true)}
              className="bg-transparent border-none outline-none text-slate-900 placeholder-slate-400 text-xs w-full cursor-pointer"
            />
          </div>
        </div>
      </div>

      {/* Right Controls */}
      <div className="flex items-center gap-3 relative">
        {/* Notifications Icon */}
        <div className="relative">
          <button
            id="btn-top-notifications"
            onClick={() => setNotificationsOpen(!notificationsOpen)}
            className="w-9 h-9 rounded-full bg-slate-100/80 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition-colors relative cursor-pointer"
            title="Notifications"
          >
            <Bell className="w-4 h-4" />
            <span className="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
          </button>

          {/* Notifications Dropdown */}
          {notificationsOpen && (
            <div
              id="notifications-popover"
              className="absolute right-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl p-4 z-50 text-xs shadow-xl"
            >
              <div className="pb-2 mb-2 border-b border-slate-100 flex items-center justify-between">
                <span className="font-bold text-slate-900 text-sm">Notifications</span>
                <span className="text-[10px] font-semibold bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full">
                  3 NEW
                </span>
              </div>
              <div className="space-y-2 max-h-64 overflow-y-auto pr-1">
                <div className="p-2.5 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                  <div className="flex items-center justify-between text-[10px] text-slate-400 mb-0.5">
                    <span className="font-semibold text-slate-600">LAB REPORT</span>
                    <span>10m ago</span>
                  </div>
                  <p className="font-bold text-slate-900 text-xs">James Wilson (MRN #99102)</p>
                  <p className="text-slate-500 text-[11px] mt-0.5">Metabolic Panel ready for review.</p>
                </div>
                <div className="p-2.5 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                  <div className="flex items-center justify-between text-[10px] text-slate-400 mb-0.5">
                    <span className="font-semibold text-slate-600">TRIAGE ARRIVAL</span>
                    <span>25m ago</span>
                  </div>
                  <p className="font-bold text-slate-900 text-xs">Robert Johnson checked in</p>
                  <p className="text-slate-500 text-[11px] mt-0.5">Assigned to Room 02.</p>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Help Icon */}
        <button
          id="btn-top-help"
          onClick={() => showToast('Clinical Manual', 'City Clinic Electronic Health Record guidelines and support desk.', 'info')}
          className="w-9 h-9 rounded-full bg-slate-100/80 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-200/80 transition-colors cursor-pointer"
          title="Help"
        >
          <HelpCircle className="w-4 h-4" />
        </button>

        {/* Doctor Avatar / Profile Switcher */}
        <div className="relative">
          <button
            id="btn-top-profile"
            onClick={() => setIsDoctorMenuOpen(!isDoctorMenuOpen)}
            className="flex items-center gap-1.5 p-1 rounded-full hover:bg-slate-100 transition-all cursor-pointer"
          >
            <img
              src={currentDoctor.avatar}
              alt={currentDoctor.name}
              referrerPolicy="no-referrer"
              className="w-8 h-8 rounded-full object-cover border border-slate-200"
            />
            <ChevronDown className="w-3.5 h-3.5 text-slate-400" />
          </button>

          {/* Doctor Switcher Dropdown */}
          {isDoctorMenuOpen && (
            <div
              id="doctor-selector-menu"
              className="absolute right-0 mt-2 w-64 bg-white border border-slate-200 rounded-2xl p-3 z-50 shadow-xl"
            >
              <div className="pb-2.5 mb-2 border-b border-slate-100">
                <p className="text-[10px] font-medium text-slate-400 uppercase tracking-wider">
                  Signed in as
                </p>
                <p className="font-bold text-slate-900 text-sm mt-0.5">
                  {currentDoctor.name}
                </p>
                <p className="text-xs text-slate-500">{currentDoctor.specialty}</p>
              </div>

              <div className="space-y-1">
                {doctors.map((doc) => (
                  <button
                    key={doc.id}
                    onClick={() => {
                      setCurrentDoctor(doc);
                      setIsDoctorMenuOpen(false);
                      showToast('Provider Assigned', `Active practitioner changed to ${doc.name}.`, 'info');
                    }}
                    className={`w-full p-2 rounded-xl text-left flex items-center justify-between text-xs transition-colors ${
                      doc.id === currentDoctor.id
                        ? 'bg-blue-50 text-blue-800 font-bold'
                        : 'text-slate-700 hover:bg-slate-100'
                    }`}
                  >
                    <div className="flex items-center gap-2">
                      <img
                        src={doc.avatar}
                        alt={doc.name}
                        referrerPolicy="no-referrer"
                        className="w-6 h-6 rounded-full object-cover"
                      />
                      <span>{doc.name}</span>
                    </div>
                    {doc.id === currentDoctor.id && <Check className="w-3.5 h-3.5 text-blue-600" />}
                  </button>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </header>
  );
};
