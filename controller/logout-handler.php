<?php
// Logout handler - destroy session
session_start();
session_unset();
session_destroy();

header("Location: ../");
?>
