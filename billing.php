<?php
$pageTitle = "Billing & Financial Invoices - ClinicFlow";
$activePage = "billing";
include __DIR__ . '/includes/header.php';

// Fetch invoices
$invoices = $pdo->query("SELECT * FROM invoices ORDER BY due_date ASC")->fetchAll();

$totalPending = array_reduce($invoices, fn($acc, $i) => $i['status'] === 'Pending' ? $acc + $i['patient_owed'] : $acc, 0);
$totalOverdue = array_reduce($invoices, fn($acc, $i) => $i['status'] === 'Overdue' ? $acc + $i['patient_owed'] : $acc, 0);
$totalCollected = array_reduce($invoices, fn($acc, $i) => $i['status'] === 'Paid' ? $acc + $i['amount'] : $acc, 0);
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-on-surface">Billing & Invoices</h1>
        <p class="text-xs text-outline font-medium">Manage patient invoices, insurance claim balances, and payment receipts</p>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Collected Payments</p>
            <h3 class="text-2xl font-bold text-emerald-600 mt-1">$<?= number_format($totalCollected, 2) ?></h3>
            <p class="text-[11px] text-outline mt-1">Paid in full</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">check_circle</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Pending Balances</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">$<?= number_format($totalPending, 2) ?></h3>
            <p class="text-[11px] text-outline mt-1">Awaiting patient payment</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">pending</span>
        </div>
    </div>

    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/30 flex items-center justify-between shadow-xs">
        <div>
            <p class="text-[11px] font-bold text-outline uppercase tracking-wider">Overdue Accounts</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">$<?= number_format($totalOverdue, 2) ?></h3>
            <p class="text-[11px] text-outline mt-1">Past due date</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
            <span class="material-symbols-outlined text-2xl">error</span>
        </div>
    </div>
</div>

<!-- Invoices Table -->
<div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/30 p-5 shadow-xs">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b border-outline-variant/30 text-outline uppercase text-[10px] font-bold tracking-wider">
                    <th class="py-2.5 px-3">Invoice #</th>
                    <th class="py-2.5 px-3">Patient Name</th>
                    <th class="py-2.5 px-3">Service Date</th>
                    <th class="py-2.5 px-3">Total Amount</th>
                    <th class="py-2.5 px-3">Insurance Paid</th>
                    <th class="py-2.5 px-3">Patient Due</th>
                    <th class="py-2.5 px-3">Status</th>
                    <th class="py-2.5 px-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/20">
                <?php foreach ($invoices as $inv): ?>
                    <tr class="hover:bg-surface-container-low transition">
                        <td class="py-3 px-3 font-mono font-bold text-primary"><?= htmlspecialchars($inv['invoice_number']) ?></td>
                        <td class="py-3 px-3 font-bold text-on-surface">
                            <?= htmlspecialchars($inv['patient_name']) ?>
                            <span class="block text-[10px] font-mono text-outline"><?= htmlspecialchars($inv['patient_mrn']) ?></span>
                        </td>
                        <td class="py-3 px-3 text-outline font-medium"><?= htmlspecialchars($inv['service_date']) ?></td>
                        <td class="py-3 px-3 font-bold text-on-surface">$<?= number_format($inv['amount'], 2) ?></td>
                        <td class="py-3 px-3 font-medium text-emerald-600">$<?= number_format($inv['insurance_covered'], 2) ?></td>
                        <td class="py-3 px-3 font-bold text-slate-800">$<?= number_format($inv['patient_owed'], 2) ?></td>
                        <td class="py-3 px-3">
                            <?php
                            $statusStyle = match($inv['status']) {
                                'Paid' => 'bg-emerald-100 text-emerald-800',
                                'Overdue' => 'bg-red-100 text-red-800',
                                default => 'bg-amber-100 text-amber-800'
                            };
                            ?>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $statusStyle ?>">
                                <?= htmlspecialchars($inv['status']) ?>
                            </span>
                        </td>
                        <td class="py-3 px-3 text-right space-x-1">
                            <a href="print_invoice.php?id=<?= htmlspecialchars($inv['id']) ?>" target="_blank" class="inline-block px-2.5 py-1 bg-surface-container-high text-primary rounded-lg text-[11px] font-semibold hover:bg-surface-container-highest transition">
                                Invoice
                            </a>
                            <?php if ($inv['status'] !== 'Paid'): ?>
                                <form action="actions/billing_pay.php" method="POST" class="inline">
                                    <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($inv['id']) ?>">
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-[11px] font-semibold hover:bg-emerald-700 transition">
                                        Mark Paid
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="print_receipt.php?id=<?= htmlspecialchars($inv['id']) ?>" target="_blank" class="inline-block px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-[11px] font-semibold hover:bg-emerald-200 transition">
                                    Receipt
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
