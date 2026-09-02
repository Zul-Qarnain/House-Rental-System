<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 flex flex-col">
    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary">Your inbox</p>
            <h1 class="font-headline-xl text-2xl font-bold tracking-tight text-on-surface sm:text-3xl">Direct Messages</h1>
        </div>
        <p class="text-sm text-on-surface-variant">Stay connected with your rental community.</p>
    </div>

    <div class="grid min-h-[560px] flex-1 grid-cols-1 overflow-hidden rounded-2xl border border-outline-variant/70 bg-surface-container-lowest shadow-[0_12px_40px_rgba(28,27,31,0.08)] md:grid-cols-3">
        <!-- Conversations List -->
        <div class="flex flex-col border-b border-outline-variant/70 bg-surface-container-low/70 p-4 md:border-b-0 md:border-r md:p-5">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-on-surface">Conversations</h3>
                    <p class="mt-1 text-xs text-on-surface-variant">Your recent messages</p>
                </div>
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-container text-primary shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">forum</span>
                </span>
            </div>
            <?php if (empty($threads)): ?>
                <div class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-outline-variant bg-surface-container-lowest/60 px-5 py-10 text-center">
                    <span class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-surface-container-high text-outline">
                        <span class="material-symbols-outlined text-[24px]">chat_bubble_outline</span>
                    </span>
                    <p class="text-sm font-semibold text-on-surface">No conversations yet</p>
                    <p class="mt-1 max-w-[220px] text-xs leading-5 text-on-surface-variant">Your messages with tenants, owners, and brokers will appear here.</p>
                </div>
            <?php else: ?>
                <div class="flex-1 space-y-2 overflow-y-auto pr-1">
                    <?php foreach ($threads as $t): ?>
                        <a href="/messages?with=<?= $t['other_user_id'] ?>" class="group flex items-center gap-3 rounded-xl border px-3 py-3 transition duration-200 <?= $receiver_id == $t['other_user_id'] ? 'border-primary/20 bg-primary-container text-on-primary shadow-sm' : 'border-outline-variant/50 bg-surface-container-lowest text-on-surface hover:-translate-y-0.5 hover:border-primary/30 hover:bg-surface-container-high hover:shadow-sm' ?>">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full <?= $receiver_id == $t['other_user_id'] ? 'bg-primary/15 text-on-primary' : 'bg-primary-container text-primary' ?>">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-semibold"><?= htmlspecialchars($t['other_user_name']) ?></span>
                                <span class="mt-1 block text-[10px] font-semibold uppercase tracking-wider opacity-70"><?= htmlspecialchars($t['other_user_role']) ?></span>
                            </span>
                            <span class="material-symbols-outlined text-[18px] opacity-0 transition group-hover:opacity-60">chevron_right</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Chat Window -->
        <div class="flex flex-col bg-surface-container-lowest p-4 sm:p-6 md:col-span-2">
            <?php if ($receiver_id): ?>
                <div class="mb-5 flex items-center gap-3 border-b border-outline-variant/60 pb-4">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-container text-primary">
                        <span class="material-symbols-outlined text-[22px]">person</span>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-on-surface-variant">Active conversation</p>
                        <p class="mt-0.5 text-sm font-semibold text-on-surface">Message thread</p>
                    </div>
                </div>
                <!-- Thread History -->
                <div class="mb-5 flex-1 space-y-5 overflow-y-auto rounded-xl bg-surface-container-low/40 px-3 py-4 sm:px-5">
                    <?php if (empty($messages)): ?>
                        <div class="flex h-full min-h-[220px] flex-col items-center justify-center text-center">
                            <span class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-surface-container-high text-primary">
                                <span class="material-symbols-outlined text-[28px]">waving_hand</span>
                            </span>
                            <p class="text-sm font-semibold text-on-surface">Start the conversation</p>
                            <p class="mt-1 text-xs text-on-surface-variant">Send a message to get things moving.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="flex flex-col gap-1 <?= $msg['sender_id'] == $user['user_id'] ? 'items-end' : 'items-start' ?>">
                                <div class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm sm:max-w-md <?= $msg['sender_id'] == $user['user_id'] ? 'rounded-br-md bg-primary-container text-on-primary' : 'rounded-bl-md border border-outline-variant/50 bg-surface-container-high text-on-surface' ?>">
                                    <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                </div>
                                <span class="px-1 text-[10px] text-on-surface-variant"><?= htmlspecialchars($msg['sent_at']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Send Message Form -->
                <form action="/messages/send" method="POST" class="flex items-center gap-2 rounded-xl border border-outline-variant/70 bg-surface-container-low p-2 shadow-sm focus-within:border-primary/50 focus-within:ring-2 focus-within:ring-primary/10">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                    <input type="text" name="content" required placeholder="Write a message..." class="min-w-0 flex-1 border-0 bg-transparent px-3 py-2 text-sm text-on-surface outline-none placeholder:text-on-surface-variant/70"/>
                    <button type="submit" class="flex shrink-0 items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-on-primary shadow-sm transition hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-1">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span class="hidden sm:inline">Send</span>
                    </button>
                </form>
            <?php else: ?>
                <div class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-outline-variant bg-surface-container-low/40 px-6 py-16 text-center text-on-surface-variant">
                    <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-container text-primary shadow-sm">
                        <span class="material-symbols-outlined text-[32px]">forum</span>
                    </span>
                    <p class="text-base font-semibold text-on-surface">Choose a conversation</p>
                    <p class="mt-1 max-w-sm text-sm leading-6">Select a thread from the panel to read your message history and send a reply.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
