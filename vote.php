<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Models.php';
header('Content-Type: application/json; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(false, 'Metoda jo e vlefshme.');
if (!isLoggedIn()) jsonResponse(false, 'Duhet te jeni i kycur.', ['code' => 401]);
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) jsonResponse(false, 'CSRF i pavlefshem.');
$postId = (int)($_POST['post_id'] ?? 0);
$type   = $_POST['type'] ?? '';
if ($postId <= 0 || !in_array($type, ['up','down'])) jsonResponse(false, 'Parametra te gabuar.');
try {
    $pm   = new PostModel();
    $post = $pm->getById($postId);
    if (!$post) jsonResponse(false, 'Postimi nuk u gjet.');
    $vm     = new VoteModel();
    $result = $vm->vote((int)$_SESSION['user_id'], $postId, $type);
    jsonResponse(true, 'OK', [
        'score'     => (int)$result['score'],
        'upvotes'   => (int)$result['upvotes'],
        'downvotes' => (int)$result['downvotes'],
        'action'    => $result['action'],
    ]);
} catch (RuntimeException $e) { jsonResponse(false, 'Gabim gjate votimit.'); }
