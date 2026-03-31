<?php 
    
    $name = htmlspecialchars($_POST["name"]);
    $gender = $_POST["gender"];
    $birth_date = $_POST["birth_date"];
    $id = htmlspecialchars($_POST["id"]);
    $school = $_POST["school"];
    $level = $_POST["level"];
    $parent = htmlspecialchars($_POST["parent"]);
    $relation = $_POST["relation"];
    $phone_number = htmlspecialchars($_POST["phone_number"]);
    $email = htmlspecialchars($_POST["email"]);
    $habit = $_POST["habit"];
    $take = $_POST["take"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>資料結果</title>
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
            width: 600px;
            height: 500px;
            padding: 10px;
            border:10px solid white;
            border-radius: 10px;
            background-color: rgba(0,0,0,.6);
            backdrop-filter: blur(3px);
            color:white;
        }
        .wrap h1{
            text-align: center;
        }
        .wrap ul{
            list-style-type: none;
            padding: 10px 10px;
        }
        .wrap ul li{
            margin-bottom: 7px;
            font-size: 18px;
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
        <form action="save.php">
            <h1>請確認資料是否正確</h1>
            <div class="user-data">
                <ul>
                    <li>姓名: <?php echo $name;?></li>
                    <li>性別: <?php echo $gender;?></li>
                    <li>出生年月日: <?php echo $birth_date;?></li>
                    <li>身份證字號: <?php echo $id;?></li>
                    <li>就讀學校: <?php echo $school;?></li>
                    <li>年級: <?php echo $level;?></li>
                    <li>家長/監護人: <?php echo $parent;?></li>
                    <li>與學員關係: <?php echo $relation;?></li>
                    <li>連絡電話: <?php echo $phone_number;?></li>
                    <li>電子郵件: <?php echo $email;?></li>
                    <li>飲食習慣: <?php echo $habit;?></li>
                    <li>接送方式: <?php echo $take;?></li>
                </ul>
            </div>
            <div class="btn-group">
                <input type="submit" value="確認送出" class="btn">
                <button type="button" class="btn" onclick="location.href='form.php'">回到上一頁</button>
            </div>
        </form>
    </div>
</body>
</html>