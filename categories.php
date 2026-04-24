<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

$pageTitle = 'Kategoritë';

// Numëro postimet per kategori
$catCounts = [];
foreach ($GLOBALS['posts'] as $p) {
    $cat = $p['category'];
    if (!isset($catCounts[$cat])) {
        $catCounts[$cat] = ['posts' => 0, 'comments' => 0, 'votes' => 0];
    }
    $catCounts[$cat]['posts']++;
    $catCounts[$cat]['comments'] += count($p['comments']);
    $catCounts[$cat]['votes']    += $p['upvotes'] + $p['downvotes'];
}

// Shfaq postimet per kategori të zgjedhur
$activeCat = $_GET['cat'] ?? '';
$catPosts  = [];
if ($activeCat && array_key_exists($activeCat, $GLOBALS['categories'])) {
    $catPosts = array_filter($GLOBALS['posts'], fn($p) => $p['category'] === $activeCat);
    usort($catPosts, fn($a, $b) => getPostScore($b) <=> getPostScore($a));
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>📂 Kategoritë</h1>
        <p>Zgjidh një kategori për të eksploruar postimet</p>
    </div>

    <!-- Grid Kategorive -->
    <div class="categories-grid" style="margin-bottom:40px">
        <?php foreach ($GLOBALS['categories'] as $name => $info):
            $stats = $catCounts[$name] ?? ['posts' => 0, 'comments' => 0, 'votes' => 0];
            $isActive = ($activeCat === $name);
        ?>
            <a href="categories.php?cat=<?= urlencode($name) ?>"
               class="category-card <?= $isActive ? 'active' : '' ?>"
               style="<?= $isActive ? 'border-color:'.$info['color'].';box-shadow:0 0 0 2px '.$info['color'] : '' ?>">
                <div class="cat-icon"><?= $info['icon'] ?></div>
                <div class="cat-name"><?= e($name) ?></div>
                <div class="cat-desc"><?= e($info['desc']) ?></div>
                <div style="display:flex;gap:12px;margin-top:12px;font-size:12px;color:var(--text-dim)">
                    <span>📝 <?= $stats['posts'] ?> postime</span>
                    <span>💬 <?= $stats['comments'] ?> komente</span>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Postimet e kategorisë -->
    <?php if ($activeCat && !empty($catPosts)): ?>
        <hr class="divider">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <h2 style="font-family:var(--font-display);font-size:22px;font-weight:700">
                <?= $GLOBALS['categories'][$activeCat]['icon'] ?> <?= e($activeCat) ?>
            </h2>
            <a href="categories.php" style="font-size:13px;color:var(--text-muted)">× Mbyll</a>
        </div>
        <div class="posts-list">
            <?php foreach ($catPosts as $p):
                $postObj = Post::fromArray($p);
                $author  = getUserById($p['author_id']);
            ?>
                <article class="post-card">
                    <div class="vote-col">
                        <button class="vote-btn upvote">▲</button>
                        <span class="vote-score"><?= $postObj->getScore() ?></span>
                        <button class="vote-btn downvote">▼</button>
                    </div>
                    <div class="post-content">
                        <div class="post-meta">
                            <span>nga <strong><?= e($author['username'] ?? '?') ?></strong></span>
                            <span><?= formatDate($p['date']) ?></span>
                        </div>
                        <a href="post.php?id=<?= $p['id'] ?>" class="post-title"><?= e($p['title']) ?></a>
                        <p class="post-excerpt"><?= e($postObj->getExcerpt()) ?></p>
                        <div class="post-footer">
                            <span class="post-stat">💬 <?= count($p['comments']) ?></span>
                            <?php foreach ($p['tags'] as $tag): ?>
                                <span class="tag"><?= e($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php elseif ($activeCat && empty($catPosts)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>Asnjë postim në këtë kategori</h3>
            <a href="new_post.php" class="btn btn--primary mt-2">✍️ Bëhu i pari</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
