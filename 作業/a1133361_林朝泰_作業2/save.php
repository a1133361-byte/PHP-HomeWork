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
            display: flex;
            justify-content: center;
            flex-direction: column;
            border:10px solid white;
            border-radius: 10px;
            background-color: rgba(0,0,0,.6);
            backdrop-filter: blur(3px);
            color:white;
        }
        .wrap h1{
            width: 100%;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>已收到表單內容，祝您旅途愉快!😄</h1>
    </div>
</body>
</html>