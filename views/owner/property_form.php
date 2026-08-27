<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-3xl mx-auto py-10 px-4 flex-1">
    <div class="bg-surface-container-lowest border border-outline-variant p-8 rounded-xl shadow-sm">
        <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-1">Add New Property Listing</h2>
        <p class="font-body-sm text-on-surface-variant mb-6">Enter listing specs, address details, and cover image URL.</p>

        <form action="/properties/create" method="POST" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <?php if (Auth::user()['role'] === 'broker'): ?>
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Owner User ID</label>
                    <input type="number" name="owner_id" required placeholder="e.g. 2" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
            <?php endif; ?>

            <div>
                <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Property Title</label>
                <input type="text" name="title" required placeholder="e.g. The Aurora Residences" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Address Line</label>
                    <input type="text" name="address_line" required placeholder="100 Skyline Blvd" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">City</label>
                    <input type="text" name="city" required placeholder="New York" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Price / Month ($)</label>
                    <input type="number" step="0.01" name="price_per_month" required placeholder="3500.00" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Bedrooms</label>
                    <input type="number" name="bedrooms" placeholder="2" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Bathrooms</label>
                    <input type="number" name="bathrooms" placeholder="2" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
                <div>
                    <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Area (sqft)</label>
                    <input type="number" step="0.1" name="area_sqft" placeholder="1200" class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
                </div>
            </div>

            <div>
                <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Cover Image URL</label>
                <input type="url" name="cover_image_url" placeholder="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00..." class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"/>
            </div>

            <div>
                <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Description</label>
                <textarea name="description" rows="4" placeholder="Describe layout, view, and amenities..." class="w-full px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface"></textarea>
            </div>

            <button type="submit" class="w-full bg-primary-container text-on-primary py-3 rounded-lg font-semibold hover:bg-on-primary-fixed transition">
                Create Listing
            </button>
        </form>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
