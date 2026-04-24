<?php
// =============================================
// LEXO Forum - Klasat e Domenit
// =============================================

/**
 * Klasa bazë Post - enkapsulim i plotë
 */
class Post {
    private int    $id;
    private string $title;
    private string $content;
    private int    $authorId;
    private string $category;
    private array  $tags;
    private int    $upvotes;
    private int    $downvotes;
    private string $date;
    private array  $comments;

    // Konstruktor
    public function __construct(
        int    $id,
        string $title,
        string $content,
        int    $authorId,
        string $category,
        array  $tags      = [],
        int    $upvotes   = 0,
        int    $downvotes = 0,
        string $date      = '',
        array  $comments  = []
    ) {
        $this->id        = $id;
        $this->title     = $title;
        $this->content   = $content;
        $this->authorId  = $authorId;
        $this->category  = $category;
        $this->tags      = $tags;
        $this->upvotes   = $upvotes;
        $this->downvotes = $downvotes;
        $this->date      = $date ?: date('Y-m-d');
        $this->comments  = $comments;
    }

    // --- Getters ---
    public function getId():        int    { return $this->id; }
    public function getTitle():     string { return $this->title; }
    public function getContent():   string { return $this->content; }
    public function getAuthorId():  int    { return $this->authorId; }
    public function getCategory():  string { return $this->category; }
    public function getTags():      array  { return $this->tags; }
    public function getUpvotes():   int    { return $this->upvotes; }
    public function getDownvotes(): int    { return $this->downvotes; }
    public function getDate():      string { return $this->date; }
    public function getComments():  array  { return $this->comments; }

    // --- Setters me validim ---
    public function setTitle(string $title): void {
        if (strlen(trim($title)) < 5) {
            throw new InvalidArgumentException('Titulli duhet të ketë të paktën 5 karaktere.');
        }
        $this->title = trim($title);
    }

    public function setContent(string $content): void {
        if (strlen(trim($content)) < 20) {
            throw new InvalidArgumentException('Përmbajtja duhet të ketë të paktën 20 karaktere.');
        }
        $this->content = trim($content);
    }

    public function setCategory(string $category): void {
        $this->category = $category;
    }

    // Llogarit score neto
    public function getScore(): int {
        return $this->upvotes - $this->downvotes;
    }

    // Shto koment
    public function addComment(string $author, string $text): void {
        $this->comments[] = [
            'author' => $author,
            'text'   => $text,
            'date'   => date('Y-m-d'),
        ];
    }

    // Kthe numrin e komenteve
    public function getCommentCount(): int {
        return count($this->comments);
    }

    // Konverto në array
    public function toArray(): array {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'content'   => $this->content,
            'author_id' => $this->authorId,
            'category'  => $this->category,
            'tags'      => $this->tags,
            'upvotes'   => $this->upvotes,
            'downvotes' => $this->downvotes,
            'date'      => $this->date,
            'comments'  => $this->comments,
        ];
    }

    // Metoda statike: krijo nga array
    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['title'],
            $data['content'],
            $data['author_id'],
            $data['category'],
            $data['tags']      ?? [],
            $data['upvotes']   ?? 0,
            $data['downvotes'] ?? 0,
            $data['date']      ?? '',
            $data['comments']  ?? []
        );
    }

    // Shfaq excerpt
    public function getExcerpt(int $length = 150): string {
        $plain = strip_tags($this->content);
        if (strlen($plain) <= $length) return $plain;
        return substr($plain, 0, $length) . '…';
    }
}


/**
 * Klasa AdminPost - trashëgon Post
 * Demonstrim i trashëgimisë
 */
class AdminPost extends Post {
    private bool   $isPinned;
    private bool   $isLocked;
    private string $adminNote;

    public function __construct(
        int    $id,
        string $title,
        string $content,
        int    $authorId,
        string $category,
        array  $tags      = [],
        int    $upvotes   = 0,
        int    $downvotes = 0,
        string $date      = '',
        array  $comments  = [],
        bool   $isPinned  = false,
        bool   $isLocked  = false,
        string $adminNote = ''
    ) {
        // Thirrja e konstruktorit prind
        parent::__construct($id, $title, $content, $authorId, $category, $tags, $upvotes, $downvotes, $date, $comments);
        $this->isPinned  = $isPinned;
        $this->isLocked  = $isLocked;
        $this->adminNote = $adminNote;
    }

    // Getters/Setters shtesë
    public function isPinned():  bool   { return $this->isPinned; }
    public function isLocked():  bool   { return $this->isLocked; }
    public function getAdminNote(): string { return $this->adminNote; }

    public function pin():   void { $this->isPinned = true; }
    public function unpin(): void { $this->isPinned = false; }
    public function lock():  void { $this->isLocked = true; }
    public function unlock(): void { $this->isLocked = false; }

    public function setAdminNote(string $note): void {
        $this->adminNote = $note;
    }

    // Override toArray
    public function toArray(): array {
        $base = parent::toArray();
        $base['is_pinned']   = $this->isPinned;
        $base['is_locked']   = $this->isLocked;
        $base['admin_note']  = $this->adminNote;
        return $base;
    }

    // Shfaq badge
    public function getBadge(): string {
        if ($this->isPinned && $this->isLocked) return '📌🔒';
        if ($this->isPinned)  return '📌';
        if ($this->isLocked)  return '🔒';
        return '';
    }
}


/**
 * Klasa User - encapsulim
 */
class User {
    private int    $id;
    private string $username;
    private string $email;
    private string $role;
    private string $bio;
    private int    $karma;
    private string $joined;
    private string $avatar;

    public function __construct(
        int    $id,
        string $username,
        string $email,
        string $role    = 'user',
        string $bio     = '',
        int    $karma   = 0,
        string $joined  = '',
        string $avatar  = ''
    ) {
        $this->id       = $id;
        $this->username = $username;
        $this->email    = $email;
        $this->role     = $role;
        $this->bio      = $bio;
        $this->karma    = $karma;
        $this->joined   = $joined ?: date('Y-m-d');
        $this->avatar   = $avatar ?: strtoupper(substr($username, 0, 1));
    }

    // Getters
    public function getId():       int    { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail():    string { return $this->email; }
    public function getRole():     string { return $this->role; }
    public function getBio():      string { return $this->bio; }
    public function getKarma():    int    { return $this->karma; }
    public function getJoined():   string { return $this->joined; }
    public function getAvatar():   string { return $this->avatar; }

    // Setters me validim
    public function setEmail(string $email): void {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email-i nuk është i vlefshëm.');
        }
        $this->email = $email;
    }

    public function setBio(string $bio): void {
        $this->bio = substr($bio, 0, 500);
    }

    public function addKarma(int $points): void {
        $this->karma += $points;
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public static function fromArray(array $data): self {
        return new self(
            $data['id'],
            $data['username'],
            $data['email'],
            $data['role']   ?? 'user',
            $data['bio']    ?? '',
            $data['karma']  ?? 0,
            $data['joined'] ?? '',
            $data['avatar'] ?? ''
        );
    }
}


// =============================================
// Validimi me Shprehje të Rregullta (Regex)
// =============================================

/**
 * Valido email me regex
 */
function validateEmail(string $email): bool {
    $pattern = '/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/';
    return (bool) preg_match($pattern, $email);
}

/**
 * Valido username: vetëm shkronja, numra, nënviza, 3-20 karaktere
 */
function validateUsername(string $username): bool {
    $pattern = '/^[a-zA-Z0-9_]{3,20}$/';
    return (bool) preg_match($pattern, $username);
}

/**
 * Valido titullin e postimit: min 5, max 200 karaktere, pa karaktere të rrezikshme
 */
function validatePostTitle(string $title): bool {
    $pattern = '/^[^\<\>\{\}]{5,200}$/u';
    return (bool) preg_match($pattern, trim($title));
}

/**
 * Valido fjalëkalimin: min 6 karaktere, të paktën 1 numër
 */
function validatePassword(string $password): bool {
    $pattern = '/^(?=.*[0-9]).{6,}$/';
    return (bool) preg_match($pattern, $password);
}
