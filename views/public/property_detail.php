<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-tertiary-container text-on-tertiary-container text-xs px-2.5 py-0.5 rounded-full font-semibold capitalize">
                    <?= htmlspecialchars($property['availability_status']) ?>
                </span>
                <?php if ($property['is_verified']): ?>
                    <span class="bg-secondary-container text-on-secondary-container text-xs px-2.5 py-0.5 rounded-full font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">verified</span> Verified Listing
                    </span>
                <?php endif; ?>
            </div>
            <h1 class="font-headline-xl text-headline-xl text-on-surface font-bold"><?= htmlspecialchars($property['title']) ?></h1>
            <p class="font-body-md text-on-surface-variant flex items-center gap-1 mt-1">
                <span class="material-symbols-outlined text-[18px]">location_on</span> <?= htmlspecialchars($property['address_line']) ?>, <?= htmlspecialchars($property['city']) ?>
            </p>
        </div>
        <div class="flex flex-col md:items-end">
            <span class="text-3xl font-bold text-on-surface">$<?= number_format($property['price_per_month'], 2) ?> <span class="text-sm font-normal text-on-surface-variant">/ month</span></span>
            <?php if (Auth::check() && Auth::user()['role'] === 'tenant'): ?>
                <?php if (!empty($user_app_status) && $user_app_status === 'approved'): ?>
                    <div class="mt-2 bg-tertiary-container text-on-tertiary-container px-4 py-2.5 rounded-lg font-bold flex items-center gap-2 shadow-sm text-sm">
                        <span class="material-symbols-outlined text-[20px]">verified_user</span> Booked by You
                    </div>
                <?php elseif (!empty($user_app_status) && $user_app_status === 'pending'): ?>
                    <div class="mt-2 bg-secondary-container text-on-secondary-container px-4 py-2.5 rounded-lg font-bold flex items-center gap-2 shadow-sm text-sm">
                        <span class="material-symbols-outlined text-[20px]">pending</span> You Applied (Application Pending)
                    </div>
                <?php elseif ($property['availability_status'] === 'available'): ?>
                    <a href="/rentals/apply/<?= $property['property_id'] ?>" class="mt-2 bg-on-tertiary-container text-on-primary px-6 py-2.5 rounded-lg font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">assignment_add</span> Apply to Rent
                    </a>
                <?php else: ?>
                    <button disabled class="mt-2 bg-surface-container-high text-outline px-6 py-2.5 rounded-lg font-semibold cursor-not-allowed">
                        Currently Unavailable
                    </button>
                <?php endif; ?>
            <?php elseif (!Auth::check()): ?>
                <a href="/login" class="mt-2 bg-primary-container text-on-primary px-6 py-2.5 rounded-lg font-semibold hover:bg-on-primary-fixed transition">
                    Sign in to Apply
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <?php if (!empty($images)): ?>
            <?php foreach (array_slice($images, 0, 3) as $idx => $img): ?>
                <div class="<?= $idx === 0 ? 'md:col-span-2 h-96' : 'h-96' ?> rounded-xl overflow-hidden bg-surface-container-highest">
                    <img src="<?= htmlspecialchars($img['image_url']) ?>" alt="Property Image" class="w-full h-full object-cover"/>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="md:col-span-3 h-96 rounded-xl overflow-hidden bg-surface-container-highest">
                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1200&q=80" alt="Property Image" class="w-full h-full object-cover"/>
            </div>
        <?php endif; ?>
    </div>

    <!-- Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
                <h3 class="font-headline-lg text-lg font-semibold text-on-surface mb-4">Property Features & Overview</h3>
                <div class="grid grid-cols-3 gap-4 mb-6 border-b border-outline-variant pb-6">
                    <div>
                        <span class="text-xs uppercase text-on-surface-variant font-semibold">Bedrooms</span>
                        <p class="text-xl font-bold text-on-surface mt-1 flex items-center gap-1"><span class="material-symbols-outlined">bed</span> <?= (int)$property['bedrooms'] ?></p>
                    </div>
                    <div>
                        <span class="text-xs uppercase text-on-surface-variant font-semibold">Bathrooms</span>
                        <p class="text-xl font-bold text-on-surface mt-1 flex items-center gap-1"><span class="material-symbols-outlined">bathtub</span> <?= (int)$property['bathrooms'] ?></p>
                    </div>
                    <div>
                        <span class="text-xs uppercase text-on-surface-variant font-semibold">Total Area</span>
                        <p class="text-xl font-bold text-on-surface mt-1 flex items-center gap-1"><span class="material-symbols-outlined">square_foot</span> <?= number_format((float)$property['area_sqft']) ?> sqft</p>
                    </div>
                </div>
                <h4 class="font-title-md font-semibold text-on-surface mb-2">Description</h4>
                <p class="font-body-md text-on-surface-variant leading-relaxed"><?= nl2br(htmlspecialchars($property['description'] ?? 'No additional description provided for this listing.')) ?></p>
            </div>

            <!-- Verified Tenant Reviews -->
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
                <h3 class="font-headline-lg text-lg font-semibold text-on-surface mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-tertiary-fixed-dim">star</span> Tenant Reviews
                </h3>

                <?php if (empty($reviews)): ?>
                    <p class="text-sm text-on-surface-variant italic">No reviews yet for this property.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($reviews as $rev): ?>
                            <div class="border-b border-outline-variant/60 pb-4 last:border-none">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-semibold text-sm text-on-surface"><?= htmlspecialchars($rev['tenant_name']) ?></span>
                                    <div class="flex text-amber-500">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="material-symbols-outlined text-[16px]"><?= $i <= $rev['rating'] ? 'star' : 'star_outline' ?></span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-sm text-on-surface-variant"><?= htmlspecialchars($rev['feedback']) ?></p>
                                <?php if (!empty($rev['reply_text'])): ?>
                                    <div class="mt-2 ml-4 p-3 bg-surface-container-low border-l-2 border-on-tertiary-container text-xs rounded-r-lg">
                                        <span class="font-semibold text-on-surface block mb-1">Owner Reply:</span>
                                        <p class="text-on-surface-variant"><?= htmlspecialchars($rev['reply_text']) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar / Contact Host & Request Visit -->
        <div class="space-y-6">
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
                <h3 class="font-headline-lg text-lg font-semibold text-on-surface mb-3">Property Representative</h3>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-primary-container text-on-primary rounded-full flex items-center justify-center font-bold text-lg">
                        <?= strtoupper(substr($property['owner_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h4 class="font-semibold text-on-surface"><?= htmlspecialchars($property['owner_name']) ?></h4>
                        <span class="text-xs text-on-surface-variant uppercase font-semibold">Verified Owner</span>
                    </div>
                </div>

                <?php if (Auth::check()): ?>
                    <a href="/messages?with=<?= $property['owner_id'] ?>" class="w-full bg-surface-container-high hover:bg-outline-variant text-on-surface py-2.5 rounded-lg font-medium text-sm transition flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">chat</span> Direct Message Host
                    </a>
                <?php else: ?>
                    <a href="/login" class="w-full bg-surface-container-high hover:bg-outline-variant text-on-surface py-2.5 rounded-lg font-medium text-sm transition text-center block">
                        Sign in to message host
                    </a>
                <?php endif; ?>
            </div>

            <!-- Request Property Walkthrough Visit Form -->
            <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl shadow-sm">
                <h3 class="font-headline-lg text-lg font-semibold text-on-surface mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-on-tertiary-container">calendar_month</span> Request Walkthrough Visit
                </h3>
                <p class="text-xs text-on-surface-variant mb-4">Request a date and time to visit this property. The homeowner will assign a licensed broker to conduct your walkthrough.</p>
                
                <?php if (Auth::check() && Auth::user()['role'] === 'tenant'): ?>
                    <form action="/tenant/visits/request" method="POST" class="flex flex-col gap-3">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                        
                        <div>
                            <label class="text-xs font-semibold text-on-surface uppercase mb-1 block">Preferred Date & Time</label>
                            <input type="datetime-local" name="scheduled_at" required class="w-full text-xs px-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md"/>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-on-surface uppercase mb-1 block">Notes for Visit</label>
                            <textarea name="notes" rows="2" placeholder="e.g. Interested in morning visit..." class="w-full text-xs px-3 py-2 border border-outline-variant rounded-lg bg-surface-container-lowest text-on-surface font-body-md"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-on-tertiary-container text-on-primary py-2.5 rounded-lg font-semibold text-sm hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">event_available</span> Request Walkthrough Visit
                        </button>
                    </form>
                <?php elseif (!Auth::check()): ?>
                    <a href="/login" class="w-full bg-primary-container text-on-primary py-2.5 rounded-lg font-semibold text-sm transition text-center block">
                        Sign in to Schedule Visit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
