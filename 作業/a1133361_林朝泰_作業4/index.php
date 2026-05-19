<?php
// =========================================================================
// 1. 手動引入 PHPMailer 核心檔案 (請確保與 index.php 同一層目錄有 PHPMailer 資料夾)
// =========================================================================
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// =========================================================================
// 2. 資料庫連線設定 (具備自動建立資料表之安全防呆設計)
// =========================================================================
$host = 'localhost';
$dbuser = 'root'; // 依照您的環境修改
$dbpass = '';     // 依照您的環境修改
$dbname = 'spam_system';

// 嘗試連線（若資料庫不存在，嘗試先連線主機並自動建立）
$link = @mysqli_connect($host, $dbuser, $dbpass);
if (!$link) {
    die("<div style='color:#ef4444; font-family:sans-serif; font-weight:bold; padding:30px; background:#fee2e2; border-radius:8px; margin:20px; border:1px solid #fca5a5;'>
            資料庫主機連線失敗: " . mysqli_connect_error() . "<br>
            請確認 MySQL 服務已啟動，並確認帳號密碼是否正確！
         </div>");
}

// 自動建立並選擇資料庫
mysqli_query($link, "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($link, $dbname);

// 自動建立名單資料表
$create_table_sql = "
CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `created_at_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
mysqli_query($link, $create_table_sql);

$message = "";

// =========================================================================
// 功能 A：手動新增 Email 進入資料庫 (安全預備陳述式 Prepared Statement)
// =========================================================================
if (isset($_POST['add_email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = mysqli_prepare($link, "INSERT IGNORE INTO subscribers (email) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $email);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $message = "<div class='alert success'>🎉 <b>Email 新增成功！</b> 已順利寫入名單資料庫。</div>";
            } else {
                $message = "<div class='alert warning'>⚠️ <b>該 Email 已存在！</b> 資料庫內已有此名單，未重複寫入。</div>";
            }
        } else {
            $message = "<div class='alert danger'>❌ <b>寫入失敗：</b> " . mysqli_error($link) . "</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<div class='alert danger'>❌ 請輸入格式正確的 Email 信箱！</div>";
    }
}

// =========================================================================
// 核心 AJAX 端點：處理前端傳送的非同步非阻塞排程請求
// =========================================================================
if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    
    // API 1: 取得要發送的目標名單
    if ($_GET['action'] === 'get_targets') {
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 1;
        $emails = [];
        
        $sql = "SELECT email FROM subscribers ORDER BY id DESC LIMIT ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $limit);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($res)) {
                $emails[] = $row['email'];
            }
            mysqli_stmt_close($stmt);
        }
        
        echo json_encode(['success' => true, 'emails' => $emails]);
        exit;
    }
    
    // API 2: 單發一封郵件 (完美規避超時，由前端 JS 自主排程循環發送)
    if ($_GET['action'] === 'send_single') {
        $to = isset($_POST['email']) ? trim($_POST['email']) : '';
        $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        
        if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => '無效的 Email 信箱格式']);
            exit;
        }
        
        $mail = new PHPMailer(true);
        try {
            // --- SMTP 伺服器發信設定 (Gmail 整合) ---
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true; 
            $mail->Username   = 'a0903291833@gmail.com';       // 您的 Gmail 帳號
            $mail->Password   = 'jxmx nkrh kdkc kdiu';         // Google 16 位數應用程式密碼
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587; 
            $mail->CharSet    = 'UTF-8';

            // 設定寄件人
            $mail->setFrom('a0903291833@gmail.com', '垃圾郵件發送者');
            $mail->addAddress($to);

            // 設定郵件格式
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br(htmlspecialchars($content));

            $mail->send();
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $mail->ErrorInfo]);
        } catch (\Throwable $t) {
            echo json_encode(['success' => false, 'error' => $t->getMessage()]);
        }
        exit;
    }
}

// 取得最新名單總筆數
$count_res = mysqli_query($link, "SELECT COUNT(*) as total FROM subscribers");
$total_emails = 0;
if ($count_res) {
    $count_row = mysqli_fetch_assoc($count_res);
    $total_emails = intval($count_row['total']);
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>垃圾郵件寄送系統</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --success: #10b981;
            --success-hover: #059669;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg-main: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif, "Microsoft JhengHei";
            background-color: var(--bg-main);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            line-height: 1.5;
        }

        .container {
            max-width: 800px;
            background: var(--bg-card);
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            margin: 20px auto;
        }

        .header-section {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-section h2 {
            margin: 0 0 10px 0;
            color: #1e3a8a;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header-section p {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .badge-count {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
        }

        h3 {
            color: #0f172a;
            margin-top: 30px;
            font-size: 18px;
            border-left: 4px solid var(--primary);
            padding-left: 10px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
        }

        input[type="text"], input[type="email"], input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background-color: #f8fafc;
            transition: all 0.2s;
        }

        input:focus, textarea:focus {
            background-color: #fff;
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        /* 參數區網格配置（改為三欄式響應設計） */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        @media (max-width: 640px) {
            .settings-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 15px;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            width: 100%;
            color: white;
            text-align: center;
        }

        .btn-secondary { background-color: #475569; }
        .btn-secondary:hover { background-color: #334155; }

        .btn-success { background-color: var(--success); }
        .btn-success:hover { background-color: var(--success-hover); }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
        }
        .success { background-color: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .danger { background-color: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
        .warning { background-color: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

        /* 動態儀表進度板 */
        .progress-card {
            background: #fafafa;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
            display: none;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .progress-title {
            font-weight: 700;
            font-size: 15px;
            color: #1e293b;
        }

        .progress-bar-bg {
            background-color: #e2e8f0;
            border-radius: 9999px;
            height: 12px;
            overflow: hidden;
            position: relative;
            margin-bottom: 15px;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, #3b82f6, #10b981);
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 9999px;
        }

        .progress-status-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* 極速終端日誌盒 */
        .console-box {
            background: #0f172a;
            color: #38bdf8;
            border-radius: 10px;
            padding: 18px;
            margin-top: 20px;
            font-family: 'Fira Code', Consolas, Monaco, monospace;
            font-size: 12.5px;
            max-height: 250px;
            overflow-y: auto;
            line-height: 1.7;
            border: 1px solid #1e293b;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.8);
        }

        .console-line {
            margin-bottom: 4px;
            border-bottom: 1px dashed #1e293b;
            padding-bottom: 4px;
        }
        .console-success { color: #4ade80; font-weight: bold; }
        .console-error { color: #f87171; font-weight: bold; }
        .console-warning { color: #fbbf24; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h2>垃圾郵件自動寄送系統</h2>
    </div>

    <?php if(!empty($message)) echo $message; ?>

    <!-- 功能 A：名單資料庫收集 -->
    <h3>A. 訂閱戶管理資料庫 <span class="badge-count">現有 <?php echo $total_emails; ?> 筆</span></h3>
    <form action="" method="POST" class="form-group" style="margin-bottom: 35px;">
        <div style="display: flex; gap: 10px; align-items: flex-end;">
            <div style="flex-grow: 1;">
                <label for="email">快速登錄新信箱 (Email)：</label>
                <input type="email" id="email" name="email" required placeholder="example@gmail.com" style="background-color: #fff;">
            </div>
            <button type="submit" name="add_email" class="btn btn-secondary" style="width: auto; height: 46px; white-space: nowrap;">
                加入資料庫
            </button>
        </div>
    </form>

    <!-- 功能 B：寄送郵件設定與動態控制台 -->
    <h3>B. 自動群發郵件設定</h3>
    <form id="mail-settings-form" onsubmit="event.preventDefault(); initiateSendProcess();">
        <div class="form-group">
            <label for="subject">郵件主旨：</label>
            <input type="text" id="subject" name="subject" required placeholder="請輸入本次發送主旨">
        </div>
        
        <div class="form-group">
            <label for="content">信件HTML內文：</label>
            <textarea id="content" name="content" rows="4" required placeholder="請輸入主體信件內容..."></textarea>
        </div>

        <!-- 3欄式設定區域：限制筆數、重複寄送次數、安全延遲秒數 -->
        <div class="settings-grid">
            <div class="form-group">
                <label for="send_limit">發送目標上限 (筆數)：</label>
                <input type="number" id="send_limit" min="1" max="<?php echo ($total_emails > 0) ? $total_emails : 1; ?>" value="<?php echo ($total_emails > 0) ? $total_emails : 1; ?>" required>
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">由最新名單往前算</small>
            </div>

            <div class="form-group">
                <label for="repeat_times">重複寄送次數 (次)：</label>
                <input type="number" id="repeat_times" min="1" value="1" required>
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">同一人連續發送N次</small>
            </div>

            <div class="form-group">
                <label for="interval">安全間隔秒數 (延遲)：</label>
                <input type="number" id="interval" min="0" value="2" required>
                <small style="color: var(--text-muted); display: block; margin-top: 4px;">秒/每封 (防被判為垃圾信)</small>
            </div>
        </div>

        <button type="submit" id="btn-submit-send" class="btn btn-success" <?php echo ($total_emails === 0) ? 'disabled' : ''; ?>>
            🚀 開始自動群發排程
        </button>
        <?php if($total_emails === 0): ?>
            <p style="color: var(--danger); text-align: center; font-size: 13px; font-weight: bold; margin-top: 5px;">⚠️ 請先新增至少一筆訂閱信箱，才能開始寄送！</p>
        <?php endif; ?>
    </form>

    <!-- AJAX 動態進度條面板 -->
    <div id="progress-panel" class="progress-card">
        <div class="progress-header">
            <span class="progress-title">📧 發送任務排程監控面板</span>
            <span id="progress-percentage-top" class="badge-count" style="background:#10b981; color:#fff;">0%</span>
        </div>
        
        <div class="progress-bar-bg">
            <div id="progress-bar-fill" class="progress-bar-fill"></div>
        </div>

        <div class="progress-status-row">
            <span id="progress-status-text">初始化任務中...</span>
            <span id="progress-ratio-text">0 / 0</span>
        </div>

        <!-- 實時 Console 日誌輸出 -->
        <div id="console-log" class="console-box"></div>
    </div>
</div>

<!-- =========================================================================
    3. AJAX 排程控制引擎 (JavaScript)
========================================================================= -->
<script>
    let sendTargets = [];
    let currentIndex = 0;
    let totalTargets = 0;
    let timer = null;

    // 開始進行發送流程
    async function initiateSendProcess() {
        const limit = document.getElementById('send_limit').value;
        const repeatTimes = parseInt(document.getElementById('repeat_times').value) || 1;
        const panel = document.getElementById('progress-panel');
        const consoleLog = document.getElementById('console-log');
        const submitBtn = document.getElementById('btn-submit-send');

        // 初始化面板與控制台狀態
        panel.style.display = 'block';
        consoleLog.innerHTML = `<div class="console-line">🛠️ [系統準備] 正在跟資料庫請求發送目標 (限制 ${limit} 筆)...</div>`;
        submitBtn.disabled = true;
        submitBtn.innerText = '⏳ 排程引擎運轉中...';

        currentIndex = 0;
        updateProgressBar(0, 0);

        try {
            // 1. 取得基本發送名單
            const response = await fetch(`?action=get_targets&limit=${limit}`);
            const data = await response.json();
            
            if (data.success && data.emails.length > 0) {
                sendTargets = [];
                
                // 核心調度：根據設定的重複次數擴展發送佇列
                for (let email of data.emails) {
                    for (let r = 1; r <= repeatTimes; r++) {
                        sendTargets.push({
                            email: email,
                            runIndex: r,
                            totalRuns: repeatTimes
                        });
                    }
                }
                
                totalTargets = sendTargets.length;
                consoleLog.innerHTML += `<div class="console-line">✅ [成功] 已取得 ${data.emails.length} 位不重複對象。每人寄送 ${repeatTimes} 次，共計產生 ${totalTargets} 封待寄信件。</div>`;
                
                // 2. 啟動非同步循環寄信
                sendNextEmail();
            } else {
                consoleLog.innerHTML += `<div class="console-line console-error">❌ [異常] 無法取得名單，或名單為空。</div>`;
                submitBtn.disabled = false;
                submitBtn.innerText = '🚀 開始自動群發排程';
            }
        } catch (error) {
            consoleLog.innerHTML += `<div class="console-line console-error">❌ [核心錯誤] 無法向伺服器連線: ${error.message}</div>`;
            submitBtn.disabled = false;
            submitBtn.innerText = '🚀 開始自動群發排程';
        }
    }

    // 發送單一信件之遞迴控制函數
    async function sendNextEmail() {
        if (currentIndex >= totalTargets) {
            appendConsoleLog('🎉 [任務完成] 所有指定郵件皆已發送程序完畢！', 'console-success');
            document.getElementById('progress-bar-fill').style.background = '#10b981';
            document.getElementById('progress-status-text').innerText = '🎉 任務成功發送完畢！';
            resetSubmitBtn();
            return;
        }

        const currentTask = sendTargets[currentIndex];
        const email = currentTask.email;
        const runIndex = currentTask.runIndex;
        const totalRuns = currentTask.totalRuns;

        const subject = document.getElementById('subject').value;
        const content = document.getElementById('content').value;
        const interval = parseInt(document.getElementById('interval').value) * 1000;

        appendConsoleLog(`⏳ [${currentIndex + 1}/${totalTargets}] 正在發信至：${email} (第 ${runIndex}/${totalRuns} 次) ...`);
        document.getElementById('progress-status-text').innerText = `正在發送至: ${email} (重複次數: ${runIndex}/${totalRuns})...`;

        // 封裝 POST 資料
        const formData = new FormData();
        formData.append('email', email);
        formData.append('subject', subject);
        formData.append('content', content);

        try {
            const res = await fetch('?action=send_single', {
                method: 'POST',
                body: formData
            });
            const result = await res.json();

            if (result.success) {
                appendConsoleLog(`✔️ [成功] 成功送達: ${email} (第 ${runIndex}/${totalRuns} 次)`, 'console-success');
            } else {
                appendConsoleLog(`✖️ [失敗] 遞送失敗: ${email} (第 ${runIndex}/${totalRuns} 次) - 原因: ${result.error}`, 'console-error');
            }
        } catch (err) {
            appendConsoleLog(`✖️ [錯誤] 請求出錯: ${email} (原因: ${err.message})`, 'console-error');
        }

        // 更新進度條
        currentIndex++;
        updateProgressBar(currentIndex, totalTargets);

        // 如果還有下一封，排定延遲定時器
        if (currentIndex < totalTargets) {
            appendConsoleLog(`💤 等待設定的間隔時間: ${interval/1000} 秒...`);
            timer = setTimeout(sendNextEmail, interval);
        } else {
            // 已全數發完，進入下一遞迴處理收尾
            sendNextEmail();
        }
    }

    // 更新前端進度條 UI
    function updateProgressBar(current, total) {
        if (total === 0) return;
        const pct = Math.round((current / total) * 100);
        document.getElementById('progress-bar-fill').style.width = `${pct}%`;
        document.getElementById('progress-percentage-top').innerText = `${pct}%`;
        document.getElementById('progress-ratio-text').innerText = `${current} / ${total}`;
    }

    // 日誌輸出與自動滾動
    function appendConsoleLog(text, className = '') {
        const consoleLog = document.getElementById('console-log');
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0];
        
        let formattedText = `[${timeStr}] ${text}`;
        if (className) {
            formattedText = `<span class="${className}">${formattedText}</span>`;
        }
        
        consoleLog.innerHTML += `<div class="console-line">${formattedText}</div>`;
        consoleLog.scrollTop = consoleLog.scrollHeight;
    }

    // 重設發送按鈕
    function resetSubmitBtn() {
        const submitBtn = document.getElementById('btn-submit-send');
        submitBtn.disabled = false;
        submitBtn.innerText = '🚀 開始自動群發排程';
    }
</script>
</body>
</html>
<?php 
// 關閉資料庫連線
mysqli_close($link); 
?>