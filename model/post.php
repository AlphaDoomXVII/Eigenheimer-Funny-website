<?php
//if posted -> sanatize data
//connect database -> send data
//else redirect to page with error


if(isset($_POST)){
  filter_input($_POST);
  foreach($_POST as $row){
  
    print_r($row);

  }
}




?>
