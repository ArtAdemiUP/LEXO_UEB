<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Models.php';
header('Content-Type: application/json; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(false, 'Metoda jo e vlefshme.');
if (!isLoggedIn()) jsonResponse(false, 'Duhet te jeni i kycur.', ['code' => 401]);
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) jsonResponse(false, 'CSRF i pavlefshem.');
$action = $_POST['action'] ?? '';
try {
    $cm = new CommentModel();
    if ($action === 'create') {
        $postId  = (int)($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        if ($postId <= 0)            jsonResponse(false, 'Post ID i pavlefshem.');
        if (strlen($content) < 5)    jsonResponse(false, 'Komenti: min. 5 karaktere.');
        if (strlen($content) > 2000) jsonResponse(false, 'Komenti: maks. 2000 karaktere.');
        if (preg_match('/<script|<iframe|javascript:/i', $content)) jsonResponse(false, 'Kod i palejueshem.');
        $pm   = new PostModel();
        $post = $pm->getById($postId);
        if (!$post) jsonResponse(false, 'Postimi nuk ekziston.');
        if ($post['is_locked'] && !hasRole('admin')) jsonResponse(false, 'Postimi eshte i kycur.');
        $id      = $cm->create($postId, (int)$_SESSION['user_id'], $content);
        $comment = $cm->getFullById($id);
        $count   = $cm->countByPost($postId);
        jsonResponse(true, 'Komenti u shtua.', [
            'comment' => [
                'id'          => $comment['id'],
                'content'     => $comment['content'],
                'username'    => $comment['username'],
                'author_role' => $comment['author_role'],
                'avatar'      => $comment['avatar'] ?? '',
                'created_at'  => 'tani',
            ],
            'count' => $count,
        ]);
    }
    if ($action === 'delete') {
        $id      = (int)($_POST['comment_id'] ?? 0);
        $deleted = $cm->delete($id, (int)$_SESSION['user_id']);
        jsonResponse($deleted, $deleted ? 'Komenti u fshi.' : 'Nuk keni leje.');
    }
    jsonResponse(false, 'Veprim i panjohur.');
} catch (RuntimeException $e) { jsonResponse(false, 'Gabim teknik.'); }
