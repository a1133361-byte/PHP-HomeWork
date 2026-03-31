<?php 
    session_start();

    $user_name = $_POST["user-name"];
    $user_password = $_POST["user-password"];

    if(isset($user_name) && isset($user_password)){
        if($user_name == "Lin" && $user_password == "123456789"){
            header("Location:form.php");
        }else{
            $_SESSION["error_message"] = "使用者名稱或密碼輸入錯誤";
            header("Location:login.php");
        }
    }
?>