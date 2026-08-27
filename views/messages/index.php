<?php require __DIR__ . '/../layout/header.php'; ?>
<?php require __DIR__ . '/../layout/nav.php'; ?>
<main class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 flex flex-col">
    <h1 class="font-headline-xl text-2xl font-bold text-on-surface mb-6">Direct Messages & Communication</h1>

    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm flex-1 grid grid-cols-1 md:grid-cols-3 min-h-[500px]">
        <!-- Conversations List -->
        <div class="border-r border-outline-variant p-4 bg-surface-container-low flex flex-col">
            <h3 class="font-bold text-xs uppercase text-on-surface-variant mb-4">Conversations</h3>
            <?php if (empty($threads)): ?>
                <p class="text-xs text-on-surface-variant italic">No previous message threads.</p>
            <?php else: ?>
                <div class="space-y-2 flex-1 overflow-y-auto">
                    <?php foreach ($threads as $t): ?>
                        <a href="/messages?with=<?= $t['other_user_id'] ?>" class="block p-3 rounded-lg border border-outline-variant/60 transition <?= $receiver_id == $t['other_user_id'] ? 'bg-primary-container text-on-primary font-semibold' : 'bg-surface-container-lowest hover:bg-surface-container-high text-on-surface' ?>">
                            <div class="text-sm font-semibold"><?= htmlspecialchars($t['other_user_name']) ?></div>
                            <div class="text-xs opacity-75 uppercase"><?= htmlspecialchars($t['other_user_role']) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Chat Window -->
        <div class="md:col-span-2 flex flex-col p-6 bg-surface-container-lowest">
            <?php if ($receiver_id): ?>
                <!-- Thread History -->
                <div class="flex-1 overflow-y-auto space-y-4 mb-4 pr-2">
                    <?php if (empty($messages)): ?>
                        <p class="text-xs text-on-surface-variant italic text-center py-8">Start the conversation below.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <div class="flex flex-col <?= $msg['sender_id'] == $user['user_id'] ? 'items-end' : 'items-start' ?>">
                                <div class="max-w-md p-3 rounded-xl text-sm <?= $msg['sender_id'] == $user['user_id'] ? 'bg-primary-container text-on-primary rounded-br-none' : 'bg-surface-container-high text-on-surface rounded-bl-none' ?>">
                                    <?= nl2br(htmlspecialchars($msg['content'])) ?>
                                </div>
                                <span class="text-[10px] text-on-surface-variant mt-1"><?= htmlspecialchars($msg['sent_at']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Send Message Form -->
                <form action="/messages/send" method="POST" class="flex gap-2 border-t border-outline-variant pt-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="receiver_id" value="<?= $receiver_id ?>">
                    <input type="text" name="content" required placeholder="Type your message..." class="flex-1 px-4 py-2.5 border border-outline-variant rounded-lg bg-surface-container-lowest text-sm text-on-surface"/>
                    <button type="submit" class="bg-on-tertiary-container text-on-primary px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-tertiary-fixed hover:text-on-tertiary-fixed transition">
                        Send
                    </button>
                </form>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-[48px] mb-2 text-outline">chat_bubble_outline</span>
                    <p class="text-sm">Select a conversation thread on the left to read and send messages.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
