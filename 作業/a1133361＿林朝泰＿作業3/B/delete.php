<?php
    $date = time() - 1;
    foreach ($_COOKIE as $key => $item) {    
        if (is_array($item)) {
            setcookie($key . "[name]", "", $date);
            setcookie($key . "[amount]", "", $date);
            setcookie($key . "[price]", "", $date);
        }
    }
    header("Location: shoppingcart.php");