<?php
session_start();

if (isset($_SESSION['connected_id'])) {
    
    session_destroy();
    header("Location: login.php");
    exit;
} else {
    header("Location: login.php");
}
