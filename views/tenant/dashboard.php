<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface">Tenant Portal - My Home</h1>
            <p class="text-sm text-on-surface-variant">Manage your lease agreements, property visits, rental applications, and reviews.</p>
        </div>
        <a href="/" class="bg-primary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:bg-on-primary-fixed transition">
            Browse Listings
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 rounded-xl bg-tertiary-container text-on-tertiary-container border border-tertiary-fixed text-sm font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">check_circle</span>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-on-tertiary-container hover:opacity-75">&times;</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Active Agreements Section -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-on-tertiary-container">key</span> Active & Completed Rental Agreements
        </h2>
        <?php if (empty($agreements)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                You do not have any active or previous rental agreements.
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($agreements as $ag): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="bg-tertiary-container text-on-tertiary-container text-xs px-2.5 py-0.5 rounded-full font-semibold uppercase"><?= htmlspecialchars($ag['status']) ?></span>
                                    <h3 class="font-bold text-lg text-on-surface mt-1"><?= htmlspecialchars($ag['property_title']) ?></h3>
                                </div>
                                <span class="font-bold text-lg text-on-surface">$<?= number_format($ag['monthly_rent'], 2) ?>/mo</span>
                            </div>
                            <p class="text-xs text-on-surface-variant mb-4"><?= htmlspecialchars($ag['address_line']) ?>, <?= htmlspecialchars($ag['city']) ?></p>
                            <div class="text-xs text-on-surface-variant space-y-1 mb-4">
                                <p><strong>Lease Term:</strong> <?= htmlspecialchars($ag['start_date']) ?> to <?= htmlspecialchars($ag['end_date'] ?? 'N/A') ?></p>
                            </div>
                        </div>

                        <div class="border-t border-outline-variant/60 pt-4 mt-2">
                            <!-- Submit Review Form -->
                            <form action="/reviews/submit" method="POST" class="space-y-3">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="agreement_id" value="<?= $ag['agreement_id'] ?>">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-semibold text-on-surface uppercase">Leave a Review</label>
                                    <select name="rating" class="text-xs px-2 py-1 border border-outline-variant rounded bg-surface-container-lowest">
                                        <option value="5">5 Stars ★★★★★</option>
                                        <option value="4">4 Stars ★★★★☆</option>
                                        <option value="3">3 Stars ★★★☆☆</option>
                                        <option value="2">2 Stars ★★☆☆☆</option>
                                        <option value="1">1 Star ★☆☆☆☆</option>
                                    </select>
                                </div>
                                <textarea name="feedback" rows="2" placeholder="Write feedback about your stay..." class="w-full text-xs p-2 border border-outline-variant rounded bg-surface-container-lowest"></textarea>
                                <button type="submit" class="w-full bg-surface-container-high hover:bg-outline-variant text-on-surface text-xs py-1.5 rounded font-semibold transition">
                                    Submit Property Review
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Requested Property Walkthrough Visits -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">calendar_month</span>
            My Requested Walkthrough Visits
        </h2>
        <?php if (empty($visits)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No walkthrough visits requested yet. Browse properties to request a visit.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">Property</th>
                            <th class="p-4 font-semibold">Scheduled Date & Time</th>
                            <th class="p-4 font-semibold">Assigned Broker</th>
                            <th class="p-4 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($visits as $v): ?>
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($v['property_title']) ?></td>
                                <td class="p-4 text-on-surface-variant text-xs font-semibold"><?= htmlspecialchars($v['scheduled_at']) ?></td>
                                <td class="p-4 text-xs">
                                    <?php if (!empty($v['broker_name'])): ?>
                                        <span class="font-bold text-on-tertiary-container"><?= htmlspecialchars($v['broker_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-outline italic">Awaiting Owner Assignment</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase 
                                        <?= $v['status'] === 'completed' ? 'bg-tertiary-container text-on-tertiary-container' : ($v['status'] === 'cancelled' ? 'bg-error-container text-on-error-container' : 'bg-secondary-container text-on-secondary-container') ?>">
                                        <?= htmlspecialchars($v['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rental Requests History -->
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-secondary font-bold">assignment</span> Rental Request History
        </h2>
        <?php if (empty($requests)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No rental requests submitted yet.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">Property</th>
                            <th class="p-4 font-semibold">Requested Move-In</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Submitted On</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($req['property_title']) ?></td>
                                <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($req['requested_move_in'] ?? 'N/A') ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase 
                                        <?= $req['status'] === 'approved' ? 'bg-tertiary-container text-on-tertiary-container' : ($req['status'] === 'rejected' ? 'bg-error-container text-on-error-container' : 'bg-secondary-container text-on-secondary-container') ?>">
                                        <?= htmlspecialchars($req['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-xs text-on-surface-variant"><?= htmlspecialchars($req['requested_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
