<?php
$pageTitle = "Pharmacy & Supplies Inventory - ClinicFlow";
$activePage = "inventory";
include __DIR__ . '/includes/header.php';

// Fetch inventory
$items = $pdo->query("SELECT * FROM inventory ORDER BY name ASC")->fetchAll();
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">Pharmacy & Supplies Inventory</h1>
        <p class="text-xs text-outline font-medium">Track stock levels, reorder thresholds, and restock medical supplies</p>
    </div>
</div>

<!-- Inventory Grid Table -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-outline-variant/30 text-outline uppercase text-[10px] font-bold tracking-wider">
                    <th class="py-2.5 px-3">Item Name & SKU</th>
                    <th class="py-2.5 px-3">Category</th>
                    <th class="py-2.5 px-3">Stock Level</th>
                    <th class="py-2.5 px-3">Min Threshold</th>
                    <th class="py-2.5 px-3">Status</th>
                    <th class="py-2.5 px-3">Last Restocked</th>
                    <th class="py-2.5 px-3 text-right">Quick Restock</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
                <?php foreach ($items as $item): ?>
                    <tr class="hover:bg-surface-container-low transition">
                        <td class="py-3.5 px-3 font-bold text-on-surface">
                            <?= htmlspecialchars($item['name']) ?>
                            <span class="block text-[10px] font-mono text-outline font-normal"><?= htmlspecialchars($item['sku']) ?></span>
                        </td>
                        <td class="py-3.5 px-3 font-medium text-on-surface-variant">
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px]">
                                <?= htmlspecialchars($item['category']) ?>
                            </span>
                        </td>
                        <td class="py-3.5 px-3 font-mono font-bold text-base text-on-surface">
                            <?= htmlspecialchars($item['current_stock']) ?> <span class="text-xs font-normal text-outline"><?= htmlspecialchars($item['unit']) ?></span>
                        </td>
                        <td class="py-3.5 px-3 font-mono text-outline"><?= htmlspecialchars($item['min_threshold']) ?></td>
                        <td class="py-3.5 px-3">
                            <?php if ($item['status'] === 'Low Stock'): ?>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 text-[10px] font-bold">
                                    <span class="material-symbols-outlined text-xs">warning</span>
                                    Low Stock
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">
                                    In Stock
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3.5 px-3 text-outline font-medium"><?= htmlspecialchars($item['last_restocked']) ?></td>
                        <td class="py-3.5 px-3 text-right">
                            <form action="actions/inventory_restock.php" method="POST" class="inline-flex items-center gap-1.5 justify-end">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <input type="number" name="amount" value="50" min="1" class="w-16 bg-surface-container-low px-2 py-1 rounded-lg border border-outline-variant/40 text-xs font-mono font-bold text-center">
                                <button type="submit" class="px-2.5 py-1 bg-primary text-white rounded-lg text-[11px] font-semibold hover:bg-primary/90 transition shadow-xs">
                                    + Add Stock
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
