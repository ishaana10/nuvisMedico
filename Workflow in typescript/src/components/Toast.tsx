import React from 'react';
import { CheckCircle2, AlertTriangle, AlertCircle, Info, X } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const Toast: React.FC = () => {
  const { toast, hideToast } = useClinic();

  if (!toast) return null;

  const getIcon = () => {
    switch (toast.type) {
      case 'success':
        return <CheckCircle2 className="w-4 h-4 text-black shrink-0" />;
      case 'warning':
        return <AlertTriangle className="w-4 h-4 text-amber-700 shrink-0" />;
      case 'error':
        return <AlertCircle className="w-4 h-4 text-rose-700 shrink-0" />;
      case 'info':
      default:
        return <Info className="w-4 h-4 text-black shrink-0" />;
    }
  };

  return (
    <div
      id="clinic-toast"
      className="fixed bottom-8 right-8 z-50 animate-in slide-in-from-bottom-5 fade-in duration-200"
    >
      <div
        className="flex items-start gap-3.5 p-4 border-2 border-black bg-white shadow-2xl max-w-sm w-full font-mono"
      >
        <div className="pt-0.5">{getIcon()}</div>
        <div className="flex-1 text-xs">
          <p className="font-serif italic text-sm font-bold text-black">{toast.title}</p>
          {toast.message && <p className="text-[11px] text-[#555] font-sans mt-0.5">{toast.message}</p>}
        </div>
        <button
          onClick={hideToast}
          className="p-1 text-black hover:bg-black hover:text-white transition-colors border border-black/15 cursor-pointer"
        >
          <X className="w-3.5 h-3.5" />
        </button>
      </div>
    </div>
  );
};
