<?php 
    $user_name = $_POST["user-name"];
    $user_password = $_POST["user-password"];

    if(isset($user_name) && isset($user_password)){
        if($user_name == "Lin" && $user_password == "123456789"){
            header("Location:form.php");
        }else{
            header("Location:login.php");
        }
    }
?>