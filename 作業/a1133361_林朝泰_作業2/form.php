<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>夏令營報名表單</title>
    <style>
        body{
            font-family: "Segoe UI", "Microsoft JhengHei", sans-serif;
            background: url(img/background.jpg) no-repeat center center / cover;
        }
        h1{
            text-align: center;
            width: 50%;
            margin: 20px auto;
            color:white;
        }
        form{
            width: 50%;
            margin: auto;
            border:10px solid white;
            border-radius: 10px;
            padding: 15px 15px;
            color:white;
            background-color: rgba(0,0,0,.6);
            backdrop-filter: blur(3px);
        }
        fieldset {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid white;
            line-height: 2;
        }
        .btn{
            text-align: center;
        }
        input[type="submit"],input[type="reset"]{
            padding:15px 10px;
            margin: auto;
            margin-top: 5px;
            margin-bottom: 10px;
            margin-left: 10px;
            background-color: rgb(118, 117, 117); 
            color: white; 
            border: none;
            border-radius: 20px;
        }
        input[type="submit"]:hover,input[type="reset"]:hover{
            cursor: pointer;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <form action="confirm.php" method="POST">
        <h1>夏令營報名表單</h1>
        <fieldset>
            <legend>學生基本資料</legend>
        姓名: <input type="text" name="name" required> <br>
        性別: 男生<input type="radio" name="gender" value="m"> 女生<input type="radio" name="gender" value="f"> <br>
        出生年月日: <input type="date" name="birth_date" required> <br>
        身份證字號: <input type="text" name="id" required> <br>
        就讀學校: <select name="school" required>
                    <option value="勝利國小">勝利國小</option>
                    <option value="成功國小">成功國小</option>
                    <option value="竭誠國小">竭誠國小</option>
                    <option value="皇昌國小">皇昌國小</option>
                 </select> <br>
        就讀年級: <select name="level" required>
                    <option value="一年級">一年級</option>
                    <option value="二年級">二年級</option>
                    <option value="三年級">三年級</option>
                    <option value="四年級">四年級</option>
                    <option value="五年級">五年級</option>
                    <option value="六年級">六年級</option>
                 </select> <br>
        </fieldset>
        
        <fieldset>
            <legend>家長/緊急聯絡人資訊</legend>
            家長/監護人姓名: <input type="text" name="parent" required> <br>
            與學員關係: 父子/父女 <input type="radio" name="relation" value="父子/父女">
                       母子/母女 <input type="radio" name="relation" value="母子/母女">
                       其他 <input type="radio" name="relation" value="其他"> <br>
            連絡電話: <input type="text" name="phone_number" required> <br>
            常用 Email: <input type="email" name="email" required> <br>
        </fieldset>
        
        <fieldset>
            <legend>其他資訊</legend>
            飲食習慣: 
                    葷食 <input type="radio" name="habit" value="葷食"> 
                    素食 <input type="radio" name="habit" value="素食"> <br>
            接送方式: 
                    家長接送<input type="radio" name="take" value="家長接送">
                    自行前往<input type="radio" name="take" value="自行前往">
                    搭乘營隊接駁車<input type="radio" name="take" value="搭乘營隊接駁車">
        </fieldset>
        <div class="btn">
            <input type="reset" value="重新填寫">
            <input type="submit" value="送出資訊">
        </div>
    </form>
</body>
</html>