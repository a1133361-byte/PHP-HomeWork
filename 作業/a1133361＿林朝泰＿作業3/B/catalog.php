<?php 
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        session_start();

        $product = $_POST["product"];
        $amount = $_POST["amount"];

        $_SESSION["product"] = $product;
        $_SESSION["amount"] = $amount;

        header("Location: savecart.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>加入購物車</title>
</head>
<body>
    <form action="catalog.php" method="POST">
        加入購物車:
        <select name="product">
            <option value="computer">電腦 - $20000</option>
            <option value="smartphone">聰明手機 - $12000</option>
            <option value="tablet">平板電腦 - $18000</option>
        </select>
        購買數量:
        <input type="number" name="amount"> <br>
        <button type="submit">送出</button>
    </form>
    <h1><a href="shoppingcart.php">檢視購物車</a></h1>
</body>
</html>