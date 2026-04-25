<?php
// Sigurohu sesioni është hapur
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';


$theme = $_COOKIE['lexo_theme'] ?? 'dark';
$fontSize = $_COOKIE['lexo_fontsize'] ?? 'medium';
?>
<!DOCTYPE html>
<html lang="sq" data-theme="<?= e($theme) ?>" data-fontsize="<?= e($fontSize) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' · LEXO' : 'LEXO — Forumi i Librave' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="theme-<?= e($theme) ?> font-<?= e($fontSize) ?>">

<div class="site-wrapper">
    <!-- HEADER -->
    <header class="site-header">
        <div class="header-inner">
            <a href="<?= BASE_URL ?>/pages/home.php" class="logo">
                <span class="logo-mark">L</span>
                <span class="logo-text">LEXO</span>
                <span class="logo-tagline">forum</span>
            </a>

            <nav class="main-nav">
                <a href="<?= BASE_URL ?>/pages/home.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'home.php') ? 'active' : '' ?>">
                    🏠 Kryefaqja
                </a>
                <a href="<?= BASE_URL ?>/pages/categories.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) === 'categories.php') ? 'active' : '' ?>">
                    📂 Kategoritë
                </a>
                <?php if (isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/pages/new_post.php" class="nav-link nav-link--cta">
                        ✍️ Post i Ri
                    </a>
                    <?php if (hasRole('admin')): ?>
                        <a href="<?= BASE_URL ?>/pages/admin.php" class="nav-link nav-link--admin">
                            ⚙️ Admin
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <div class="header-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <button class="user-menu-trigger" onclick="toggleUserMenu()">
                            <span class="user-avatar"><?= e($_SESSION['avatar'] ?? 'U') ?></span>
                            <span class="user-name"><?= e($_SESSION['username']) ?></span>
                            <?php if (hasRole('admin')): ?>
                                <span class="role-badge role-badge--admin">Admin</span>
                            <?php endif; ?>
                            <span class="chevron">▾</span>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="<?= BASE_URL ?>/pages/profile.php">👤 Profili Im</a>
                            <a href="<?= BASE_URL ?>/pages/settings.php">⚙️ Cilësimet</a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= BASE_URL ?>/pages/logout.php" class="dropdown-logout">🚪 Dil</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/pages/login.php" class="btn btn--outline">Hyr</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash flash--<?= e($_SESSION['flash']['type']) ?>">
            <?= e($_SESSION['flash']['msg']) ?>
            <button onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <main class="site-main">
