CREATE DATABASE IF NOT EXISTS lexo_forum
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lexo_forum;

CREATE TABLE IF NOT EXISTS users (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('admin','user') NOT NULL DEFAULT 'user',
    avatar        VARCHAR(255) DEFAULT NULL,
    bio           TEXT         DEFAULT NULL,
    karma         INT          NOT NULL DEFAULT 0,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_username (username),
    INDEX idx_email    (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    icon        VARCHAR(10)  DEFAULT '📁',
    description TEXT         DEFAULT NULL,
    color       VARCHAR(7)   DEFAULT '#888888',
    post_count  INT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relacione: posts.author_id -> users.id
--            posts.category_id -> categories.id
CREATE TABLE IF NOT EXISTS posts (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title       VARCHAR(300) NOT NULL,
    content     TEXT         NOT NULL,
    author_id   INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    upvotes     INT UNSIGNED NOT NULL DEFAULT 0,
    downvotes   INT UNSIGNED NOT NULL DEFAULT 0,
    view_count  INT UNSIGNED NOT NULL DEFAULT 0,
    is_pinned   TINYINT(1)   NOT NULL DEFAULT 0,
    is_locked   TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_author   (author_id),
    INDEX idx_category (category_id),
    INDEX idx_created  (created_at DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relacione: comments.post_id -> posts.id
--            comments.author_id -> users.id
CREATE TABLE IF NOT EXISTS comments (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    post_id    INT UNSIGNED NOT NULL,
    author_id  INT UNSIGNED NOT NULL,
    content    TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (post_id)   REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_post   (post_id),
    INDEX idx_author (author_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Relacione: votes.user_id -> users.id
--            votes.post_id -> posts.id
-- UNIQUE key parandalon votim te dyfishtë
CREATE TABLE IF NOT EXISTS votes (
    id         INT UNSIGNED      NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED      NOT NULL,
    post_id    INT UNSIGNED      NOT NULL,
    vote_type  ENUM('up','down') NOT NULL,
    created_at DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_user_post (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
    id      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name    VARCHAR(100) NOT NULL,
    email   VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT         NOT NULL,
    is_read TINYINT(1)   NOT NULL DEFAULT 0,
    sent_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA
-- ============================================================
INSERT INTO categories (name, slug, icon, description, color) VALUES
('Letersi Shqipe',   'letersi-shqipe',  '📚', 'Vepra te autoreve shqiptare',             '#e74c3c'),
('Filozofi',         'filozofi',         '🧠', 'Mendime dhe ide filozofike',               '#9b59b6'),
('Fantashkence',     'fantashkence',     '🚀', 'Bote te imagjinuara dhe teknologji',       '#3498db'),
('Histori',          'histori',          '🏛', 'Ngjarje dhe dokumente historike',           '#e67e22'),
('Letersi Boterore', 'letersi-boterore', '🌍', 'Klasike dhe bashkekohere nderkombetar',    '#1abc9c'),
('Njoftime',         'njoftime',         '📢', 'Njoftime nga stafi i LEXO',                '#2ecc71');

-- Fjalekalimet: admin123 / artan123 / blerina123
INSERT INTO users (username, email, password_hash, role, bio, karma) VALUES
('admin',   'admin@lexo.al',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'admin', 'Administratori i forumit LEXO.', 9999),
('artan',   'artan@lexo.al',
 '$2y$12$TKh8H1.PfuphF2.2rxEYa.VvRG3Xag7gzLiAF7Smt.VV4VN8GWKY',
 'user', 'Lexues i apasionuar i librave.', 420),
('blerina', 'blerina@lexo.al',
 '$2y$12$u/H3kn0oXc6h.g1WGBc3BOv3GbbwTNB1Wr0VEW8ZKfRpI0P43R8iq',
 'user', 'Shkrimtare dhe kritike letrare.', 785);

INSERT INTO posts (title, content, author_id, category_id, upvotes, downvotes, is_pinned) VALUES
('Gjenerali i Ushtrise se Vdekur - Diskutim',
 'Sapo mbarova librin e Ismail Kadares. Mendoj se eshte nje nga veprat me te fuqishme te letersise shqipe. Si ju duket pershkrimi i luftes dhe kujteses kolektive? Kuptimi i saj nuk eshte thjesht historik por edhe filozofik.',
 2, 1, 147, 8, 0),
('Rekomandime per Filozofi - Fillestare',
 'Ku te filloj me filozofine? Kam lexuar Sofies Velt dhe dua te thellohem me shume. A ka ndonje liber te mire hyres? Mendoj se filozofia eshte themeli i te gjitha dijeve.',
 3, 2, 89, 3, 0),
('Librat me te mire Sci-Fi te 2024',
 'Kete vit kam lexuar shume libra science fiction. Projekti Hail Mary i Andy Weir ishte fantastik! Cfare keni lexuar ju kete vit nga zhanri?',
 2, 3, 203, 12, 0),
('[NJOFTIM] Rregullat e reja te komunitetit',
 'Te nderuar anetare, kemi shtuar rregulla te reja: 1) Respektoni njeri-tjetrin. 2) Mos postoni spam. 3) Citoni burimet. 4) Mbajeni diskutimin relevant. Faleminderit!',
 1, 6, 56, 1, 1),
('Historia e Shqiperise - librat qe rekomandoj',
 'Pas shume vitesh leximi mbi historine shqiptare, dua te ndaj librat qe me kane ndikuar. Nga Historia e Skenderbeut tek punimet akademike moderne, ka shume burime te shkëlqyera.',
 3, 4, 134, 5, 0);

INSERT INTO comments (post_id, author_id, content) VALUES
(1, 3, 'Dakord plotesisht! Kadare ka nje ze unik ne letersine boterore.'),
(1, 1, 'Nje nga librat qe duhet ta lexoje cdo shqiptar!'),
(2, 2, 'Fillo me Republiken e Platonit - eshte baze e mire!'),
(2, 1, 'Marcus Aurelius Meditaciones eshte ideal per fillestare.'),
(3, 3, 'The Martian ishte edhe ai shume i mire nga i njejti autor!');

UPDATE categories SET post_count = (
    SELECT COUNT(*) FROM posts WHERE category_id = categories.id
);
