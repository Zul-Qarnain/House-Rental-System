<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
feature/admin-module
<main class="max-w-[1440px] mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-10 rounded-2xl bg-primary-container px-6 py-7 text-on-primary shadow-lg sm:px-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-tertiary-fixed">Administration</p>
                <h1 class="font-headline-xl text-3xl font-bold tracking-tight">Admin Control Center</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-primary-fixed">Manage platform users, property verification, broker assignments, resolution desk & audit logs.</p>
            </div>
            <span class="material-symbols-outlined hidden text-[56px] text-tertiary-fixed/80 sm:block">admin_panel_settings</span>
        </div>
    </div>

    <!-- Admin Overview / Quick Stats -->
    <section class="mb-10" aria-labelledby="admin-overview-heading">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <p class="mb-1 text-xs font-bold uppercase tracking-[0.2em] text-on-tertiary-container">At a glance</p>
                <h2 id="admin-overview-heading" class="text-lg font-bold tracking-tight text-on-surface">Admin Overview</h2>
            </div>
            <span class="hidden text-sm text-on-surface-variant sm:block">Quick Stats</span>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl border border-primary-fixed/30 bg-primary-container p-5 text-on-primary shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-primary-fixed">Total Users</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight"><?= count($users) ?></p>
                    </div>
                    <span class="material-symbols-outlined rounded-xl bg-on-primary/10 p-2 text-2xl text-tertiary-fixed" aria-hidden="true">group</span>
                </div>
            </div>

            <div class="rounded-2xl border border-tertiary-fixed/30 bg-tertiary-container p-5 text-on-tertiary-container shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-on-tertiary-container/80">Active Users</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight"><?= count(array_filter($users, static fn($user) => !empty($user['is_active']))) ?></p>
                    </div>
                    <span class="material-symbols-outlined rounded-xl bg-on-tertiary-container/10 p-2 text-2xl" aria-hidden="true">person_check</span>
                </div>
            </div>

            <div class="rounded-2xl border border-secondary-fixed/30 bg-secondary-container p-5 text-on-secondary-container shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-on-secondary-container/80">Open Complaints</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight"><?= count(array_filter($complaints, static fn($complaint) => $complaint['status'] === 'open')) ?></p>
                    </div>
                    <span class="material-symbols-outlined rounded-xl bg-on-secondary-container/10 p-2 text-2xl" aria-hidden="true">report_problem</span>
                </div>
            </div>

            <div class="rounded-2xl border border-outline-variant bg-surface-container-high p-5 text-on-surface shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-on-surface-variant">Audit Actions</p>
                        <p class="mt-3 text-3xl font-bold tracking-tight"><?= count($audit_logs) ?></p>
                    </div>
                    <span class="material-symbols-outlined rounded-xl bg-on-surface/10 p-2 text-2xl text-on-tertiary-container" aria-hidden="true">history</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Admin Modules -->
    <section class="mb-10 rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 shadow-sm sm:p-6" aria-labelledby="quick-navigation-heading">
        <div class="mb-4">
            <p class="mb-1 text-xs font-bold uppercase tracking-[0.2em] text-on-tertiary-container">Manage platform operations</p>
            <h2 id="quick-navigation-heading" class="text-lg font-bold tracking-tight text-on-surface">Admin Modules</h2>
        </div>
        <nav class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Admin dashboard sections">
            <a href="#user-accounts-roles" class="group flex min-h-[148px] flex-col justify-between rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 text-on-surface shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary-container hover:shadow-md">
                <span class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary-container/10 text-on-tertiary-container"><span class="material-symbols-outlined text-2xl" aria-hidden="true">group</span></span>
                    <span class="material-symbols-outlined text-xl text-on-surface-variant transition-transform group-hover:translate-x-1" aria-hidden="true">arrow_forward</span>
                </span>
                <span>
                    <span class="block text-base font-bold tracking-tight">Users</span>
                    <span class="mt-1 block text-xs leading-5 text-on-surface-variant">Manage account status and roles</span>
                </span>
            </a>
            <a href="#property-approvals-broker-assignment" class="group flex min-h-[148px] flex-col justify-between rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 text-on-surface shadow-sm transition-all hover:-translate-y-0.5 hover:border-tertiary-container hover:shadow-md">
                <span class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-tertiary-container/10 text-on-tertiary-container"><span class="material-symbols-outlined text-2xl" aria-hidden="true">real_estate_agent</span></span>
                    <span class="material-symbols-outlined text-xl text-on-surface-variant transition-transform group-hover:translate-x-1" aria-hidden="true">arrow_forward</span>
                </span>
                <span>
                    <span class="block text-base font-bold tracking-tight">Properties</span>
                    <span class="mt-1 block text-xs leading-5 text-on-surface-variant">Review listings and broker assignments</span>
                </span>
            </a>
            <a href="#complaints-resolution-desk" class="group flex min-h-[148px] flex-col justify-between rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 text-on-surface shadow-sm transition-all hover:-translate-y-0.5 hover:border-secondary-container hover:shadow-md">
                <span class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-secondary-container/10 text-on-tertiary-container"><span class="material-symbols-outlined text-2xl" aria-hidden="true">report_problem</span></span>
                    <span class="material-symbols-outlined text-xl text-on-surface-variant transition-transform group-hover:translate-x-1" aria-hidden="true">arrow_forward</span>
                </span>
                <span>
                    <span class="block text-base font-bold tracking-tight">Complaints</span>
                    <span class="mt-1 block text-xs leading-5 text-on-surface-variant">Handle and resolve reported issues</span>
                </span>
            </a>
            <a href="#admin-audit-trail" class="group flex min-h-[148px] flex-col justify-between rounded-2xl border border-outline-variant bg-surface-container-lowest p-5 text-on-surface shadow-sm transition-all hover:-translate-y-0.5 hover:border-outline hover:shadow-md">
                <span class="flex items-start justify-between gap-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-surface-container-high text-on-tertiary-container"><span class="material-symbols-outlined text-2xl" aria-hidden="true">history</span></span>
                    <span class="material-symbols-outlined text-xl text-on-surface-variant transition-transform group-hover:translate-x-1" aria-hidden="true">arrow_forward</span>
                </span>
                <span>
                    <span class="block text-base font-bold tracking-tight">Audit Logs</span>
                    <span class="mt-1 block text-xs leading-5 text-on-surface-variant">Track administrator activities</span>
                </span>
            </a>
        </nav>
    </section>

    <!-- User Management Table -->
    <div id="user-accounts-roles" class="mb-10 scroll-mt-6">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold tracking-tight text-on-surface"><span class="h-6 w-1 rounded-full bg-on-tertiary-container"></span>User Accounts & Roles</h2>
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label for="user-search" class="sr-only">Search users by name, email or role...</label>
            <div class="relative w-full sm:max-w-md">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xl text-on-surface-variant" aria-hidden="true">search</span>
                <input id="user-search" type="search" placeholder="Search users by name, email or role..." autocomplete="off" class="w-full rounded-xl border border-outline-variant bg-surface-container-lowest py-3 pl-10 pr-4 text-sm text-on-surface outline-none transition focus:border-primary-container focus:ring-2 focus:ring-primary-container/20" />
            </div>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-sm">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="border-b border-outline-variant bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">

<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-headline-xl text-2xl font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-[28px] text-on-tertiary-container">admin_panel_settings</span>
                Admin Control Center
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">Manage user account statuses, resolution desk & audit logs.</p>
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
                <tbody id="user-table-body" class="divide-y divide-outline-variant/50">
                    <?php foreach ($users as $u): ?>

                        <tr class="transition-colors hover:bg-surface-container-low">
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
                                <?php if (in_array($u['role'], ['owner', 'OWNER'], true)): ?>
                                    <a href="/messages?with=<?= $u['user_id'] ?>" class="mr-2 inline-flex items-center gap-1 rounded-lg bg-primary-container px-3 py-1.5 text-xs font-semibold text-on-primary transition-colors hover:bg-on-primary-fixed" aria-label="Message <?= htmlspecialchars($u['name']) ?>">
                                        <span class="material-symbols-outlined text-base" aria-hidden="true">mail</span>
                                        Message
                                    </a>
                                <?php endif; ?>
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
    <!-- Property Verification & Broker Assignment Desk -->
    <div id="property-approvals-broker-assignment" class="mb-10 scroll-mt-6">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold tracking-tight text-on-surface"><span class="h-6 w-1 rounded-full bg-on-tertiary-container"></span>Property Approvals & Broker Assignment</h2>
        <div class="overflow-x-auto rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-sm">
            <table class="w-full min-w-[860px] text-left text-sm">
                <thead class="border-b border-outline-variant bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">
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
                        <tr class="transition-colors hover:bg-surface-container-low">
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
                                        <button type="submit" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold transition-colors hover:bg-tertiary-fixed">Approve</button>
                                    </form>
                                <?php endif; ?>
                                <!-- Broker Assignment Form -->
                                <form action="/admin/properties/assign-broker" method="POST" class="inline-flex gap-1">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                    <input type="hidden" name="property_id" value="<?= $p['property_id'] ?>">
                                    <input type="number" name="broker_id" required placeholder="Broker ID" class="w-28 sm:w-32 rounded-lg border border-outline-variant bg-surface-container-lowest px-3 py-2 text-sm"/>
                                    <button type="submit" class="bg-primary-container text-on-primary text-xs px-3 py-1.5 rounded-lg font-semibold transition-colors hover:bg-on-primary-fixed">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Complaints Resolution Desk -->
    <div id="complaints-resolution-desk" class="mb-10 scroll-mt-6">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold tracking-tight text-on-surface"><span class="h-6 w-1 rounded-full bg-on-tertiary-container"></span>Resolution Desk (Complaints)</h2>

    <!-- Complaints Resolution Desk -->
    <div class="mb-10">
        <h2 class="text-lg font-bold text-on-surface mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-[20px]">gavel</span>
            Resolution Desk (Complaints)
        </h2>

        <?php if (empty($complaints)): ?>
            <div class="rounded-2xl border border-dashed border-outline-variant bg-surface-container-lowest p-8 text-center text-sm text-on-surface-variant">
                No active complaints filed.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-sm">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="border-b border-outline-variant bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">
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
                            <tr class="transition-colors hover:bg-surface-container-low">
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
                                            <button type="submit" name="status" value="resolved" class="bg-tertiary-container text-on-tertiary-container text-xs px-3 py-1.5 rounded-lg font-semibold transition-colors hover:bg-tertiary-fixed">Resolve</button>
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
    <div id="admin-audit-trail" class="scroll-mt-6">
        <h2 class="mb-4 flex items-center gap-2 text-lg font-bold tracking-tight text-on-surface"><span class="h-6 w-1 rounded-full bg-on-tertiary-container"></span>Admin Audit Trail (admin_actions)</h2>
        <div class="overflow-x-auto rounded-2xl border border-outline-variant bg-surface-container-lowest shadow-sm">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-outline-variant bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">
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
                        <tr class="transition-colors hover:bg-surface-container-low">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('user-search');
        const userRows = document.querySelectorAll('#user-table-body tr');

        if (!searchInput) return;

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLowerCase();

            userRows.forEach(function (row) {
                const searchableText = Array.from(row.cells)
                    .slice(1, 3)
                    .map(function (cell) { return cell.textContent.toLowerCase(); })
                    .join(' ');

                row.hidden = query !== '' && !searchableText.includes(query);
            });
        });
    });
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
