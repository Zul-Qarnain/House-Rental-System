<?php $user = Auth::user(); ?>
<header class="bg-primary-container text-on-primary sticky top-0 z-50 shadow-md">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[28px]">domain</span>
            <span class="font-headline-lg text-lg tracking-tight font-bold">PropTech OS</span>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
            <a href="/" class="hover:text-tertiary-fixed transition-colors">Properties</a>
            <?php if ($user): ?>
                <?php if ($user['role'] === 'tenant'): ?>
                    <a href="/tenant/dashboard" class="hover:text-tertiary-fixed transition-colors">My Tenant Portal</a>
                <?php elseif ($user['role'] === 'owner'): ?>
                    <a href="/owner/dashboard" class="hover:text-tertiary-fixed transition-colors">Owner Dashboard</a>
                    <a href="/properties/create" class="hover:text-tertiary-fixed transition-colors">+ Add Property</a>
                <?php elseif ($user['role'] === 'broker'): ?>
                    <a href="/broker/dashboard" class="hover:text-tertiary-fixed transition-colors">Broker Portal</a>
                    <a href="/properties/create" class="hover:text-tertiary-fixed transition-colors">+ Add Property</a>
                <?php elseif ($user['role'] === 'admin'): ?>
                    <a href="/admin/users" class="hover:text-tertiary-fixed transition-colors">Admin Desk</a>
                <?php endif; ?>
                <a href="/messages" class="hover:text-tertiary-fixed transition-colors">Messages</a>
            <?php endif; ?>
        </nav>

        <div class="flex items-center gap-4">
            <?php if ($user): ?>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary-fixed text-[20px]">account_circle</span>
                    <span class="text-sm font-medium"><?= htmlspecialchars($user['name']) ?> (<?= ucfirst(htmlspecialchars($user['role'])) ?>)</span>
                </div>
                <form action="/logout" method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= Auth::csrfToken() ?>">
                    <button type="submit" class="text-xs bg-surface-container-highest text-on-surface px-3 py-1.5 rounded font-semibold hover:bg-outline-variant transition">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="text-sm font-medium hover:text-tertiary-fixed">Sign In</a>
                <a href="/register" class="bg-on-tertiary-container text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition">Get Started</a>
            <?php endif; ?>
        </div>
    </div>
</header>
