<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-2xl mx-auto py-10 px-4 flex-1">
    <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl shadow-sm">
        <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-1">Rental Application Checkout</h2>
        <p class="font-body-sm text-on-surface-variant mb-6">Submit your formal rental application for <strong class="text-on-surface"><?= htmlspecialchars($property['title']) ?></strong>.</p>

        <div class="bg-surface-container-low p-4 rounded-lg mb-6 flex items-center justify-between border border-outline-variant">
            <div>
                <span class="text-xs uppercase text-on-surface-variant font-semibold">Monthly Rent</span>
                <p class="text-xl font-bold text-on-surface">$<?= number_format($property['price_per_month'], 2) ?>/mo</p>
            </div>
            <div class="text-right">
                <span class="text-xs uppercase text-on-surface-variant font-semibold">Location</span>
                <p class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($property['city']) ?></p>
            </div>
        </div>

        <form action="/rentals/apply" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">

            <div>
                <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Desired Move-In Date</label>
                <input type="date" name="requested_move_in" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
            </div>

            <div>
                <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Application Message / Background Details</label>
                <textarea name="message" rows="4" placeholder="Introduce yourself, your employment, or lease length preferences..." class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"></textarea>
            </div>

            <button type="submit" class="w-full bg-on-tertiary-container text-on-primary py-3 rounded-lg font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition shadow-sm">
                Submit Rental Request
            </button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
