<?php
session_start();

// Destroy the session and redirect to login page
session_destroy();
header("Location: index.php");
exit();
?>
