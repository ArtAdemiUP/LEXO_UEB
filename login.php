<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

// Nëse tashmë është i kyçur, ridrejto
if (isLoggedIn()) {
    redirect('/pages/home.php');
}

$pageTitle = 'Kyçu';
$errors    = [];
$formData  = ['username' => '', 'email' => ''];

// Procesimi i formës
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // ---- VALIDIMI SERVER-SIDE me regex ----

    // 1. Kontrollo username me regex
    if (empty($username)) {
        $errors['username'] = 'Emri i përdoruesit është i detyrueshëm.';
    } elseif (!validateUsername($username)) {
        $errors['username'] = 'Emri i përdoruesit: vetëm shkronja, numra, nënvizë (3-20 karaktere).';
    }

    // 2. Kontrollo fjalëkalimin
    if (empty($password)) {
        $errors['password'] = 'Fjalëkalimi është i detyrueshëm.';
    } elseif (!validatePassword($password)) {
        $errors['password'] = 'Fjalëkalimi duhet të ketë min. 6 karaktere dhe 1 numër.';
    }

    // 3. Nëse validimi kalon, kontrollo kredencialet
    if (empty($errors)) {
        $user = getUserByUsername($username);

        if ($user && $user['password'] === $password) {
            // Suksesi — vendos session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['avatar']   = $user['avatar'];
            $_SESSION['email']    = $user['email'];

            // Cookie personalizim (30 ditë)
            if (!isset($_COOKIE['lexo_theme'])) {
                setcookie('lexo_theme', 'dark', time() + (30 * 86400), '/');
            }
            if (!isset($_COOKIE['lexo_fontsize'])) {
                setcookie('lexo_fontsize', 'medium', time() + (30 * 86400), '/');
            }

            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Mirë se vini, ' . $user['username'] . '! 👋'];
            redirect('/pages/home.php');
        } else {
            $errors['general'] = 'Kredencialet janë të gabuara. Provoni përsëri.';
        }
    }

    $formData['username'] = e($username);
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="login-wrap">
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <div class="logo-mark" style="width:44px;height:44px;font-size:26px;border-radius:12px">L</div>
            <span class="logo-text" style="font-family:var(--font-display);font-weight:900;font-size:28px">LEXO</span>
        </div>

        <h1 class="login-title">Mirë se vini!</h1>
        <p class="login-subtitle">Kyçuni për të marrë pjesë në diskutime</p>

        <!-- Gabim i përgjithshëm -->
        <?php if (isset($errors['general'])): ?>
            <div class="flash flash--error" style="border-radius:8px;margin-bottom:20px">
                ⚠️ <?= e($errors['general']) ?>
            </div>
        <?php endif; ?>

        <!-- Forma -->
        <form method="POST" action="login.php" novalidate>

            <div class="form-group">
                <label class="form-label" for="username">Emri i Përdoruesit</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control <?= isset($errors['username']) ? 'error' : '' ?>"
                    value="<?= $formData['username'] ?>"
                    placeholder="emri_juaj"
                    autocomplete="username"
                    style="<?= isset($errors['username']) ? 'border-color:var(--accent-2)' : '' ?>"
                >
                <?php if (isset($errors['username'])): ?>
                    <div class="form-error">⚠ <?= e($errors['username']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Fjalëkalimi</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    style="<?= isset($errors['password']) ? 'border-color:var(--accent-2)' : '' ?>"
                >
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error">⚠ <?= e($errors['password']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn--primary btn--full" style="margin-top:8px;padding:13px">
                🔑 Kyçu
            </button>
        </form>

        <!-- Kredencialet Demo -->
        <div style="margin-top:28px;padding:18px;background:var(--bg);border-radius:10px;border:1px solid var(--border)">
            <div style="font-size:12px;font-weight:700;color:var(--text-dim);text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px">
                Llogari Demo
            </div>
            <?php
            // Cikël mbi userat — array iteration
            $demoUsers = [
                ['username' => 'admin',   'password' => 'admin123',   'role' => 'Admin',    'color' => 'var(--purple)'],
                ['username' => 'artan',   'password' => 'artan123',   'role' => 'Përdorues','color' => 'var(--accent)'],
                ['username' => 'blerina', 'password' => 'blerina123', 'role' => 'Përdorues','color' => 'var(--blue)'],
            ];
            foreach ($demoUsers as $demo): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <div>
                        <span style="font-weight:600;font-size:13px"><?= e($demo['username']) ?></span>
                        <span style="font-size:11px;background:rgba(0,0,0,.2);padding:1px 8px;border-radius:99px;margin-left:6px;color:<?= $demo['color'] ?>;font-weight:600">
                            <?= $demo['role'] ?>
                        </span>
                    </div>
                    <code style="font-size:12px;color:var(--text-dim);font-family:var(--font-mono)"><?= e($demo['password']) ?></code>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
