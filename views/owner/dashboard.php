<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface">Property Owner Dashboard</h1>
            <p class="text-sm text-on-surface-variant">Manage your property portfolio, rental applications, and reviews.</p>
        </div>
        <a href="/properties/create" class="bg-on-tertiary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">add</span> Add New Property
        </a>
    </div>

    <!-- Properties Portfolio Grid -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4">My Property Portfolio</h2>
        <?php if (empty($properties)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl text-center">
                <p class="text-sm text-on-surface-variant mb-4">No properties listed yet.</p>
                <a href="/properties/create" class="inline-block bg-primary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold">List Your First Property</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($properties as $p): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-on-surface text-lg"><?= htmlspecialchars($p['title']) ?></h3>
                                <span class="text-sm font-bold text-on-surface">$<?= number_format($p['price_per_month'], 2) ?></span>
                            </div>
                            <p class="text-xs text-on-surface-variant mb-3"><?= htmlspecialchars($p['address_line']) ?>, <?= htmlspecialchars($p['city']) ?></p>
                            
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded <?= $p['is_approved'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-error-container text-on-error-container' ?>">
                                    <?= $p['is_approved'] ? 'Approved' : 'Pending Admin Approval' ?>
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-outline-variant/60 pt-4">
                            <!-- Toggle Availability Status Form -->
                            <form action="/properties/toggle-status" method="POST" class="flex items-center gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase">Status:</label>
                                <select name="availability_status" onchange="this.form.submit()" class="text-xs py-1 px-2 border border-outline-variant rounded bg-surface-container-lowest text-on-surface font-semibold">
                                    <option value="available" <?= $p['availability_status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                    <option value="pending" <?= $p['availability_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="rented" <?= $p['availability_status'] === 'rented' ? 'selected' : '' ?>>Rented</option>
                                </select>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Rental Requests Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4">Incoming Rental Applications</h2>
        <?php if (empty($requests)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No rental applications currently pending.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">Property</th>
                            <th class="p-4 font-semibold">Applicant</th>
                            <th class="p-4 font-semibold">Requested Move-In</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($req['property_title']) ?></td>
                                <td class="p-4 text-on-surface">
                                    <div><?= htmlspecialchars($req['tenant_name']) ?></div>
                                    <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($req['tenant_email']) ?></div>
                                </td>
                                <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($req['requested_move_in'] ?? 'N/A') ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase 
                                        <?= $req['status'] === 'approved' ? 'bg-tertiary-container text-on-tertiary-container' : ($req['status'] === 'rejected' ? 'bg-error-container text-on-error-container' : 'bg-secondary-container text-on-secondary-container') ?>">
                                        <?= htmlspecialchars($req['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <form action="/rentals/decision" method="POST" class="inline-flex gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                            <button type="submit" name="decision" value="approved" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded font-semibold hover:bg-tertiary-fixed">Approve</button>
                                            <button type="submit" name="decision" value="rejected" class="bg-error-container text-on-error-container text-xs px-3 py-1.5 rounded font-semibold hover:bg-error/20">Reject</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-outline italic">Decision Recorded</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Reviews & Owner Reply Section -->
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4">Property Reviews & Replies</h2>
        <?php if (empty($reviews)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No reviews received yet.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($reviews as $rev): ?>
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-semibold text-on-surface"><?= htmlspecialchars($rev['tenant_name']) ?></span>
                                <span class="text-xs text-on-surface-variant block">Reviewed <?= htmlspecialchars($rev['property_title']) ?></span>
                            </div>
                            <span class="font-bold text-amber-500"><?= (int)$rev['rating'] ?> ★</span>
                        </div>
                        <p class="text-sm text-on-surface-variant mb-4"><?= htmlspecialchars($rev['feedback']) ?></p>

                        <?php if (!empty($rev['reply_text'])): ?>
                            <div class="p-3 bg-surface-container-low border-l-2 border-on-tertiary-container text-xs rounded-r-lg">
                                <span class="font-semibold text-on-surface block mb-1">Your Reply:</span>
                                <p class="text-on-surface-variant"><?= htmlspecialchars($rev['reply_text']) ?></p>
                            </div>
                        <?php else: ?>
                            <form action="/reviews/reply" method="POST" class="mt-3 flex gap-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="review_id" value="<?= $rev['review_id'] ?>">
                                <input type="text" name="reply_text" placeholder="Write a response..." required class="flex-1 text-xs px-3 py-1.5 border border-outline-variant rounded bg-surface-container-lowest"/>
                                <button type="submit" class="bg-primary-container text-on-primary text-xs px-4 py-1.5 rounded font-semibold hover:bg-on-primary-fixed">Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
