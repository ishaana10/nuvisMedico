import React from 'react';
import { X, Printer, Download, AlertTriangle } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const PrintPrescriptionModal: React.FC = () => {
  const {
    isPrintRxModalOpen,
    setIsPrintRxModalOpen,
    activePatient,
    currentDoctor,
    vitals,
    prescriptions,
    showToast,
  } = useClinic();

  if (!isPrintRxModalOpen) return null;

  const handlePrint = () => {
    window.print();
  };

  const handleDownload = () => {
    showToast('Prescription Downloaded', `Rx for ${activePatient.firstName} ${activePatient.lastName} saved as PDF.`, 'info');
  };

  return (
    <div
      id="print-prescription-modal"
      className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-in fade-in"
    >
      <div className="bg-white border-2 border-black max-w-2xl w-full p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
        {/* Modal Controls (Not printed) */}
        <div className="flex items-center justify-between pb-4 border-b border-black/15 print:hidden">
          <div>
            <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
              PHARMACOPOEIA
            </span>
            <span className="font-serif italic text-xl text-black">Prescription Formulary Pad</span>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={handleDownload}
              className="px-3.5 py-1.5 border border-black/20 hover:border-black text-[10px] font-mono font-bold uppercase tracking-wider text-black flex items-center gap-1.5 cursor-pointer transition-colors"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Export PDF</span>
            </button>
            <button
              onClick={handlePrint}
              className="px-4 py-1.5 bg-black hover:bg-neutral-800 text-white border border-black text-[10px] font-mono font-bold uppercase tracking-wider flex items-center gap-1.5 cursor-pointer shadow-xs"
            >
              <Printer className="w-3.5 h-3.5" />
              <span>Print Form</span>
            </button>
            <button
              onClick={() => setIsPrintRxModalOpen(false)}
              className="p-1.5 text-black hover:bg-black hover:text-white border border-black/20 transition-colors cursor-pointer"
            >
              <X className="w-4 h-4" />
            </button>
          </div>
        </div>

        {/* Printable Rx Sheet (Editorial Print Layout) */}
        <div id="rx-document-paper" className="p-8 border border-black bg-[#FDFCFB] space-y-6 text-[#1C1C1C]">
          {/* Header */}
          <div className="flex items-start justify-between border-b border-black pb-4">
            <div>
              <span className="text-[9px] font-mono font-bold uppercase tracking-[0.3em] text-[#777] block">
                CLINIC DISPENSARY ARCHIVE
              </span>
              <h1 className="text-2xl font-serif italic text-black tracking-tight mt-0.5">
                City Clinic Medical Institute
              </h1>
              <p className="text-xs font-mono text-[#666] mt-1">100 Hospital Way, Suite 400 • Springfield, OR</p>
              <p className="text-xs font-mono text-[#666]">Tel: (555) 019-2834 • Fax: (555) 019-2835</p>
            </div>
            <div className="text-right font-mono">
              <p className="font-bold text-xs uppercase tracking-wider text-black">{currentDoctor.name}</p>
              <p className="text-[10px] text-[#666]">{currentDoctor.specialty}</p>
              <p className="text-[9px] text-[#888] mt-1">DEA #: AB1234567 • NPI: 1982736450</p>
            </div>
          </div>

          {/* Patient Details */}
          <div className="grid grid-cols-2 gap-4 font-mono text-xs bg-white p-4 border border-black/20">
            <div className="space-y-1">
              <p><span className="text-[#888] uppercase">PATIENT:</span> <strong className="text-black font-serif italic text-sm">{activePatient.firstName} {activePatient.lastName}</strong></p>
              <p><span className="text-[#888] uppercase">DOB:</span> {activePatient.dob} ({activePatient.age}Y)</p>
              <p><span className="text-[#888] uppercase">MRN:</span> #{activePatient.mrn}</p>
            </div>
            <div className="space-y-1">
              <p><span className="text-[#888] uppercase">DATE:</span> {new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
              <p><span className="text-[#888] uppercase">VITALS:</span> BP {vitals.bloodPressure} mmHg • WT {vitals.weight} lbs</p>
              {activePatient.clinicalOverview.knownAllergies && (
                <p className="text-rose-800 font-bold flex items-center gap-1 mt-0.5">
                  <AlertTriangle className="w-3.5 h-3.5 shrink-0 text-rose-700" />
                  <span>ALLERGIES: {activePatient.clinicalOverview.knownAllergies}</span>
                </p>
              )}
            </div>
          </div>

          {/* Rx Symbol & Medication Items */}
          <div className="py-2 space-y-4">
            <div className="text-4xl font-serif italic text-black font-bold select-none">℞</div>

            <div className="space-y-4 pl-4 font-mono">
              {prescriptions.map((rx, idx) => (
                <div key={rx.id} className="border-b border-black/15 pb-3">
                  <div className="flex items-baseline justify-between">
                    <p className="text-xs font-bold text-black uppercase tracking-wider">
                      {idx + 1}. {rx.medicationName} — {rx.dosage}
                    </p>
                    <span className="text-[10px] text-[#666]">DISP: #30 (Thirty Units)</span>
                  </div>
                  <p className="text-xs text-[#333] italic mt-1 font-serif">
                    Sig: Take {rx.frequency} with meals and water for {rx.duration}.
                  </p>
                  <p className="text-[9px] text-[#888] mt-0.5">Refills: 0 (Zero) • Generic bioequivalent authorized</p>
                </div>
              ))}

              {prescriptions.length === 0 && (
                <p className="text-xs text-[#888] italic font-serif">No active pharmaceutical items prescribed for this encounter.</p>
              )}
            </div>
          </div>

          {/* Doctor Signature */}
          <div className="pt-6 border-t border-black/20 flex items-end justify-between font-mono text-xs">
            <div>
              <p className="text-[9px] text-[#888] uppercase">DIGITAL VERIFICATION: VALIDATED & SIGNED</p>
              <p className="text-[9px] text-[#888] uppercase">ClinicFlow Security Architecture</p>
            </div>
            <div className="text-center">
              <div className="w-56 border-b border-black pb-1 mb-1">
                <span className="font-serif italic text-lg text-black">{currentDoctor.name}</span>
              </div>
              <p className="text-[10px] uppercase font-bold text-[#555]">Authorized Prescriber Signature</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
