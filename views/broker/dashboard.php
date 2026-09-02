<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px] text-on-tertiary-container">badge</span>
                Broker Portal & Commission Ledger
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">View assigned client properties, conduct assigned walkthrough visits, and track earned deal commissions.</p>
        </div>
    </div>

    <!-- Assigned Properties Grid -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">domain</span>
            Assigned Client Listings
        </h2>
        <?php if (empty($assignments)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No active property assignments currently assigned by homeowners.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($assignments as $as): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm">
                        <span class="text-xs uppercase font-semibold text-on-tertiary-container">Owner: <?= htmlspecialchars($as['owner_name']) ?></span>
                        <h3 class="font-bold text-on-surface text-lg mt-1"><?= htmlspecialchars($as['title']) ?></h3>
                        <p class="text-xs text-on-surface-variant mb-3"><?= htmlspecialchars($as['city']) ?> • $<?= number_format($as['price_per_month'], 2) ?>/mo</p>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-tertiary-container text-on-tertiary-container uppercase"><?= htmlspecialchars($as['availability_status']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Assigned Property Walkthroughs Table -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
            Assigned Property Walkthrough Visits
        </h2>
        <?php if (empty($visits)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No property walkthrough visits assigned to you yet.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">Visit ID</th>
                            <th class="p-4 font-semibold">Property</th>
                            <th class="p-4 font-semibold">Tenant</th>
                            <th class="p-4 font-semibold">Scheduled Date & Time</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($visits as $v): ?>
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="p-4 font-mono text-xs text-on-surface">#<?= $v['visit_id'] ?></td>
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($v['property_title']) ?></td>
                                <td class="p-4 text-on-surface">
                                    <div class="font-semibold"><?= htmlspecialchars($v['tenant_name']) ?></div>
                                    <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($v['tenant_phone'] ?? '') ?></div>
                                </td>
                                <td class="p-4 text-on-surface-variant text-xs font-semibold"><?= htmlspecialchars($v['scheduled_at']) ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-secondary-container text-on-secondary-container">
                                        <?= htmlspecialchars($v['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($v['status'] === 'scheduled'): ?>
                                        <form action="/broker/visits/status" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="visit_id" value="<?= $v['visit_id'] ?>">
                                            <button type="submit" name="status" value="completed" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-tertiary-fixed transition-colors">Mark Completed</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-outline italic">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Commissions Ledger -->
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">payments</span>
            Commission Records & Earnings
        </h2>
        <?php if (empty($commissions)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No commission records earned yet.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">Agreement ID</th>
                            <th class="p-4 font-semibold">Property</th>
                            <th class="p-4 font-semibold">Commission Amount</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Paid Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($commissions as $c): ?>
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="p-4 font-mono text-xs text-on-surface">#AGR-<?= $c['agreement_id'] ?></td>
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($c['property_title']) ?></td>
                                <td class="p-4 font-bold text-on-surface">$<?= number_format($c['amount'], 2) ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-tertiary-container text-on-tertiary-container">
                                        <?= htmlspecialchars($c['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-on-surface-variant"><?= htmlspecialchars($c['paid_at'] ?? 'Pending') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
