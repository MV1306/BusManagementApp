<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
header("Location: Index.php"); // Redirect to home
exit();
?>
