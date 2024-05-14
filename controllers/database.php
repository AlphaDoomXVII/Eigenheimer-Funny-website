<?php

namespace Eigenheimer\controllers;

use PDO;
use PDOException;

class connect_Database
{
  private function connect_main()
  {

  // Database credentials
  $servername = "localhost"; // Replace with your MySQL server address
  $username = "username"; // Replace with your MySQL username
  $password = "password"; // Replace with your MySQL password
  $dbname = "database"; // Replace with your MySQL database name

    try {
      // Create connection
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      
      // Set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
      return $conn; // Return the connection object
      
      } 
    catch(PDOException $e) {
      return $e; // Return the exception object
    }
  }
  
  public static function query_order_food()
  {
    $db = new self();
    return $db->connect_main();
  }
  
}

