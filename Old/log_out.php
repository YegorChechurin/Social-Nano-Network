<?php

  /* We are setting the user status to be logged out */
  session_start();
  $_SESSION['logged_in'] = 0;
  
  /* Rederecting to the home page of the Nano Social Network */
  header('Location: home.php');
   
?>