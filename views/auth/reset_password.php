<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-md mx-auto py-12 px-4 flex-1 flex flex-col justify-center">
    <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl shadow-sm">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Reset Password</h2>
        <p class="font-body-sm text-on-surface-variant mb-6">Enter your new password below.</p>

        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-error-container text-on-error-container text-sm rounded-lg border border-error/20">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form action="/reset-password" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="flex flex-col gap-1.5">
                <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="password">New Password</label>
                <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="password" name="password" required type="password" placeholder="••••••••"/>
            </div>

            <button class="w-full py-2.5 px-4 rounded-lg font-title-md text-on-primary bg-primary-container hover:bg-on-primary-fixed transition-colors mt-2" type="submit">
                Set New Password
            </button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
