<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

$_SESSION = [];
session_destroy();

// Ridrejto me mesazh
session_start();
$_SESSION['flash'] = ['type' => 'info', 'msg' => 'Jeni dalë nga llogaria. Shihemi sërisht! 👋'];

redirect('/pages/login.php');
