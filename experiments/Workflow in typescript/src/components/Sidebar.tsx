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
  Building2,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';
import { NavTab } from '../types';

export const Sidebar: React.FC = () => {
  const { activeTab, setActiveTab, showToast } = useClinic();

  const navItems: Array<{ id: NavTab; label: string; icon: React.ComponentType<{ className?: string }> }> = [
    { id: 'dashboard', label: 'Dashboard', icon: LayoutDashboard },
    { id: 'patients', label: 'Patients', icon: Users },
    { id: 'calendar', label: 'Calendar', icon: CalendarIcon },
    { id: 'billing', label: 'Billing', icon: CreditCard },
    { id: 'inventory', label: 'Inventory', icon: Package },
    { id: 'settings', label: 'Settings', icon: Settings },
  ];

  return (
    <aside
      id="main-sidebar"
      className="w-64 bg-slate-50/50 border-r border-slate-200 flex flex-col justify-between h-screen shrink-0 sticky top-0 select-none z-20 font-sans"
    >
      {/* Top Section */}
      <div className="p-4 flex flex-col">
        {/* Brand Header */}
        <div
          id="clinic-brand-header"
          onClick={() => setActiveTab('dashboard')}
          className="cursor-pointer mb-6 px-2 flex items-center gap-3"
        >
          <div className="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm shrink-0">
            <Building2 className="w-5 h-5" />
          </div>
          <div>
            <h1 className="font-bold text-slate-900 text-base leading-tight">
              City Clinic
            </h1>
            <p className="text-xs font-medium text-slate-500">
              Admin Portal
            </p>
          </div>
        </div>

        {/* Primary Action Button: Register Patient */}
        <button
          id="btn-sidebar-register-patient"
          onClick={() => setActiveTab('register-patient')}
          className="w-full bg-[#0f2d71] hover:bg-[#0a1f50] text-white font-semibold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all mb-6 shadow-sm cursor-pointer"
        >
          <Plus className="w-4 h-4 stroke-[2.5]" />
          <span>Register Patient</span>
        </button>

        {/* Navigation Menu */}
        <nav id="sidebar-nav" className="space-y-1">
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = activeTab === item.id || (item.id === 'patients' && activeTab === 'clinical-encounter');
            return (
              <button
                key={item.id}
                id={`nav-${item.id}`}
                onClick={() => setActiveTab(item.id)}
                className={`w-full flex items-center gap-3 px-3.5 py-2.5 text-xs font-medium rounded-xl transition-all text-left ${
                  isActive
                    ? 'bg-blue-600 text-white font-semibold shadow-sm'
                    : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80'
                }`}
              >
                <Icon className={`w-4 h-4 ${isActive ? 'text-white' : 'text-slate-500'}`} />
                <span>{item.label}</span>
              </button>
            );
          })}
        </nav>
      </div>

      {/* Bottom Footer Section */}
      <div id="sidebar-footer" className="p-4 border-t border-slate-200/60 space-y-1">
        <button
          id="btn-sidebar-help"
          onClick={() => showToast('ClinicFlow Support', 'IT Support desk: Ext. 4400 / help@cityclinic.org', 'info')}
          className="w-full flex items-center gap-3 px-3.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 rounded-xl transition-colors"
        >
          <HelpCircle className="w-4 h-4 text-slate-500" />
          <span>Help</span>
        </button>

        <button
          id="btn-sidebar-logout"
          onClick={() => showToast('Session Concluded', 'Logged out of City Clinic Portal.', 'info')}
          className="w-full flex items-center gap-3 px-3.5 py-2 text-xs font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 rounded-xl transition-colors"
        >
          <LogOut className="w-4 h-4 text-slate-500" />
          <span>Logout</span>
        </button>
      </div>
    </aside>
  );
};
