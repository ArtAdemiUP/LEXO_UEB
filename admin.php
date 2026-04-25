<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

if (!isLoggedIn()) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Duhet të jeni i kyçur.'];
    redirect('/pages/login.php');
}

if (!hasRole('admin')) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => '⛔ Nuk keni akses në këtë faqe. Vetëm administratorët.'];
    redirect('/pages/home.php');
}

$pageTitle = 'Paneli Admin';


$allPosts = $GLOBALS['posts'];
$allUsers = $GLOBALS['users'];

$totalVotes    = array_sum(array_map(fn($p) => $p['upvotes'] + $p['downvotes'], $allPosts));
$totalComments = array_sum(array_map(fn($p) => count($p['comments']), $allPosts));
$avgScore      = count($allPosts) > 0
    ? round(array_sum(array_map('getPostScore', $allPosts)) / count($allPosts), 1)
    : 0;


$topPosts = $allPosts;
usort($topPosts, fn($a, $b) => getPostScore($b) <=> getPostScore($a));
$topPosts = array_slice($topPosts, 0, 3);


$catStats = [];
foreach ($allPosts as $p) {
    $cat = $p['category'];
    if (!isset($catStats[$cat])) {
        $catStats[$cat] = ['posts' => 0, 'votes' => 0, 'comments' => 0];
    }
    $catStats[$cat]['posts']++;
    $catStats[$cat]['votes']    += $p['upvotes'] + $p['downvotes'];
    $catStats[$cat]['comments'] += count($p['comments']);
}


arsort($catStats); 

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="admin-title">
            ⚙️ Paneli i Administratorit
            <span class="role-badge role-badge--admin" style="font-size:13px">ADMIN</span>
        </div>
        <p style="color:var(--text-muted);margin-top:4px;font-size:14px">
            Mirë se vini, <strong><?= e($_SESSION['username']) ?></strong>. Ke kontroll të plotë mbi forumit.
        </p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <?php
        $statsData = [
            ['num' => count($allPosts),    'lbl' => 'Postime',   'icon' => '📝'],
            ['num' => count($allUsers),    'lbl' => 'Anëtarë',   'icon' => '👥'],
            ['num' => $totalComments,      'lbl' => 'Komente',   'icon' => '💬'],
            ['num' => $totalVotes,         'lbl' => 'Vota',      'icon' => '⬆️'],
            ['num' => $avgScore,           'lbl' => 'Avg Score', 'icon' => '📊'],
            ['num' => count($catStats),    'lbl' => 'Kategori',  'icon' => '📂'],
        ];
        foreach ($statsData as $s): ?>
            <div class="stat-card">
                <div style="font-size:22px;margin-bottom:4px"><?= $s['icon'] ?></div>
                <div class="stat-number"><?= $s['num'] ?></div>
                <div class="stat-label"><?= e($s['lbl']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px">

        <!-- Anëtarët -->
        <div class="card">
            <div class="widget-title" style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border)">
                👥 Menaxhimi i Anëtarëve
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Emri</th>
                        <th>Email</th>
                        <th>Roli</th>
                        <th>Karma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Itero mbi users — cikël
                    foreach ($allUsers as $u): ?>
                        <tr>
                            <td style="font-family:var(--font-mono);color:var(--text-dim)">#<?= $u['id'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <div style="width:26px;height:26px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">
                                        <?= e($u['avatar']) ?>
                                    </div>
                                    <strong><?= e($u['username']) ?></strong>
                                </div>
                            </td>
                            <td style="font-size:13px;color:var(--text-muted)"><?= e($u['email']) ?></td>
                            <td>
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="role-badge role-badge--admin">Admin</span>
                                <?php else: ?>
                                    <span class="tag">User</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-family:var(--font-mono);color:var(--gold)">⭐ <?= number_format($u['karma']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistikat sipas Kategorive -->
        <div class="card">
            <div class="widget-title" style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border)">
                📊 Statistika Kategorive
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kategoria</th>
                        <th>Postime</th>
                        <th>Vota</th>
                        <th>Komente</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($catStats as $catName => $stats):
                        $catInfo = $GLOBALS['categories'][$catName] ?? ['icon' => '📁'];
                    ?>
                        <tr>
                            <td>
                                <span><?= $catInfo['icon'] ?></span>
                                <span style="font-size:13px"> <?= e($catName) ?></span>
                            </td>
                            <td style="font-family:var(--font-mono)"><?= $stats['posts'] ?></td>
                            <td style="font-family:var(--font-mono)"><?= $stats['votes'] ?></td>
                            <td style="font-family:var(--font-mono)"><?= $stats['comments'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Postimet -->
    <div class="card" style="margin-bottom:28px">
        <div style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid var(--border)">
            🏆 Top 3 Postimet (sipas score)
        </div>
        <?php foreach ($topPosts as $i => $p):
            $postObj = Post::fromArray($p);
            $auth    = getUserById($p['author_id']);
        ?>
            <div style="display:flex;align-items:flex-start;gap:16px;padding:14px 0;border-bottom:1px solid var(--border)">
                <div style="font-family:var(--font-display);font-size:28px;font-weight:900;color:var(--accent);width:32px;text-align:center;flex-shrink:0">
                    <?= $i + 1 ?>
                </div>
                <div style="flex:1">
                    <a href="post.php?id=<?= $p['id'] ?>" style="font-weight:700;color:var(--text);font-size:15px">
                        <?= e($p['title']) ?>
                    </a>
                    <div style="font-size:13px;color:var(--text-dim);margin-top:4px">
                        nga <?= e($auth['username'] ?? '?') ?> ·
                        <?= e($p['category']) ?> ·
                        Score: <strong style="color:var(--accent)"><?= $postObj->getScore() ?></strong>
                    </div>
                </div>
                <div style="display:flex;gap:8px">
                    <a href="post.php?id=<?= $p['id'] ?>" class="btn btn--sm btn--outline">Shiko</a>
                    <?php if ($p['author_id'] !== (int)$_SESSION['user_id']): ?>
                        <button class="btn btn--sm btn--danger" onclick="confirmAction('Fshij këtë postim?')">Fshij</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Të gjitha postimet -->
    <div class="card">
        <div style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
            <span>📝 Të Gjitha Postimet</span>
            <a href="new_post.php" class="btn btn--primary btn--sm">+ Post i Ri</a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titulli</th>
                    <th>Autori</th>
                    <th>Kategoria</th>
                    <th>Score</th>
                    <th>Komente</th>
                    <th>Data</th>
                    <th>Veprime</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Sorto postimet sipas datës (descending) për tabelën
                $sortedByDate = $GLOBALS['posts'];
                usort($sortedByDate, fn($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

                foreach ($sortedByDate as $p):
                    $postObj = Post::fromArray($p);
                    $auth    = getUserById($p['author_id']);
                ?>
                    <tr>
                        <td style="font-family:var(--font-mono);color:var(--text-dim)">#<?= $p['id'] ?></td>
                        <td style="max-width:220px">
                            <a href="post.php?id=<?= $p['id'] ?>" style="color:var(--text);font-size:14px;font-weight:500">
                                <?= e(mb_strimwidth($p['title'], 0, 50, '…')) ?>
                            </a>
                        </td>
                        <td style="font-size:13px"><?= e($auth['username'] ?? '?') ?></td>
                        <td>
                            <span class="tag" style="font-size:11px"><?= e($p['category']) ?></span>
                        </td>
                        <td style="font-family:var(--font-mono);font-weight:700;color:<?= $postObj->getScore() > 0 ? 'var(--green)' : 'var(--accent-2)' ?>">
                            <?= $postObj->getScore() ?>
                        </td>
                        <td style="font-family:var(--font-mono)"><?= count($p['comments']) ?></td>
                        <td style="font-size:12px;color:var(--text-dim)"><?= e($p['date']) ?></td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <a href="post.php?id=<?= $p['id'] ?>" class="btn btn--sm btn--outline">👁</a>
                                <button class="btn btn--sm btn--danger" onclick="confirmAction('Fshij postimin #<?= $p['id'] ?>?')">🗑</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
