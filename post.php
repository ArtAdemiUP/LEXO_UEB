<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

$id = (int)($_GET['id'] ?? 0);
$postData = getPostById($id);

if (!$postData) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Postimi nuk u gjet.'];
    redirect('/pages/home.php');
}

$post   = Post::fromArray($postData);
$author = getUserById($post->getAuthorId());

$pageTitle = $post->getTitle();


$commentError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $commentText = trim($_POST['comment'] ?? '');
   
    if (empty($commentText)) {
        $commentError = 'Komenti nuk mund të jetë bosh.';
    } elseif (strlen($commentText) < 5) {
        $commentError = 'Komenti duhet të ketë të paktën 5 karaktere.';
    } elseif (preg_match('/<script|<iframe|javascript:/i', $commentText)) {
        $commentError = 'Komenti përmban kod të palejueshëm.';
    } else {
        
        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Komenti juaj u shtua! ✓'];
        redirect('/pages/post.php?id=' . $id);
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-layout">
    <div>
        <!-- Breadcrumb -->
        <div style="font-size:13px;color:var(--text-dim);margin-bottom:20px;display:flex;align-items:center;gap:8px">
            <a href="home.php" style="color:var(--text-muted)">Kryefaqja</a>
            <span>›</span>
            <a href="home.php?cat=<?= urlencode($post->getCategory()) ?>" style="color:var(--text-muted)"><?= e($post->getCategory()) ?></a>
            <span>›</span>
            <span><?= e(mb_strimwidth($post->getTitle(), 0, 45, '…')) ?></span>
        </div>

        <!-- Post Header -->
        <div class="post-detail-header">
            <div class="post-meta" style="margin-bottom:10px">
                <a href="home.php?cat=<?= urlencode($post->getCategory()) ?>" class="post-category">
                    <?= e($post->getCategory()) ?>
                </a>
                <span>nga <strong><?= e($author['username'] ?? 'I panjohur') ?></strong></span>
                <span><?= formatDate($post->getDate()) ?></span>
                <?php if ($author && $author['role'] === 'admin'): ?>
                    <span class="role-badge role-badge--admin">Admin</span>
                <?php endif; ?>
            </div>

            <h1 class="post-detail-title"><?= e($post->getTitle()) ?></h1>

            <!-- Tags -->
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                <?php foreach ($post->getTags() as $tag): ?>
                    <span class="tag"><?= e($tag) ?></span>
                <?php endforeach; ?>
            </div>

            <!-- Vote & Stats Bar -->
            <div style="display:flex;align-items:center;gap:20px;padding:14px 18px;background:var(--bg-card);border:1px solid var(--border);border-radius:10px;margin-bottom:20px">
                <div style="display:flex;align-items:center;gap:10px">
                    <button class="vote-btn upvote" style="font-size:20px">▲</button>
                    <span style="font-family:var(--font-mono);font-size:18px;font-weight:700;color:var(--accent)">
                        <?= $post->getScore() ?>
                    </span>
                    <button class="vote-btn downvote" style="font-size:20px">▼</button>
                </div>
                <div style="display:flex;align-items:center;gap:6px;color:var(--text-muted);font-size:14px">
                    <span>⬆️ <?= $post->getUpvotes() ?></span>
                    <span style="color:var(--text-dim)">·</span>
                    <span>⬇️ <?= $post->getDownvotes() ?></span>
                    <span style="color:var(--text-dim)">·</span>
                    <span>💬 <?= $post->getCommentCount() ?> komente</span>
                </div>
            </div>
        </div>

        <!-- Post Body -->
        <div class="post-detail-body">
            <?= nl2br(e($post->getContent())) ?>
        </div>

        <!-- Kontrolla Admin -->
        <?php if (hasRole('admin')): ?>
            <div style="background:rgba(155,89,182,.1);border:1px solid rgba(155,89,182,.3);border-radius:10px;padding:16px 20px;margin-bottom:24px;display:flex;align-items:center;gap:14px">
                <span style="color:var(--purple);font-weight:700;font-size:14px">⚙️ Kontrolla Admin</span>
                <a href="admin.php" class="btn btn--sm btn--outline" style="border-color:var(--purple);color:var(--purple)">🗂 Paneli</a>
                <button class="btn btn--sm btn--danger" onclick="confirmAction('Fshij këtë postim?')">🗑 Fshij Postimin</button>
            </div>
        <?php endif; ?>

        <!-- Komentet -->
        <section class="comments-section">
            <h2 class="comment-title">
                💬 <?= $post->getCommentCount() ?> Komente
            </h2>

            <?php if ($post->getCommentCount() === 0): ?>
                <div class="no-comments">
                    Asnjë koment akoma. Bëhu i pari që komenton!
                </div>
            <?php else: ?>
                <?php
                // Cikël mbi komentet — itero
                $comments = $post->getComments();
                foreach ($comments as $comment):
                    $commentUser = getUserByUsername($comment['author']);
                ?>
                    <div class="comment">
                        <div class="comment-avatar">
                            <?= e($commentUser['avatar'] ?? strtoupper(substr($comment['author'], 0, 1))) ?>
                        </div>
                        <div>
                            <div style="display:flex;align-items:center;gap:10px">
                                <span class="comment-author"><?= e($comment['author']) ?></span>
                                <?php if ($commentUser && $commentUser['role'] === 'admin'): ?>
                                    <span class="role-badge role-badge--admin">Admin</span>
                                <?php endif; ?>
                                <span class="comment-date"><?= formatDate($comment['date']) ?></span>
                            </div>
                            <p class="comment-text"><?= e($comment['text']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Forma Komentit -->
            <?php if (isLoggedIn()): ?>
                <div style="margin-top:24px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px;padding:20px">
                    <h3 style="font-size:15px;font-weight:700;margin-bottom:14px">✍️ Shto koment</h3>
                    <?php if ($commentError): ?>
                        <div class="flash flash--error" style="border-radius:8px;margin-bottom:14px">⚠️ <?= e($commentError) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="form-group" style="margin-bottom:12px">
                            <textarea name="comment" class="form-control" rows="3"
                                placeholder="Shkruaj mendimin tënd..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn--primary">💬 Posto Komentin</button>
                    </form>
                </div>
            <?php else: ?>
                <div style="margin-top:24px;text-align:center;padding:24px;background:var(--bg-card);border:1px dashed var(--border);border-radius:12px">
                    <p style="color:var(--text-muted);margin-bottom:14px">Kyçu për të komentuar</p>
                    <a href="login.php" class="btn btn--primary">🔑 Kyçu</a>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-widget">
            <div class="widget-title">👤 Autori</div>
            <?php if ($author): ?>
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
                    <div class="user-avatar" style="width:40px;height:40px;font-size:18px;flex-shrink:0;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700">
                        <?= e($author['avatar']) ?>
                    </div>
                    <div>
                        <div style="font-weight:700"><?= e($author['username']) ?></div>
                        <div style="font-size:12px;color:var(--text-dim)"><?= e($author['role']) ?></div>
                    </div>
                </div>
                <p style="font-size:13px;color:var(--text-muted)"><?= e($author['bio']) ?></p>
                <div class="karma-badge mt-2">⭐ <?= number_format($author['karma']) ?> karma</div>
            <?php endif; ?>
        </div>

        <div class="sidebar-widget">
            <div class="widget-title">📂 Kategoria</div>
            <p style="font-size:14px;margin-bottom:12px">
                <?= e($GLOBALS['categories'][$post->getCategory()]['icon'] ?? '📁') ?>
                <strong><?= e($post->getCategory()) ?></strong>
            </p>
            <a href="home.php?cat=<?= urlencode($post->getCategory()) ?>" class="btn btn--outline btn--sm btn--full">
                Shiko të gjitha
            </a>
        </div>
    </aside>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
