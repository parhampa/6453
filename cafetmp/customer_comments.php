<?php
/**
 * ================================================================
 * گرفتن همه نظرات کاربر برای آیتم‌های منو
 * ----------------------------------------------------------------
 * ساختار خروجی:
 *   comments: {
 *       itemId: [
 *           { id, score, comment_text, comment_date },
 *           ...
 *       ]
 *   }
 * ================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

ob_start();

$response = ['success' => false, 'message' => 'خطای نامشخص', 'comments' => new stdClass()];
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
        }
    }

    $db = new database();
    $db->connect();
    $ml = new mobile_input();

    $phone_raw = $ml->set_name("phone")->set_title("شماره تلفن")->set_important(false)->post_str();
    $cafe_id = $ml->set_name("cafe_id")->set_title("شناسه کافه")->set_important(false)->post_int();

    $phone = preg_replace('/[^0-9]/', '', (string)$phone_raw);
    if (!preg_match('/^09\d{9}$/', $phone)) {
        throw new Exception('شماره تلفن نامعتبر است');
    }

    $phone_s = mysqli_real_escape_string($db->connection, $phone);

    /* ---------- پیدا کردن مشتری ---------- */
    $db->query("SELECT `id` FROM `customers` WHERE `tel` = '$phone_s' LIMIT 1");
    if (mysqli_num_rows($db->res) == 0) {
        throw new Exception('مشتری یافت نشد');
    }

    $customer_id = (int)mysqli_fetch_assoc($db->res)['id'];

    /* ---------- گرفتن همه نظرات کاربر ---------- */
    if ($cafe_id > 0) {
        $sql = "SELECT cm.`id`, cm.`menu_item_id`, cm.`score`, cm.`comment_text`, cm.`comment_date`
                FROM `comments` cm
                JOIN `menu_items` mi ON mi.`id` = cm.`menu_item_id`
                JOIN `cafe_categories` cc ON cc.`id` = mi.`category_id`
                WHERE cm.`customer_id` = $customer_id
                  AND cc.`cafe_id` = $cafe_id
                ORDER BY cm.`id` DESC";
    } else {
        $sql = "SELECT cm.`id`, cm.`menu_item_id`, cm.`score`, cm.`comment_text`, cm.`comment_date`
                FROM `comments` cm
                WHERE cm.`customer_id` = $customer_id
                ORDER BY cm.`id` DESC";
    }

    $db->query($sql);

    $comments = [];
    while ($row = mysqli_fetch_assoc($db->res)) {
        $item_id = (string)(int)$row['menu_item_id'];

        if (!isset($comments[$item_id])) {
            $comments[$item_id] = [];
        }

        $comments[$item_id][] = [
            'id' => (int)$row['id'],
            'score' => (int)$row['score'],
            'comment_text' => (string)$row['comment_text'],
            'comment_date' => (string)$row['comment_date'],
        ];
    }

    $response = [
        'success' => true,
        'message' => 'ok',
        'comments' => !empty($comments) ? $comments : new stdClass(),
    ];

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