<?php
session_start();
include("../lib/php/lib_include.php");
include("check_admin_session.php");

$thisuser = $_SESSION['tel'] ?? '';
$db = new database();
$db->connect();
$fm_tmp = new makeform();
$user_safe = $fm_tmp->sqlstr($thisuser);

$db->query("SELECT * FROM `marketers` WHERE `tel`='$user_safe' LIMIT 1");
$row = mysqli_fetch_assoc($db->res);
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<title>پروفایل — پنل بازاریاب ها</title>
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

<label class="form-label">نام</label>
<input type="text" name="name" id="name" class="form-control mb-3 datasender" value="<?= htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<label class="form-label">نام خانوادگی</label>
<input type="text" name="family" id="family" class="form-control mb-3 datasender" value="<?= htmlspecialchars($row['family'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

<label class="form-label">شماره تماس 2</label>
<input type="text" name="tel2" id="tel2" class="form-control mb-3 datasender" value="<?= htmlspecialchars($row['tel2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">


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