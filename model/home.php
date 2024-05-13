<?php
  require_once "model/guide.php";

  use Eigenheimer\Controllers\UI;
  

  $buttons = []; 
  $buttons[] = ['title' => 'Button 1', 'url' => 'http://example.com/button1'];
  $buttons[] = ['title' => 'Button 2', 'url' => 'http://example.com/button2'];
  $buttons[] = ['title' => 'Button 3', 'url' => 'http://example.com/button3'];
  $buttons[] = ['title' => 'Button 4', 'url' => 'http://example.com/button4'];
  UI::navbar($buttons);
  
?>

