<?php
    session_start();

    $role = $_SESSION["role"];
    if(isset($role) && $role === "teacher"){
        echo "<h1>你好，老師!</h1>";
        echo "<h1><a href='logout.php'>登出</a></h1>";
    }else{
        echo "<h1>非法進入老師網頁!!</h1>";
        header("refresh:2;url=login.php");
    }

    //cookie判斷
    if(isset($_COOKIE["id"])){
        echo "<h1>你的id名稱: ".$_COOKIE["id"]."</h1>";
        echo "<h1><a href='deleteCookie.php'>刪除cookie</a></h1>";
    }
