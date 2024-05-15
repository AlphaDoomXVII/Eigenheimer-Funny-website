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
  $username = "timo"; // Replace with your MySQL username
  $password = "timo"; // Replace with your MySQL password
  $dbname = "eigenheimer"; // Replace with your MySQL database name

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
  
  public static function safequery($query)
  {
      $db = new self();
      $conn = $db->connect_main();
      
      // Check if the connection is successful
      if ($conn instanceof PDO) {
          try {
              // Execute the provided query
              $result = $conn->query($query);
    
              // Check if the query executed successfully
              if ($result !== false) {
                  // Fetch the data and return
                  return $result->fetchAll(PDO::FETCH_ASSOC);
              } else {
                  return false; // Query failed
              }
          } catch (PDOException $e) {
              return $e; // Return the exception object
          }
      } else {
          return false; // Failed to connect to the database
      }
  }
  
}



