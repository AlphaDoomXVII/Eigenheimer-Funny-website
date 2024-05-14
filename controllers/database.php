<?php

namespace Eigenheimer\controllers;

class connect_Database
{
  private function connect_main()
  {
    $bro = 'connected to database!';
    return $bro;
  }
  
  public static function query_order_food()
  {
    $db = new self();
    return $db->connect_main();
  }
  
}
