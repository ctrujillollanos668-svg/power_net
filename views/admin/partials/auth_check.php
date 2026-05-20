<?php
// Protección de acceso al panel admin
// Incluir al inicio de cada vista del admin
if (session_status() === PHP_SESSION_NONE) {
}
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php");
    exit;
}
