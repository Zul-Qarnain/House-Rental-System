<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>

<?php
// Calculate summary metrics safely in frontend memory without modifying backend queries
$totalAssigned = !empty($assignments) ? count($assignments) : 0;
$pendingVisits = !empty($visits) ? count(array_filter($visits, fn($v) => ($v['status'] ?? '') === 'scheduled')) : 0;
$totalCommission = !empty($commissions) ? array_sum(array_column($commissions, 'amount')) : 0.00;
?>

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-outline-variant/60 pb-6">
        <div>
            <h1 class="font-headline-xl text-2xl sm:text-3xl font-bold text-on-surface flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[32px] text-primary">badge</span>
                Broker Portal & Commission Ledger
            </h1>
            <p class="text-sm text-on-surface-variant mt-1.5">Manage assigned properties, conduct walkthroughs, and monitor earnings.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-tertiary-container text-on-tertiary-container">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                Active Broker Session
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards (Frontend Metric Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-10">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Assigned Listings</span>
                <span class="material-symbols-outlined text-primary bg-primary/10 p-2 rounded-lg text-[22px]">domain</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-bold text-on-surface"><?= $totalAssigned ?></span>
                <p class="text-xs text-on-surface-variant mt-1">Properties under your supervision</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Scheduled Visits</span>
                <span class="material-symbols-outlined text-secondary bg-secondary/10 p-2 rounded-lg text-[22px]">event_available</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-bold text-on-surface"><?= $pendingVisits ?></span>
                <p class="text-xs text-on-surface-variant mt-1">Upcoming tenant walkthroughs</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Total Earnings</span>
                <span class="material-symbols-outlined text-tertiary bg-tertiary/10 p-2 rounded-lg text-[22px]">payments</span>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-bold text-on-surface">$<?= number_format($totalCommission, 2) ?></span>
                <p class="text-xs text-on-surface-variant mt-1">Accumulated deal commissions</p>
            </div>
        </div>
    </div>

    <!-- Assigned Properties Grid -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px] text-primary">domain</span>
                Assigned Client Listings
            </h2>
            <span class="text-xs font-semibold text-on-surface-variant bg-surface-container-low px-2.5 py-1 rounded-md">
                <?= $totalAssigned ?> Total
            </span>
        </div>

        <?php if (empty($assignments)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl text-center shadow-sm">
                <span class="material-symbols-outlined text-outline text-[40px] mb-2 block">home_work</span>
                <p class="text-sm font-semibold text-on-surface">No assigned listings available</p>
                <p class="text-xs text-on-surface-variant mt-1">Properties assigned by homeowners will appear in this section.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($assignments as $as): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm hover:border-primary/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-[11px] uppercase font-bold tracking-wider text-primary truncate">
                                    Owner: <?= htmlspecialchars($as['owner_name']) ?>
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full uppercase bg-tertiary-container text-on-tertiary-container shrink-0">
                                    <?= htmlspecialchars($as['availability_status']) ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-on-surface text-lg leading-snug"><?= htmlspecialchars($as['title']) ?></h3>
                            <p class="text-xs text-on-surface-variant mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">location_on</span>
                                <?= htmlspecialchars($as['city']) ?>
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-outline-variant/60 flex items-center justify-between">
                            <span class="text-xs text-on-surface-variant">Monthly Rent</span>
                            <span class="text-base font-bold text-on-surface">$<?= number_format($as['price_per_month'], 2) ?><span class="text-xs font-normal text-on-surface-variant">/mo</span></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Assigned Property Walkthroughs Table -->
    <div class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px] text-secondary">calendar_month</span>
                Assigned Property Walkthrough Visits
            </h2>
        </div>

        <?php if (empty($visits)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl text-center shadow-sm">
                <span class="material-symbols-outlined text-outline text-[40px] mb-2 block">event_busy</span>
                <p class="text-sm font-semibold text-on-surface">No walkthrough visits scheduled</p>
                <p class="text-xs text-on-surface-variant mt-1">Tenant booking requests assigned to you will show up here.</p>
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant font-semibold">
                            <tr>
                                <th class="p-4">Visit ID</th>
                                <th class="p-4">Property</th>
                                <th class="p-4">Tenant Information</th>
                                <th class="p-4">Scheduled Date & Time</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/50">
                            <?php foreach ($visits as $v): ?>
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="p-4 font-mono text-xs font-semibold text-on-surface">#<?= $v['visit_id'] ?></td>
                                    <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($v['property_title']) ?></td>
                                    <td class="p-4 text-on-surface">
                                        <div class="font-semibold text-sm"><?= htmlspecialchars($v['tenant_name']) ?></div>
                                        <?php if (!empty($v['tenant_phone'])): ?>
                                            <div class="text-xs text-on-surface-variant flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-[14px]">call</span>
                                                <?= htmlspecialchars($v['tenant_phone']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-on-surface text-xs font-medium">
                                        <?= htmlspecialchars($v['scheduled_at']) ?>
                                    </td>
                                    <td class="p-4">
                                        <?php if ($v['status'] === 'completed'): ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                <?= htmlspecialchars($v['status']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-secondary-container text-on-secondary-container">
                                                <?= htmlspecialchars($v['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-right">
                                        <?php if ($v['status'] === 'scheduled'): ?>
                                            <form action="/broker/visits/status" method="POST" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <input type="hidden" name="visit_id" value="<?= $v['visit_id'] ?>">
                                                <button type="submit" name="status" value="completed" class="bg-primary text-on-primary text-xs px-3.5 py-1.5 rounded-lg font-semibold hover:opacity-90 transition-opacity shadow-sm cursor-pointer">
                                                    Mark Completed
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs text-outline italic inline-flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[15px]">done_all</span> Completed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Commissions Ledger -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[22px] text-tertiary">payments</span>
                Commission Records & Earnings
            </h2>
        </div>

        <?php if (empty($commissions)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl text-center shadow-sm">
                <span class="material-symbols-outlined text-outline text-[40px] mb-2 block">receipt_long</span>
                <p class="text-sm font-semibold text-on-surface">No commission records yet</p>
                <p class="text-xs text-on-surface-variant mt-1">Earnings will appear once rental deals are successfully closed.</p>
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant font-semibold">
                            <tr>
                                <th class="p-4">Agreement ID</th>
                                <th class="p-4">Property</th>
                                <th class="p-4">Commission Amount</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Settlement Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/50">
                            <?php foreach ($commissions as $c): ?>
                                <tr class="hover:bg-surface-container-low/40 transition-colors">
                                    <td class="p-4 font-mono text-xs font-semibold text-on-surface">#AGR-<?= $c['agreement_id'] ?></td>
                                    <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($c['property_title']) ?></td>
                                    <td class="p-4 font-bold text-on-surface text-base">$<?= number_format($c['amount'], 2) ?></td>
                                    <td class="p-4">
                                        <?php if (strtolower($c['status']) === 'paid'): ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                <?= htmlspecialchars($c['status']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                                <?= htmlspecialchars($c['status']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-xs text-on-surface-variant font-medium">
                                        <?= htmlspecialchars($c['paid_at'] ?? 'Pending Settlement') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>