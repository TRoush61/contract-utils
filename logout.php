<?php
  session_start();
  session_unset();
  header("Location: contracts.php");

  exit;
?>
