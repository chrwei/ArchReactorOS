<?php
include 'init.php';

function ProcessLogout() {
  global $user;

  $user->Logout();  
  header("Location: login.php"); 
}

/***********************************************************************************************
Main
***********************************************************************************************/
ProcessLogout();
?>