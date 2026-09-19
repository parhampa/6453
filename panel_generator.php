<?php
/**
 * Panel Generator — سازنده‌ی پنل جدید برای پروژه‌ی cafe_menu
 * PHP 7+
 * این فایل را در ریشه‌ی پروژه (کنار admin/ و client/ و cuser/) قرار دهید.
 */

$errors = [];
$log = [];
$success = false;

$defaults = [
    'folder' => '',
    'panel_title' => '',
    'table' => '',
    'user_field' => '',
    'pass_field' => 'pass',
    'active_field' => 'status',
    'session_key' => '',
    'display_field' => '',
    'id_func' => '',
    'profile_fields' => '',
    'source_panel' => 'admin',
    'copy_assets' => '1',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $in = [];
    foreach ($defaults as $k => $v) {
        $in[$k] = trim($_POST[$k] ?? $v);
    }

    /* ============ اعتبارسنجی ============ */
    if ($in['folder'] === '' || !preg_match('/^[a-zA-Z0-9_\-]+$/', $in['folder'])) {
        $errors[] = 'نام پوشه نامعتبر است (فقط حروف، عدد، _ و -).';
    }
    if ($in['panel_title'] === '') $errors[] = 'عنوان پنل الزامی است.';
    if ($in['table'] === '') $errors[] = 'نام جدول الزامی است.';
    if ($in['user_field'] === '') $errors[] = 'فیلد نام کاربری الزامی است.';
    if ($in['pass_field'] === '') $errors[] = 'فیلد رمز عبور الزامی است.';

    if ($in['session_key'] === '') {
        $in['session_key'] = $in['user_field'];
    }
    if ($in['id_func'] === '') {
        $in['id_func'] = 'get_' . preg_replace('/[^a-z0-9_]/i', '_', $in['folder']) . '_id';
    }
    if ($in['display_field'] === '') {
        $in['display_field'] = $in['user_field'];
    }

    $source_path = '';
    if ($in['copy_assets'] === '1' && $in['source_panel'] !== '') {
        $source_path = __DIR__ . DIRECTORY_SEPARATOR . $in['source_panel'];
        if (!is_dir($source_path)) {
            $errors[] = 'پوشه‌ی منبع «' . $in['source_panel'] . '» پیدا نشد.';
        }
    }

    if (empty($errors)) {

        $folder_path = __DIR__ . DIRECTORY_SEPARATOR . $in['folder'];

        if (file_exists($folder_path)) {
            $errors[] = 'پوشه «' . $in['folder'] . '» از قبل وجود دارد.';
        } elseif (!@mkdir($folder_path, 0755, true)) {
            $errors[] = 'ساخت پوشه ممکن نشد. دسترسی نوشتن روی پوشه‌ی ریشه را چک کنید.';
        } else {

            /* ---- پردازش فیلدهای پروفایل ---- */
            $profile_fields = [];
            if ($in['profile_fields'] !== '') {
                foreach (explode(',', $in['profile_fields']) as $pair) {
                    $pair = trim($pair);
                    if ($pair === '') continue;
                    if (strpos($pair, ':') !== false) {
                        list($f, $label) = explode(':', $pair, 2);
                        $profile_fields[trim($f)] = trim($label);
                    } else {
                        $profile_fields[$pair] = $pair;
                    }
                }
            }

            /* ---- ساخت placeholderها ---- */
            $placeholders = [
                '{{FOLDER}}' => $in['folder'],
                '{{PANEL_TITLE}}' => $in['panel_title'],
                '{{TABLE}}' => $in['table'],
                '{{USER_FIELD}}' => $in['user_field'],
                '{{PASS_FIELD}}' => $in['pass_field'],
                '{{ACTIVE_FIELD}}' => $in['active_field'],
                '{{SESSION_KEY}}' => $in['session_key'],
                '{{DISPLAY_FIELD}}' => $in['display_field'],
                '{{ID_FUNC}}' => $in['id_func'],
                '{{PROFILE_FIELDS_LIST}}' => implode(',', array_keys($profile_fields)),
            ];

            /* ---- ساخت HTML فرم پروفایل ---- */
            $profile_html = '';
            foreach ($profile_fields as $f => $label) {
                $f_esc = htmlspecialchars($f, ENT_QUOTES, 'UTF-8');
                $l_esc = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
                $profile_html .=
                    '<label class="form-label">' . $l_esc . '</label>' . "\n" .
                    '<input type="text" name="' . $f_esc . '" id="' . $f_esc . '" ' .
                    'class="form-control mb-3 datasender" ' .
                    'value="<?= htmlspecialchars($row[\'' . $f . '\'] ?? \'\', ENT_QUOTES, \'UTF-8\') ?>">' . "\n\n";
            }
            $placeholders['{{PROFILE_HTML}}'] = $profile_html;

            /* ---- ساخت PHP ویرایش پروفایل ---- */
            $edit_vars = '';
            $update_set = [];
            foreach ($profile_fields as $f => $label) {
                $edit_vars .= '$' . $f . ' = $ml->set_name("' . $f . '")->set_title("' . $label . '")->set_important(false)->post_str();' . "\n";
                $update_set[] = "`$f`='$" . $f . "'";
            }
            $placeholders['{{PROFILE_EDIT_VARS}}'] = $edit_vars;
            $placeholders['{{PROFILE_EDIT_UPDATE}}'] = implode(",\n            ", $update_set);

            /* ---- لیست فایل‌ها ---- */
            $files = [
                'check_admin_session.php' => 'tpl_check_admin_session',
                'login.php' => 'tpl_login',
                'logout.php' => 'tpl_logout',
                'index.php' => 'tpl_index',
                'nav.php' => 'tpl_nav',
                'top.php' => 'tpl_top',
                'footer.php' => 'tpl_footer',
                'calhead.php' => 'tpl_calhead',
                'profile.php' => 'tpl_profile',
                'profile_edit.php' => 'tpl_profile_edit',
                'security.php' => 'tpl_security',
                'security_edit.php' => 'tpl_security_edit',
                'cap.php' => 'tpl_cap',
                'README.txt' => 'tpl_readme',
            ];

            foreach ($files as $filename => $func) {
                $content = $func();
                $content = strtr($content, $placeholders);
                $full_path = $folder_path . DIRECTORY_SEPARATOR . $filename;

                if (@file_put_contents($full_path, $content) === false) {
                    $errors[] = 'نوشتن فایل ' . $filename . ' ممکن نشد.';
                } else {
                    $log[] = $filename;
                }
            }

            /* ---- کپی assetها از پوشه‌ی منبع ---- */
            if (empty($errors) && $source_path !== '' && is_dir($source_path)) {
                $copied = copy_recursive_assets($source_path, $folder_path);
                if ($copied > 0) {
                    $log[] = '[assets: ' . $copied . ' فایل از پوشه «' . $in['source_panel'] . '» کپی شد]';
                }
            }

            if (empty($errors)) {
                $success = true;
            }
        }
    }
}

/* ============================================================
   کپی بازگشتی assetها (بدون .php)
   ============================================================ */
function copy_recursive_assets($src, $dst)
{
    $count = 0;
    if (!is_dir($src) || !is_dir($dst)) return 0;

    $items = @scandir($src);
    if ($items === false) return 0;

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $src_path = $src . DIRECTORY_SEPARATOR . $item;
        $dst_path = $dst . DIRECTORY_SEPARATOR . $item;

        if (is_dir($src_path)) {
            if (!is_dir($dst_path)) {
                @mkdir($dst_path, 0755, true);
            }
            $count += copy_recursive_assets($src_path, $dst_path);
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if ($ext === 'php') continue;
            if (in_array($item, ['README.txt', 'README.md'], true)) continue;

            if (@copy($src_path, $dst_path)) {
                $count++;
            }
        }
    }

    return $count;
}

?>
    <!DOCTYPE html>
    <html lang="fa" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>سازنده‌ی پنل جدید</title>
        <style>
            :root {
                --panel-primary: #2c3e50;
                --panel-accent: #16a085;
                --panel-bg: #f4f6f9;
                --panel-text: #2c3e50;
            }

            * {
                box-sizing: border-box;
            }

            body {
                background: var(--panel-bg);
                font-family: Tahoma, "Segoe UI", sans-serif;
                color: var(--panel-text);
                margin: 0;
                padding: 30px 16px;
            }

            .container {
                max-width: 780px;
                margin: 0 auto;
            }

            .card {
                background: #fff;
                border-radius: 14px;
                box-shadow: 0 6px 30px rgba(0, 0, 0, 0.08);
                overflow: hidden;
                margin-bottom: 20px;
            }

            .card-header {
                background: linear-gradient(135deg, var(--panel-primary) 0%, #3d5468 100%);
                color: #fff;
                padding: 18px 24px;
                font-weight: bold;
                font-size: 15px;
                border-bottom: 3px solid var(--panel-accent);
            }

            .card-body {
                padding: 22px 24px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            @media (max-width: 600px) {
                .form-row {
                    grid-template-columns: 1fr;
                }
            }

            .field {
                display: flex;
                flex-direction: column;
                gap: 5px;
                margin-bottom: 14px;
            }

            .field.full {
                grid-column: 1 / -1;
            }

            label {
                font-size: 12.5px;
                font-weight: bold;
                color: #5a6b7a;
            }

            label .req {
                color: #e74c3c;
            }

            input[type="text"], input[type="number"], textarea, select {
                padding: 9px 12px;
                border: 1.5px solid #e8ecef;
                border-radius: 8px;
                font-size: 13px;
                font-family: Tahoma;
                background: #fbfcfd;
                color: var(--panel-text);
                transition: all 0.2s ease;
            }

            input[type="text"]:focus, input[type="number"]:focus,
            textarea:focus, select:focus {
                border-color: var(--panel-accent);
                background: #fff;
                box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
                outline: none;
            }

            textarea {
                min-height: 70px;
                resize: vertical;
            }

            .hint {
                font-size: 10.5px;
                color: #95a5a6;
                margin-top: -2px;
            }

            .checkbox-wrap {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 14px;
                background: #fbfcfd;
                border: 1.5px solid #e8ecef;
                border-radius: 8px;
            }

            .checkbox-wrap input[type="checkbox"] {
                width: 18px;
                height: 18px;
                accent-color: var(--panel-accent);
                cursor: pointer;
            }

            .btn-submit {
                background: var(--panel-accent);
                color: #fff;
                border: none;
                padding: 12px 26px;
                border-radius: 9px;
                font-size: 14px;
                font-weight: bold;
                font-family: Tahoma;
                cursor: pointer;
                transition: all 0.2s ease;
                box-shadow: 0 4px 14px rgba(22, 160, 133, 0.3);
            }

            .btn-submit:hover {
                background: #12876f;
                transform: translateY(-1px);
            }

            .alert {
                border-radius: 10px;
                padding: 12px 18px;
                font-size: 13px;
                margin-bottom: 16px;
            }

            .alert-error {
                background: #fdecea;
                color: #c0392b;
                border: 1px solid #f5b7b1;
            }

            .alert-success {
                background: #d5f4ea;
                color: #0d7a5f;
                border: 1px solid #a8e6cf;
            }

            .alert ul {
                margin: 0;
                padding-right: 20px;
            }

            .log-list {
                list-style: none;
                padding: 0;
                margin: 0;
                font-size: 12.5px;
                font-family: monospace;
                direction: ltr;
            }

            .log-list li {
                padding: 4px 8px;
                border-bottom: 1px dashed #eef2f5;
                color: #16a085;
            }

            .log-list li:last-child {
                border-bottom: none;
            }

            .log-list li::before {
                content: "✓  ";
                font-weight: bold;
            }
        </style>
    </head>
    <body>

    <div class="container">
        <div class="card">
            <div class="card-header">
                🛠 سازنده‌ی پنل جدید — Panel Generator
            </div>
            <div class="card-body">

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <strong>خطا:</strong>
                        <ul>
                            <?php foreach ($errors as $e): ?>
                                <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <strong>✅ پنل «<?= htmlspecialchars($_POST['folder'], ENT_QUOTES, 'UTF-8') ?>» با موفقیت ساخته
                            شد.</strong>
                        <br>
                        <small>حالا از مسیر <code><?= htmlspecialchars($_POST['folder'], ENT_QUOTES, 'UTF-8') ?>
                                /login.php</code> وارد شوید.</small>
                    </div>
                    <div style="margin-top:14px;background:#fbfcfd;border:1px solid #eef2f5;border-radius:8px;padding:12px 16px;">
                        <div style="font-size:12px;font-weight:bold;color:#2c3e50;margin-bottom:8px;">فایل‌های
                            ساخته‌شده:
                        </div>
                        <ul class="log-list">
                            <?php foreach ($log as $f): ?>
                                <li><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" style="margin-top:10px;">
                    <div class="form-row">

                        <div class="field">
                            <label>نام پوشه‌ی پنل <span class="req">*</span></label>
                            <input type="text" name="folder" required
                                   placeholder="مثلاً marketer یا admin2"
                                   value="<?= htmlspecialchars($_POST['folder'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="hint">فقط حروف، عدد، _ و -</div>
                        </div>

                        <div class="field">
                            <label>عنوان فارسی پنل <span class="req">*</span></label>
                            <input type="text" name="panel_title" required
                                   placeholder="مثلاً پنل بازاریاب‌ها"
                                   value="<?= htmlspecialchars($_POST['panel_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>نام جدول در دیتابیس <span class="req">*</span></label>
                            <input type="text" name="table" required
                                   placeholder="مثلاً marketers"
                                   value="<?= htmlspecialchars($_POST['table'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>فیلد نام کاربری <span class="req">*</span></label>
                            <input type="text" name="user_field" required
                                   placeholder="مثلاً tel یا username"
                                   value="<?= htmlspecialchars($_POST['user_field'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>فیلد رمز عبور <span class="req">*</span></label>
                            <input type="text" name="pass_field" required
                                   placeholder="مثلاً pass"
                                   value="<?= htmlspecialchars($_POST['pass_field'] ?? 'pass', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>فیلد فعال/غیرفعال</label>
                            <input type="text" name="active_field"
                                   placeholder="مثلاً status یا active"
                                   value="<?= htmlspecialchars($_POST['active_field'] ?? 'status', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="hint">اگر جدول ستون فعال/غیرفعال ندارد، <code>1</code> بگذارید.</div>
                        </div>

                        <div class="field">
                            <label>کلید سشن</label>
                            <input type="text" name="session_key"
                                   placeholder="پیش‌فرض: هم‌نام فیلد نام کاربری"
                                   value="<?= htmlspecialchars($_POST['session_key'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>فیلد نام نمایشی</label>
                            <input type="text" name="display_field"
                                   placeholder="مثلاً fullname یا name"
                                   value="<?= htmlspecialchars($_POST['display_field'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>نام تابع ID</label>
                            <input type="text" name="id_func"
                                   placeholder="پیش‌فرض: get_{folder}_id"
                                   value="<?= htmlspecialchars($_POST['id_func'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        </div>

                        <div class="field">
                            <label>پوشه‌ی منبع برای کپی assetها</label>
                            <input type="text" name="source_panel"
                                   placeholder="مثلاً admin"
                                   value="<?= htmlspecialchars($_POST['source_panel'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>">
                            <div class="hint">پوشه‌ای که css/js/bootstrap در آن قرار دارد.</div>
                        </div>

                        <div class="field full">
                            <label>فیلدهای قابل ویرایش در پروفایل</label>
                            <textarea name="profile_fields"
                                      placeholder="fullname:نام و نام خانوادگی,email:ایمیل"><?= htmlspecialchars($_POST['profile_fields'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            <div class="hint">فرمت: <code>field:عنوان فارسی</code> با کاما جدا کنید.</div>
                        </div>

                        <div class="field full">
                            <label class="checkbox-wrap">
                                <input type="checkbox" name="copy_assets" value="1"
                                    <?= (!isset($_POST['copy_assets']) || $_POST['copy_assets'] == '1') ? 'checked' : '' ?>>
                                <span>کپی خودکار پوشه‌های asset (css، js، bootstrap و...) از پوشه‌ی منبع</span>
                            </label>
                        </div>

                    </div>

                    <button type="submit" class="btn-submit">🚀 ساخت پنل</button>
                </form>

            </div>
        </div>
    </div>

    </body>
    </html>

<?php
/* ============================================================
   TEMPLATES
   ============================================================ */

function tpl_check_admin_session()
{
    return <<<'EOT'
<?php
/**
 * Auto-generated by Panel Generator
 */
$adminses = new ses();
$adminses->check_session("{{TABLE}}", "{{USER_FIELD}}", "{{ACTIVE_FIELD}}", "login.php", "شما به این قسمت دسترسی ندارید");
?>
EOT;
}

function tpl_login()
{
    return <<<'EOT'
<?php
session_start();
include("../lib/php/lib_include.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>ورود به {{PANEL_TITLE}}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<style>
    html, body { height: 100%; direction: rtl; }
    body {
        font-family: Tahoma, "Segoe UI", sans-serif;
        font-size: 13px;
        background: linear-gradient(135deg, #f4f6f9 0%, #e3e9ef 100%);
        color: #2c3e50;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
    }
    .login-wrapper { width: 100%; max-width: 420px; padding: 16px; }
    .login-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
        overflow: hidden;
    }
    .login-card .login-header {
        background: #2c3e50;
        color: #fff;
        text-align: center;
        padding: 22px 18px 18px;
        border-bottom: 3px solid #16a085;
    }
    .login-card .login-header h3 { font-size: 17px; font-weight: bold; margin: 0; }
    .login-card .login-body { padding: 24px 26px; }
    .login-card .form-control {
        border: 1px solid #d6dbe1;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 13px;
        background: #fbfcfd;
    }
    .login-card .form-control:focus {
        border-color: #16a085;
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.15);
        background: #fff;
    }
    .login-footer {
        text-align: center;
        padding: 14px 18px;
        background: #f9fafb;
        border-top: 1px solid #eef2f5;
        font-size: 12px;
        color: #7f8c9b;
    }
</style>
<body>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h3><i class="fa fa-lock"></i> ورود به {{PANEL_TITLE}}</h3>
        </div>
        <div class="login-body">
            <?php
            $lg = new loginpg();
            $lg->inputclass = "form-control";
            $lg->showlogin("{{TABLE}}", "{{USER_FIELD}}", "{{PASS_FIELD}}", "index.php");
            ?>
        </div>
        <div class="login-footer">
            <i class="fa fa-shield-halved"></i> پنل مدیریت
        </div>
    </div>
</div>

</body>
</html>
EOT;
}

function tpl_logout()
{
    return <<<'EOT'
<?php
session_start();
echo("خداحافظ");
session_destroy();
?>
<script>
    location.replace("login.php");
</script>
EOT;
}

function tpl_index()
{
    return <<<'EOT'
<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>داشبورد — {{PANEL_TITLE}}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<?php include("calhead.php"); ?>
<style>
    body { background: #f4f6f9; font-family: Tahoma, sans-serif; color: #2c3e50; }
    .welcome-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        padding: 40px 30px;
        text-align: center;
        margin: 40px auto;
        max-width: 640px;
    }
    .welcome-card .icon-box {
        width: 80px; height: 80px;
        background: linear-gradient(135deg, #16a085, #1abc9c);
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 34px;
        margin: 0 auto 20px;
        box-shadow: 0 8px 24px rgba(22,160,133,0.3);
    }
    .welcome-card h2 { font-size: 20px; color: #2c3e50; margin: 0 0 10px; }
    .welcome-card p { font-size: 13px; color: #7f8c9b; line-height: 1.9; }
    .welcome-card .user-id { font-size: 11px; color: #95a5a6; margin-top: 20px; }
</style>
<body style="direction: rtl;">

<?php
$uid = (int){{ID_FUNC}}();
?>

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container-fluid px-3 py-3">
    <div class="welcome-card">
        <div class="icon-box">
            <i class="fa fa-gauge-high"></i>
        </div>
        <h2>خوش آمدید به {{PANEL_TITLE}}</h2>
        <p>این پنل به‌صورت خودکار توسط Panel Generator ساخته شده است.<br>
           از منوی کناری می‌توانید به بخش‌های مختلف دسترسی داشته باشید.</p>
        <div class="user-id">شناسه‌ی شما: <strong>#<?php echo $uid; ?></strong></div>
    </div>
</div>

<?php include("footer.php"); ?>
</body>
</html>
EOT;
}

function tpl_nav()
{
    return <<<'EOT'
<?php
$current_page = basename($_SERVER['PHP_SELF']);

if (!function_exists('is_active')) {
    function is_active($page, $current)
    {
        return $page === $current ? 'active' : '';
    }
}

/* واکشی اطلاعات کاربر از سشن */
$thisuser = $_SESSION['{{SESSION_KEY}}'] ?? '';
$dbt = new database();
$dbt->connect();

$fm_tmp = new makeform();
$user_safe = $fm_tmp->sqlstr($thisuser);

$dbt->query("select * from `{{TABLE}}` where `{{USER_FIELD}}`='$user_safe' limit 1");
$fildt = mysqli_fetch_assoc($dbt->res);

$display_val = $fildt['{{DISPLAY_FIELD}}'] ?? '';
$avatar = mb_substr($display_val, 0, 1, 'UTF-8');
?>

<style>
    .sidebar-menu > li > a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        color: #c8d1da;
        text-decoration: none;
        font-size: 13.5px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
        position: relative;
        margin: 2px 0;
    }
    .sidebar-menu > li > a:hover { background: rgba(255,255,255,0.06); color: #fff; }
    .sidebar-menu > li > a i:first-child {
        width: 20px; text-align: center; font-size: 14px;
        color: var(--panel-accent, #16a085);
    }
    .sidebar-menu > li > a .menu-text { flex-grow: 1; }
    .sidebar-menu > li > a.active {
        background: linear-gradient(90deg, rgba(22,160,133,0.30) 0%, rgba(22,160,133,0.15) 60%, transparent 100%);
        color: #fff;
        font-weight: 700;
        padding-right: 20px;
        box-shadow: inset -3px 0 0 0 var(--panel-accent, #16a085);
    }
    .sidebar-menu > li > a.active i:first-child { color: #1abc9c; transform: scale(1.15); }
    .sidebar-menu .divider {
        list-style: none;
        height: 1px;
        background: rgba(255,255,255,0.08);
        margin: 10px 16px;
        border-radius: 1px;
    }
</style>

<ul class="sidebar-menu" id="sidebarMenu">

    <li>
        <a href="index.php" class="<?php echo is_active('index.php', $current_page); ?>">
            <i class="fas fa-gauge-high"></i>
            <span class="menu-text">داشبورد</span>
        </a>
    </li>

    <li class="divider"></li>

</ul>
</div>
</aside>

<div class="main-content">
    <header class="dashboard-header">
        <div class="header-container">
            <button class="hamburger-btn" id="hamburgerBtn"><i class="fas fa-bars"></i></button>
            <div class="search-wrapper header-search-desktop">
                <div class="input-group search-input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="جستجو..." id="desktopSearchInput">
                </div>
            </div>
            <div class="user-actions">
                <div class="dropdown profile-dropdown">
                    <div class="user-info dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown"
                         aria-expanded="false">
                        <div class="user-avatar"><span><?php echo htmlspecialchars($avatar); ?></span></div>
                        <span class="user-name"><?php echo htmlspecialchars($display_val); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-circle"></i> پروفایل من</a></li>
                        <li><a class="dropdown-item" href="security.php"><i class="fas fa-lock"></i> تغییر رمز عبور</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> خروج</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="content-area">
EOT;
}

function tpl_top()
{
    return <<<'EOT'
<div class="admin-wrapper">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-inner">
            <div class="text-center mb-3 mt-2">
                <i class="fas fa-user-shield fs-1" style="color:#7aa9e2;"></i>
                <h5 class="fw-semibold mt-2" style="color:#eef2ff">{{PANEL_TITLE}}</h5>
            </div>
            <div class="sidebar-search-mobile" id="sidebarSearchMobile">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="جستجو..." id="mobileSearchInput">
                </div>
            </div>
EOT;
}

function tpl_footer()
{
    return <<<'EOT'
</div>
</div>
</div>

<script>
    (function () {
        var sidebar = document.getElementById('mainSidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        var hamburgerBtn = document.getElementById('hamburgerBtn');
        var body = document.body;
        if (!sidebar) return;

        var isSidebarOpen = false;

        function openSidebar() {
            if (window.innerWidth >= 992) return;
            sidebar.classList.add('open');
            if (backdrop) backdrop.classList.add('show');
            isSidebarOpen = true;
            body.classList.add('menu-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('show');
            isSidebarOpen = false;
            body.classList.remove('menu-open');
        }

        function toggleSidebar() { isSidebarOpen ? closeSidebar() : openSidebar(); }

        if (backdrop) backdrop.addEventListener('click', closeSidebar);
        if (hamburgerBtn) hamburgerBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });
    })();
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOT;
}

function tpl_calhead()
{
    return <<<'EOT'
<link rel="stylesheet" type="text/css" media="all" href="../lib/js/cal/skins/aqua/theme.css" title="Aqua"/>
<script src="../lib/js/cal/jalali.js"></script>
<script src="../lib/js/cal/calendar.js"></script>
<script src="../lib/js/cal/calendar-setup.js"></script>
<script src="../lib/js/cal/lang/calendar-fa.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="../lib/js/selector.js"></script>
<style>
    .calendar { z-index: 9999; }
</style>

<script>
    function gregorian_to_jalali(gy, gm, gd) {
        var g_d_m, jy, jm, jd, gy2, days;
        g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        gy2 = (gm > 2) ? (gy + 1) : gy;
        days = 355666 + (365 * gy) + ~~((gy2 + 3) / 4) - ~~((gy2 + 99) / 100) + ~~((gy2 + 399) / 400) + gd + g_d_m[gm - 1];
        jy = -1595 + (33 * ~~(days / 12053));
        days %= 12053;
        jy += 4 * ~~(days / 1461);
        days %= 1461;
        if (days > 365) {
            jy += ~~((days - 1) / 365);
            days = (days - 1) % 365;
        }
        if (days < 186) {
            jm = 1 + ~~(days / 31);
            jd = 1 + (days % 31);
        } else {
            jm = 7 + ~~((days - 186) / 30);
            jd = 1 + ((days - 186) % 30);
        }
        return [jy, jm, jd];
    }

    function jalali_to_gregorian(jy, jm, jd) {
        var sal_a, gy, gm, gd, days;
        jy += 1595;
        days = -355668 + (365 * jy) + (~~(jy / 33) * 8) + ~~(((jy % 33) + 3) / 4) + jd + ((jm < 7) ? (jm - 1) * 31 : ((jm - 7) * 30) + 186);
        gy = 400 * ~~(days / 146097);
        days %= 146097;
        if (days > 36524) {
            gy += 100 * ~~(--days / 36524);
            days %= 36524;
            if (days >= 365) days++;
        }
        gy += 4 * ~~(days / 1461);
        days %= 1461;
        if (days > 365) {
            gy += ~~((days - 1) / 365);
            days = (days - 1) % 365;
        }
        gd = days + 1;
        sal_a = [0, 31, ((gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        for (gm = 0; gm < 13 && gd > sal_a[gm]; gm++) gd -= sal_a[gm];
        return [gy, gm, gd];
    }

    function shamsibemiladi(id) {
        var el = document.getElementById('ta' + id);
        if (!el) return;
        var thisval = el.value;
        if (thisval.length == 10) {
            var d = new Date(thisval);
            var day = d.getDate();
            var month = d.getMonth() + 1;
            var year = d.getFullYear();
            var miladi = jalali_to_gregorian(year, month, day);
            var gyear = miladi[0];
            var gmont = miladi[1] < 10 ? "0" + miladi[1] : miladi[1];
            var gday = miladi[2] < 10 ? "0" + miladi[2] : miladi[2];
            var target = document.getElementById(id);
            if (target) target.value = gyear + "-" + gmont + "-" + gday;
        } else {
            var target2 = document.getElementById(id);
            if (target2) target2.value = "";
        }
    }
</script>

<?php
if (!function_exists('{{ID_FUNC}}')) {
    function {{ID_FUNC}}()
    {
        if (!isset($_SESSION['{{SESSION_KEY}}'])) return 0;

        $key = $_SESSION['{{SESSION_KEY}}'];

        $db = new database();
        $db->connect();

        $fm_tmp = new makeform();
        $key_safe = $fm_tmp->sqlstr($key);

        $db->query("SELECT `id` FROM `{{TABLE}}` WHERE `{{USER_FIELD}}`='$key_safe' LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) return 0;

        $row = mysqli_fetch_assoc($db->res);
        return (int)$row['id'];
    }
}
?>
EOT;
}

function tpl_profile()
{
    return <<<'EOT'
<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");

$thisuser = $_SESSION['{{SESSION_KEY}}'] ?? '';
$db = new database();
$db->connect();
$fm_tmp = new makeform();
$user_safe = $fm_tmp->sqlstr($thisuser);

$db->query("SELECT * FROM `{{TABLE}}` WHERE `{{USER_FIELD}}`='$user_safe' LIMIT 1");
$row = mysqli_fetch_assoc($db->res);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>پروفایل — {{PANEL_TITLE}}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<?php include("calhead.php"); ?>
<style>
    body { background: #f4f6f9; font-family: Tahoma, sans-serif; color: #2c3e50; }
    .panel-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        max-width: 640px;
        margin: 30px auto;
    }
    .panel-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #3d5468);
        color: #fff;
        text-align: center;
        padding: 14px;
        font-weight: bold;
        border-bottom: 3px solid #16a085;
    }
    .panel-card .card-body { padding: 20px; }
    .form-label { font-weight: 600; font-size: 13px; margin-bottom: 6px; }
    .form-control {
        border: 1.5px solid #e8ecef;
        border-radius: 8px;
        padding: 9px 12px;
        font-family: Tahoma;
        font-size: 13px;
        background: #fbfcfd;
    }
    .form-control:focus {
        border-color: #16a085;
        box-shadow: 0 0 0 3px rgba(22,160,133,0.12);
        outline: none;
    }
    .btn-panel {
        background: #16a085;
        color: #fff;
        border: none;
        padding: 11px;
        border-radius: 9px;
        font-weight: bold;
        width: 100%;
        font-family: Tahoma;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-panel:hover { background: #12876f; }
</style>
<body>

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container-fluid">
    <div class="panel-card">
        <div class="card-header">ویرایش پروفایل</div>
        <div class="card-body">

            <form id="profile-form" onsubmit="return false;">

{{PROFILE_HTML}}
                <button type="button" class="btn btn-panel mt-2" onclick="snddata()">اعمال تغییرات</button>

            </form>

        </div>
    </div>
</div>

<div id="msgBox" style="display:none;position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#fff;padding:14px 24px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.2);z-index:99999;font-size:13px;"></div>

<script>
    function showMsg(type, msg) {
        var box = document.getElementById('msgBox');
        box.style.display = 'block';
        box.style.background = type === 'success' ? '#d5f4ea' : '#fdecea';
        box.style.color = type === 'success' ? '#0d7a5f' : '#c0392b';
        box.innerText = msg;
        setTimeout(function () { box.style.display = 'none'; }, 3000);
    }

    function snddata() {
        var formData = {};
        $('.datasender').each(function () {
            formData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: 'profile_edit.php',
            type: 'POST',
            data: formData,
            dataType: 'text',
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; }
                catch (e) { showMsg('error', 'پاسخ نامعتبر'); return; }
                showMsg(parseInt(res.status, 10) === 1 ? 'success' : 'error', res.msg);
            },
            error: function () { showMsg('error', 'خطای ارتباط'); }
        });
    }
</script>

<?php include("footer.php"); ?>
</body>
</html>
EOT;
}

function tpl_profile_edit()
{
    return <<<'EOT'
<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

if (ob_get_length() > 0) ob_clean();
header('Content-Type: application/json; charset=utf-8');

$ml = new mobile_input();

{{PROFILE_EDIT_VARS}}

$thisuser = $_SESSION['{{SESSION_KEY}}'] ?? '';

function reply($status, $msg)
{
    if (ob_get_length() > 0) ob_clean();
    echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    while (ob_get_level() > 0) ob_end_flush();
    exit;
}

$db = new database();
$db->connect();
$fm = new makeform();

$user_safe = $fm->sqlstr($thisuser);

$sql = "UPDATE `{{TABLE}}` SET
            {{PROFILE_EDIT_UPDATE}}
        WHERE `{{USER_FIELD}}` = '$user_safe'";

$db->query($sql);

if (ob_get_length() > 0) ob_clean();

if ($db->res) {
    reply(1, 'اطلاعات با موفقیت بروزرسانی شد.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>
EOT;
}

function tpl_security()
{
    return <<<'EOT'
<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>تغییر رمز عبور — {{PANEL_TITLE}}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/w3.css">
<link href="../fontawesome-free-6.7.2-web/css/all.min.css" rel="stylesheet">
<script src="../lib/js/jquery.js"></script>
<script src="../lib/js/palib.js"></script>
<script src="js/fnuser.js"></script>
<script src="js/modal.js"></script>
<link href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/new.css">
<style>
    body { background: #f4f6f9; font-family: Tahoma, sans-serif; color: #2c3e50; }
    .panel-card {
        background: #fff; border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden; max-width: 600px; margin: 30px auto;
    }
    .panel-card .card-header {
        background: linear-gradient(135deg, #2c3e50, #3d5468);
        color: #fff; text-align: center; padding: 14px;
        font-weight: bold; border-bottom: 3px solid #16a085;
    }
    .panel-card .card-body { padding: 20px; }
    .form-label { font-weight: 600; font-size: 13px; margin-bottom: 6px; }
    .form-control {
        border: 1.5px solid #e8ecef; border-radius: 8px;
        padding: 9px 12px; font-family: Tahoma; font-size: 13px;
        background: #fbfcfd; width: 100%;
    }
    .form-control:focus {
        border-color: #16a085;
        box-shadow: 0 0 0 3px rgba(22,160,133,0.12);
        outline: none;
    }
    .btn-panel {
        background: #16a085; color: #fff; border: none;
        padding: 11px; border-radius: 9px; font-weight: bold;
        width: 100%; font-family: Tahoma; cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-panel:hover { background: #12876f; }
</style>
<body>

<?php include("top.php"); ?>
<?php include("nav.php"); ?>

<div class="container-fluid">
    <div class="panel-card">
        <div class="card-header">تغییر کلمه عبور</div>
        <div class="card-body">

            <form id="sec-form" onsubmit="return false;">

                <label class="form-label">کلمه عبور پیشین</label>
                <input type="password" name="pass" id="pass" class="form-control mb-3 datasender" autocomplete="off">

                <label class="form-label">کلمه عبور جدید</label>
                <input type="password" name="newpass" id="newpass" class="form-control mb-3 datasender" autocomplete="off">

                <label class="form-label">تکرار کلمه عبور جدید</label>
                <input type="password" name="newpass2" id="newpass2" class="form-control mb-3 datasender" autocomplete="off">

                <button type="button" class="btn btn-panel" onclick="snddata()">اعمال تغییرات</button>

            </form>

        </div>
    </div>
</div>

<div id="msgBox" style="display:none;position:fixed;top:20px;left:50%;transform:translateX(-50%);background:#fff;padding:14px 24px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.2);z-index:99999;font-size:13px;"></div>

<script>
    function showMsg(type, msg, redirect) {
        var box = document.getElementById('msgBox');
        box.style.display = 'block';
        box.style.background = type === 'success' ? '#d5f4ea' : '#fdecea';
        box.style.color = type === 'success' ? '#0d7a5f' : '#c0392b';
        box.innerText = msg;
        if (redirect) {
            setTimeout(function () { location.replace(redirect); }, 2000);
        } else {
            setTimeout(function () { box.style.display = 'none'; }, 3000);
        }
    }

    function snddata() {
        var formData = {};
        $('.datasender').each(function () {
            formData[$(this).attr('name')] = $(this).val();
        });

        $.ajax({
            url: 'security_edit.php',
            type: 'POST',
            data: formData,
            dataType: 'text',
            success: function (raw) {
                var res;
                try { res = typeof raw === 'string' ? JSON.parse(raw) : raw; }
                catch (e) { showMsg('error', 'پاسخ نامعتبر'); return; }
                if (parseInt(res.status, 10) === 1) {
                    showMsg('success', res.msg, 'logout.php');
                } else {
                    showMsg('error', res.msg);
                }
            },
            error: function () { showMsg('error', 'خطای ارتباط'); }
        });
    }
</script>

<?php include("footer.php"); ?>
</body>
</html>
EOT;
}

function tpl_security_edit()
{
    return <<<'EOT'
<?php
error_reporting(0);
ini_set('display_errors', 0);
session_start();
ob_start();

include("../lib/php/lib_include.php");
include("check_admin_session.php");

if (ob_get_length() > 0) ob_clean();
header('Content-Type: application/json; charset=utf-8');

function reply($status, $msg)
{
    if (ob_get_length() > 0) ob_clean();
    echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
    while (ob_get_level() > 0) ob_end_flush();
    exit;
}

$ml = new mobile_input();

$pass     = $ml->set_name("pass")->set_title("کلمه عبور پیشین")->set_important(true)->post_str();
$newpass  = $ml->set_name("newpass")->set_title("کلمه عبور جدید")->set_important(true)->post_str();
$newpass2 = $ml->set_name("newpass2")->set_title("تکرار کلمه عبور جدید")->set_important(true)->post_str();

$thisuser = $_SESSION['{{SESSION_KEY}}'] ?? '';

if ($newpass !== $newpass2) {
    reply(0, 'کلمه عبور جدید و تکرار آن یکسان نیستند.');
}

$db = new database();
$db->connect();
$fm = new makeform();

$user_safe = $fm->sqlstr($thisuser);
$pass_safe = $fm->sqlstr($pass);

$db->query("SELECT * FROM `{{TABLE}}` WHERE `{{USER_FIELD}}`='$user_safe' AND `{{PASS_FIELD}}`='$pass_safe' LIMIT 1");

if (mysqli_num_rows($db->res) == 0) {
    reply(0, 'کلمه عبور پیشین اشتباه است.');
}

$new_safe = $fm->sqlstr($newpass);
$db->query("UPDATE `{{TABLE}}` SET `{{PASS_FIELD}}`='$new_safe' WHERE `{{USER_FIELD}}`='$user_safe'");

if (ob_get_length() > 0) ob_clean();

if ($db->res) {
    reply(1, 'کلمه عبور با موفقیت تغییر یافت. لطفاً مجدداً وارد شوید.');
} else {
    reply(0, 'اشکال در ثبت اطلاعات');
}
?>
EOT;
}

function tpl_cap()
{
    return <<<'EOT'
<?php
session_start();
include("../lib/php/lib_include.php");

$ml = new mobile_input();
$capname = $ml->set_name("capname")->set_title("نام تصویر امنیتی")->set_important(true)->get_str();

$captcha = new captcha();
$capt = $captcha->createRandomImage($capname);
die($capt);
?>
EOT;
}

function tpl_readme()
{
    return <<<'EOT'
==============================================
{{PANEL_TITLE}}
ساخته‌شده توسط Panel Generator
==============================================

اطلاعات پنل:
- نام پوشه:    {{FOLDER}}
- جدول دیتابیس: {{TABLE}}
- فیلد کاربر:  {{USER_FIELD}}
- فیلد رمز:    {{PASS_FIELD}}
- کلید سشن:    {{SESSION_KEY}}
- تابع ID:     {{ID_FUNC}}

ورود:
    {{FOLDER}}/login.php

==============================================
EOT;
}