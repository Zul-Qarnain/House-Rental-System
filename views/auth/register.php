<?php require __DIR__ . '/../layout/header.php'; ?>
<main class="flex w-full min-h-[calc(100vh-4rem)]">
    <div class="hidden lg:block lg:w-1/2 relative bg-surface-container-highest">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1200&q=80')"></div>
        <div class="absolute inset-0 bg-primary-container/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 p-margin-desktop flex flex-col justify-between z-10">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-on-primary text-[32px]">domain</span>
                <h1 class="font-headline-lg text-headline-lg text-on-primary tracking-tight">PropTech OS</h1>
            </div>
            <div class="max-w-md">
                <h2 class="font-headline-xl text-headline-xl text-on-primary mb-4 leading-tight">Join the modern property management network.</h2>
                <p class="font-body-md text-body-md text-on-primary/80">Connect as a tenant, property owner, or real estate broker.</p>
            </div>
        </div>
    </div>
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-surface">
        <div class="w-full max-w-[440px] flex flex-col">
            <div class="mb-6">
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-2">Create Account</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Select your user role and register to get started.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="mb-4 p-3 bg-error-container text-on-error-container text-sm rounded-lg border border-error/20">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="/register" method="POST" class="flex flex-col gap-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="role">I am registering as a</label>
                    <select class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="role" name="role" required>
                        <option value="tenant">Tenant (Looking for rental property)</option>
                        <option value="owner">Property Owner (Listing & managing properties)</option>
                        <option value="broker">Real Estate Broker (Managing client listings)</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="name">Full Name</label>
                    <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="name" name="name" placeholder="John Doe" required type="text"/>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="email">Email Address</label>
                    <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="email" name="email" placeholder="john@example.com" required type="email"/>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="phone">Phone Number</label>
                    <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="phone" name="phone" placeholder="+1-555-0199" type="text"/>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface uppercase tracking-wider" for="password">Password</label>
                    <input class="block w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md" id="password" name="password" placeholder="••••••••" required type="password"/>
                </div>

                <button class="w-full py-3 px-4 rounded-lg font-title-md text-on-primary bg-primary-container hover:bg-on-primary-fixed transition-colors mt-2" type="submit">
                    Create Account
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Already have an account? <a class="font-medium text-on-tertiary-container hover:underline" href="/login">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
