<?php

  session_start(); // Start or resume a session
  require_once "view/UI.php";
  use Eigenheimer\View\UI;

  require_once "controllers/database.php";
  use Eigenheimer\controllers\connect_Database;
  require_once "controllers/dataset.php";
  use Eigenheimer\controllers\dataset;

  $data = connect_Database::safequery("SELECT * FROM order_food");
  $basket = [];
  if(isset($_POST['selector'])){
    switch ($_POST['selector']){
      case 'additem':
        $basket = additem_basket($_POST['price_item'], $_POST['uuid_item'], $_POST['name_item']);
        break;
      case 'removeitem':
        $removeitem = remove_item($_POST['basket_item_uuid']);
        UI::show_basket($basket);
        break;
        default:
    }
  }





  // if ($_GET['action'] === 'additem' && isset($_GET['id'])) {
  //     if($_GET['id'] < 0){
  //       session_destroy();
  //       header('location:'.$url = preg_replace('/additem&id=-1/', 'view', $_SERVER['REQUEST_URI']));
  //     }
  //     if (!isset($_SESSION['ids'])) {
  //         $_SESSION['ids'] = []; // Initialize the array if it's not initialized already
  //     }
  //     $_SESSION['ids'][] = ['id' => $_GET['id']]; // Add the new ID to the array
     
  //     header('location:'.$url = preg_replace('/additem&id='.$_GET['id'].'/', 'view', $_SERVER['REQUEST_URI']));
  // }

  function additem_basket($price_item, $uuid_item, $name_item)
  {
      // Generate a unique ID for the basket item
      $basket_item_uuid = dataset::guid();
  
      // Create an array with item data
      $item_data = [
          'price_item' => $price_item,
          'uuid_item' => $uuid_item,
          'name_item' => $name_item,
          'basket_item_uuid' => $basket_item_uuid
      ];
  
      // Initialize the session items array if it's not initialized already
      if (!isset($_SESSION['items'])) {
          $_SESSION['items'] = [];
      }
  
      // Add the item data to the session items array
      $_SESSION['items'][] = $item_data;
    
      // $array = [];
      // $array[] = $item_data;
      // $array[] = $item_data;
      // $array[] = $item_data;
      // Optionally, you can return the item data for further processing if needed
      return $_SESSION['items'];
  }
  function remove_item($removeitem)
  {
      $index = array_search($removeitem, array_column($_SESSION['items'], 'basket_item_uuid'));
  
      // If the item is found, remove it
      if ($index !== false) {
          unset($_SESSION['items'][$index]);
      }
  }
  


  UI::navbar();
  UI::items($data);

  UI::show_basket($_SESSION['items']);

  //todo: temporary visual in model -> needs to go to frontend


?>

