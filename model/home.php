<?php
  require_once "view/UI.php";

  use Eigenheimer\View\UI;
  

  $buttons = []; 
  $buttons[] = ['title' => 'Home', 'url' => 'http://example.com/button1' , 'class' => ''];
  $buttons[] = ['title' => 'Bestellen', 'url' => 'http://example.com/button2' , 'class' => '' ];
  $buttons[] = ['title' => 'Routes', 'url' => 'http://example.com/button3' , 'class' => ''];
  $buttons[] = ['title' => 'Over ons', 'url' => 'http://example.com/button4' , 'class' => ''];
  UI::navbar($buttons);



  
?>

