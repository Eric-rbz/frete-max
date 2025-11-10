<?php
session_start();
unset($_SESSION['logo_vista']);
session_destroy();
header('Location: index.php');
exit;
?>
