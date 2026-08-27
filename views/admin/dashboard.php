<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8">
        <h1 class="font-headline-xl text-2xl font-bold text-on-surface">Admin Control Center</h1>
        <p class="text-sm text-on-surface-variant">Manage platform users, property verification, broker assignments, resolution desk & audit logs.</p>
    </div>

    <!-- User Management Table -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4">User Accounts & Roles</h2>
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
                        <tr>
                            <td class="p-4 font-mono text-xs text-on-surface">#<?= $u['user_id'] ?></td>
                            <td class="p-4 text-on-surface">
                                <div class="font-semibold"><?= htmlspecialchars($u['name']) ?></div>
                                <div class="text-xs text-on-surface-variant"><?= htmlspecialchars($u['email']) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold uppercase bg-surface-container-high text-on-surface">
                                    <?= htmlspecialchars($u['role']) ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase <?= $u['is_active'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-error-container text-on-error-container' ?>">
                                    <?= $u['is_active'] ? 'Active' : 'Deactivated' ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <form action="/admin/users/status" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                    <input type="hidden" name="is_active" value="<?= $u['is_active'] ? 0 : 1 ?>">
                                    <button type="submit" class="text-xs px-3 py-1 rounded font-semibold <?= $u['is_active'] ? 'bg-error-container text-on-error-container hover:bg-error/20' : 'bg-tertiary-container text-on-tertiary-container hover:bg-tertiary-fixed' ?>">
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

    <!-- Property Verification & Broker Assignment Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4">Property Approvals & Broker Assignment</h2>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
            <table class="w-full text-left text-sm">
                <thead class="bg-surface-container-low text-xs uppercase text-on-surface-variant border-b border-outline-variant">
                    <tr>
                        <th class="p-4 font-semibold">ID</th>
                        <th class="p-4 font-semibold">Title & Owner</th>
                        <th class="p-4 font-semibold">City & Rent</th>
                        <th class="p-4 font-semibold">Approval Status</th>
                        <th class="p-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/50">
                    <?php foreach ($properties as $p): ?>
                        <tr>
                            <td class="p-4 font-mono text-xs text-on-surface">#<?= $p['property_id'] ?></td>
                            <td class="p-4 text-on-surface">
                                <div class="font-semibold"><?= htmlspecialchars($p['title']) ?></div>
                                <div class="text-xs text-on-surface-variant">Owner: <?= htmlspecialchars($p['owner_name']) ?></div>
                            </td>
                            <td class="p-4 text-on-surface-variant text-xs">
                                <div><?= htmlspecialchars($p['city']) ?></div>
                                <div class="font-bold text-on-surface">$<?= number_format($p['price_per_month'], 2) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase <?= $p['is_approved'] ? 'bg-tertiary-container text-on-tertiary-container' : 'bg-secondary-container text-on-secondary-container' ?>">
                                    <?= $p['is_approved'] ? 'Approved' : 'Pending' ?>
                                </span>
                            </td>
                            <td class="p-4 text-right flex items-center justify-end gap-2">
                                <?php if (!$p['is_approved']): ?>
                                    <form action="/admin/properties/approve" method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                        <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                        <input type="hidden" name="is_approved" value="1">
                                        <input type="hidden" name="is_verified" value="1">
                                        <button type="submit" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1 rounded font-semibold hover:bg-tertiary-fixed">Approve</button>
                                    </form>
                                <?php endif; ?>
                                <!-- Broker Assignment Form -->
                                <form action="/admin/properties/assign-broker" method="POST" class="inline-flex gap-1">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                    <input type="number" name="broker_id" required placeholder="Broker ID" class="w-20 text-xs px-2 py-1 border border-outline-variant rounded bg-surface-container-lowest"/>
                                    <button type="submit" class="bg-primary-container text-on-primary text-xs px-2.5 py-1 rounded font-semibold hover:bg-on-primary-fixed">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complaints Resolution Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4">Resolution Desk (Complaints)</h2>
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
                            <tr>
                                <td class="p-4 font-mono text-xs text-on-surface">#<?= $c['complaint_id'] ?></td>
                                <td class="p-4 text-on-surface font-semibold"><?= htmlspecialchars($c['filer_name']) ?></td>
                                <td class="p-4 text-xs text-on-surface-variant">
                                    <?= htmlspecialchars($c['against_property_title'] ? "Property: {$c['against_property_title']}" : "User: {$c['against_user_name']}") ?>
                                </td>
                                <td class="p-4 text-xs text-on-surface-variant max-w-xs"><?= htmlspecialchars($c['description']) ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase bg-secondary-container text-on-secondary-container">
                                        <?= htmlspecialchars($c['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($c['status'] === 'open'): ?>
                                        <form action="/admin/complaints/resolve" method="POST" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                            <input type="hidden" name="complaint_id" value="<?= $c['complaint_id'] ?>">
                                            <button type="submit" name="status" value="resolved" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1 rounded font-semibold">Resolve</button>
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
        <h2 class="text-lg font-bold text-on-surface mb-4">Admin Audit Trail (admin_actions)</h2>
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
                        <tr>
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
