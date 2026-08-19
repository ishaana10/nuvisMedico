import React, { useState } from 'react';
import { Package, AlertTriangle, Plus, Search, CheckCircle2, RotateCw } from 'lucide-react';
import { useClinic } from '../context/ClinicContext';

export const InventoryView: React.FC = () => {
  const { inventory, restockItem, showToast } = useClinic();
  const [search, setSearch] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('All');

  const filteredInventory = inventory.filter((item) => {
    const matchesSearch =
      item.name.toLowerCase().includes(search.toLowerCase()) ||
      item.sku.toLowerCase().includes(search.toLowerCase()) ||
      item.location.toLowerCase().includes(search.toLowerCase());
    const matchesCat = categoryFilter === 'All' || item.category === categoryFilter;
    return matchesSearch && matchesCat;
  });

  const lowStockItems = inventory.filter((item) => item.quantity <= item.minThreshold);

  return (
    <div id="inventory-view" className="p-8 lg:p-10 max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-200">
      {/* Header */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-black/15 pb-6">
        <div>
          <div className="flex items-center gap-2 mb-1">
            <span className="w-2 h-2 bg-black"></span>
            <span className="text-[10px] font-mono font-bold tracking-[0.3em] uppercase text-[#777]">
              SUPPLY CHAIN & DISPENSARY
            </span>
          </div>
          <h1 className="text-3xl font-serif italic text-[#1C1C1C] tracking-tight">
            Apothecary & Clinical Dispensary
          </h1>
          <p className="text-xs text-[#666] mt-1 font-sans">
            Inventory stock levels, pharmaceutical replenishment thresholds, and automated vendor procurement.
          </p>
        </div>

        <button
          onClick={() => showToast('Procurement Request Sent', 'Automated purchase orders transmitted to Cardinal Health.')}
          className="px-6 py-3 bg-black hover:bg-neutral-800 text-white text-xs font-mono font-bold uppercase tracking-[0.2em] border border-black flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-xs"
        >
          <RotateCw className="w-3.5 h-3.5" />
          <span>Restock Depleted Items</span>
        </button>
      </div>

      {/* Low Stock Alerts Banner */}
      {lowStockItems.length > 0 && (
        <div className="bg-rose-50 border border-rose-500 p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="w-10 h-10 border border-rose-600 bg-rose-100 text-rose-900 flex items-center justify-center shrink-0">
              <AlertTriangle className="w-5 h-5 text-rose-700" />
            </div>
            <div>
              <span className="text-[9px] font-mono font-bold uppercase tracking-[0.25em] text-rose-800 block">
                DEPLETION WARNING
              </span>
              <h3 className="text-sm font-serif italic text-rose-950 font-bold">
                {lowStockItems.length} Pharmaceutical Items Below Safety Threshold
              </h3>
              <p className="text-xs font-mono text-rose-800 mt-0.5">
                {lowStockItems.map((i) => `${i.name} [${i.quantity} left]`).join(' • ')}
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Filter and Table */}
      <div className="bg-white border border-black/20 shadow-2xs overflow-hidden">
        <div className="p-4 border-b border-black/15 flex flex-col sm:flex-row items-center justify-between gap-4 bg-[#FDFCFB]">
          <div className="relative flex-1 w-full max-w-md">
            <Search className="w-3.5 h-3.5 text-black absolute left-3 top-3" />
            <input
              type="text"
              placeholder="Search formulary, SKU, or storage shelf..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-9 pr-3 py-2 border border-black/20 focus:border-black text-xs font-mono text-black placeholder-[#888] bg-white focus:outline-none transition-colors"
            />
          </div>

          <div className="flex items-center gap-2">
            {['All', 'Pharmaceuticals', 'Diagnostics', 'PPE', 'Consumables'].map((cat) => (
              <button
                key={cat}
                onClick={() => setCategoryFilter(cat)}
                className={`px-3 py-1.5 text-[10px] font-mono uppercase tracking-wider transition-all cursor-pointer border ${
                  categoryFilter === cat
                    ? 'bg-black text-white border-black font-bold'
                    : 'bg-white text-[#555] border-black/15 hover:border-black'
                }`}
              >
                {cat}
              </button>
            ))}
          </div>
        </div>

        {/* Table */}
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse text-xs">
            <thead>
              <tr className="border-b border-black/15 text-[10px] font-mono font-bold text-[#888] uppercase tracking-[0.2em] bg-[#F5F5F0]">
                <th className="py-3.5 px-6">SKU Code</th>
                <th className="py-3.5 px-6">Formulary Compound</th>
                <th className="py-3.5 px-6">Classification</th>
                <th className="py-3.5 px-6 text-center">In Stock</th>
                <th className="py-3.5 px-6 text-center">Par Level</th>
                <th className="py-3.5 px-6">Dispensary Bin</th>
                <th className="py-3.5 px-6 text-center">Status</th>
                <th className="py-3.5 px-6 text-right">Quick Restock</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-black/10 text-xs">
              {filteredInventory.map((item) => {
                const isLow = item.quantity <= item.minThreshold;
                return (
                  <tr key={item.id} className="hover:bg-[#FDFCFB] transition-colors">
                    <td className="py-4 px-6 font-mono text-black">{item.sku}</td>
                    <td className="py-4 px-6 font-serif italic text-sm text-black font-semibold">{item.name}</td>
                    <td className="py-4 px-6 font-mono text-[#666]">{item.category}</td>
                    <td className="py-4 px-6 text-center font-mono font-bold text-black">
                      {item.quantity} {item.unit}
                    </td>
                    <td className="py-4 px-6 text-center font-mono text-[#888]">
                      {item.minThreshold} {item.unit}
                    </td>
                    <td className="py-4 px-6 font-mono text-[#555]">{item.location}</td>
                    <td className="py-4 px-6 text-center">
                      <span
                        className={`inline-block px-2.5 py-0.5 text-[9px] font-mono uppercase tracking-wider border ${
                          isLow
                            ? 'bg-rose-50 border-rose-500 text-rose-900 font-bold'
                            : 'bg-[#F5F5F0] border-black/20 text-black'
                        }`}
                      >
                        {isLow ? 'CRITICAL LOW' : 'OPTIMAL'}
                      </span>
                    </td>
                    <td className="py-4 px-6 text-right">
                      <button
                        onClick={() => restockItem(item.id, 20)}
                        className="px-3 py-1 bg-white hover:bg-black hover:text-white border border-black font-mono text-[10px] uppercase tracking-wider transition-colors cursor-pointer"
                      >
                        + 20 {item.unit}
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};
