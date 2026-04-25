<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

// Vetëm user-ët e kyçur mund të postojnë
if (!isLoggedIn()) {
    $_SESSION['flash'] = ['type' => 'error', 'msg' => 'Duhet të jeni i kyçur për të postuar.'];
    redirect('/pages/login.php');
}

$pageTitle = 'Post i Ri';
$errors    = [];
$formData  = ['title' => '', 'content' => '', 'category' => '', 'tags' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']    ?? '');
    $content  = trim($_POST['content']  ?? '');
    $category = trim($_POST['category'] ?? '');
    $tagsRaw  = trim($_POST['tags']     ?? '');

    // ---- VALIDIMI SERVER-SIDE ----

    // 1. Titulli - me regex
    if (empty($title)) {
        $errors['title'] = 'Titulli është i detyrueshëm.';
    } elseif (!validatePostTitle($title)) {
        $errors['title'] = 'Titulli duhet të jetë 5-200 karaktere dhe pa karaktere të veçanta < > { }.';
    }

    // 2. Përmbajtja
    if (empty($content)) {
        $errors['content'] = 'Përmbajtja është e detyrueshme.';
    } elseif (strlen($content) < 20) {
        $errors['content'] = 'Përmbajtja duhet të ketë të paktën 20 karaktere.';
    } elseif (strlen($content) > 10000) {
        $errors['content'] = 'Përmbajtja nuk mund të kalojë 10.000 karaktere.';
    }

    // 3. Kategoria
    if (empty($category) || !array_key_exists($category, $GLOBALS['categories'])) {
        $errors['category'] = 'Zgjidh një kategori të vlefshme.';
    }

    // Procesimi tags — array manipulation
    $tags = [];
    if (!empty($tagsRaw)) {
        $rawTagsArr = explode(',', $tagsRaw);
        foreach ($rawTagsArr as $t) {
            $t = trim($t);
            // Validim tag me regex — vetëm shkronja dhe numra
            if (preg_match('/^[\w\s\-]{1,30}$/u', $t) && strlen($t) > 0) {
                $tags[] = $t;
            }
        }
        $tags = array_slice(array_unique($tags), 0, 5); // max 5 tags
    }

    if (empty($errors)) {
        // Krijo Post objekt
        $newId = count($GLOBALS['posts']) + 1;
        try {
            $newPost = new Post(
                $newId,
                $title,
                $content,
                (int)$_SESSION['user_id'],
                $category,
                $tags
            );

            // Në produksion: ruaj në DB
            // Tani simulojmë suksesin
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Postimi juaj u krijua me sukses! ✓'];
            redirect('/pages/home.php');

        } catch (InvalidArgumentException $e) {
            $errors['general'] = $e->getMessage();
        }
    }

    // Ruaj vlerat e formës
    $formData = ['title' => $title, 'content' => $content, 'category' => $category, 'tags' => $tagsRaw];
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-layout--full">
    <div class="page-header">
        <h1>✍️ Post i Ri</h1>
        <p>Ndaj mendimin, pyetjen ose rekomandimin tënd me komunitetin</p>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="flash flash--error" style="border-radius:8px;margin-bottom:20px">⚠️ <?= e($errors['general']) ?></div>
    <?php endif; ?>

    <div class="card" style="border-radius:16px;padding:32px">
        <form method="POST" action="new_post.php" novalidate>

            <!-- Titulli -->
            <div class="form-group">
                <label class="form-label" for="title">Titulli *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    class="form-control"
                    value="<?= e($formData['title']) ?>"
                    placeholder="p.sh. Librat më të mirë të vitit 2025..."
                    maxlength="200"
                    style="<?= isset($errors['title']) ? 'border-color:var(--accent-2)' : '' ?>"
                >
                <?php if (isset($errors['title'])): ?>
                    <div class="form-error">⚠ <?= e($errors['title']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Kategoria -->
            <div class="form-group">
                <label class="form-label" for="category">Kategoria *</label>
                <select
                    id="category"
                    name="category"
                    class="form-control"
                    style="<?= isset($errors['category']) ? 'border-color:var(--accent-2)' : '' ?>"
                >
                    <option value="">— Zgjidh kategori —</option>
                    <?php foreach ($GLOBALS['categories'] as $name => $info): ?>
                        <option value="<?= e($name) ?>" <?= ($formData['category'] === $name) ? 'selected' : '' ?>>
                            <?= $info['icon'] ?> <?= e($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category'])): ?>
                    <div class="form-error">⚠ <?= e($errors['category']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Përmbajtja -->
            <div class="form-group">
                <label class="form-label" for="content">Përmbajtja *</label>
                <textarea
                    id="content"
                    name="content"
                    class="form-control"
                    rows="8"
                    placeholder="Shkruaj postimin tënd këtu... (min. 20 karaktere)"
                    maxlength="10000"
                    style="<?= isset($errors['content']) ? 'border-color:var(--accent-2)' : '' ?>"
                ><?= e($formData['content']) ?></textarea>
                <?php if (isset($errors['content'])): ?>
                    <div class="form-error">⚠ <?= e($errors['content']) ?></div>
                <?php endif; ?>
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label class="form-label" for="tags">Tags <span style="font-weight:400;text-transform:none;font-size:12px">(opsionale, ndaj me presje)</span></label>
                <input
                    type="text"
                    id="tags"
                    name="tags"
                    class="form-control"
                    value="<?= e($formData['tags']) ?>"
                    placeholder="p.sh. Kadare, Roman, Histori"
                >
                <div style="font-size:12px;color:var(--text-dim);margin-top:5px">Maksimum 5 tags, deri 30 karaktere secila</div>
            </div>

            <hr class="divider">

            <div style="display:flex;align-items:center;gap:14px">
                <button type="submit" class="btn btn--primary" style="padding:12px 28px">
                    🚀 Publiko Postimin
                </button>
                <a href="home.php" class="btn btn--outline">✕ Anulo</a>
                <span style="font-size:13px;color:var(--text-dim);margin-left:auto">
                    Postoni si <strong><?= e($_SESSION['username']) ?></strong>
                </span>
            </div>
        </form>
    </div>

    <!-- Rregulla -->
    <div style="margin-top:20px;padding:18px;background:var(--bg-card);border:1px solid var(--border);border-radius:12px">
        <div style="font-size:12px;font-weight:700;color:var(--text-dim);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">📋 Rregullat e Komunitetit</div>
        <?php
        $rules = [
            'Respektoni njëri-tjetrin — kritikat duhet të jenë konstruktive',
            'Postimet duhet të jenë relevante me librat dhe kulturën',
            'Mos postoni spam ose reklama',
            'Citoni burimet kur diskutoni fakte',
        ];
        // Cikël mbi rregullat — numeric array
        foreach ($rules as $i => $rule): ?>
            <div style="display:flex;gap:10px;padding:6px 0;font-size:13px;color:var(--text-muted)">
                <span style="color:var(--accent);font-weight:700"><?= $i+1 ?>.</span>
                <span><?= e($rule) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
