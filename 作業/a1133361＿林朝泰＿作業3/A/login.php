<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
</head>
<body>
    <form action="logincheck.php" method="POST">
        <div class="group">
            <span>請選擇身分:</span>
            <select name="role">
                <option value="student">學生</option>
                <option value="teacher">教師</option>
                <option value="administrator">管理者</option>
            </select>
        </div>

        <div class="group">
            <label>
                名稱:<input type="text" name="name" placeholder="請輸入名稱" required>
            </label>
        </div>
        
        <div class="group">
            <label>
                密碼:<input type="password" name="password" placeholder="請輸入密碼" required>
            </label>
        </div>

        
        <button type="submit">送出</button>
    </form>
</body>
</html>