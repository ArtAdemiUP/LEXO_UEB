<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Models.php';
header('Content-Type: application/json; charset=UTF-8');
if (!isLoggedIn()) jsonResponse(false, 'Duhet te jeni i kycur.', ['code' => 401]);
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) jsonResponse(false, 'CSRF i pavlefshem.');
$action = $_POST['action'] ?? '';
try {
    $pm = new PostModel();
    if ($action === 'delete') {
        $ok = $pm->delete((int)($_POST['post_id'] ?? 0), (int)$_SESSION['user_id']);
        jsonResponse($ok, $ok ? 'Postimi u fshi.' : 'Nuk keni leje.');
    }
    if ($action === 'pin' && hasRole('admin')) {
        $ok = $pm->togglePin((int)($_POST['post_id'] ?? 0));
        jsonResponse($ok, $ok ? 'Pin u ndryshua.' : 'Gabim.');
    }
    jsonResponse(false, 'Veprim i panjohur.');
} catch (RuntimeException $e) { jsonResponse(false, 'Gabim teknik.'); }
