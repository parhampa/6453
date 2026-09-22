<?php
/**
 * ================================================================
 * مدیریت نظرات کاربر
 * ----------------------------------------------------------------
 * اکشن‌ها:
 *   - add     →  افزودن نظر جدید (همیشه INSERT)
 *   - update  →  ویرایش نظر موجود (بر اساس comment_id)
 *   - delete  →  حذف نظر موجود (بر اساس comment_id)
 *
 * هر کاربر می‌تواند برای هر آیتم چندین نظر ثبت کند.
 * ================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

$response = ['success' => false, 'message' => 'خطای نامشخص'];
$json_sent = false;

register_shutdown_function(function () use (&$response, &$json_sent) {
    if ($json_sent) return;
    $buffer = '';
    while (ob_get_level() > 0) {
        $buffer = ob_get_clean() . $buffer;
    }
    if (!empty($buffer)) {
        $clean = trim(strip_tags($buffer));
        if (!empty($clean)) {
            $response['success'] = false;
            $response['message'] = $clean;
            $response['debug_output'] = $buffer;
        }
    }
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
});

try {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    include_once '../lib/php/lib_include.php';

    /* ---------- تبدیل JSON به POST ---------- */
    $raw = file_get_contents('php://input');
    if ($raw !== false && $raw !== '') {
        $json_input = json_decode($raw, true);
        if (is_array($json_input)) {
            $_POST = array_merge($_POST, $json_input);
            $_REQUEST = array_merge($_REQUEST, $json_input);
        } else {
            throw new Exception('فرمت درخواست نامعتبر است');
        }
    }

    $fm = new makeform();
    $db = new database();
    $db->connect();
    $ml = new mobile_input();

    /* ═══════ خواندن ورودی‌ها ═══════ */
    $action = $ml->set_name("action")->set_title("عملیات")->set_important(false)->post_str();
    $phone_raw = $ml->set_name("phone")->set_title("شماره تلفن")->set_important(false)->post_str();
    $menu_item_id = $ml->set_name("menu_item_id")->set_title("آیتم منو")->set_important(false)->post_int();
    $comment_id = $ml->set_name("comment_id")->set_title("شناسه نظر")->set_important(false)->post_int();
    $score = $ml->set_name("score")->set_title("امتیاز")->set_important(false)->post_int();
    $comment_text = $ml->set_name("comment_text")->set_title("متن نظر")->set_important(false)->post_str();

    /* ═══════ اعتبارسنجی مشترک ═══════ */
    if (!in_array($action, ['add', 'update', 'delete'], true)) {
        throw new Exception('درخواست نامعتبر (action)');
    }

    $phone = preg_replace('/[^0-9]/', '', (string)$phone_raw);
    if (!preg_match('/^09\d{9}$/', $phone)) {
        throw new Exception('شماره تلفن نامعتبر است');
    }

    if ($menu_item_id <= 0) {
        throw new Exception('آیتم منو نامعتبر است');
    }

    /* ═══════ اعتبارسنجی مخصوص add / update ═══════ */
    if ($action === 'add' || $action === 'update') {
        if ($score < 1 || $score > 5) {
            throw new Exception('امتیاز باید بین ۱ تا ۵ باشد');
        }

        $comment_len = mb_strlen($comment_text, 'UTF-8');
        if ($comment_len > 500) {
            throw new Exception('متن نظر نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد');
        }
        if ($comment_len < 1) {
            throw new Exception('متن نظر نمی‌تواند خالی باشد');
        }
    }

    /* ═══════ پیدا کردن مشتری ═══════ */
    $phone_s = mysqli_real_escape_string($db->connection, $phone);

    $db->query("SELECT `id`, `name`, `family` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");
    if (mysqli_num_rows($db->res) == 0) {
        throw new Exception('مشتری یافت نشد. ابتدا اطلاعات خود را ثبت کنید.');
    }

    $customer = mysqli_fetch_assoc($db->res);
    $customer_id = (int)$customer['id'];

    $fullname = trim(($customer['name'] ?? '') . ' ' . ($customer['family'] ?? ''));
    if ($fullname === '') {
        $fullname = 'کاربر ناشناس';
    }

    $fullname_s = mysqli_real_escape_string($db->connection, $fullname);
    $comment_s = mysqli_real_escape_string($db->connection, $comment_text);

    /* ═══════ بررسی وجود آیتم منو ═══════ */
    $db->query("SELECT `id` FROM `menu_items` WHERE `id` = $menu_item_id LIMIT 1");
    if (mysqli_num_rows($db->res) == 0) {
        throw new Exception('آیتم منو یافت نشد');
    }

    $today = date('Y-m-d');

    /* ═══════ سوئیچ روی اکشن ═══════ */
    switch ($action) {

        /* =========================================================
           افزودن نظر جدید — همیشه INSERT
           ========================================================= */
        case 'add':

            $db->query("INSERT INTO `comments`
                        (`fullname`, `score`, `comment_text`, `comment_date`,
                         `menu_item_id`, `customer_id`, `status`)
                        VALUES
                        ('$fullname_s', $score, '$comment_s', '$today',
                         $menu_item_id, $customer_id, 1)");

            $new_id = (int)mysqli_insert_id($db->connection);

            if ($new_id <= 0) {
                $mysql_err = mysqli_error($db->connection);
                throw new Exception('خطا در ثبت نظر: ' . $mysql_err);
            }

            /* ---------- آمار جدید ---------- */
            $db->query("SELECT COUNT(*) AS cnt, COALESCE(SUM(`score`),0) AS sm
                        FROM `comments`
                        WHERE `menu_item_id` = $menu_item_id
                          AND `status` = 1");

            $stats = mysqli_fetch_assoc($db->res);

            $response = [
                'success' => true,
                'message' => 'نظر شما ثبت شد',
                'action' => 'add',
                'comment_id' => $new_id,
                'comment' => [
                    'id' => $new_id,
                    'fullname' => $fullname,
                    'score' => $score,
                    'comment_text' => $comment_text,
                    'comment_date' => $today,
                ],
                'new_count' => (int)$stats['cnt'],
                'new_sum' => (int)$stats['sm'],
            ];
            break;

        /* =========================================================
           ویرایش نظر موجود — بر اساس comment_id
           ========================================================= */
        case 'update':

            if ($comment_id <= 0) {
                throw new Exception('شناسه نظر نامعتبر است');
            }

            /* بررسی اینکه این نظر متعلق به همین کاربر و همین آیتم باشه */
            $db->query("SELECT `id` FROM `comments`
                        WHERE `id` = $comment_id
                          AND `customer_id` = $customer_id
                          AND `menu_item_id` = $menu_item_id
                        LIMIT 1");

            if (mysqli_num_rows($db->res) == 0) {
                throw new Exception('نظر مورد نظر یافت نشد یا به شما تعلق ندارد');
            }

            $db->query("UPDATE `comments`
                        SET `score` = $score,
                            `comment_text` = '$comment_s',
                            `comment_date` = '$today',
                            `fullname` = '$fullname_s'
                        WHERE `id` = $comment_id
                        LIMIT 1");

            /* ---------- آمار جدید ---------- */
            $db->query("SELECT COUNT(*) AS cnt, COALESCE(SUM(`score`),0) AS sm
                        FROM `comments`
                        WHERE `menu_item_id` = $menu_item_id
                          AND `status` = 1");

            $stats = mysqli_fetch_assoc($db->res);

            $response = [
                'success' => true,
                'message' => 'نظر شما به‌روزرسانی شد',
                'action' => 'update',
                'comment_id' => $comment_id,
                'comment' => [
                    'id' => $comment_id,
                    'fullname' => $fullname,
                    'score' => $score,
                    'comment_text' => $comment_text,
                    'comment_date' => $today,
                ],
                'new_count' => (int)$stats['cnt'],
                'new_sum' => (int)$stats['sm'],
            ];
            break;

        /* =========================================================
           حذف نظر — بر اساس comment_id
           ========================================================= */
        case 'delete':

            if ($comment_id <= 0) {
                throw new Exception('شناسه نظر نامعتبر است');
            }

            $db->query("SELECT `id` FROM `comments`
                        WHERE `id` = $comment_id
                          AND `customer_id` = $customer_id
                        LIMIT 1");

            if (mysqli_num_rows($db->res) == 0) {
                throw new Exception('نظر مورد نظر یافت نشد یا به شما تعلق ندارد');
            }

            $db->query("DELETE FROM `comments` WHERE `id` = $comment_id LIMIT 1");

            /* ---------- آمار جدید ---------- */
            $db->query("SELECT COUNT(*) AS cnt, COALESCE(SUM(`score`),0) AS sm
                        FROM `comments`
                        WHERE `menu_item_id` = $menu_item_id
                          AND `status` = 1");

            $stats = mysqli_fetch_assoc($db->res);

            $response = [
                'success' => true,
                'message' => 'نظر شما حذف شد',
                'action' => 'delete',
                'comment_id' => $comment_id,
                'new_count' => (int)$stats['cnt'],
                'new_sum' => (int)$stats['sm'],
            ];
            break;

        default:
            throw new Exception('عملیات نامعتبر است: ' . htmlspecialchars($action));
    }

} catch (Throwable $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$json_sent = true;

while (ob_get_level() > 0) {
    ob_end_clean();
}

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;