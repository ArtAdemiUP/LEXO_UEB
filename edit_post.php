<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Models.php';
if (!isLoggedIn()) { setFlash('error','Duhet te jeni i kycur.'); redirect('/pages/login.php'); }
$id = (int)($_GET['id'] ?? 0);
$pm = new PostModel(); $catModel = new CategoryModel();
try {
    $post = $pm->getById($id);
    if (!$post) { setFlash('error','Postimi nuk u gjet.'); redirect('/pages/home.php'); }
    if ((int)$post['author_id'] !== (int)$_SESSION['user_id'] && !hasRole('admin')) { setFlash('error','Nuk keni leje ta editoni.'); redirect('/pages/home.php'); }
    $categories = $catModel->getAll();
} catch (RuntimeException $e) { setFlash('error','Gabim teknik.'); redirect('/pages/home.php'); }
$pageTitle = 'Edito Postimin'; $errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $title   = trim($_POST['title']        ?? '');
    $content = trim($_POST['content']      ?? '');
    $catId   = (int)($_POST['category_id'] ?? 0);
    if (!validatePostTitle($title)) $errors['title']   = 'Titulli i pavlefshem (5-300 kar., pa < > { }).';
    if (strlen($content) < 20)      $errors['content'] = 'Min. 20 karaktere.';
    if (strlen($content) > 15000)   $errors['content'] = 'Maks. 15,000 karaktere.';
    if ($catId <= 0)                $errors['category_id'] = 'Zgjidh kategori.';
    if (empty($errors)) {
        try {
            $pm->update($id, $title, $content, $catId, (int)$_SESSION['user_id']);
            setFlash('success','Postimi u perditesua! ✓');
            redirect('/pages/post.php?id='.$id);
        } catch (RuntimeException $e) { $errors['general'] = 'Gabim gjate ruajtjes.'; }
    }
} else { $title=$post['title']; $content=$post['content']; $catId=$post['category_id']; }
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-full">
    <div class="page-header"><h1>✏️ Edito Postimin</h1></div>
    <?php if (isset($errors['general'])): ?><div class="err-box mb-2">⚠️ <?= e($errors['general']) ?></div><?php endif; ?>
    <div class="card" style="padding:32px">
        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
            <div class="form-group">
                <label class="form-label">Titulli *</label>
                <input type="text" name="title" class="form-control"
                       value="<?= e($_POST['title'] ?? $title) ?>" maxlength="300"
                       style="<?= isset($errors['title'])?'border-color:var(--a2)':'' ?>">
                <?php if (isset($errors['title'])): ?><div class="form-error">⚠ <?= e($errors['title']) ?></div><?php endif; ?>
            </div>
            <div class="form-group">
                <label class="form-label">Kategoria *</label>
                <select name="category_id" class="form-control">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int)($_POST['category_id']??$catId)===(int)$c['id']?'selected':'' ?>>
                            <?= e($c['icon'].' '.$c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Permbajtja *</label>
                <textarea name="content" class="form-control" rows="10" maxlength="15000"
                          style="<?= isset($errors['content'])?'border-color:var(--a2)':'' ?>"><?= e($_POST['content'] ?? $content) ?></textarea>
                <?php if (isset($errors['content'])): ?><div class="form-error">⚠ <?= e($errors['content']) ?></div><?php endif; ?>
            </div>
            <div style="display:flex;gap:12px">
                <button type="submit" class="btn btn--primary" style="padding:12px 28px">💾 Ruaj Ndryshimet</button>
                <a href="post.php?id=<?= $id ?>" class="btn btn--outline">✕ Anulo</a>
            </div>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
