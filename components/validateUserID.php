<?php

function validateUserID(string $userID) : bool {

    $validSign = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'H','J', 'K', 'L', 'M', 'N','P','R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];


   if(strlen($userID) !== 11) {
        return false;
    }

    if(!preg_match('/^[0-9]{6}$/', substr($userID, 0, 6))) {
        return false;
    }
    if(!preg_match('/^[AY+\-]$/', $userID[6])) {
        return false;
    }

    if(!preg_match('/^[0-9]{3}$/', substr($userID, 7, 3))) {
        return false;
    }

  // Laske tarkistusmerkki

   $digits = substr($userID, 0, 6) . substr($userID, 7, 3);

   $leftover = intval($digits) % 31;

    
    if($validSign[$leftover] !== $userID[10]) {
        return false;
    }

 
    return true;
}

?>