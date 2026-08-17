<?php
session_start();
if (empty($_SESSION['authenticated'])) {
    header('Location: login.php');
    exit;
}

$invoiceId = $_GET['id'] ?? 'inv-1';

require_once __DIR__ . '/config/database.php';
$pdo = getDB();

// Fetch Clinic Settings
$settingsRows = $pdo->query("SELECT * FROM clinic_settings")->fetchAll();
$settings = [];
foreach ($settingsRows as $r) {
    $settings[$r['setting_key']] = $r['setting_value'];
}

$clinicName = $settings['clinic_name'] ?? 'ClinicFlow Medical Center';
$clinicSubtitle = $settings['clinic_subtitle'] ?? 'Integrated Primary & Specialist Healthcare';
$clinicAddress = $settings['clinic_address'] ?? '100 Healthcare Way, Suite 400, Springfield, OR 97477';
$clinicPhone = $settings['clinic_phone'] ?? '(555) 019-2831';
$clinicEmail = $settings['clinic_email'] ?? 'contact@clinicflow.com';

$invoiceHeaderTitle = $settings['invoice_header_title'] ?? 'MEDICAL SERVICES INVOICE';
$invoiceTaxId = $settings['invoice_tax_id'] ?? '93-1029384';
$invoicePaymentTerms = $settings['invoice_payment_terms'] ?? 'Net 30 Days. Please remit payment promptly.';
$invoiceFooterNote = $settings['invoice_footer_note'] ?? 'Thank you for choosing ClinicFlow Medical Center for your care.';

// Fetch Invoice
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? OR invoice_number = ?");
$stmt->execute([$invoiceId, $invoiceId]);
$invoice = $stmt->fetch();

if (!$invoice) {
    // Fallback if integer ID or missing ID is passed
    $stmt = $pdo->query("SELECT * FROM invoices ORDER BY created_at DESC LIMIT 1");
    $invoice = $stmt->fetch();
}

if (!$invoice) {
    die("Invoice file not found.");
}

$services = json_decode($invoice['services'] ?? '[]', true) ?: ['Medical Evaluation and Consultation'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invoice['invoice_number']) ?> - <?= htmlspecialchars($clinicName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
        }
    </style>
</head>
<body class="bg-slate-100 p-8 min-h-screen text-slate-800 font-sans">

<div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
    <!-- Action buttons -->
    <div class="no-print mb-6 flex justify-between items-center bg-slate-50 p-4 rounded-xl border border-slate-200">
        <a href="billing.php" class="text-xs font-semibold text-slate-600 hover:text-slate-900">&larr; Back to Invoices</a>
        <div class="flex gap-2">
            <a href="admin.php" class="px-3 py-2 bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-300 transition">Customize Template</a>
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">Print Official Invoice</button>
        </div>
    </div>

    <!-- Clinic Header -->
    <div class="border-b-2 border-blue-900 pb-4 mb-6 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-blue-900 uppercase tracking-tight"><?= htmlspecialchars($clinicName) ?></h1>
            <p class="text-xs text-blue-700 font-medium"><?= htmlspecialchars($clinicSubtitle) ?></p>
            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($clinicAddress) ?> • Phone: <?= htmlspecialchars($clinicPhone) ?></p>
            <p class="text-xs text-slate-500">Tax ID / EIN: <?= htmlspecialchars($invoiceTaxId) ?> • Email: <?= htmlspecialchars($clinicEmail) ?></p>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-900 block"><?= htmlspecialchars($invoiceHeaderTitle) ?></span>
            <p class="text-sm font-mono font-bold text-slate-900 mt-1"><?= htmlspecialchars($invoice['invoice_number']) ?></p>
        </div>
    </div>

    <!-- Bill To & Dates -->
    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 mb-6 text-xs grid grid-cols-2 gap-4">
        <div>
            <p class="text-slate-500 font-semibold uppercase text-[10px]">Billed To</p>
            <p class="font-bold text-sm text-slate-900"><?= htmlspecialchars($invoice['patient_name']) ?></p>
            <p class="text-slate-500 mt-1">MRN: <span class="font-mono font-bold text-slate-800"><?= htmlspecialchars($invoice['patient_mrn']) ?></span></p>
        </div>
        <div class="text-right">
            <p class="text-slate-500 font-semibold uppercase text-[10px]">Invoice Details</p>
            <p class="text-slate-700 mt-1"><strong>Service Date:</strong> <?= htmlspecialchars($invoice['service_date']) ?></p>
            <p class="text-slate-700"><strong>Due Date:</strong> <?= htmlspecialchars($invoice['due_date']) ?></p>
            <p class="mt-1">
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold <?= $invoice['status'] === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                    Status: <?= htmlspecialchars($invoice['status']) ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="mb-6 overflow-hidden border border-slate-200 rounded-xl">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px]">
                <tr>
                    <th class="py-3 px-4">Service Description</th>
                    <th class="py-3 px-4 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td class="py-3 px-4 font-semibold text-slate-800"><?= htmlspecialchars($service) ?></td>
                        <td class="py-3 px-4 text-right font-mono font-medium">$<?= number_format($invoice['amount'] / count($services), 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Financial Calculation Breakdown -->
    <div class="flex justify-end mb-8 text-xs">
        <div class="w-64 space-y-2 border-t border-slate-200 pt-3">
            <div class="flex justify-between text-slate-600">
                <span>Total Service Billed:</span>
                <span class="font-mono font-bold">$<?= number_format($invoice['amount'], 2) ?></span>
            </div>
            <div class="flex justify-between text-emerald-700 font-medium">
                <span>Insurance Coverage:</span>
                <span class="font-mono">-$<?= number_format($invoice['insurance_covered'], 2) ?></span>
            </div>
            <div class="flex justify-between text-slate-900 font-bold text-sm pt-2 border-t border-slate-300">
                <span>Patient Balance Due:</span>
                <span class="font-mono text-blue-900">$<?= number_format($invoice['patient_owed'], 2) ?></span>
            </div>
        </div>
    </div>

    <!-- Payment Terms & Footer Note -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
        <p><strong>Payment Terms:</strong> <?= htmlspecialchars($invoicePaymentTerms) ?></p>
        <p class="italic text-slate-500"><?= htmlspecialchars($invoiceFooterNote) ?></p>
    </div>
</div>

</body>
</html>
