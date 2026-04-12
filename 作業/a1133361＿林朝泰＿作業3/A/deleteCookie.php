<?php
    session_start();
    $role = $_SESSION["role"];
    $date = time() - 1;
    setcookie("id", "",$date);
    
    switch($role){
        case "student":
            header("Location: student.php");
            break;
        case "teacher":
            header("Location: teacher.php");
            break;
        case "administrator":
            header("Location: administrator.php");
            break;
        default:
            header("Location: login.php");
    }

