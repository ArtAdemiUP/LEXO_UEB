<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Post.php';

if (!isLoggedIn()) {
    redirect('/pages/login.php');
}

$pageTitle = 'Cilësimet';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'theme') {
        $theme = $_POST['theme'] ?? 'dark';
        $validThemes = ['dark', 'light'];
        if (in_array($theme, $validThemes)) {
            setcookie('lexo_theme', $theme, time() + (365 * 86400), '/');
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Tema u ndryshua! ✓'];
        }
        redirect('/pages/settings.php');
    }

    if ($action === 'fontsize') {
        $size = $_POST['fontsize'] ?? 'medium';
        $validSizes = ['small', 'medium', 'large'];
        if (in_array($size, $validSizes)) {
            setcookie('lexo_fontsize', $size, time() + (365 * 86400), '/');
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Madhësia e shkronjave u ndryshua! ✓'];
        }
        redirect('/pages/settings.php');
    }

    if ($action === 'profile') {
        $bio   = trim($_POST['bio']   ?? '');
        $email = trim($_POST['email'] ?? '');
        $errors = [];

        // Validim email me regex
        if (!empty($email) && !validateEmail($email)) {
            $errors[] = 'Email-i nuk është i vlefshëm (p.sh. emri@domain.com).';
        }

        // Validim bio — max 500 karaktere
        if (strlen($bio) > 500) {
            $errors[] = 'Bio nuk mund të kalojë 500 karaktere.';
        }

        if (empty($errors)) {
            // Ruaj në session (simulim pa DB)
            if (!empty($email)) $_SESSION['email'] = $email;
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Profili u përditësua! ✓'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'msg' => implode(' ', $errors)];
        }
        redirect('/pages/settings.php');
    }
}

$currentTheme    = $_COOKIE['lexo_theme']    ?? 'dark';
$currentFontSize = $_COOKIE['lexo_fontsize'] ?? 'medium';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-layout--full">
    <div class="page-header">
        <h1>⚙️ Cilësimet</h1>
        <p>Personalizo përvojën tënde në LEXO</p>
    </div>

    <!-- Tema -->
    <div class="card mb-3">
        <h2 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:18px">
            🎨 Tema e Faqes
        </h2>
        <p style="font-size:14px;color:var(--text-muted);margin-bottom:14px">
            Zgjedhja ruhet si cookie dhe mbetet aktive edhe pas daljes.
        </p>
        <form method="POST">
            <input type="hidden" name="action" value="theme">
            <div class="theme-options">
                <label class="theme-option <?= $currentTheme === 'dark' ? 'selected' : '' ?>"
                       onclick="this.closest('form').querySelector('[value=dark]').checked=true">
                    <input type="radio" name="theme" value="dark" style="display:none"
                        <?= $currentTheme === 'dark' ? 'checked' : '' ?>>
                    🌙 E Errët
                </label>
                <label class="theme-option <?= $currentTheme === 'light' ? 'selected' : '' ?>"
                       onclick="this.closest('form').querySelector('[value=light]').checked=true">
                    <input type="radio" name="theme" value="light" style="display:none"
                        <?= $currentTheme === 'light' ? 'checked' : '' ?>>
                    ☀️ E Çelët
                </label>
            </div>
            <button type="submit" class="btn btn--primary mt-2">Ruaj Temën</button>
        </form>
    </div>

    
    <div class="card mb-3">
        <h2 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:18px">
            🔤 Madhësia e Shkronjave
        </h2>
        <form method="POST">
            <input type="hidden" name="action" value="fontsize">
            <div class="theme-options">
                <?php
                $sizes = ['small' => 'Vogël (14px)', 'medium' => 'Mesme (16px)', 'large' => 'Madhe (18px)'];
                foreach ($sizes as $val => $label): ?>
                    <label class="theme-option <?= $currentFontSize === $val ? 'selected' : '' ?>"
                           onclick="this.closest('form').querySelector('[value=<?= $val ?>]').checked=true">
                        <input type="radio" name="fontsize" value="<?= $val ?>" style="display:none"
                            <?= $currentFontSize === $val ? 'checked' : '' ?>>
                        <?= $label ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn--primary mt-2">Ruaj</button>
        </form>
    </div>

   
    <div class="card mb-3">
        <h2 style="font-family:var(--font-display);font-size:18px;font-weight:700;margin-bottom:18px">
            👤 Informacioni i Profilit
        </h2>
        <form method="POST" novalidate>
            <input type="hidden" name="action" value="profile">

            <div class="form-group">
                <label class="form-label">Emri i Përdoruesit</label>
                <input type="text" class="form-control" value="<?= e($_SESSION['username']) ?>" disabled
                    style="opacity:.6;cursor:not-allowed">
                <div style="font-size:12px;color:var(--text-dim);margin-top:4px">Emri i përdoruesit nuk mund të ndryshohet.</div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email *</label>
                <input type="email" id="email" name="email" class="form-control"
                    value="<?= e($_SESSION['email'] ?? '') ?>"
                    placeholder="emri@domain.com">
                <div style="font-size:12px;color:var(--text-dim);margin-top:4px">
                    Formati i vlefshëm: emri@domain.com (validim me regex)
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="bio">Bio (opsionale)</label>
                <textarea id="bio" name="bio" class="form-control" rows="3"
                    placeholder="Trego pak për veten tënd..." maxlength="500"></textarea>
                <div style="font-size:12px;color:var(--text-dim);margin-top:4px">Maksimum 500 karaktere</div>
            </div>

            <button type="submit" class="btn btn--primary">💾 Ruaj Profilin</button>
        </form>
    </div>

    <!-- Info Sesioni & Cookies -->
    <div class="card" style="background:rgba(74,144,226,.05);border-color:rgba(74,144,226,.2)">
        <h2 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin-bottom:14px;color:var(--blue)">
            🍪 Informacioni i Sesionit & Cookies
        </h2>
        <div style="font-size:13px;display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <?php
            
            $sessionInfo = [
                'User ID'     => $_SESSION['user_id']  ?? 'N/A',
                'Username'    => $_SESSION['username'] ?? 'N/A',
                'Roli'        => $_SESSION['role']     ?? 'N/A',
                'Session ID'  => substr(session_id(), 0, 16) . '…',
            ];

            
            $cookieInfo = [
                'Tema'       => $_COOKIE['lexo_theme']    ?? '(e pavendosur)',
                'Font Size'  => $_COOKIE['lexo_fontsize'] ?? '(e pavendosur)',
            ];

            foreach (array_merge($sessionInfo, $cookieInfo) as $key => $val): ?>
                <div style="background:var(--bg);border-radius:6px;padding:10px">
                    <div style="font-size:11px;color:var(--text-dim);text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px"><?= e($key) ?></div>
                    <div style="font-family:var(--font-mono);color:var(--blue)"><?= e((string)$val) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border)">
            <a href="logout.php" class="btn btn--danger btn--sm">🚪 Dil nga Llogaria</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
