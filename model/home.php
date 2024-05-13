<?php
  require_once "view/UI.php";

  use Eigenheimer\View\UI;
  

  $buttons = []; 
  $buttons[] = ['title' => 'Home', 'url' => 'http://example.com/button1'];
  $buttons[] = ['title' => 'Bestellen', 'url' => 'http://example.com/button2'];
  $buttons[] = ['title' => 'Routes', 'url' => 'http://example.com/button3'];
  $buttons[] = ['title' => 'Over ons', 'url' => 'http://example.com/button4'];
  UI::navbar($buttons);
  echo "<h1 class='d-flex justify-content-center'>hallo wereld</h1>";
  UI::footer($buttons);
  
?>

