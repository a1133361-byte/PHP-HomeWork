<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入頁面</title>
    <style>
        *{
            margin: 0;
            padding: 0;
        }
        html, body{
            height: 100%;
        }
        body{
            display: flex;
            justify-content: center;
            align-items: center;
            background: url(img/background.jpg) no-repeat center center / cover;
        }
        .wrap{
            display: flex;
            width: 600px;
            height: 500px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border:10px solid white;
            border-radius: 10px;
            background-color: rgba(0,0,0,.6);
            backdrop-filter: blur(3px);
            color:white;
        }
        h1{
            margin-bottom: 20px;
            color:white;
        }
        form{
            width: 90%;
        }
        .group{
            margin-bottom: 20px;
        }
        .group label{
            font-size: 20px;
            line-height: 3;
        }
        .group input{
            width: 100%;
            padding-left:5px;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .btn-group{
            font-size: 0;
            text-align:center;
        }
        .btn-group .btn{
            padding:15px 10px;
            background-color: rgb(118, 117, 117);
            color:white;
            font-size: 14px;
            border:none;
            border-radius: 20px;
        } 
        .btn-group .btn + .btn{
            margin-left: 20px;
        }
        .btn-group .btn:hover{
            cursor: pointer;
            opacity: 0.8;
        }
        .error_message{
            color:red;
            text-align:center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>登入表單</h1>
        <form action="logincheck.php" method="POST">
            <div class="group">
                <label for="user-name">用戶名稱</label>
                <input type="text" name="user-name" id="user-name">
            </div>
            <div class="group">
                <label for="user-password">密碼</label>
                <input type="password" name="user-password" id="user-password">
            </div>
            <div class="btn-group">
                <input type="submit" class="btn">
                <input type="reset" class="btn">
            </div>
            <?php 
                session_start();
                if(isset($_SESSION["error_message"])){
                    echo "<p class='error_message'>".$_SESSION["error_message"]."</p>";
                    unset($_SESSION["error_message"]);
                }
            ?>
        </form>
    </div>
</body>
</html>