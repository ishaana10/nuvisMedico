import React, { useEffect, useRef } from 'react';
import { Search, X, Users, Calendar, ArrowRight } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const SearchModal: React.FC = () => {
  const {
    isSearchModalOpen,
    setIsSearchModalOpen,
    searchQuery,
    setSearchQuery,
    patients,
    doctors,
    startEncounterForPatient,
    setActiveTab,
  } = useClinic();

  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (isSearchModalOpen) {
      setTimeout(() => inputRef.current?.focus(), 50);
    }
  }, [isSearchModalOpen]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        setIsSearchModalOpen(true);
      }
      if (e.key === 'Escape') {
        setIsSearchModalOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [setIsSearchModalOpen]);

  if (!isSearchModalOpen) return null;

  const matchedPatients = patients.filter(
    (p) =>
      `${p.firstName} ${p.lastName}`.toLowerCase().includes(searchQuery.toLowerCase()) ||
      p.mrn.toLowerCase().includes(searchQuery.toLowerCase()) ||
      p.phone.includes(searchQuery)
  );

  return (
    <div
      id="global-search-modal"
      className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-start justify-center pt-24 p-4 z-50 animate-in fade-in"
      onClick={() => setIsSearchModalOpen(false)}
    >
      <div
        className="bg-white border-2 border-black max-w-xl w-full shadow-2xl overflow-hidden animate-in zoom-in-95"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Search input */}
        <div className="flex items-center gap-3 px-5 py-4 border-b border-black/15 bg-[#FDFCFB]">
          <Search className="w-4 h-4 text-black shrink-0" />
          <input
            ref={inputRef}
            type="text"
            placeholder="Search patient registry, MRN index, or clinical roster..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            className="w-full bg-transparent border-none outline-none text-xs font-mono text-black placeholder-[#888]"
          />
          <button
            onClick={() => setIsSearchModalOpen(false)}
            className="p-1 text-black hover:bg-black hover:text-white border border-black/15 transition-colors cursor-pointer"
          >
            <X className="w-3.5 h-3.5" />
          </button>
        </div>

        {/* Results list */}
        <div className="max-h-80 overflow-y-auto p-4 space-y-4 text-xs font-mono">
          {/* Patients Section */}
          <div>
            <span className="px-1 text-[9px] font-bold text-[#888] uppercase tracking-[0.25em] block mb-2">
              PATIENT REGISTRY MATCHES ({matchedPatients.length})
            </span>
            <div className="space-y-1">
              {matchedPatients.slice(0, 5).map((p) => (
                <div
                  key={p.id}
                  onClick={() => {
                    startEncounterForPatient(p);
                    setIsSearchModalOpen(false);
                  }}
                  className="p-3 border border-transparent hover:border-black/20 hover:bg-[#F5F5F0] flex items-center justify-between cursor-pointer transition-colors group"
                >
                  <div className="flex items-center gap-3">
                    <img
                      src={
                        p.avatar ||
                        'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200'
                      }
                      alt={p.firstName}
                      referrerPolicy="no-referrer"
                      className="w-9 h-9 object-cover border border-black shrink-0"
                    />
                    <div>
                      <p className="font-serif italic text-sm text-black font-semibold group-hover:underline">
                        {p.firstName} {p.lastName}
                      </p>
                      <p className="text-[10px] text-[#666]">MRN #{p.mrn} • {p.gender.toUpperCase()}, {p.age}Y</p>
                    </div>
                  </div>
                  <span className="text-black font-bold text-[10px] uppercase tracking-wider flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <span>Open Dossier</span>
                    <ArrowRight className="w-3 h-3" />
                  </span>
                </div>
              ))}
            </div>
          </div>

          {/* Quick Navigation actions */}
          <div className="border-t border-black/10 pt-3">
            <span className="px-1 text-[9px] font-bold text-[#888] uppercase tracking-[0.25em] block mb-2">
              FAST PROTOCOL ACCESS
            </span>
            <div className="grid grid-cols-2 gap-2">
              <button
                onClick={() => {
                  setActiveTab('register-patient');
                  setIsSearchModalOpen(false);
                }}
                className="p-3 text-left bg-white border border-black/20 hover:bg-black hover:text-white transition-all text-xs flex items-center gap-2 cursor-pointer"
              >
                <Users className="w-3.5 h-3.5" />
                <span className="text-[10px] uppercase tracking-wider font-bold">New Intake</span>
              </button>
              <button
                onClick={() => {
                  setActiveTab('calendar');
                  setIsSearchModalOpen(false);
                }}
                className="p-3 text-left bg-white border border-black/20 hover:bg-black hover:text-white transition-all text-xs flex items-center gap-2 cursor-pointer"
              >
                <Calendar className="w-3.5 h-3.5" />
                <span className="text-[10px] uppercase tracking-wider font-bold">Calendar Grid</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
