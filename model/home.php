<?php
  require_once "view/UI.php";
  use Eigenheimer\View\UI;

  require_once "controllers/database.php";
  use Eigenheimer\controllers\connect_Database;

  $data = connect_Database::safequery("SELECT * FROM order_food");
  var_dump($data);


  session_start(); // Start or resume a session

  if ($_GET['action'] === 'additem' && isset($_GET['id'])) {
      if($_GET['id'] < 0){
        session_destroy();
        header('location:'.$url = preg_replace('/additem&id=-1/', 'view', $_SERVER['REQUEST_URI']));
      }
      if (!isset($_SESSION['ids'])) {
          $_SESSION['ids'] = []; // Initialize the array if it's not initialized already
      }
      $_SESSION['ids'][] = ['id' => $_GET['id']]; // Add the new ID to the array
     
      header('location:'.$url = preg_replace('/additem&id='.$_GET['id'].'/', 'view', $_SERVER['REQUEST_URI']));
  }


  UI::navbar();
  UI::items($data);
  
  
  //todo: temporary visual in model -> needs to go to frontend
  

?>

