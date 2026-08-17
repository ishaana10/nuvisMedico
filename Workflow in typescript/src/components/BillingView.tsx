import React, { useState } from 'react';
import {
  CreditCard,
  AlertTriangle,
  CheckCircle2,
  DollarSign,
  Plus,
  Search,
  Clock,
  Printer,
  X,
} from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const BillingView: React.FC = () => {
  const { invoices, markInvoicePaid, addInvoice, patients, showToast } = useClinic();
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('All');
  const [showCreateModal, setShowCreateModal] = useState(false);

  // New invoice form
  const [newPatientId, setNewPatientId] = useState(patients[0]?.id || '');
  const [newAmount, setNewAmount] = useState('250');
  const [newInsuranceCovered, setNewInsuranceCovered] = useState('200');
  const [newService, setNewService] = useState('Standard Clinical Consultation');

  const filteredInvoices = invoices.filter((inv) => {
    const matchesSearch =
      inv.patientName.toLowerCase().includes(search.toLowerCase()) ||
      inv.invoiceNumber.toLowerCase().includes(search.toLowerCase()) ||
      inv.patientMrn.toLowerCase().includes(search.toLowerCase());
    const matchesStatus = statusFilter === 'All' || inv.status === statusFilter;
    return matchesSearch && matchesStatus;
  });

  const totalPending = invoices
    .filter((inv) => inv.status !== 'Paid')
    .reduce((acc, curr) => acc + curr.amount, 0);

  const overdueCount = invoices.filter((inv) => inv.status === 'Overdue').length;

  const handleCreateInvoiceSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const pat = patients.find((p) => p.id === newPatientId) || patients[0];
    const amount = Number(newAmount) || 200;
    const ins = Number(newInsuranceCovered) || 150;

    addInvoice({
      patientName: `${pat.firstName} ${pat.lastName}`,
      patientMrn: pat.mrn,
      serviceDate: new Date().toISOString().split('T')[0],
      dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      amount,
      insuranceCovered: ins,
      patientOwed: Math.max(0, amount - ins),
      status: 'Pending',
      services: [newService],
    });

    setShowCreateModal(false);
  };

  return (
    <div id="billing-view" className="p-8 lg:p-10 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-black/15 pb-6">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="w-2 h-2 bg-black"></span>
            <span className="text-[10px] font-mono font-bold tracking-[0.3em] uppercase text-[#777]">
              LEDGER & ACCOUNTS
            </span>
          </div>
          <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
            Fiscal Claims & Accounts Receivable
          </h1>
          <p className="text-xs text-[#666] mt-1 font-sans">
            Maintain reconciliation of patient statements, third-party payer disbursements, and overdue balances.
          </p>
        </div>

        <button
          onClick={() => setShowCreateModal(true)}
          className="px-6 py-3 bg-black hover:bg-neutral-800 text-white text-xs font-mono font-bold uppercase tracking-[0.2em] border border-black flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-xs"
        >
          <Plus className="w-3.5 h-3.5 stroke-[2.5]" />
          <span>Issue Statement</span>
        </button>
      </div>

      {/* Overview Cards (Artistic Stat Boxes) */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div className="bg-white border border-black/20 p-6 shadow-2xs relative">
          <span className="text-[9px] font-mono font-bold text-[#888] uppercase tracking-[0.25em] block">
            01 / PENDING RECEIVABLES
          </span>
          <p className="text-3xl font-serif italic text-black mt-2">${totalPending.toLocaleString()}</p>
          <span className="text-[10px] font-mono text-[#666] mt-1 block">Active claims awaiting payer remittance</span>
        </div>

        <div className="bg-white border border-rose-400 p-6 shadow-2xs relative">
          <span className="text-[9px] font-mono font-bold text-rose-800 uppercase tracking-[0.25em] block">
            02 / DELINQUENT BALANCES
          </span>
          <p className="text-3xl font-serif italic text-rose-950 mt-2">{overdueCount}</p>
          <span className="text-[10px] font-mono text-rose-700 mt-1 block">Accounts requiring collection notice</span>
        </div>

        <div className="bg-white border border-black/20 p-6 shadow-2xs relative">
          <span className="text-[9px] font-mono font-bold text-[#888] uppercase tracking-[0.25em] block">
            03 / MONTHLY RECONCILIATION
          </span>
          <p className="text-3xl font-serif italic text-black mt-2">$28,450</p>
          <span className="text-[10px] font-mono text-[#666] mt-1 block">96.4% payer acceptance rate</span>
        </div>
      </div>

      {/* Invoices Table Card */}
      <div className="bg-white border border-black/20 shadow-2xs overflow-hidden">
        {/* Filters */}
        <div className="p-4 border-b border-black/15 flex flex-col sm:flex-row items-center justify-between gap-4 bg-[#FDFCFB]">
          <div className="relative flex-1 w-full max-w-md">
            <Search className="w-3.5 h-3.5 text-black absolute left-3 top-3" />
            <input
              type="text"
              placeholder="Search invoice identifier, patient name, or MRN..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-3 py-2 border border-black/20 focus:border-black text-xs font-mono text-black placeholder-[#888] bg-white focus:outline-none transition-colors"
            />
          </div>

          <div className="flex items-center gap-2">
            {['All', 'Overdue', 'Pending', 'Paid'].map((st) => (
              <button
                key={st}
                onClick={() => setStatusFilter(st)}
                className={`px-3 py-1.5 text-[10px] font-mono uppercase tracking-wider transition-all cursor-pointer border ${
                  statusFilter === st
                    ? 'bg-black text-white border-black font-bold'
                    : 'bg-white text-[#555] border-black/15 hover:border-black'
                }`}
              >
                {st}
              </button>
            ))}
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse text-xs">
            <thead>
              <tr className="border-b border-black/15 text-[10px] font-mono font-bold text-[#888] uppercase tracking-[0.2em] bg-[#F5F5F0]">
                <th className="py-3.5 px-6">Invoice #</th>
                <th className="py-3.5 px-6">Patient Dossier</th>
                <th className="py-3.5 px-6">Service Date</th>
                <th className="py-3.5 px-6">Clinical Line Items</th>
                <th className="py-3.5 px-6 text-right">Gross Total</th>
                <th className="py-3.5 px-6 text-right">Patient Portion</th>
                <th className="py-3.5 px-6 text-center">Status</th>
                <th className="py-3.5 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-black/10 text-xs">
              {filteredInvoices.map((inv) => (
                <tr key={inv.id} className="hover:bg-[#FDFCFB] transition-colors">
                  <td className="py-4 px-6 font-mono font-bold text-black">{inv.invoiceNumber}</td>
                  <td className="py-4 px-6">
                    <p className="font-serif italic text-sm text-black font-semibold">{inv.patientName}</p>
                    <p className="text-[10px] font-mono text-[#888]">MRN #{inv.patientMrn}</p>
                  </td>
                  <td className="py-4 px-6 font-mono text-[#666] whitespace-nowrap">{inv.serviceDate}</td>
                  <td className="py-4 px-6 text-[#555] max-w-xs truncate font-sans">{inv.services.join(', ')}</td>
                  <td className="py-4 px-6 text-right font-mono font-bold text-black">${inv.amount}</td>
                  <td className="py-4 px-6 text-right font-mono font-bold text-black">${inv.patientOwed}</td>
                  <td className="py-4 px-6 text-center">
                    <span
                      className={`inline-block px-2.5 py-0.5 text-[9px] font-mono uppercase tracking-wider border ${
                        inv.status === 'Paid'
                          ? 'bg-[#F5F5F0] border-black text-black font-bold'
                          : inv.status === 'Overdue'
                          ? 'bg-rose-50 border-rose-500 text-rose-900 font-bold'
                          : 'bg-white border-black/30 text-[#555]'
                      }`}
                    >
                      {inv.status}
                    </span>
                  </td>
                  <td className="py-4 px-6 text-right">
                    {inv.status !== 'Paid' ? (
                      <button
                        onClick={() => markInvoicePaid(inv.id)}
                        className="px-3 py-1 bg-black text-white hover:bg-neutral-800 font-mono text-[10px] uppercase tracking-wider transition-colors cursor-pointer border border-black"
                      >
                        Settle
                      </button>
                    ) : (
                      <button
                        onClick={() => showToast('Receipt Downloaded', `Receipt for ${inv.invoiceNumber} generated.`, 'info')}
                        className="p-1.5 border border-black/20 hover:bg-black hover:text-white transition-colors cursor-pointer inline-flex items-center"
                        title="Print Receipt"
                      >
                        <Printer className="w-3.5 h-3.5" />
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Create Invoice Modal */}
      {showCreateModal && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 animate-in fade-in">
          <div className="bg-white border-2 border-black max-w-md w-full p-8 shadow-2xl space-y-6">
            <div className="flex items-center justify-between border-b border-black/15 pb-4">
              <div>
                <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-[#888] block">
                  BILLING STATEMENT
                </span>
                <h2 className="text-2xl font-serif italic text-black">Issue Ledger Invoice</h2>
              </div>
              <button
                onClick={() => setShowCreateModal(false)}
                className="p-1 hover:bg-black hover:text-white transition-colors border border-black/20 cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleCreateInvoiceSubmit} className="space-y-4 text-xs font-mono">
              <div>
                <label className="block text-[10px] uppercase font-bold text-[#555] mb-1">Select Patient</label>
                <select
                  value={newPatientId}
                  onChange={(e) => setNewPatientId(e.target.value)}
                  className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none"
                >
                  {patients.map((p) => (
                    <option key={p.id} value={p.id}>
                      {p.firstName} {p.lastName} (MRN #{p.mrn})
                    </option>
                  ))}
                </select>
              </div>

              <div>
                <label className="block text-[10px] uppercase font-bold text-[#555] mb-1">Service Description</label>
                <input
                  type="text"
                  value={newService}
                  onChange={(e) => setNewService(e.target.value)}
                  className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-sans text-black focus:outline-none"
                  placeholder="e.g. Comprehensive Consultation, Electrocardiogram"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-[10px] uppercase font-bold text-[#555] mb-1">Gross Fee ($)</label>
                  <input
                    type="number"
                    value={newAmount}
                    onChange={(e) => setNewAmount(e.target.value)}
                    className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono font-bold text-black focus:outline-none"
                  />
                </div>
                <div>
                  <label className="block text-[10px] uppercase font-bold text-[#555] mb-1">Payer Covered ($)</label>
                  <input
                    type="number"
                    value={newInsuranceCovered}
                    onChange={(e) => setNewInsuranceCovered(e.target.value)}
                    className="w-full p-3 border border-black/20 focus:border-black bg-[#FDFCFB] text-xs font-mono font-bold text-black focus:outline-none"
                  />
                </div>
              </div>

              <div className="flex items-center justify-end gap-3 pt-4 border-t border-black/10">
                <button
                  type="button"
                  onClick={() => setShowCreateModal(false)}
                  className="px-4 py-2.5 border border-black/20 hover:border-black uppercase tracking-wider text-xs font-bold transition-colors cursor-pointer"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-6 py-2.5 bg-black hover:bg-neutral-800 text-white font-bold uppercase tracking-[0.2em] text-xs border border-black cursor-pointer shadow-2xs"
                >
                  Post Statement
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};
