<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-md mx-auto py-12 px-4 flex-1 flex flex-col justify-center">
    <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl shadow-sm">
        <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Password Recovery</h2>
        <p class="font-body-sm text-on-surface-variant mb-6">Enter your account email to receive a recovery token.</p>

        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-error-container text-on-error-container text-sm rounded-lg border border-error/20">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="mb-4 p-3 bg-tertiary-fixed/30 text-on-tertiary-fixed-variant text-sm rounded-lg border border-tertiary-fixed">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form action="/forgot-password" method="POST" class="flex flex-col gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            
            <div class="flex flex-col gap-1.5">
                <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="email">Registered Email</label>
                <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="email" name="email" required type="email"/>
            </div>

            <button class="w-full py-2.5 px-4 rounded-lg font-title-md text-on-primary bg-primary-container hover:bg-on-primary-fixed transition-colors mt-2" type="submit">
                Request Reset Token
            </button>
        </form>

        <div class="mt-6 text-center">
            <a class="text-sm font-medium text-on-tertiary-container hover:underline" href="/login">Back to Sign In</a>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
