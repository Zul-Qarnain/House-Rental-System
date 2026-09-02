<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px] text-on-tertiary-container">home_work</span>
                Property Owner Dashboard
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">Manage your property portfolio, broker assignments, rental applications, and reviews.</p>
        </div>
        <a href="/properties/create" class="bg-on-tertiary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition flex items-center gap-1">
            <span class="material-symbols-outlined text-[18px]">add</span> Add New Property
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

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 rounded-xl bg-error-container text-on-error-container border border-error/30 text-sm font-semibold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined">error</span>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-on-error-container hover:opacity-75">&times;</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Properties Portfolio Grid -->
    <div class="mb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">domain</span>
                My Property Portfolio
            </h2>
            <!-- NEW FRONTEND FEATURE: Property Search Bar -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input type="text" id="propertySearch" placeholder="Search by title or city..." 
                       class="w-full pl-9 pr-4 py-2 text-sm border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container transition-shadow">
            </div>
        </div>

        <?php if (empty($properties)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl text-center">
                <p class="text-sm text-on-surface-variant mb-4">No properties listed yet.</p>
                <a href="/properties/create" class="inline-block bg-primary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold">List Your First Property</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="propertyGrid">
                <?php foreach ($properties as $p): ?>
                    <!-- Added 'property-card' class for JS filtering -->
                    <div class="property-card bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-on-surface text-lg"><?= htmlspecialchars($p['title']) ?></h3>
                                <span class="text-sm font-bold text-on-surface">$<?= number_format($p['price_per_month'], 2) ?>/mo</span>
                            </div>
                            <p class="text-xs text-on-surface-variant mb-3"><?= htmlspecialchars($p['address_line']) ?>, <?= htmlspecialchars($p['city']) ?></p>
                            
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full <?= $p['is_approved'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-error-container text-on-error-container' ?>">
                                    <?= $p['is_approved'] ? 'Approved' : 'Pending Approval' ?>
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-outline-variant/60 pt-4 space-y-3">
                            <!-- Toggle Availability Status Form -->
                            <form action="/properties/toggle-status" method="POST" class="flex items-center justify-between">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                <label class="text-xs font-semibold text-on-surface-variant uppercase">Status:</label>
                                <select name="availability_status" onchange="this.form.submit()" class="text-xs py-1 px-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-semibold">
                                    <option value="available" <?= $p['availability_status'] === 'available' ? 'selected' : '' ?>>Available</option>
                                    <option value="pending" <?= $p['availability_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="rented" <?= $p['availability_status'] === 'rented' ? 'selected' : '' ?>>Rented</option>
                                </select>
                            </form>

                            <!-- Homeowner Broker Management Form -->
                            <form action="/owner/properties/assign-broker" method="POST" class="flex flex-col gap-1">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                <div class="flex items-center justify-between text-xs">
                                    <label class="font-semibold text-on-surface-variant uppercase">Assigned Broker:</label>
                                    <?php if (!empty($p['active_broker'])): ?>
                                        <span class="font-bold text-on-tertiary-container"><?= htmlspecialchars($p['active_broker']['broker_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-outline italic">None</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex gap-1 items-center mt-1">
                                    <select name="broker_id" required class="flex-1 text-xs px-2 py-1.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface">
                                        <option value="">-- Select Broker --</option>
                                        <?php foreach ($brokers as $b): ?>
                                            <option value="<?= $b['user_id'] ?>" <?= (!empty($p['active_broker']) && $p['active_broker']['broker_id'] == $b['user_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($b['name']) ?> (#<?= $b['user_id'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="bg-primary-container text-on-primary text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-on-primary-fixed transition-colors">
                                        <?= !empty($p['active_broker']) ? 'Change' : 'Assign' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Hidden message for when search yields no results -->
            <div id="noPropertyResults" class="hidden mt-6 p-6 rounded-xl bg-surface-container-lowest border border-outline-variant text-center text-sm text-on-surface-variant">
                No properties match your search.
            </div>
        <?php endif; ?>
    </div>

    <!-- Rental Requests Desk -->
    <div class="mb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-3">
            <h2 class="text-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">inbox</span>
                Incoming Rental Applications
            </h2>
            <!-- NEW FRONTEND FEATURE: Application Search Bar -->
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[18px]">search</span>
                <input type="text" id="requestSearch" placeholder="Search by tenant or property..." 
                       class="w-full pl-9 pr-4 py-2 text-sm border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container transition-shadow">
            </div>
        </div>

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
                    <tbody class="divide-y divide-outline-variant/50" id="requestTableBody">
                        <?php foreach ($requests as $req): ?>
                            <!-- Added 'request-row' class for JS filtering -->
                            <tr class="request-row hover:bg-surface-container-low/50 transition-colors">
                                <td class="p-4 font-semibold text-on-surface"><?= htmlspecialchars($req['property_title']) ?></td>
                                <td class="p-4 text-on-surface">
                                    <div class="font-semibold"><?= htmlspecialchars($req['tenant_name']) ?></div>
                                    <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($req['tenant_email']) ?></div>
                                </td>
                                <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($req['requested_move_in'] ?? 'N/A') ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase 
                                        <?= $req['status'] === 'approved' ? 'bg-tertiary-container text-on-tertiary-container' : ($req['status'] === 'rejected' ? 'bg-error-container text-on-error-container' : 'bg-secondary-container text-on-secondary-container') ?>">
                                        <?= htmlspecialchars($req['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($req['status'] === 'pending'): ?>
                                        <form action="/rentals/decision" method="POST" class="inline-flex gap-2">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                                            <button type="submit" name="decision" value="approved" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-tertiary-fixed transition-colors">Approve</button>
                                            <button type="submit" name="decision" value="rejected" class="bg-error-container text-on-error-container text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-error/20 transition-colors">Reject</button>
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
            <!-- Hidden message for when search yields no results -->
            <div id="noRequestResults" class="hidden mt-4 p-6 rounded-xl bg-surface-container-lowest border border-outline-variant text-center text-sm text-on-surface-variant">
                No applications match your search.
            </div>
        <?php endif; ?>
    </div>

    <!-- Reviews & Owner Reply Section -->
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">rate_review</span>
            Property Reviews & Replies
        </h2>
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
                                <input type="text" name="reply_text" placeholder="Write a response..." required class="flex-1 text-xs px-3 py-1.5 border border-outline-variant rounded-lg bg-surface-container-lowest"/>
                                <button type="submit" class="bg-primary-container text-on-primary text-xs px-4 py-1.5 rounded-lg font-semibold hover:bg-on-primary-fixed transition-colors">Reply</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</main>

<!-- NEW FRONTEND FEATURE: Live Search JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Property Portfolio Search
        const propertySearch = document.getElementById('propertySearch');
        if (propertySearch) {
            propertySearch.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.property-card');
                let visibleCount = 0;

                cards.forEach(card => {
                    const text = card.innerText.toLowerCase();
                    if (text.includes(term)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Show/hide "No results" message
                const noResultsMsg = document.getElementById('noPropertyResults');
                if (noResultsMsg) {
                    noResultsMsg.classList.toggle('hidden', visibleCount > 0);
                }
            });
        }

        // 2. Rental Applications Search
        const requestSearch = document.getElementById('requestSearch');
        if (requestSearch) {
            requestSearch.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('.request-row');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.innerText.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Show/hide "No results" message
                const noResultsMsg = document.getElementById('noRequestResults');
                if (noResultsMsg) {
                    noResultsMsg.classList.toggle('hidden', visibleCount > 0);
                }
            });
        }
    });
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>