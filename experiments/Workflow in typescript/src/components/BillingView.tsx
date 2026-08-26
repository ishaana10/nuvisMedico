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
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
            Fiscal Claims & Billing
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Reconciliation of patient statements, third-party payer claims, and overdue balances.
          </p>
        </div>

        <button
          onClick={() => setShowCreateModal(true)}
          className="px-5 py-2.5 bg-[#0f2d71] hover:bg-[#0c245a] text-white text-xs font-semibold rounded-xl flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-sm"
        >
          <Plus className="w-4 h-4" />
          <span>Issue Invoice</span>
        </button>
      </div>

      {/* Overview Metric Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div className="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs">
          <span className="text-xs font-medium text-slate-500 block">
            Pending Receivables
          </span>
          <p className="text-2xl font-bold text-slate-900 mt-1">${totalPending.toLocaleString()}</p>
          <span className="text-[11px] font-semibold text-amber-600 mt-1 block">Active claims awaiting remittance</span>
        </div>

        <div className="bg-white border border-red-200 rounded-2xl p-5 shadow-2xs">
          <span className="text-xs font-medium text-slate-500 block">
            Delinquent / Overdue
          </span>
          <p className="text-2xl font-bold text-red-600 mt-1">{overdueCount} Accounts</p>
          <span className="text-[11px] font-semibold text-red-600 mt-1 block">Requires collection action</span>
        </div>

        <div className="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs">
          <span className="text-xs font-medium text-slate-500 block">
            Monthly Collection Rate
          </span>
          <p className="text-2xl font-bold text-slate-900 mt-1">96.4%</p>
          <span className="text-[11px] font-semibold text-emerald-600 mt-1 block">+2.1% from previous month</span>
        </div>
      </div>

      {/* Invoices Table Card */}
      <div className="bg-white border border-slate-200/80 rounded-2xl shadow-2xs overflow-hidden">
        {/* Filters */}
        <div className="p-4 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
          <div className="relative flex-1 w-full max-w-md">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" />
            <input
              type="text"
              placeholder="Search invoice #, patient name, or MRN..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl focus:border-blue-600 text-xs text-slate-900 placeholder-slate-400 bg-white focus:outline-none transition-all"
            />
          </div>

          <div className="flex items-center gap-1.5 bg-slate-200/60 p-1 rounded-xl">
            {['All', 'Overdue', 'Pending', 'Paid'].map((st) => (
              <button
                key={st}
                onClick={() => setStatusFilter(st)}
                className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-all cursor-pointer ${
                  statusFilter === st
                    ? 'bg-white text-blue-700 shadow-xs font-bold'
                    : 'text-slate-600 hover:text-slate-900'
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
              <tr className="border-b border-slate-200 text-[11px] font-semibold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                <th className="py-3.5 px-6">Invoice #</th>
                <th className="py-3.5 px-6">Patient</th>
                <th className="py-3.5 px-6">Service Date</th>
                <th className="py-3.5 px-6">Line Items</th>
                <th className="py-3.5 px-6 text-right">Gross Total</th>
                <th className="py-3.5 px-6 text-right">Patient Portion</th>
                <th className="py-3.5 px-6 text-center">Status</th>
                <th className="py-3.5 px-6 text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-xs font-medium">
              {filteredInvoices.map((inv) => (
                <tr key={inv.id} className="hover:bg-slate-50/80 transition-colors">
                  <td className="py-4 px-6 font-bold text-slate-900">{inv.invoiceNumber}</td>
                  <td className="py-4 px-6">
                    <p className="font-bold text-slate-900">{inv.patientName}</p>
                    <p className="text-[10px] text-slate-400">MRN #{inv.patientMrn}</p>
                  </td>
                  <td className="py-4 px-6 text-slate-600 whitespace-nowrap">{inv.serviceDate}</td>
                  <td className="py-4 px-6 text-slate-600 max-w-xs truncate">{inv.services.join(', ')}</td>
                  <td className="py-4 px-6 text-right font-bold text-slate-900">${inv.amount}</td>
                  <td className="py-4 px-6 text-right font-bold text-slate-900">${inv.patientOwed}</td>
                  <td className="py-4 px-6 text-center">
                    <span
                      className={`inline-block px-2.5 py-1 rounded-full text-[11px] font-semibold ${
                        inv.status === 'Paid'
                          ? 'bg-emerald-100 text-emerald-800'
                          : inv.status === 'Overdue'
                          ? 'bg-red-100 text-red-700'
                          : 'bg-amber-100 text-amber-800'
                      }`}
                    >
                      • {inv.status}
                    </span>
                  </td>
                  <td className="py-4 px-6 text-right">
                    {inv.status !== 'Paid' ? (
                      <button
                        onClick={() => markInvoicePaid(inv.id)}
                        className="px-3 py-1 bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-semibold rounded-lg transition-colors cursor-pointer shadow-xs"
                      >
                        Settle
                      </button>
                    ) : (
                      <button
                        onClick={() => showToast('Receipt Downloaded', `Receipt for ${inv.invoiceNumber} generated.`, 'info')}
                        className="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer inline-flex items-center"
                        title="Print Receipt"
                      >
                        <Printer className="w-4 h-4" />
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
