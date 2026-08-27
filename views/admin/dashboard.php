<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px] text-on-tertiary-container">admin_panel_settings</span>
                Admin Control Center
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">Manage platform users, property verification, resolution desk & audit logs.</p>
        </div>
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

    <!-- User Management Table -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">group</span>
            User Accounts & Roles
        </h2>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                    <tr>
                        <th class="p-4 font-semibold">User ID</th>
                        <th class="p-4 font-semibold">Name & Email</th>
                        <th class="p-4 font-semibold">Role</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4 font-mono text-xs text-on-surface">#<?= $u['user_id'] ?></td>
                            <td class="p-4 text-on-surface">
                                <div class="font-semibold"><?= htmlspecialchars($u['name']) ?></div>
                                <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($u['email']) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold uppercase bg-surface-container-high text-on-surface border border-outline-variant">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase <?= $u['is_active'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-error-container text-on-error-container' ?>">
                                    <?= $u['is_active'] ? 'Active' : 'Deactivated' ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="/admin/users/status" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                    <input type="hidden" name="is_active" value="<?= $u['is_active'] ? 0 : 1 ?>">
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-colors <?= $u['is_active'] ? 'bg-error-container text-on-error-container hover:bg-error/20' : 'bg-tertiary-container text-on-tertiary-container hover:bg-tertiary-fixed' ?>">
                                        <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Property Verification Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">verified</span>
            Property Verification & Approvals
        </h2>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                    <tr>
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Title & Owner</th>
                        <th class="p-4 font-semibold">City & Rent</th>
                        <th class="p-4 font-semibold">Approval Status</th>
                        <th class="p-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50">
                    <?php foreach ($properties as $p): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4 font-mono text-xs text-on-surface">#<?= $p['property_id'] ?></td>
                            <td class="p-4 text-on-surface">
                                <div class="font-semibold"><?= htmlspecialchars($p['title']) ?></div>
                                <div class="text-xs text-on-surface-variant">Owner: <?= htmlspecialchars($p['owner_name']) ?></div>
                            </td>
                            <td class="p-4 text-on-surface-variant text-xs">
                                <div><?= htmlspecialchars($p['city']) ?></div>
                                <div class="font-bold text-on-surface">$<?= number_format($p['price_per_month'], 2) ?>/mo</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase <?= $p['is_approved'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-secondary-container text-on-secondary-container' ?>">
                                    <?= $p['is_approved'] ? 'Approved' : 'Pending' ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <?php if (!$p['is_approved']): ?>
                                    <form action="/admin/properties/approve" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                        <input type="hidden" name="is_approved" value="1">
                                        <input type="hidden" name="is_verified" value="1">
                                        <button type="submit" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-tertiary-fixed transition-colors">Approve</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-outline italic">Verified</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complaints Resolution Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">gavel</span>
            Resolution Desk (Complaints)
        </h2>
        <?php if (empty($complaints)): ?>
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl text-sm text-on-surface-variant">
                No active complaints filed.
            </div>
        <?php else: ?>
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                        <tr>
                            <th class="p-4 font-semibold">ID</th>
                            <th class="p-4 font-semibold">Filed By</th>
                            <th class="p-4 font-semibold">Target (User/Property)</th>
                            <th class="p-4 font-semibold">Description</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/50">
                        <?php foreach ($complaints as $c): ?>
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="p-4 font-mono text-xs text-on-surface">#<?= $c['complaint_id'] ?></td>
                                <td class="p-4 text-on-surface font-semibold"><?= htmlspecialchars($c['filer_name']) ?></td>
                                <td class="p-4 text-xs text-on-surface-variant">
                                    <?= htmlspecialchars($c['against_property_title'] ? "Property: {$c['against_property_title']}" : "User: {$c['against_user_name']}") ?>
                                </td>
                                <td class="p-4 text-xs text-on-surface-variant max-w-xs"><?= htmlspecialchars($c['description']) ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase bg-secondary-container text-on-secondary-container">
                                        <?= htmlspecialchars($c['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($c['status'] === 'open'): ?>
                                        <form action="/admin/complaints/resolve" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="complaint_id" value="<?= $c['complaint_id'] ?>">
                                            <button type="submit" name="status" value="resolved" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold hover:bg-tertiary-fixed transition-colors">Resolve</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-xs text-outline italic">Resolved</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Admin Audit Trail -->
    <div>
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">history</span>
            Admin Audit Trail (admin_actions)
        </h2>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                    <tr>
                        <th class="p-4 font-semibold">Action ID</th>
                        <th class="p-4 font-semibold">Admin</th>
                        <th class="p-4 font-semibold">Action Type</th>
                        <th class="p-4 font-semibold">Target</th>
                        <th class="p-4 font-semibold">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50">
                    <?php foreach ($audit_logs as $log): ?>
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="p-4 font-mono text-xs text-on-surface">#<?= $log['action_id'] ?></td>
                            <td class="p-4 text-on-surface font-semibold"><?= htmlspecialchars($log['admin_name']) ?></td>
                            <td class="p-4 font-semibold text-xs text-on-tertiary-container uppercase"><?= htmlspecialchars($log['action_type']) ?></td>
                            <td class="p-4 text-xs text-on-surface-variant"><?= htmlspecialchars($log['target_type']) ?> #<?= $log['target_id'] ?></td>
                            <td class="p-4 text-xs text-on-surface-variant"><?= htmlspecialchars($log['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
