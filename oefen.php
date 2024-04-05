<?php

greetings("John");
rijbewijs(7);


function greetings($name)
{
   
    echo "hello $name";
}


function rijbewijs($age){
if ($age >= 16) {

    echo "you are old enough";
}

else
{
    echo "you're not old enough";
}
}






?>