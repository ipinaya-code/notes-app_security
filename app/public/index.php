<?php
session_start();
header('Location: ' . (!empty($_SESSION['user_id']) ? 'notes.php' : 'login.php'));
exit;
