<?php
// session_start();

if (!isset($_SESSION['connected_id'])) {
    header('Location: login.php');
    exit;
}
