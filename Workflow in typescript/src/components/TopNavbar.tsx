import React, { useState } from 'react';
import { Search, Bell, HelpCircle, LogOut, ChevronDown, Check } from 'lucide-react';
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
      className="h-20 bg-[#FDFCFB] border-b border-black/15 px-8 flex items-center justify-between sticky top-0 z-10 select-none"
    >
      {/* Left: Brand / Section Label & Search */}
      <div className="flex items-center gap-8 flex-1 max-w-2xl">
        <div className="hidden lg:flex flex-col">
          <span className="font-mono text-[9px] uppercase tracking-[0.3em] text-[#888]">
            Directory & Registry
          </span>
          <span className="font-serif italic text-lg text-[#1C1C1C] font-normal leading-tight">
            Consultation Desk
          </span>
        </div>

        {/* Global Search Bar with Artistic Flair */}
        <div className="relative flex-1">
          <div
            id="global-search-container"
            onClick={() => setIsSearchModalOpen(true)}
            className="flex items-center gap-3 bg-white border border-black/20 hover:border-black transition-all px-4 py-2.5 text-[#666] cursor-pointer text-xs w-full shadow-2xs group"
          >
            <Search className="w-3.5 h-3.5 text-[#777] group-hover:text-black shrink-0 transition-colors" />
            <input
              id="top-search-input"
              type="text"
              placeholder="Search patients by name, MRN, phone..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              onFocus={() => setIsSearchModalOpen(true)}
              className="bg-transparent border-none outline-none text-[#1C1C1C] placeholder-[#999] text-xs font-sans w-full cursor-pointer"
            />
            <kbd className="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-mono text-[#555] bg-[#F5F5F0] border border-black/20">
              ⌘K
            </kbd>
          </div>
        </div>
      </div>

      {/* Right Controls */}
      <div className="flex items-center gap-4 relative">
        {/* Notifications Icon with Badge */}
        <div className="relative">
          <button
            id="btn-top-notifications"
            onClick={() => setNotificationsOpen(!notificationsOpen)}
            className="w-9 h-9 border border-black/15 bg-white flex items-center justify-center text-[#444] hover:text-black hover:border-black transition-colors relative cursor-pointer"
            title="Notifications"
          >
            <Bell className="w-4 h-4" />
            <span className="absolute -top-1 -right-1 w-2 h-2 bg-black border border-white"></span>
          </button>

          {/* Notifications Dropdown */}
          {notificationsOpen && (
            <div
              id="notifications-popover"
              className="absolute right-0 mt-2 w-84 bg-[#FDFCFB] border border-black p-4 z-50 text-xs shadow-xl animate-in fade-in zoom-in-95 duration-100"
            >
              <div className="pb-2.5 mb-2.5 border-b border-black/15 flex items-center justify-between">
                <div>
                  <span className="font-mono text-[9px] uppercase tracking-[0.25em] text-[#888] block">Feed</span>
                  <span className="font-serif italic text-base text-[#1C1C1C]">Notices & Alerts</span>
                </div>
                <span className="text-[10px] font-mono border border-black bg-black text-white px-1.5 py-0.5">
                  03 NEW
                </span>
              </div>
              <div className="space-y-2.5 max-h-64 overflow-y-auto pr-1">
                <div className="p-2.5 bg-white border border-black/10 hover:border-black transition-colors cursor-pointer">
                  <div className="flex items-center justify-between text-[10px] font-mono text-[#888] mb-1">
                    <span>LAB REPORT</span>
                    <span>10m ago</span>
                  </div>
                  <p className="font-semibold text-[#1C1C1C] text-xs">James Wilson (MRN #99102)</p>
                  <p className="text-[#666] text-[11px] mt-0.5">Complete Metabolic Panel received and ready for review.</p>
                </div>
                <div className="p-2.5 bg-white border border-black/10 hover:border-black transition-colors cursor-pointer">
                  <div className="flex items-center justify-between text-[10px] font-mono text-[#888] mb-1">
                    <span>TRIAGE ARRIVAL</span>
                    <span>25m ago</span>
                  </div>
                  <p className="font-semibold text-[#1C1C1C] text-xs">Robert Johnson checked in</p>
                  <p className="text-[#666] text-[11px] mt-0.5">Assigned to Room 02 for routine hypertension consult.</p>
                </div>
                <div className="p-2.5 bg-white border border-black/10 hover:border-black transition-colors cursor-pointer">
                  <div className="flex items-center justify-between text-[10px] font-mono text-rose-700 mb-1">
                    <span>INVENTORY LOW</span>
                    <span>1h ago</span>
                  </div>
                  <p className="font-semibold text-[#1C1C1C] text-xs">Amoxicillin 500mg (14 units)</p>
                  <p className="text-[#666] text-[11px] mt-0.5">Stock level fell below reorder threshold.</p>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Help Icon */}
        <button
          id="btn-top-help"
          onClick={() => showToast('Clinical Manual', 'City Clinic Electronic Health Record guidelines and support desk.', 'info')}
          className="w-9 h-9 border border-black/15 bg-white flex items-center justify-center text-[#444] hover:text-black hover:border-black transition-colors cursor-pointer"
          title="Clinical Manual"
        >
          <HelpCircle className="w-4 h-4" />
        </button>

        {/* Doctor Avatar / Profile Switcher */}
        <div className="relative">
          <button
            id="btn-top-profile"
            onClick={() => setIsDoctorMenuOpen(!isDoctorMenuOpen)}
            className="flex items-center gap-3 p-1.5 pl-2.5 pr-2 border border-black/20 bg-white hover:border-black transition-all cursor-pointer"
          >
            <div className="text-left hidden sm:block">
              <p className="text-[9px] font-mono uppercase tracking-[0.2em] text-[#888] leading-none">
                ATTENDING
              </p>
              <p className="font-serif italic text-sm text-[#1C1C1C] leading-tight">
                {currentDoctor.name}
              </p>
            </div>
            <img
              src={currentDoctor.avatar}
              alt={currentDoctor.name}
              referrerPolicy="no-referrer"
              className="w-8 h-8 object-cover border border-black"
            />
            <ChevronDown className="w-3.5 h-3.5 text-[#666]" />
          </button>

          {/* Doctor Switcher Dropdown */}
          {isDoctorMenuOpen && (
            <div
              id="doctor-selector-menu"
              className="absolute right-0 mt-2 w-72 bg-[#FDFCFB] border border-black p-3 z-50 shadow-xl animate-in fade-in zoom-in-95 duration-100"
            >
              <div className="pb-2.5 mb-2 border-b border-black/15">
                <p className="text-[9px] font-mono text-[#888] font-bold uppercase tracking-[0.25em]">
                  Active Practitioner
                </p>
                <p className="font-serif italic text-base font-normal text-[#1C1C1C] mt-0.5">
                  {currentDoctor.name}
                </p>
                <p className="text-[11px] font-mono text-[#666]">{currentDoctor.specialty} • {currentDoctor.licenseNumber}</p>
              </div>

              <div>
                <p className="px-1 py-1 text-[9px] font-mono font-bold text-[#888] uppercase tracking-[0.25em]">
                  Switch Roster
                </p>
                <div className="space-y-1 mt-1">
                  {doctors.map((doc) => (
                    <button
                      key={doc.id}
                      onClick={() => {
                        setCurrentDoctor(doc);
                        setIsDoctorMenuOpen(false);
                        showToast('Provider Assigned', `Active practitioner changed to ${doc.name}.`, 'info');
                      }}
                      className={`w-full p-2 text-left flex items-center justify-between text-xs transition-colors border ${
                        doc.id === currentDoctor.id
                          ? 'bg-black text-white border-black'
                          : 'bg-white text-[#333] border-black/10 hover:border-black'
                      }`}
                    >
                      <div className="flex items-center gap-2.5">
                        <img
                          src={doc.avatar}
                          alt={doc.name}
                          referrerPolicy="no-referrer"
                          className="w-6 h-6 object-cover border border-black/30"
                        />
                        <div>
                          <p className="font-serif italic leading-none">{doc.name}</p>
                          <p className={`text-[10px] font-mono ${doc.id === currentDoctor.id ? 'text-white/70' : 'text-[#777]'}`}>{doc.specialty}</p>
                        </div>
                      </div>
                      {doc.id === currentDoctor.id && <Check className="w-3.5 h-3.5 text-white" />}
                    </button>
                  ))}
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </header>
  );
};
