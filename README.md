# LEXO_UEB
📖 LEXO — Forumi Shqiptar i Librave

Projekt akademik PHP — Forum si Reddit për diskutime letrare dhe kulturore.

📋 Përshkrimi
LEXO është një aplikacion web forum i ndërtuar me PHP të pastër (pa framework, pa databazë).
Lejon përdoruesit të diskutojnë libra, të postojnë opinione dhe të votojnë për postimet e njëri-tjetrit —
i ngjashëm me Reddit, por i fokusuar në letërsi shqiptare dhe botërore.

✨ Veçoritë Kryesore

🔐 Login / Logout me kredenciale hardcoded dhe ruajtje me $_SESSION
👥 Dy role përdoruesish — admin me panel të plotë dhe user me akses standard
📝 Postime & Komente me sistem votimi (upvote/downvote)
📂 Kategori — filtrim dhe shfletim sipas kategorisë
🔃 Sortim — Hot, i Ri, Top (me usort)
✅ Validim server-side me shprehje të rregullta (regex)
🍪 Cookies për personalizim (temë e errët/e çelët, madhësi shkronjash)
🎨 Dark / Light Theme me ndërrues në Cilësimet


🗂️ Struktura e Projektit
lexo/
├── index.php                  # Ridrejto në faqen kryesore
│
├── classes/
│   └── Post.php               # Klasat Post, AdminPost, User + funksionet regex
│
├── includes/
│   ├── config.php             # Konfigurim global, arrays, funksione helper
│   ├── header.php             # Header i përbashkët + navigim
│   └── footer.php             # Footer i përbashkët
│
├── pages/
│   ├── home.php               # Kryefaqja — feed me sortim dhe filtrim
│   ├── login.php              # Forma e hyrjes me validim
│   ├── logout.php             # Shkatërrimi i sesionit
│   ├── post.php               # Detajet e postimit + komente
│   ├── new_post.php           # Krijo postim të ri (kërkon login)
│   ├── categories.php         # Lista e kategorive
│   ├── profile.php            # Profili i përdoruesit të kyçur
│   ├── admin.php              # Panel admin (vetëm roli admin)
│   └── settings.php           # Cilësimet — temë, font, profil
│
└── assets/
    ├── css/style.css          # Stilizimi i plotë me CSS variables
    └── js/main.js             # JavaScript minimal

🚀 Udhëzime Ekzekutimi
Kërkesat paraprake

PHP 8.0 ose më i ri
Server lokal: XAMPP, WAMP, MAMP, Laragon, ose PHP built-in server


Metoda 1 — XAMPP / WAMP (Rekomandohet)

Shkarko dhe ekstrakto projektin:

   lexo_forum.zip  →  ekstrakto  →  dosja "lexo/"

Kopjo dosjen në direktorinë e serverit:

XAMPP (Windows): C:\xampp\htdocs\lexo\
XAMPP (macOS): /Applications/XAMPP/htdocs/lexo/
WAMP: C:\wamp64\www\lexo\


Fillo Apache nga paneli i XAMPP/WAMP.
Hap shfletuesin dhe shko te:

   http://localhost/lexo/

Metoda 2 — PHP Built-in Server (pa XAMPP)
Nëse ke PHP të instaluar direkt:
bash# 1. Ekstrakto zip dhe hyr në dosje
cd lexo/

# 2. Fillo serverin e integruar PHP
php -S localhost:8080

# 3. Hap shfletuesin
# http://localhost:8080/

Metoda 3 — Laragon

Kopjo dosjen lexo/ brenda C:\laragon\www\
Fillo Laragon dhe hap: http://lexo.test/


🔑 Kredencialet e Hyrjes
EmriFjalëkalimiRoliAksetadminadmin123AdministratorPanel admin, fshij postime, shfaq statistikaartanartan123PërdoruesPosto, komento, votoblerinablerina123PërdoruesPosto, komento, voto

⚠️ Kredencialet janë hardcoded në includes/config.php — nuk ka databazë.


📄 Faqet e Aplikacionit
URLPërshkrimiKërkon Login?/pages/home.phpKryefaqja me feed postime❌/pages/login.phpForma e hyrjes❌/pages/post.php?id=1Detajet e postimit❌/pages/categories.phpShfletim sipas kategorive❌/pages/new_post.phpKrijo postim të ri✅/pages/profile.phpProfili im✅/pages/settings.phpCilësimet & personalizimi✅/pages/admin.phpPaneli i administratorit✅ Admin/pages/logout.phpDil nga llogaria✅

🛠️ Konceptet PHP të Implementuara
OOP & Klasa

Post — klasë bazë me konstruktor, getter/setter, enkapsulim private
AdminPost extends Post — trashëgimi, metoda të shtuar (pin(), lock())
User — klasë e dytë e domenit me validim të brendshëm

Arrays
Sortimi
Validimi me Regex
Session & Cookies

🎨 Temat
Aplikacioni mbështet dy tema të ndërrushme nga faqja Cilësimet:
TemëPamja🌙 E Errët (default)Sfond #0f1117, tekst #e8eaf0☀️ E ÇelëtSfond #f5f4ef, tekst #1a1814
Zgjedhja ruhet në cookie lexo_theme dhe mbetet aktive pas rifutjes.

👨‍💻 Zhvilluar me

PHP 8.x — logjika server-side
HTML5 / CSS3 — strukturë dhe stil
Vanilla JavaScript — interaktivitet minimal
Google Fonts — Playfair Display + DM Sans


📝 Shënime

Nuk përdoret databazë — të gjitha të dhënat janë hardcoded në config.php
Postimet e reja dhe komentet simulohen (flash message suksesi) dhe nuk ruhen pas refresh-it
Projekti është ndërtuar si demonstrim akademik i koncepteve PHP


Ndërtuar si projekt kursi PHP — LEXO Forum © 2025
