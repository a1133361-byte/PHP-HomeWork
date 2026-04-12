<?php 
    session_start();

    $product = $_SESSION["product"];
    $amount = $_SESSION["amount"];
    switch($product){
        case "computer":
            $price = 20000;
            break;
        case "smartphone":
            $price = 12000;
            break;
        case "tablet":
            $price = 18000;
            break;
    }


    $date = strtotime("+ 1day", time());

    setcookie($product."[name]", $product, $date);
    setcookie($product."[amount]", $amount, $date);
    setcookie($product."[price]", $price, $date);

    header("Location: shoppingcart.php");