<?php
    echo "<h1>這裡是購物車拉</h1>";
    
    foreach($_COOKIE as $item){
        if(is_array($item)){
            echo "商品名稱: ". $item["name"];
            echo " 數量: ". $item["amount"];
            echo " 總價格: ". $item["price"] * $item["amount"]."<br>";
        }
    }
    
    echo "<h1><a href='delete.php'>刪除購物車</a></h1>";
    echo "<h1><a href='catalog.php'>回到catalog.php</a></h1>";