<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

if (!isLoggedIn()) {
    redirect('/pages/login.php');
}

$pageTitle = 'Profili Im';
$userData  = getUserById((int)$_SESSION['user_id']);
$userObj   = User::fromArray($userData);


$myPosts = array_filter($GLOBALS['posts'], fn($p) => $p['author_id'] === $userObj->getId());
usort($myPosts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));


$myTotalVotes    = array_sum(array_map(fn($p) => $p['upvotes'] + $p['downvotes'], $myPosts));
$myTotalComments = array_sum(array_map(fn($p) => count($p['comments']), $myPosts));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-layout">
    <div>
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-avatar"><?= e($userObj->getAvatar()) ?></div>
            <div>
                <div class="profile-name"><?= e($userObj->getUsername()) ?></div>
                <div class="profile-meta">
                    📧 <?= e($userObj->getEmail()) ?> ·
                    📅 Anëtar që <?= e($userObj->getJoined()) ?>
                    <?php if ($userObj->isAdmin()): ?>
                        <span class="role-badge role-badge--admin" style="margin-left:8px">Admin</span>
                    <?php endif; ?>
                </div>
                <?php if ($userObj->getBio()): ?>
                    <p style="font-size:14px;color:var(--text-muted);margin-top:6px"><?= e($userObj->getBio()) ?></p>
                <?php endif; ?>
                <div class="karma-badge">⭐ <?= number_format($userObj->getKarma()) ?> karma</div>
            </div>
        </div>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:28px">
            <?php
            $profileStats = [
                ['n' => count($myPosts),    'l' => 'Postime',  'i' => '📝'],
                ['n' => $myTotalVotes,      'l' => 'Vota',     'i' => '⬆️'],
                ['n' => $myTotalComments,   'l' => 'Komente',  'i' => '💬'],
            ];
            foreach ($profileStats as $ps): ?>
                <div class="stat-card">
                    <div style="font-size:20px;margin-bottom:4px"><?= $ps['i'] ?></div>
                    <div class="stat-number" style="font-size:28px"><?= $ps['n'] ?></div>
                    <div class="stat-label"><?= $ps['l'] ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Postimet e mia -->
        <h2 style="font-family:var(--font-display);font-size:20px;font-weight:700;margin-bottom:16px">
            📝 Postimet e Mia
        </h2>

        <?php if (empty($myPosts)): ?>
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <h3>Asnjë postim akoma</h3>
                <p>Kalo tek <a href="new_post.php">Post i Ri</a> për të filluar.</p>
            </div>
        <?php else: ?>
            <div class="posts-list">
                <?php foreach ($myPosts as $p):
                    $postObj = Post::fromArray($p);
                ?>
                    <article class="post-card">
                        <div class="vote-col">
                            <span style="font-size:11px;color:var(--text-dim)">▲</span>
                            <span class="vote-score"><?= $postObj->getScore() ?></span>
                            <span style="font-size:11px;color:var(--text-dim)">▼</span>
                        </div>
                        <div class="post-content">
                            <div class="post-meta">
                                <span class="post-category"><?= e($p['category']) ?></span>
                                <span><?= formatDate($p['date']) ?></span>
                            </div>
                            <a href="post.php?id=<?= $p['id'] ?>" class="post-title">
                                <?= e($p['title']) ?>
                            </a>
                            <p class="post-excerpt"><?= e($postObj->getExcerpt()) ?></p>
                            <div class="post-footer">
                                <span class="post-stat">💬 <?= count($p['comments']) ?> komente</span>
                                <?php foreach ($p['tags'] as $tag): ?>
                                    <span class="tag"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-widget">
            <div class="widget-title">🎯 Veprime</div>
            <a href="new_post.php" class="btn btn--primary btn--full mb-2">✍️ Post i Ri</a>
            <a href="settings.php" class="btn btn--outline btn--full mb-2">⚙️ Cilësimet</a>
            <?php if (hasRole('admin')): ?>
                <a href="admin.php" class="btn btn--outline btn--full" style="border-color:var(--purple);color:var(--purple)">⚙️ Paneli Admin</a>
            <?php endif; ?>
        </div>

        <div class="sidebar-widget">
            <div class="widget-title">📊 Statistikat</div>
            <div style="font-size:14px">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-muted)">Roli</span>
                    <strong><?= ucfirst(e($userObj->getRole())) ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-muted)">Anëtar që</span>
                    <strong><?= e($userObj->getJoined()) ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0">
                    <span style="color:var(--text-muted)">Karma</span>
                    <strong style="color:var(--gold)">⭐ <?= number_format($userObj->getKarma()) ?></strong>
                </div>
            </div>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
