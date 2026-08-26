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
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
          <h1 className="text-2xl font-bold text-slate-900 tracking-tight">
            Inventory & Dispensary
          </h1>
          <p className="text-xs text-slate-500 mt-0.5">
            Stock level management, pharmaceutical replenishment thresholds, and automated reorders.
          </p>
        </div>

        <button
          onClick={() => showToast('Procurement Request Sent', 'Automated purchase orders transmitted to supplier.')}
          className="px-5 py-2.5 bg-[#0f2d71] hover:bg-[#0c245a] text-white text-xs font-semibold rounded-xl flex items-center gap-2 transition-all self-start sm:self-auto cursor-pointer shadow-sm"
        >
          <RotateCw className="w-4 h-4" />
          <span>Restock Depleted Items</span>
        </button>
      </div>

      {/* Low Stock Alerts Banner */}
      {lowStockItems.length > 0 && (
        <div className="bg-red-50 border border-red-200 rounded-2xl p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
          <div className="flex items-center gap-4">
            <div className="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
              <AlertTriangle className="w-5 h-5 text-red-600" />
            </div>
            <div>
              <h3 className="text-sm font-bold text-red-900">
                {lowStockItems.length} Items Below Minimum Par Level
              </h3>
              <p className="text-xs text-red-700 mt-0.5">
                {lowStockItems.map((i) => `${i.name} (${i.quantity} left)`).join(' • ')}
              </p>
            </div>
          </div>
        </div>
      )}

      {/* Filter and Table */}
      <div className="bg-white border border-slate-200/80 rounded-2xl shadow-2xs overflow-hidden">
        <div className="p-4 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50">
          <div className="relative flex-1 w-full max-w-md">
            <Search className="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" />
            <input
              type="text"
              placeholder="Search items, SKU, or bin location..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl focus:border-blue-600 text-xs text-slate-900 placeholder-slate-400 bg-white focus:outline-none transition-all"
            />
          </div>

          <div className="flex items-center gap-1.5 bg-slate-200/60 p-1 rounded-xl">
            {['All', 'Pharmaceuticals', 'Diagnostics', 'PPE', 'Consumables'].map((cat) => (
              <button
                key={cat}
                onClick={() => setCategoryFilter(cat)}
                className={`px-3 py-1.5 text-xs font-semibold rounded-lg transition-all cursor-pointer ${
                  categoryFilter === cat
                    ? 'bg-white text-blue-700 shadow-xs font-bold'
                    : 'text-slate-600 hover:text-slate-900'
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
              <tr className="border-b border-slate-200 text-[11px] font-semibold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                <th className="py-3.5 px-6">SKU Code</th>
                <th className="py-3.5 px-6">Item Name</th>
                <th className="py-3.5 px-6">Category</th>
                <th className="py-3.5 px-6 text-center">In Stock</th>
                <th className="py-3.5 px-6 text-center">Min Threshold</th>
                <th className="py-3.5 px-6">Location</th>
                <th className="py-3.5 px-6 text-center">Status</th>
                <th className="py-3.5 px-6 text-right">Quick Restock</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100 text-xs font-medium">
              {filteredInventory.map((item) => {
                const isLow = item.quantity <= item.minThreshold;
                return (
                  <tr key={item.id} className="hover:bg-slate-50/80 transition-colors">
                    <td className="py-4 px-6 font-mono text-slate-600">{item.sku}</td>
                    <td className="py-4 px-6 font-bold text-slate-900">{item.name}</td>
                    <td className="py-4 px-6 text-slate-600">{item.category}</td>
                    <td className="py-4 px-6 text-center font-bold text-slate-900">
                      {item.quantity} {item.unit}
                    </td>
                    <td className="py-4 px-6 text-center text-slate-400">
                      {item.minThreshold} {item.unit}
                    </td>
                    <td className="py-4 px-6 text-slate-600">{item.location}</td>
                    <td className="py-4 px-6 text-center">
                      <span
                        className={`inline-block px-2.5 py-1 rounded-full text-[11px] font-semibold ${
                          isLow
                            ? 'bg-red-100 text-red-700'
                            : 'bg-emerald-100 text-emerald-800'
                        }`}
                      >
                        • {isLow ? 'Low Stock' : 'Optimal'}
                      </span>
                    </td>
                    <td className="py-4 px-6 text-right">
                      <button
                        onClick={() => restockItem(item.id, 20)}
                        className="px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold rounded-lg transition-colors cursor-pointer"
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
