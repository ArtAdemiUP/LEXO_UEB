<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Models.php';
if (isLoggedIn()) redirect('/pages/home.php');
$pageTitle = 'Regjistrohu';
$errors = []; $form = ['username'=>'','email'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrf();
    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $password2 = $_POST['password2']      ?? '';
    if (!validateUsername($username)) $errors['username']  = 'Emri: 3-30 kar., shkronja/numra/nenviize.';
    if (!validateEmail($email))       $errors['email']     = 'Email i pavlefshem. Format: emri@domain.com';
    if (!validatePassword($password)) $errors['password']  = 'Fjalekalimi: min. 8 kar., 1 e madhe, 1 numer.';
    if ($password !== $password2)     $errors['password2'] = 'Fjalekalimetнuk perputhmen.';
    if (empty($errors)) {
        try {
            $um = new UserModel();
            if ($um->usernameExists($username)) {
                $errors['username'] = 'Ky emer perdoruesi eshte i zene.';
            } elseif ($um->emailExists($email)) {
                $errors['email'] = 'Ky email eshte i regjistruar tashmë.';
            } else {
                $newId = $um->create($username, $email, $password);
                session_regenerate_id(true);
                $user = $um->findById($newId);
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['avatar']   = '';
                setFlash('success','Llogaria u krijua! Mire se vini, '.$username.' 🎉');
                redirect('/pages/home.php');
            }
        } catch (RuntimeException $e) { $errors['general'] = 'Gabim gjate regjistrimit.'; }
    }
    $form = compact('username','email');
}
require_once __DIR__ . '/../includes/header.php';
?>
<div class="auth-wrap">
<div class="auth-card" style="max-width:460px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:28px">
        <div class="logo-mark" style="width:44px;height:44px;font-size:26px;border-radius:12px">L</div>
        <span style="font-family:var(--fd);font-weight:900;font-size:28px">LEXO</span>
    </div>
    <h1 class="auth-title">Krijo Llogari</h1>
    <p class="auth-sub">Bashkohu me komunitetin shqiptar te librave</p>
    <?php if (isset($errors['general'])): ?><div class="err-box">⚠️ <?= e($errors['general']) ?></div><?php endif; ?>
    <form method="POST" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(generateCsrfToken()) ?>">
        <div class="form-group">
            <label class="form-label">Emri i Perdoruesit *</label>
            <input type="text" name="username" class="form-control" value="<?= e($form['username']) ?>"
                   placeholder="emri_juaj" style="<?= isset($errors['username'])?'border-color:var(--a2)':'' ?>">
            <?php if (isset($errors['username'])): ?><div class="form-error">⚠ <?= e($errors['username']) ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" value="<?= e($form['email']) ?>"
                   placeholder="emri@domain.com" style="<?= isset($errors['email'])?'border-color:var(--a2)':'' ?>">
            <?php if (isset($errors['email'])): ?><div class="form-error">⚠ <?= e($errors['email']) ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label class="form-label">Fjalekalimi *</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 8 kar., 1 e madhe, 1 numer"
                   oninput="checkPassStrength(this.value)" style="<?= isset($errors['password'])?'border-color:var(--a2)':'' ?>">
            <div class="pass-bar" id="passBar" style="width:0;margin-top:6px"></div>
            <?php if (isset($errors['password'])): ?><div class="form-error">⚠ <?= e($errors['password']) ?></div><?php endif; ?>
        </div>
        <div class="form-group">
            <label class="form-label">Konfirmo Fjalekalimin *</label>
            <input type="password" name="password2" class="form-control" placeholder="Persërit fjalekalimin"
                   style="<?= isset($errors['password2'])?'border-color:var(--a2)':'' ?>">
            <?php if (isset($errors['password2'])): ?><div class="form-error">⚠ <?= e($errors['password2']) ?></div><?php endif; ?>
        </div>
        <button type="submit" class="btn btn--primary btn--full" style="padding:13px">🚀 Krijo Llogarine</button>
    </form>
    <p style="text-align:center;margin-top:18px;font-size:14px;color:var(--muted)">
        Ke llogari? <a href="login.php" style="color:var(--accent);font-weight:600">Hyr ketu</a>
    </p>
</div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
