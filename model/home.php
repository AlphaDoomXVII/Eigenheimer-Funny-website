<?php
  require_once "view/UI.php";

  use Eigenheimer\View\UI;
 
  session_start(); // Start or resume a session

  if ($_GET['action'] === 'additem' && isset($_GET['id'])) {
      
      if (!isset($_SESSION['ids'])) {
          $_SESSION['ids'] = []; // Initialize the array if it's not initialized already
      }
      $_SESSION['ids'][] = $_GET['id']; // Add the new ID to the array
      var_dump($_SESSION['ids']);
 
      
  }
  UI::navbar();
  UI::items(100);


  
?>

