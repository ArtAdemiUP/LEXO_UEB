<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

$pageTitle = 'Kryefaqja';

// Merr të gjitha postimet
$allPosts = $GLOBALS['posts'];

// Sortimi — nymerici & associative array
$sort = $_GET['sort'] ?? 'hot';
$validSorts = ['hot', 'new', 'top'];
if (!in_array($sort, $validSorts)) $sort = 'hot';

// Filter per kategori
$catFilter = $_GET['cat'] ?? '';

// Apliko filter
if ($catFilter) {
    $allPosts = array_filter($allPosts, fn($p) => $p['category'] === $catFilter);
}

// Apliko sortim me funksione array
switch ($sort) {
    case 'hot':
        // Score + recency
        usort($allPosts, function($a, $b) {
            $scoreA = getPostScore($a) + (strtotime($a['date']) / 86400);
            $scoreB = getPostScore($b) + (strtotime($b['date']) / 86400);
            return $scoreB <=> $scoreA;
        });
        break;

    case 'new':
        usort($allPosts, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));
        break;

    case 'top':
        usort($allPosts, fn($a, $b) => getPostScore($b) <=> getPostScore($a));
        break;
}

// Konverto në objekte Post
$postObjects = array_map(fn($p) => Post::fromArray($p), $allPosts);

// Statistika
$totalPosts = count($GLOBALS['posts']);
$totalUsers = count($GLOBALS['users']);
$totalComments = array_sum(array_map(fn($p) => count($p['comments']), $GLOBALS['posts']));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-layout">
    <!-- MAIN FEED -->
    <div>
        <div class="page-header">
            <h1>📖 Forumi LEXO</h1>
            <p>Diskutime mbi libra, letërsi dhe kulturë shqiptare e botërore</p>
        </div>

        <!-- Sort Bar -->
        <div class="sort-bar">
            <span class="sort-label">Rendit:</span>
            <a href="?sort=hot<?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?>"
               class="sort-btn <?= $sort === 'hot' ? 'active' : '' ?>">🔥 Hot</a>
            <a href="?sort=new<?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?>"
               class="sort-btn <?= $sort === 'new' ? 'active' : '' ?>">✨ I Ri</a>
            <a href="?sort=top<?= $catFilter ? '&cat='.urlencode($catFilter) : '' ?>"
               class="sort-btn <?= $sort === 'top' ? 'active' : '' ?>">⬆️ Top</a>

            <?php if ($catFilter): ?>
                <span style="margin-left:auto; font-size:13px; color:var(--text-muted)">
                    Filtruar: <strong><?= e($catFilter) ?></strong>
                    <a href="?" style="margin-left:6px; font-size:12px">× Hiqe</a>
                </span>
            <?php endif; ?>
        </div>

        <!-- Posts List -->
        <div class="posts-list">
            <?php if (empty($postObjects)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h3>Asnjë postim</h3>
                    <p>Nuk ka postime për këtë filtër. Bëhu i pari!</p>
                </div>
            <?php else: ?>
                <?php foreach ($postObjects as $post): ?>
                    <?php
                    $author = getUserById($post->getAuthorId());
                    $isAdminPost = ($author && $author['role'] === 'admin');
                    ?>
                    <article class="post-card <?= $isAdminPost ? 'post-card--pinned' : '' ?>">
                        <!-- Vote Column -->
                        <div class="vote-col">
                            <button class="vote-btn upvote" title="Voto lart">▲</button>
                            <span class="vote-score"><?= $post->getScore() ?></span>
                            <button class="vote-btn downvote" title="Voto poshtë">▼</button>
                        </div>

                        <!-- Post Content -->
                        <div class="post-content">
                            <div class="post-meta">
                                <?php if ($isAdminPost): ?>
                                    <span style="color:var(--gold); font-weight:700; font-size:11px">📌 NJOFTIM</span>
                                <?php endif; ?>
                                <a href="?cat=<?= urlencode($post->getCategory()) ?>&sort=<?= e($sort) ?>"
                                   class="post-category"><?= e($post->getCategory()) ?></a>
                                <span>nga <strong><?= e($author['username'] ?? 'I panjohur') ?></strong></span>
                                <span><?= formatDate($post->getDate()) ?></span>
                            </div>

                            <a href="post.php?id=<?= $post->getId() ?>" class="post-title">
                                <?= e($post->getTitle()) ?>
                            </a>

                            <p class="post-excerpt"><?= e($post->getExcerpt()) ?></p>

                            <div class="post-footer">
                                <span class="post-stat">
                                    💬 <?= $post->getCommentCount() ?> komente
                                </span>
                                <?php foreach ($post->getTags() as $tag): ?>
                                    <span class="tag"><?= e($tag) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <!-- Community Info -->
        <div class="sidebar-widget">
            <div class="widget-title">ℹ️ Rreth LEXO</div>
            <p style="font-size:14px; color:var(--text-muted); line-height:1.6; margin-bottom:14px">
                LEXO është komuniteti shqiptar për dashamirësit e librave.
                Diskuto, rekomando dhe zbulo botë të reja.
            </p>
            <!-- Statistika me cikël -->
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-bottom:14px">
                <?php
                $stats = [
                    ['num' => $totalPosts,    'lbl' => 'Postime'],
                    ['num' => $totalUsers,    'lbl' => 'Anëtarë'],
                    ['num' => $totalComments, 'lbl' => 'Komente'],
                ];
                foreach ($stats as $stat): ?>
                    <div style="text-align:center; background:var(--bg); border-radius:8px; padding:10px 4px">
                        <div style="font-family:var(--font-display);font-size:22px;font-weight:900;color:var(--accent)">
                            <?= $stat['num'] ?>
                        </div>
                        <div style="font-size:11px;color:var(--text-dim);font-weight:600"><?= $stat['lbl'] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (isLoggedIn()): ?>
                <a href="new_post.php" class="btn btn--primary btn--full">✍️ Post i Ri</a>
            <?php else: ?>
                <a href="login.php" class="btn btn--primary btn--full">🔑 Kyçu për të postuar</a>
            <?php endif; ?>
        </div>

        <!-- Kategoritë -->
        <div class="sidebar-widget">
            <div class="widget-title">📂 Kategoritë</div>
            <ul class="category-list">
                <?php
                // Numëro postimet për çdo kategori — itero
                $catCounts = [];
                foreach ($GLOBALS['posts'] as $p) {
                    $catCounts[$p['category']] = ($catCounts[$p['category']] ?? 0) + 1;
                }

                foreach ($GLOBALS['categories'] as $name => $info):
                    $count = $catCounts[$name] ?? 0;
                ?>
                    <li>
                        <a href="?cat=<?= urlencode($name) ?>&sort=<?= e($sort) ?>">
                            <?= $info['icon'] ?> <?= e($name) ?>
                        </a>
                        <span class="cat-count"><?= $count ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Top Contributors -->
        <div class="sidebar-widget">
            <div class="widget-title">🏆 Top Kontribues</div>
            <?php
            // Sorto userët sipas karma — array_multisort
            $sortedUsers = $GLOBALS['users'];
            usort($sortedUsers, fn($a, $b) => $b['karma'] <=> $a['karma']);
            $rank = 1;
            foreach ($sortedUsers as $u): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="font-size:12px;color:var(--text-dim);font-family:var(--font-mono);width:18px"><?= $rank++ ?></span>
                    <div class="user-avatar" style="width:30px;height:30px;font-size:13px;flex-shrink:0;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                        <?= e($u['avatar']) ?>
                    </div>
                    <span style="font-size:14px;font-weight:500;flex:1"><?= e($u['username']) ?></span>
                    <span style="font-size:12px;color:var(--gold);font-weight:700">⭐ <?= number_format($u['karma']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
