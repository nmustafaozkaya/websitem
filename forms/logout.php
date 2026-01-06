<?php
session_start();

// Tüm session verilerini temizle
$_SESSION = array();

// Session cookie'sini de sil
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session'ı tamamen yok et
session_destroy();

// Cache kontrolleri
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Login sayfasına yönlendir
header('Location: login.php?message=logout');
exit();
?>