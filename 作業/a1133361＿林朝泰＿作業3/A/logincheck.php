<?php
    session_start();
    
    $role = $_POST["role"];
    $password = $_POST["password"];
    $name = $_POST["name"];

    //role: student, password:12345
    //role: teacher, password:54321
    //role: administrator, password:67890

    if(isset($role)){
        $date = strtotime("+ 1day", time());
        switch($role){
            case "student":
                if($password == "12345"){
                    $_SESSION["role"] = "student";
                    setcookie("id", $name ,$date);
                    header("Location: student.php");
                    exit();
                }
                break;
            case "teacher":
                if($password == "54321"){
                    $_SESSION["role"] = "teacher";
                    setcookie("id", $name ,$date);
                    header("Location: teacher.php");
                    exit();
                }
                break;
            case "administrator":
                if($password == "67890"){
                    $_SESSION["role"] = "administrator";
                    setcookie("id", $name ,$date);
                    header("Location: administrator.php");
                    exit();
                }
                break;
            default:
                echo "<h1>不要再冒充我的身份了!<h1>";
        }
    }
    header("Location: login.php");
    
