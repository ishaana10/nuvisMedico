import React from 'react';
import {
  LayoutDashboard,
  Users,
  Calendar as CalendarIcon,
  CreditCard,
  Package,
  Settings,
  HelpCircle,
  LogOut,
  Plus,
  Stethoscope,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';
import { NavTab } from '../types';

export const Sidebar: React.FC = () => {
  const { activeTab, setActiveTab, showToast } = useClinic();

  const navItems: Array<{ id: NavTab; label: string; icon: React.ComponentType<{ className?: string }> }> = [
    { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { id: 'patients', label: 'Patient Registry', icon: Users },
    { id: 'calendar', label: 'Schedule & Queue', icon: CalendarIcon },
    { id: 'billing', label: 'Billing & Ledger', icon: CreditCard },
    { id: 'inventory', label: 'Apothecary & Stock', icon: Package },
    { id: 'settings', label: 'Clinic Setup', icon: Settings },
  ];

  return (
    <aside
      id="main-sidebar"
      className="w-72 bg-[#FDFCFB] border-r border-black/15 flex flex-col justify-between h-screen shrink-0 sticky top-0 select-none z-20"
    >
      {/* Top Section */}
      <div className="p-6 flex flex-col">
        {/* Clinic Brand Header with Artistic Flair */}
        <div
          id="clinic-brand-header"
          onClick={() => setActiveTab('dashboard')}
          className="cursor-pointer group mb-8 pb-5 border-b border-black/10 flex items-start justify-between"
        >
          <div>
            <div className="flex items-center gap-2 mb-1.5">
              <span className="inline-block w-2 h-2 bg-[#1C1C1C]"></span>
              <span className="font-mono text-[9px] font-bold tracking-[0.35em] text-[#717171] uppercase">
                Clinical Archive
              </span>
            </div>
            <h1 className="font-serif italic text-2xl font-normal text-[#1C1C1C] tracking-tight leading-none group-hover:opacity-80 transition-opacity">
              City Clinic
            </h1>
            <p className="text-[11px] font-sans text-[#717171] tracking-wide mt-1">
              Physician & Operations Console
            </p>
          </div>
          <div className="w-8 h-8 border border-black flex items-center justify-center text-xs font-serif italic bg-white group-hover:bg-black group-hover:text-white transition-colors">
            CC
          </div>
        </div>

        {/* Primary Action Button: Register Patient */}
        <button
          id="btn-sidebar-register-patient"
          onClick={() => setActiveTab('register-patient')}
          className="w-full bg-[#1C1C1C] text-white hover:bg-black active:scale-[0.99] border border-black font-sans text-xs font-semibold uppercase tracking-[0.2em] py-3.5 px-4 flex items-center justify-center gap-2.5 transition-all mb-7 shadow-xs cursor-pointer"
        >
          <Plus className="w-4 h-4 stroke-[2.5]" />
          <span>Register Patient</span>
        </button>

        {/* Section Label */}
        <div className="mb-2.5 px-2 flex items-center justify-between">
          <span className="text-[10px] font-mono font-bold uppercase tracking-[0.25em] text-[#888]">
            Navigation
          </span>
          <span className="text-[9px] font-mono text-[#AAA]">01 — 06</span>
        </div>

        {/* Navigation Menu */}
        <nav id="sidebar-nav" className="space-y-1">
          {navItems.map((item, index) => {
            const Icon = item.icon;
            const isActive = activeTab === item.id || (item.id === 'patients' && activeTab === 'clinical-encounter');
            return (
              <button
                key={item.id}
                id={`nav-${item.id}`}
                onClick={() => setActiveTab(item.id)}
                className={`w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-medium tracking-wide transition-all text-left border ${
                  isActive
                    ? 'bg-[#1C1C1C] text-white border-black shadow-xs font-semibold'
                    : 'text-[#444] border-transparent hover:text-black hover:bg-black/5 hover:border-black/10'
                }`}
              >
                <div className="flex items-center gap-3">
                  <Icon className={`w-4 h-4 ${isActive ? 'text-white' : 'text-[#666]'}`} />
                  <span>{item.label}</span>
                </div>
                <span className={`text-[10px] font-mono ${isActive ? 'text-white/60' : 'text-[#999]'}`}>
                  0{index + 1}
                </span>
              </button>
            );
          })}

          {/* Shortcut to Current Clinical Consultation */}
          <div className="pt-3 mt-3 border-t border-black/10">
            <div className="px-2 mb-1.5 flex items-center justify-between">
              <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888]">
                Encounter
              </span>
              <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <button
              id="nav-clinical-encounter"
              onClick={() => setActiveTab('clinical-encounter')}
              className={`w-full flex items-center justify-between px-3.5 py-2.5 text-xs font-medium tracking-wide transition-all text-left border ${
                activeTab === 'clinical-encounter'
                  ? 'bg-[#1C1C1C] text-white border-black shadow-xs font-semibold'
                  : 'text-[#444] border-black/10 bg-white/60 hover:text-black hover:bg-black/5'
              }`}
            >
              <div className="flex items-center gap-3">
                <Stethoscope className={`w-4 h-4 ${activeTab === 'clinical-encounter' ? 'text-white' : 'text-[#666]'}`} />
                <span>Active Encounter</span>
              </div>
              <span className={`text-[10px] font-mono ${activeTab === 'clinical-encounter' ? 'text-white/70' : 'text-emerald-700'}`}>
                LIVE
              </span>
            </button>
          </div>
        </nav>
      </div>

      {/* Bottom Footer Section */}
      <div id="sidebar-footer" className="p-6 border-t border-black/15 bg-[#F8F7F4]/50">
        <div className="flex items-center justify-between mb-3 text-[10px] font-mono text-[#888]">
          <span className="tracking-[0.2em] uppercase font-bold">Edition 24/02</span>
          <span>&copy; 2026</span>
        </div>

        <div className="grid grid-cols-2 gap-2">
          <button
            id="btn-sidebar-help"
            onClick={() => showToast('ClinicFlow Support', 'IT Support desk: Ext. 4400 / help@cityclinic.org', 'info')}
            className="flex items-center justify-center gap-1.5 py-2 px-2 border border-black/15 bg-white text-[11px] font-mono text-[#444] hover:bg-black hover:text-white hover:border-black transition-colors"
          >
            <HelpCircle className="w-3.5 h-3.5" />
            <span>Guide</span>
          </button>

          <button
            id="btn-sidebar-logout"
            onClick={() => showToast('Session Concluded', 'Logged out of City Clinic Portal.', 'info')}
            className="flex items-center justify-center gap-1.5 py-2 px-2 border border-black/15 bg-white text-[11px] font-mono text-[#444] hover:bg-rose-950 hover:text-white hover:border-rose-950 transition-colors"
          >
            <LogOut className="w-3.5 h-3.5" />
            <span>Exit</span>
          </button>
        </div>
      </div>
    </aside>
  );
};
