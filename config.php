<?php
// =============================================
// LEXO Forum - Konfigurimi Global
// =============================================

define('APP_NAME', 'LEXO');
define('APP_VERSION', '1.0.0');
define('BASE_URL', '/lexo');

// Kredencialet e hardcoded (pa databazë)
$GLOBALS['users'] = [
    [
        'id'       => 1,
        'username' => 'admin',
        'password' => 'admin123',
        'email'    => 'admin@lexo.al',
        'role'     => 'admin',
        'avatar'   => 'A',
        'bio'      => 'Administratori i LEXO',
        'joined'   => '2024-01-01',
        'karma'    => 9999,
    ],
    [
        'id'       => 2,
        'username' => 'artan',
        'password' => 'artan123',
        'email'    => 'artan@lexo.al',
        'role'     => 'user',
        'avatar'   => 'R',
        'bio'      => 'Lexues i apasionuar i librave.',
        'joined'   => '2024-03-15',
        'karma'    => 420,
    ],
    [
        'id'       => 3,
        'username' => 'blerina',
        'password' => 'blerina123',
        'email'    => 'blerina@lexo.al',
        'role'     => 'user',
        'avatar'   => 'B',
        'bio'      => 'Shkrimtare dhe kritike letrare.',
        'joined'   => '2024-05-20',
        'karma'    => 785,
    ],
];


$GLOBALS['posts'] = [
    [
        'id'        => 1,
        'title'     => 'Libri "Gjenerali i Ushtrisë së Vdekur" - Diskutim',
        'content'   => 'Sapo mbarova librin e Ismail Kadaresë. Mendoj se është një nga veprat më të fuqishme të letërsisë shqipe. Si ju duket përshkrimi i luftës dhe kujtesës kolektive? Kuptimi i saj nuk është thjesht historik por edhe filozofik. Jam i mahnitur me stilin e Kadaresë dhe mënyrën si ndërthur realitetin me mitin.',
        'author_id' => 2,
        'category'  => 'Letërsi Shqipe',
        'tags'      => ['Kadare', 'Roman', 'Histori'],
        'upvotes'   => 147,
        'downvotes' => 8,
        'date'      => '2025-04-20',
        'comments'  => [
            ['author' => 'blerina', 'text' => 'Dakord plotësisht! Kadare ka një zë unik në letërsinë botërore.', 'date' => '2025-04-20'],
            ['author' => 'admin',   'text' => 'Një nga librat që duhet të lexojë çdo shqiptar.',            'date' => '2025-04-21'],
        ],
    ],
    [
        'id'        => 2,
        'title'     => 'Rekomandime për Filozofi - Fillestare',
        'content'   => 'Ku të filloj me filozofinë? Kam lexuar "Sofies Velt" dhe dua të thellohem më shumë. A ka ndonjë libër të mirë hyrës? Mendoj se filozofia është themeli i të gjitha dijeve dhe dua të kuptoj mendimtarët e mëdhenj si Sokrati, Platoni dhe Aristoteli. Çfarë sugjeroni?',
        'author_id' => 3,
        'category'  => 'Filozofi',
        'tags'      => ['Filozofi', 'Fillestare', 'Rekomandim'],
        'upvotes'   => 89,
        'downvotes' => 3,
        'date'      => '2025-04-19',
        'comments'  => [
            ['author' => 'artan', 'text' => 'Fillo me "Republika" e Platonit - është bazë e mirë!',             'date' => '2025-04-19'],
            ['author' => 'admin', 'text' => 'Marcus Aurelius "Meditaciones" është ideal për fillestare.',        'date' => '2025-04-20'],
        ],
    ],
    [
        'id'        => 3,
        'title'     => 'Librat më të mirë Sci-Fi të 2024',
        'content'   => 'Këtë vit kam lexuar shumë libra science fiction. Projekti Hail Mary i Andy Weir ishte fantastik! Po ashtu edhe "Iron Widow". Çfarë keni lexuar ju këtë vit nga zhanri? Jam duke kërkuar rekomandime të reja sepse kam mbetur pa libra të papërdorur në listën time.',
        'author_id' => 2,
        'category'  => 'Fantashkencë',
        'tags'      => ['SciFi', 'Rekomandim', '2024'],
        'upvotes'   => 203,
        'downvotes' => 12,
        'date'      => '2025-04-18',
        'comments'  => [
            ['author' => 'blerina', 'text' => 'The Martian ishte edhe ai shumë i mirë nga i njëjti autor!', 'date' => '2025-04-18'],
        ],
    ],
    [
        'id'        => 4,
        'title'     => '[NJOFTIM] Rregullat e reja të komunitetit LEXO',
        'content'   => 'Të nderuar anëtarë, duke filluar nga sot kemi shtuar rregulla të reja për të mbajtur komunitetin tonë cilësor dhe miqësor. Ju lutemi lexoni me kujdes: 1) Respektoni mendimet e njëri-tjetrit, 2) Mos postoni spam, 3) Citoni burimet kur diskutoni fakte, 4) Mbajeni diskutimin relevant me temën. Faleminderit!',
        'author_id' => 1,
        'category'  => 'Njoftime',
        'tags'      => ['Admin', 'Rregulla', 'Njoftim'],
        'upvotes'   => 56,
        'downvotes' => 1,
        'date'      => '2025-04-17',
        'comments'  => [],
    ],
    [
        'id'        => 5,
        'title'     => 'Historia e Shqipërisë - librat që rekomandoj',
        'content'   => 'Pas shumë vitesh leximi mbi historinë shqiptare, dua të ndaj me ju librat që m\'kanë ndikuar më shumë. Nga "Historia e Skënderbeut" tek punimet akademike moderne, ka shumë burime të shkëlqyera. Cili libër historik shqiptar ju ka lënë përshtypje të veçantë?',
        'author_id' => 3,
        'category'  => 'Histori',
        'tags'      => ['Histori', 'Shqipëri', 'Kulturë'],
        'upvotes'   => 134,
        'downvotes' => 5,
        'date'      => '2025-04-16',
        'comments'  => [
            ['author' => 'artan', 'text' => '"Historia e Popullit Shqiptar" është referenca kryesore!', 'date' => '2025-04-16'],
            ['author' => 'admin', 'text' => 'Postim i shkëlqyer! Faleminderit për kontributin.',          'date' => '2025-04-17'],
        ],
    ],
];

// Kategoritë - associative array
$GLOBALS['categories'] = [
    'Letërsi Shqipe'  => ['icon' => '📚', 'color' => '#e74c3c', 'desc' => 'Vepra të autorëve shqiptarë'],
    'Filozofi'        => ['icon' => '🧠', 'color' => '#9b59b6', 'desc' => 'Mendime dhe ide filozofike'],
    'Fantashkencë'    => ['icon' => '🚀', 'color' => '#3498db', 'desc' => 'Botë të imagjinuara'],
    'Histori'         => ['icon' => '🏛️', 'color' => '#e67e22', 'desc' => 'Ngjarje dhe dokumente historike'],
    'Njoftime'        => ['icon' => '📢', 'color' => '#2ecc71', 'desc' => 'Njoftime nga stafi'],
    'Letërsi Botërore'=> ['icon' => '🌍', 'color' => '#1abc9c', 'desc' => 'Klasikë dhe bashkëkohorë'],
];

// Funksion helper: gjej user by username
function getUserByUsername(string $username): ?array {
    foreach ($GLOBALS['users'] as $user) {
        if ($user['username'] === $username) return $user;
    }
    return null;
}

// Funksion helper: gjej user by id
function getUserById(int $id): ?array {
    foreach ($GLOBALS['users'] as $user) {
        if ($user['id'] === $id) return $user;
    }
    return null;
}

// Funksion helper: gjej post by id
function getPostById(int $id): ?array {
    foreach ($GLOBALS['posts'] as $post) {
        if ($post['id'] === $id) return $post;
    }
    return null;
}

// Funksion: kontrollo nëse user është i kyçur
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

// Funksion: kontrollo rolin
function hasRole(string $role): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Funksion: redirect
function redirect(string $path): void {
    header("Location: " . BASE_URL . $path);
    exit;
}

// Funksion: escape HTML
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Funksion: llogarit score të postimit (numeric sort)
function getPostScore(array $post): int {
    return $post['upvotes'] - $post['downvotes'];
}

// Funksion: format data
function formatDate(string $date): string {
    $ts = strtotime($date);
    $diff = time() - $ts;
    if ($diff < 3600)  return floor($diff / 60) . ' min më parë';
    if ($diff < 86400) return floor($diff / 3600) . ' orë më parë';
    return floor($diff / 86400) . ' ditë më parë';
}
