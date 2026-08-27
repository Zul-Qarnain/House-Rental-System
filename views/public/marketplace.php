<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <!-- Header Banner -->
    <div class="mb-8">
        <h1 class="font-headline-xl text-headline-xl text-on-surface mb-2 font-bold">Rental Marketplace</h1>
        <p class="font-body-md text-on-surface-variant">Discover high-end verified residential listings available for lease.</p>
    </div>

    <!-- Filter Bar -->
    <form action="/" method="GET" class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm mb-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div>
            <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">City</label>
            <input type="text" name="city" value="<?= htmlspecialchars($city ?? '') ?>" placeholder="e.g. New York" class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest"/>
        </div>
        <div>
            <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Min Price ($)</label>
            <input type="number" name="min_price" value="<?= htmlspecialchars($min_price ?? '') ?>" placeholder="0" class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest"/>
        </div>
        <div>
            <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Max Price ($)</label>
            <input type="number" name="max_price" value="<?= htmlspecialchars($max_price ?? '') ?>" placeholder="10000" class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest"/>
        </div>
        <div>
            <label class="font-label-caps text-xs text-on-surface uppercase tracking-wider block mb-1.5 font-semibold">Min Bedrooms</label>
            <input type="number" name="bedrooms" value="<?= htmlspecialchars($bedrooms ?? '') ?>" placeholder="1" class="w-full px-3 py-2 border border-outline-variant rounded-lg text-sm bg-surface-container-lowest"/>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="w-full bg-primary-container text-on-primary py-2 px-4 rounded-lg font-medium text-sm hover:bg-on-primary-fixed transition flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-[18px]">search</span> Filter
            </button>
            <a href="/" class="bg-surface-container-high text-on-surface py-2 px-3 rounded-lg font-medium text-sm hover:bg-outline-variant transition flex items-center justify-center">Reset</a>
        </div>
    </form>

    <!-- Property Grid -->
    <?php if (empty($properties)): ?>
        <div class="bg-surface-container-lowest p-12 text-center rounded-xl border border-outline-variant">
            <span class="material-symbols-outlined text-[48px] text-outline mb-2">home_work</span>
            <h3 class="font-headline-lg text-on-surface text-lg font-semibold mb-1">No Properties Found</h3>
            <p class="font-body-sm text-on-surface-variant">Try broadening your filter criteria or checking back later.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($properties as $prop): ?>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm hover:shadow-md transition flex flex-col">
                    <div class="relative h-48 bg-surface-container-highest">
                        <img src="<?= htmlspecialchars($prop['cover_image'] ?? 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80') ?>" alt="<?= htmlspecialchars($prop['title']) ?>" class="w-full h-full object-cover"/>
                        <span class="absolute top-3 right-3 bg-tertiary-container text-on-tertiary-container text-xs px-2.5 py-1 rounded-full font-semibold">
                            $<?= number_format($prop['price_per_month'], 2) ?>/mo
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-on-tertiary-container"><?= htmlspecialchars($prop['city']) ?></span>
                            <h3 class="font-title-md text-on-surface text-lg font-semibold mt-1 mb-2"><?= htmlspecialchars($prop['title']) ?></h3>
                            <p class="font-body-sm text-on-surface-variant line-clamp-2 text-sm mb-4"><?= htmlspecialchars($prop['description'] ?? 'No description provided.') ?></p>
                        </div>
                        <div>
                            <div class="flex items-center gap-4 text-xs font-medium text-on-surface-variant border-t border-outline-variant/50 pt-3 mb-4">
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">bed</span> <?= (int)$prop['bedrooms'] ?> Bed</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">bathtub</span> <?= (int)$prop['bathrooms'] ?> Bath</span>
                                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">square_foot</span> <?= number_format((float)$prop['area_sqft']) ?> sqft</span>
                            </div>
                            <a href="/property/<?= $prop['property_id'] ?>" class="block text-center w-full bg-surface-container-high hover:bg-primary-container hover:text-on-primary text-on-surface py-2 rounded-lg font-medium text-sm transition">
                                View Details & Request Lease
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
