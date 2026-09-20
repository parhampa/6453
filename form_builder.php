<?php
/**
 * Form Builder — فرم‌ساز گام‌به‌گام
 * PHP 7+ | ID + Filters + Auto Filters + Auto Values
 */

$result = null;
$edit = false;

$saved = [
    'panel_title' => 'فرم جدید',
    'table_name' => '',
    'table_key' => 'id',
    'table_desc' => 'مدیریت اطلاعات',
    'alow_edit' => true,
    'alow_del' => true,
    'where_show' => '',
    'where_delete' => '',
    'where_edit' => '',
    'auto_values' => [],
    'filters_enabled' => false,
    'auto_filters_enabled' => false,
    'auto_filter_field' => '',
    'auto_filter_show' => true,
    'auto_filter_delete' => true,
    'auto_filter_edit' => true,
    'id_func' => [
        'enabled' => false,
        'name' => '',
        'variable_name' => '$mid',
        'session_key' => '',
        'table' => '',
        'user_field' => '',
        'extra_where' => '',
    ],
    'fields' => [],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $saved['panel_title'] = trim($_POST['panel_title'] ?? 'فرم جدید');
    $saved['table_name'] = trim($_POST['table_name'] ?? '');
    $saved['table_key'] = trim($_POST['table_key'] ?? 'id');
    $saved['table_desc'] = trim($_POST['table_desc'] ?? 'مدیریت اطلاعات');
    $saved['alow_edit'] = !empty($_POST['alow_edit']);
    $saved['alow_del'] = !empty($_POST['alow_del']);
    $saved['where_show'] = trim($_POST['where_show'] ?? '');
    $saved['where_delete'] = trim($_POST['where_delete'] ?? '');
    $saved['where_edit'] = trim($_POST['where_edit'] ?? '');
    $saved['filters_enabled'] = !empty($_POST['filters_enabled']);
    $saved['auto_filters_enabled'] = !empty($_POST['auto_filters_enabled']);
    $saved['auto_filter_field'] = trim($_POST['auto_filter_field'] ?? '');
    $saved['auto_filter_show'] = !empty($_POST['auto_filter_show']);
    $saved['auto_filter_delete'] = !empty($_POST['auto_filter_delete']);
    $saved['auto_filter_edit'] = !empty($_POST['auto_filter_edit']);
    $saved['auto_values'] = json_decode($_POST['auto_values_json'] ?? '[]', true);
    if (!is_array($saved['auto_values'])) $saved['auto_values'] = [];
    $saved['fields'] = json_decode($_POST['fields_json'] ?? '[]', true);
    if (!is_array($saved['fields'])) $saved['fields'] = [];

    $saved['id_func'] = [
        'enabled' => !empty($_POST['id_func_enabled']),
        'name' => trim($_POST['id_func_name'] ?? ''),
        'variable_name' => trim($_POST['id_func_variable_name'] ?? '$mid'),
        'session_key' => trim($_POST['id_func_session_key'] ?? ''),
        'table' => trim($_POST['id_func_table'] ?? ''),
        'user_field' => trim($_POST['id_func_user_field'] ?? ''),
        'extra_where' => trim($_POST['id_func_extra_where'] ?? ''),
    ];

    /* ═══════ اگر فیلتر خودکار فعاله، مقادیر فیلترها رو از نو بساز ═══════ */
    if (
        $saved['auto_filters_enabled'] &&
        $saved['id_func']['enabled'] &&
        $saved['id_func']['variable_name'] !== '' &&
        $saved['auto_filter_field'] !== ''
    ) {
        $var = $saved['id_func']['variable_name'];
        $field = $saved['auto_filter_field'];
        $cond = "{$field}={$var}";

        if ($saved['auto_filter_show']) $saved['where_show'] = $cond;
        if ($saved['auto_filter_delete']) $saved['where_delete'] = $cond;
        if ($saved['auto_filter_edit']) $saved['where_edit'] = $cond;
    }

    if (isset($_POST['back_to_edit'])) {
        $edit = true;
    } elseif (isset($_POST['finish'])) {
        if ($saved['table_name'] === '' || empty($saved['fields'])) {
            $result = ['error' => 'نام جدول و حداقل یک فیلد الزامی است.'];
            $edit = true;
        } else {
            $php = gen_php($saved);
            $sql = gen_sql($saved['table_name'], $saved['table_key'], $saved['fields']);

            $extras = [];
            foreach ($saved['fields'] as $f) {
                if ($f['type'] === 'file' && !empty($f['in_table'])) {
                    $extras['_col_' . $f['name'] . '.php'] = gen_file_column_helper($f);
                }
            }

            if ($saved['id_func']['enabled'] && $saved['id_func']['name'] !== '') {
                $extras['_id_function.php'] = gen_id_function($saved['id_func']);
            }

            $result = [
                'php' => $php,
                'sql' => $sql,
                'file' => $saved['table_name'] . '.php',
                'table' => $saved['table_name'],
                'extras' => $extras,
            ];
        }
    }
}

/* ═══════════════════════════════════════════════════
   تولید تابع get_..._id()
   ═══════════════════════════════════════════════════ */
function gen_id_function($d)
{
    $name = $d['name'];
    $session_key = $d['session_key'];
    $table = $d['table'];
    $user_field = $d['user_field'];
    $extra_where = $d['extra_where'];

    $where_extra = '';
    if ($extra_where !== '') $where_extra = " AND {$extra_where} ";

    $C = "<?php\n";
    $C .= "/**\n";
    $C .= " * تابع شناسه‌ی کاربر جاری\n";
    $C .= " * به انتهای فایل calhead.php پنل اضافه کنید.\n";
    $C .= " *\n";
    $C .= " * کلید سشن:  \$_SESSION['{$session_key}']\n";
    $C .= " * جدول:      {$table}\n";
    $C .= " * فیلد تطبیق: {$user_field}\n";
    $C .= " */\n\n";
    $C .= "if (!function_exists('{$name}')) {\n";
    $C .= "    function {$name}()\n";
    $C .= "    {\n";
    $C .= "        if (!isset(\$_SESSION['{$session_key}'])) return 0;\n\n";
    $C .= "        \$key = \$_SESSION['{$session_key}'];\n\n";
    $C .= "        \$db = new database();\n";
    $C .= "        \$db->connect();\n\n";
    $C .= "        \$fm_tmp = new makeform();\n";
    $C .= "        \$key_safe = \$fm_tmp->sqlstr(\$key);\n\n";
    $C .= "        \$db->query(\"SELECT `id` FROM `{$table}` WHERE `{$user_field}`='\$key_safe'{$where_extra}LIMIT 1\");\n";
    $C .= "        if (mysqli_num_rows(\$db->res) == 0) return 0;\n\n";
    $C .= "        \$row = mysqli_fetch_assoc(\$db->res);\n";
    $C .= "        return (int)\$row['id'];\n";
    $C .= "    }\n";
    $C .= "}\n";
    $C .= "?>\n";

    return $C;
}

/* ═══════════════════════════════════════════════════
   تولید کد PHP
   ═══════════════════════════════════════════════════ */
function gen_php($d)
{
    $title = $d['panel_title'];
    $table = $d['table_name'];
    $key = $d['table_key'];
    $desc = $d['table_desc'];
    $alow_edit = $d['alow_edit'] ? 'true' : 'false';
    $alow_del = $d['alow_del'] ? 'true' : 'false';
    $wh_show = $d['where_show'];
    $wh_delete = $d['where_delete'];
    $wh_edit = $d['where_edit'];
    $auto_vals = $d['auto_values'];
    $fields = $d['fields'];
    $filters_on = $d['filters_enabled'];
    $id_func = $d['id_func'];

    $has_date = false;
    foreach ($fields as $f) if ($f['type'] === 'date') $has_date = true;

    $C = "<?php\n";
    $C .= "session_start();\n";
    $C .= "include(\"../lib/php/lib_include.php\");\n";
    $C .= "include(\"check_admin_session.php\");\n";
    if ($has_date) $C .= "include(\"calhead.php\");\n";

    /* متغیر شناسه */
    if ($id_func['enabled'] && $id_func['name'] !== '' && $id_func['variable_name'] !== '') {
        $C .= "\n/* ────── تعریف متغیر شناسه‌ی کاربر ────── */\n";
        $C .= $id_func['variable_name'] . " = (int)" . $id_func['name'] . "();\n\n";
    }

    $C .= "?>\n";
    $C .= "<!DOCTYPE html>\n";
    $C .= "<html lang=\"fa\" dir=\"rtl\">\n";
    $C .= "<title>{$title}</title>\n";
    $C .= "<meta charset=\"UTF-8\">\n";
    $C .= "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
    $C .= "<link rel=\"stylesheet\" href=\"css/w3.css\">\n";
    $C .= "<link href=\"../fontawesome-free-6.7.2-web/css/all.min.css\" rel=\"stylesheet\">\n";
    $C .= "<script src=\"../lib/js/jquery.js\"></script>\n";
    $C .= "<script src=\"js/fnuser.js\"></script>\n";
    $C .= "<script src=\"js/modal.js\"></script>\n";
    $C .= "<link href=\"bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css\" rel=\"stylesheet\">\n";
    $C .= "<link rel=\"stylesheet\" href=\"css/new.css\">\n";

    $C .= "<style>\n";
    $C .= "    :root { --panel-primary: #2c3e50; --panel-secondary: #34495e; --panel-accent: #16a085; --panel-bg: #f4f6f9; --panel-text: #2c3e50; }\n";
    $C .= "    body { background-color: var(--panel-bg); font-family: Tahoma, sans-serif; color: var(--panel-text); }\n";
    $C .= "    .panel-card { background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); overflow: hidden; }\n";
    $C .= "    .panel-card .card-header { background: linear-gradient(135deg, var(--panel-primary), #3d5468); color: #fff; text-align: center; padding: 14px; font-weight: bold; border-bottom: 3px solid var(--panel-accent); font-size: 14.5px; }\n";
    $C .= "    .panel-card .form-label { font-weight: 600; color: var(--panel-text); margin-bottom: 6px; font-size: 13px; }\n";
    $C .= "    .panel-card .form-control, .panel-card .form-select { border: 1.5px solid #e8ecef; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: Tahoma; background-color: #fbfcfd; transition: all 0.2s ease; }\n";
    $C .= "    .panel-card .form-control:focus, .panel-card .form-select:focus { border-color: var(--panel-accent); box-shadow: 0 0 0 3px rgba(22,160,133,0.12); background-color: #fff; outline: none; }\n";
    $C .= "    .panel-card .btn-panel { background: var(--panel-accent); color: #fff; border: none; border-radius: 9px; padding: 11px; font-weight: bold; font-size: 13.5px; font-family: Tahoma; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(22,160,133,0.25); cursor: pointer; }\n";
    $C .= "    .panel-card .btn-panel:hover { background: #12876f; color: #fff; transform: translateY(-1px); }\n";
    $C .= "    .required-field { background-image: url(\"data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24'><text x='0' y='20' font-size='22' fill='%23e74c3c' font-family='Arial'>*</text></svg>\"); background-repeat: no-repeat; background-position: left 12px center; background-size: 12px 12px; padding-left: 32px; }\n";
    $C .= "    .form-control:not(.required-field), .form-select:not(.required-field) { padding-left: 12px; }\n";
    $C .= "    .tbl-thumb { display: inline-block; width: 44px; height: 44px; border-radius: 6px; overflow: hidden; border: 1.5px solid #e8ecef; background: #fff; padding: 2px; transition: all 0.2s ease; }\n";
    $C .= "    .tbl-thumb:hover { border-color: var(--panel-accent); transform: scale(1.1); box-shadow: 0 4px 12px rgba(22,160,133,0.25); }\n";
    $C .= "    .tbl-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }\n";
    $C .= "    .tbl-file-link { display: inline-flex; align-items: center; gap: 5px; padding: 5px 10px; background: #e8f5e9; color: #16a085; border-radius: 7px; font-size: 11px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }\n";
    $C .= "    .tbl-file-link:hover { background: var(--panel-accent); color: #fff; }\n";
    $C .= "</style>\n";
    $C .= "<body style=\"direction: rtl;\">\n\n";

    $C .= "<?php include(\"top.php\"); ?>\n";
    $C .= "<?php include(\"nav.php\"); ?>\n\n";
    $C .= "<div class=\"container mt-5\" dir=\"rtl\">\n";
    $C .= "    <div class=\"row justify-content-center\">\n";
    $C .= "        <div class=\"col-md-11 col-lg-9\">\n";
    $C .= "            <div class=\"panel-card\">\n";
    $C .= "                <div class=\"card-header\">{$title}</div>\n";
    $C .= "                <div class=\"card-body p-4\">\n\n";

    $C .= "<?php\n";
    $C .= "\$fm = new makeform();\n";
    $C .= "\$fm->set_tbl_key(\"{$table}\", \"{$key}\", 1, \"{$desc}\");\n";

    foreach ($fields as $f) {
        if ($f['type'] === 'file' && !empty($f['in_table'])) {
            $helper = '_col_' . $f['name'] . '.php';
            $C .= "\$fm->setInclude(\"{$helper}\", \"{$f['name']}\");\n";
        }
    }
    $C .= "\n";

    if ($filters_on) {
        if ($wh_show !== '') $C .= "\$fm->setwhere(\" {$wh_show} \");\n";
        if ($wh_delete !== '') $C .= "\$fm->deletewhere(\" {$wh_delete} \");\n";
        if ($wh_edit !== '') $C .= "\$fm->set_where_edit(\" {$wh_edit} \");\n";
        if ($wh_show !== '' || $wh_delete !== '' || $wh_edit !== '') $C .= "\n";

        if (!empty($auto_vals)) {
            $C .= "/* ────── مقادیر خودکار ────── */\n";
            foreach ($auto_vals as $av) {
                if (empty($av['name']) || $av['value'] === '') continue;
                $vname = $av['name'];
                $vtype = $av['type'] ?? 'int';
                $vval = $av['value'];
                if ($vtype === 'string') $C .= "\$fm->set_str_val(\"{$vname}\", {$vval});\n";
                else                     $C .= "\$fm->set_int_val(\"{$vname}\", {$vval});\n";
            }
            $C .= "\n";
        }
    }

    $C .= "\$fm->CSRF_token();\n";
    $C .= "\$fm->alow_edit = {$alow_edit};\n";
    $C .= "\$fm->alow_del  = {$alow_del};\n\n";

    foreach ($fields as $f) {
        $name = $f['name'];
        $label = $f['label'];
        $type = $f['type'];
        $req = !empty($f['required']) ? 1 : 0;
        $tbl = !empty($f['in_table']) ? 1 : 0;
        $def = $f['default'] ?? '';
        $ph = $f['placeholder'] ?? '';
        $req_c = $req ? ' required-field' : '';

        $C .= "/* {$label} */\n";

        if ($type === 'date') {
            $C .= "\$fm->dateinput(\"{$name}\", \"{$label}\", {$req}, 1, {$tbl});\n\n";
        } elseif ($type === 'file') {
            $C .= "\$fm->fileinput(\"{$label}\", \"{$name}\", \"form-control mb-3\", \"form-label\", {$tbl});\n\n";
        } elseif ($type === 'select') {
            $sel_source = $f['select_source'] ?? 'manual';
            $C .= "\$fm->label(\"{$label}\", \"form-label\")\n";
            $C .= "    ->select()->selectname(\"{$name}\")->selectid(\"{$name}\")\n";
            $C .= "    ->selectclasses(\"form-select mb-3{$req_c}\")\n";
            $C .= "    ->selectaddval(\"\", \"انتخاب {$label}\");\n";

            if ($sel_source === 'manual') {
                foreach (($f['manual_options'] ?? []) as $opt) {
                    $val = htmlspecialchars($opt['value'], ENT_QUOTES, 'UTF-8');
                    $lbl = htmlspecialchars($opt['label'], ENT_QUOTES, 'UTF-8');
                    $C .= "    ->selectaddval(\"{$val}\", \"{$lbl}\")\n";
                }
                $C = rtrim($C, "\n") . ";\n";
                $C .= "\$fm->end()->sndform(\"{$name}\", 2, {$req}, \"{$label}\", 1, {$tbl});\n\n";
            } else {
                $sel_tbl = $f['select_table'] ?? '';
                $sel_val = $f['select_value'] ?? 'id';
                $sel_dsp = $f['select_display'] ?? [];
                $sel_order = $f['select_order'] ?? '';
                $sel_where = $f['select_where'] ?? '';
                $order_sql = $sel_order !== '' ? " ORDER BY {$sel_order}" : '';
                $where_sql = $sel_where !== '' ? " WHERE {$sel_where}" : '';

                $C .= "\$db = new database();\n";
                $C .= "\$db->connect()->query(\"SELECT * FROM `{$sel_tbl}`{$where_sql}{$order_sql}\");\n";
                $C .= "while (\$row = mysqli_fetch_assoc(\$db->res)) {\n";
                if (count($sel_dsp) === 1) {
                    $C .= "    \$display = \$row['{$sel_dsp[0]}'];\n";
                } else {
                    $parts = [];
                    foreach ($sel_dsp as $fld) $parts[] = "\$row['{$fld}']";
                    $C .= "    \$display = " . implode(" . ' ' . ", $parts) . ";\n";
                }
                $C .= "    \$fm->selectaddval(\$row['{$sel_val}'], \$display);\n";
                $C .= "}\n";
                $C .= "\$fm->end()->sndform(\"{$name}\", 2, {$req}, \"{$label}\", 1, {$tbl});\n\n";
            }
        } else {
            $type_num = ($type === 'number') ? 1 : 0;
            $C .= "\$fm->label(\"{$label}\", \"form-label\")\n";
            $C .= "    ->input()->inpname(\"{$name}\")->inpid(\"{$name}\")\n";
            $C .= "    ->inptype(\"{$type}\")\n";
            $C .= "    ->inpclasses(\"form-control mb-3{$req_c}\")\n";
            if ($ph !== '') $C .= "    ->inpplaceholder(\"{$ph}\")\n";
            if ($def !== '') $C .= "    ->inpval(\"{$def}\")\n";
            $C .= "    ->end()\n";
            $C .= "    ->sndform(\"{$name}\", {$type_num}, {$req}, \"{$label}\", 1, {$tbl});\n\n";
        }
    }

    $C .= "\$fm->input()->inptype(\"submit\")->inpval(\"ثبت\")->inpclasses(\"btn btn-panel w-100 mt-2\")->end();\n\n";
    $C .= "\$fm->addform();\n";
    $C .= "\$fm->show();\n";
    $C .= "?>\n\n";

    $C .= "                </div>\n            </div>\n        </div>\n    </div>\n</div>\n\n";
    $C .= "<?php include(\"footer.php\"); ?>\n</body>\n</html>\n";

    return $C;
}

/* ═══════════════════════════════════════════════════
   فایل کمکی نمایش فایل در جدول
   ═══════════════════════════════════════════════════ */
function gen_file_column_helper($field)
{
    $name = $field['name'];
    $C = "<?php\n/** فایل کمکی نمایش ستون «{$field['label']}» در جدول */\n\n";
    $C .= "\$file_val = '';\n";
    $C .= "if (isset(\$fildt['{$name}'])) \$file_val = trim(\$fildt['{$name}']);\n\n";
    $C .= "if (\$file_val === '') {\n";
    $C .= "    \$restbl .= '<span style=\"color:#b8c3cd;font-size:11px;\">— بدون فایل —</span>';\n";
    $C .= "} else {\n";
    $C .= "    \$ext = strtolower(pathinfo(\$file_val, PATHINFO_EXTENSION));\n";
    $C .= "    \$img_exts = ['jpg','jpeg','png','gif','webp','bmp','svg'];\n";
    $C .= "    \$file_src = htmlspecialchars(\$file_val, ENT_QUOTES, 'UTF-8');\n\n";
    $C .= "    if (in_array(\$ext, \$img_exts, true)) {\n";
    $C .= "        \$restbl .= '<a href=\"' . \$file_src . '\" target=\"_blank\" class=\"tbl-thumb\" title=\"مشاهده\">';\n";
    $C .= "        \$restbl .= '<img src=\"' . \$file_src . '\" alt=\"تصویر\">';\n";
    $C .= "        \$restbl .= '</a>';\n";
    $C .= "    } else {\n";
    $C .= "        \$base_name = basename(\$file_val);\n";
    $C .= "        if (mb_strlen(\$base_name, 'UTF-8') > 18) \$base_name = mb_substr(\$base_name, 0, 15, 'UTF-8') . '...';\n";
    $C .= "        \$restbl .= '<a href=\"' . \$file_src . '\" target=\"_blank\" class=\"tbl-file-link\">';\n";
    $C .= "        \$restbl .= '<i class=\"fas fa-download\"></i> ' . htmlspecialchars(\$base_name, ENT_QUOTES, 'UTF-8');\n";
    $C .= "        \$restbl .= '</a>';\n";
    $C .= "    }\n}\n?>\n";
    return $C;
}

/* ═══════════════════════════════════════════════════
   تولید SQL
   ═══════════════════════════════════════════════════ */
function gen_sql($table, $key, $fields)
{
    $S = "CREATE TABLE `{$table}` (\n";
    $S .= "  `{$key}` int(11) NOT NULL AUTO_INCREMENT,\n";
    foreach ($fields as $f) {
        $name = $f['name'];
        $type = $f['type'];
        $req = !empty($f['required']);
        $def = $f['default'] ?? '';
        switch ($type) {
            case 'number':
                $col = "int(11) NOT NULL DEFAULT " . ($def !== '' ? (int)$def : 0);
                break;
            case 'select':
                $source = $f['select_source'] ?? 'manual';
                $col = ($source === 'manual')
                    ? "varchar(250) COLLATE utf8_persian_ci " . ($req ? "NOT NULL" : "DEFAULT NULL")
                    : "int(11) NOT NULL DEFAULT " . ($def !== '' ? (int)$def : 0);
                break;
            case 'date':
                $col = "date " . ($req ? "NOT NULL" : "DEFAULT NULL");
                break;
            case 'textarea':
                $col = "text COLLATE utf8_persian_ci " . ($req ? "NOT NULL" : "DEFAULT NULL");
                break;
            case 'file':
                $col = "text COLLATE utf8_persian_ci DEFAULT NULL";
                break;
            default:
                $col = "varchar(250) COLLATE utf8_persian_ci " . ($req ? "NOT NULL" : "DEFAULT NULL");
        }
        $S .= "  `{$name}` {$col},\n";
    }
    $S .= "  PRIMARY KEY (`{$key}`)\n) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;\n";
    return $S;
}

function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function json_h($a)
{
    return htmlspecialchars(json_encode($a, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فرم‌ساز گام‌به‌گام</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap-5.3.7-dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --accent: #16a085;
            --bg: #f4f6f9;
            --text: #2c3e50;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            font-family: Tahoma, sans-serif;
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        .top-header {
            background: linear-gradient(135deg, #2c3e50, #3d5468);
            color: #fff;
            padding: 24px 20px;
            text-align: center;
            border-bottom: 4px solid var(--accent);
        }

        .top-header .icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 10px;
            border: 2px solid rgba(22, 160, 133, 0.5);
        }

        .top-header h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0 0 4px;
        }

        .top-header p {
            font-size: 12.5px;
            margin: 0;
            opacity: 0.85;
        }

        .wrap {
            max-width: 1000px;
            margin: 0 auto;
            padding: 22px 16px 60px;
        }

        .card-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 16px;
        }

        .card-box .head {
            background: linear-gradient(135deg, #2c3e50, #3d5468);
            color: #fff;
            padding: 13px 20px;
            font-weight: bold;
            font-size: 14px;
            border-bottom: 3px solid var(--accent);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-box .head i {
            color: #1abc9c;
        }

        .card-box .head .step-num {
            width: 26px;
            height: 26px;
            background: var(--accent);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .card-box .head .step-opt {
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: normal;
        }

        .card-box .body {
            padding: 20px;
        }

        .form-label {
            font-weight: 600;
            font-size: 12.5px;
            margin-bottom: 5px;
            color: #5a6b7a;
        }

        .form-control, .form-select {
            border: 1.5px solid #e8ecef;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13px;
            font-family: Tahoma;
            background-color: #fbfcfd;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.12);
            background-color: #fff;
            outline: none;
        }

        .form-check-input:checked {
            background-color: var(--accent);
            border-color: var(--accent);
        }

        .form-check-label {
            font-size: 12.5px;
            font-weight: 600;
            color: #5a6b7a;
        }

        .btn-accent {
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 10px 22px;
            font-weight: bold;
            font-size: 13px;
            font-family: Tahoma;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(22, 160, 133, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-accent:hover {
            background: #12876f;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: #f4f6f9;
            color: #2c3e50;
            border: 1.5px solid #e8ecef;
            border-radius: 9px;
            padding: 9px 18px;
            font-weight: 600;
            font-size: 12.5px;
            font-family: Tahoma;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-ghost:hover {
            background: #e8edf2;
            color: #2c3e50;
        }

        .field-item {
            background: #fbfcfd;
            border: 1.5px solid #eef2f5;
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            cursor: move;
            user-select: none;
        }

        .field-item:hover {
            border-color: var(--accent);
            background: #fff;
        }

        .field-item.dragging {
            opacity: 0.4;
            transform: scale(0.98);
            cursor: grabbing;
        }

        .field-item.drag-over {
            border-color: var(--accent);
            background: rgba(22, 160, 133, 0.08);
            border-style: dashed;
        }

        .field-item .fi-handle {
            color: #b8c3cd;
            font-size: 16px;
            cursor: grab;
            flex-shrink: 0;
            padding: 0 4px;
        }

        .field-item .fi-handle:hover {
            color: var(--accent);
        }

        .field-item .fi-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #2c3e50, #3d5468);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .field-item .fi-info {
            flex-grow: 1;
            min-width: 0;
        }

        .field-item .fi-info .fi-name {
            font-weight: bold;
            font-size: 13px;
            color: var(--primary);
        }

        .field-item .fi-info .fi-meta {
            font-size: 11px;
            color: #95a5a6;
            margin-top: 3px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .field-item .fi-info .fi-meta span {
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .field-item .fi-actions {
            display: flex;
            gap: 6px;
            flex-shrink: 0;
        }

        .field-item .fi-edit {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #e3f2fd;
            color: #2980b9;
            border: 1px solid #aed6f1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .field-item .fi-edit:hover {
            background: #2980b9;
            color: #fff;
            border-color: #2980b9;
        }

        .field-item .fi-remove {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #f5b7b1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .field-item .fi-remove:hover {
            background: #c0392b;
            color: #fff;
            border-color: #c0392b;
        }

        .empty-box {
            text-align: center;
            padding: 40px 20px;
            color: #95a5a6;
            font-size: 13px;
        }

        .empty-box i {
            font-size: 40px;
            color: #d5dde3;
            display: block;
            margin-bottom: 10px;
        }

        .code-wrap {
            background: #0f172a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            margin-bottom: 16px;
        }

        .code-head {
            background: #1e293b;
            padding: 11px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid #334155;
        }

        .code-head .filename {
            color: #94a3b8;
            font-family: monospace;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .code-head .filename .badge-helper {
            background: #f59e0b;
            color: #fff;
            font-size: 9.5px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 8px;
            font-family: Tahoma;
            letter-spacing: 0.3px;
        }

        .code-head .filename .badge-id {
            background: #8e44ad;
            color: #fff;
            font-size: 9.5px;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 8px;
            font-family: Tahoma;
            letter-spacing: 0.3px;
        }

        .btn-code {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 6px 14px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 600;
            font-family: Tahoma;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-code:hover {
            background: var(--accent);
            border-color: var(--accent);
        }

        .btn-code.copied {
            background: #10b981;
            border-color: #10b981;
        }

        .code-body pre {
            margin: 0;
            padding: 18px 22px;
            color: #e2e8f0;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 12.5px;
            line-height: 1.7;
            direction: ltr;
            text-align: left;
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
            background: #0f172a;
        }

        .code-body pre::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .code-body pre::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .code-body pre::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 5px;
        }

        .toast-box {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #10b981;
            color: #fff;
            padding: 12px 26px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 99999;
            pointer-events: none;
        }

        .toast-box.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }

        .info-line {
            font-size: 12px;
            color: #7f8c9b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-line i {
            color: var(--accent);
        }

        .type-chips {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 8px;
            margin-bottom: 16px;
        }

        .type-chip {
            background: #fbfcfd;
            border: 2px solid #eef2f5;
            border-radius: 10px;
            padding: 12px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .type-chip:hover {
            border-color: var(--accent);
            background: #fff;
            transform: translateY(-2px);
        }

        .type-chip.active {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
            box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
        }

        .type-chip.active .chip-icon {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .type-chip .chip-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eef2f5;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            margin-bottom: 8px;
        }

        .type-chip .chip-title {
            font-size: 11.5px;
            font-weight: 600;
        }

        .src-toggle {
            display: inline-flex;
            background: #f4f6f9;
            border-radius: 10px;
            padding: 4px;
            gap: 4px;
            border: 1.5px solid #e8ecef;
            margin-bottom: 12px;
        }

        .src-toggle input {
            display: none;
        }

        .src-toggle label {
            padding: 7px 18px;
            border-radius: 7px;
            font-size: 12.5px;
            font-weight: 600;
            color: #7f8c9b;
            cursor: pointer;
            transition: all 0.2s ease;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .src-toggle input:checked + label {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(22, 160, 133, 0.3);
        }

        .manual-opt {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fbfcfd;
            border: 1.5px solid #eef2f5;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 6px;
        }

        .manual-opt .opt-num {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: var(--accent);
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .manual-opt .opt-fields {
            flex-grow: 1;
            display: flex;
            gap: 6px;
        }

        .manual-opt .opt-fields input {
            border: 1px solid #e8ecef;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12.5px;
            font-family: Tahoma;
            background: #fff;
        }

        .manual-opt .opt-fields input:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 2px rgba(22, 160, 133, 0.12);
        }

        .manual-opt .opt-remove {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #f5b7b1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .manual-opt .opt-remove:hover {
            background: #c0392b;
            color: #fff;
        }

        .filter-box {
            background: #fbfcfd;
            border: 1.5px dashed #d6dbe1;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .filter-box .filter-label {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-box .filter-label i {
            color: var(--accent);
        }

        .auto-val-row {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #eef2f5;
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 6px;
        }

        .auto-val-row .av-num {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #8e44ad;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .auto-val-row .av-fields {
            flex-grow: 1;
            display: grid;
            grid-template-columns: 1fr 110px 1fr;
            gap: 6px;
        }

        .auto-val-row .av-fields input, .auto-val-row .av-fields select {
            border: 1px solid #e8ecef;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12.5px;
            font-family: Tahoma;
            background: #fff;
        }

        .auto-val-row .av-fields input:focus, .auto-val-row .av-fields select:focus {
            border-color: var(--accent);
            outline: none;
            box-shadow: 0 0 0 2px rgba(22, 160, 133, 0.12);
        }

        .auto-val-row .av-remove {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #fdecea;
            color: #c0392b;
            border: 1px solid #f5b7b1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .auto-val-row .av-remove:hover {
            background: #c0392b;
            color: #fff;
        }

        .edit-banner {
            background: linear-gradient(135deg, #fef5e7, #fdebd0);
            border: 1.5px solid #f5cba7;
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #8b5a1c;
            font-size: 13px;
        }

        .edit-banner i {
            font-size: 22px;
            color: #d68910;
        }

        .note-box {
            background: #e3f2fd;
            border-right: 4px solid #2980b9;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12.5px;
            color: #1b4f72;
            margin-bottom: 14px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .note-box i {
            color: #2980b9;
            font-size: 16px;
            margin-top: 1px;
        }

        .id-func-box {
            background: linear-gradient(135deg, #f5f0fa, #efe4f7);
            border: 1.5px solid #d7bfe9;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .id-func-box .filter-label {
            color: #5b2c87;
        }

        .id-func-box .filter-label i {
            color: #8e44ad;
        }

        .id-hint {
            background: #fff;
            border-right: 3px solid #8e44ad;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #5b2c87;
            margin-bottom: 12px;
            line-height: 1.7;
        }

        .id-hint code {
            background: #f0e6f7;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            color: #5b2c87;
        }

        .toggle-card {
            background: linear-gradient(135deg, #f4f9f7, #ecf6f3);
            border: 1.5px solid #c9e6dc;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .toggle-card:hover {
            border-color: var(--accent);
            box-shadow: 0 4px 14px rgba(22, 160, 133, 0.12);
        }

        .toggle-card.active {
            background: linear-gradient(135deg, #d5f4ea, #c3ecdf);
            border-color: var(--accent);
        }

        .toggle-card .switch-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: linear-gradient(135deg, #16a085, #1abc9c);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(22, 160, 133, 0.3);
            transition: all 0.2s ease;
        }

        .toggle-card.active .switch-icon {
            background: linear-gradient(135deg, #0d7a5f, #16a085);
            transform: rotate(180deg);
        }

        .toggle-card .switch-info {
            flex-grow: 1;
            min-width: 0;
        }

        .toggle-card .switch-title {
            font-size: 14px;
            font-weight: bold;
            color: var(--primary);
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toggle-card .switch-desc {
            font-size: 11.5px;
            color: #7f8c9b;
            line-height: 1.6;
        }

        .toggle-card .switch-desc code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10.5px;
            color: #16a085;
            font-family: 'Consolas', monospace;
        }

        .toggle-card .switch-state {
            font-size: 11px;
            font-weight: bold;
            color: #95a5a6;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: #fff;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .toggle-card.active .switch-state {
            color: #16a085;
        }

        .collapse-panel {
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease, margin-top 0.3s ease;
            max-height: 0;
            opacity: 0;
            margin-top: 0;
        }

        .collapse-panel.open {
            max-height: 5000px;
            opacity: 1;
            margin-top: 16px;
        }

        .var-chips-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            background: linear-gradient(135deg, #faf7fd, #f4ecfa);
            border: 1.5px dashed #d7bfe9;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 12px;
        }

        .var-chips-bar .vc-label {
            color: #5b2c87;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .var-chips-bar .vc-label i {
            color: #8e44ad;
        }

        .var-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1.5px solid #d7bfe9;
            color: #5b2c87;
            padding: 4px 12px;
            border-radius: 14px;
            font-family: 'Consolas', monospace;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .var-chip:hover {
            background: #8e44ad;
            color: #fff;
            border-color: #8e44ad;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(142, 68, 173, 0.3);
        }

        .var-chip i {
            font-size: 10px;
            opacity: 0.7;
        }

        .var-chip:hover i {
            opacity: 1;
        }

        .var-chip.last-target {
            border-color: #16a085;
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.15);
        }

        .var-empty-msg {
            font-size: 11px;
            color: #95a5a6;
            font-style: italic;
        }

        /* ═══ Auto Filter Box ═══ */
        .auto-filter-box {
            background: linear-gradient(135deg, #fff8ec, #fef3e2);
            border: 1.5px solid #f5cba7;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 14px;
        }

        .auto-filter-box .af-label {
            font-size: 12.5px;
            font-weight: 700;
            color: #8b5a1c;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .auto-filter-box .af-label i {
            color: #d68910;
        }

        .auto-filter-box .af-hint {
            background: #fff;
            border-right: 3px solid #d68910;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #8b5a1c;
            margin-bottom: 12px;
            line-height: 1.7;
        }

        .auto-filter-box .af-hint code {
            background: #fef0e6;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            color: #8b5a1c;
            font-family: 'Consolas', monospace;
        }

        .af-row {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #fff;
            border: 1.5px solid #f5dcc4;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 8px;
        }

        .af-row .af-check {
            width: 20px;
            height: 20px;
            accent-color: #d68910;
            cursor: pointer;
            flex-shrink: 0;
        }

        .af-row .af-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #8b5a1c;
            min-width: 110px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .af-row .af-name i {
            color: #d68910;
            font-size: 11px;
        }

        .af-row .af-preview {
            flex-grow: 1;
            font-family: 'Consolas', monospace;
            font-size: 12px;
            background: #fef8f0;
            padding: 6px 12px;
            border-radius: 6px;
            color: #5a3a10;
            border: 1px dashed #f5cba7;
            word-break: break-all;
        }

        .af-row .af-preview.disabled {
            background: #f5f5f5;
            color: #b8b8b8;
            border-color: #e0e0e0;
            font-style: italic;
        }

        .af-field-input {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1.5px solid #f5cba7;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 12px;
        }

        .af-field-input label {
            font-size: 12.5px;
            font-weight: 600;
            color: #8b5a1c;
            white-space: nowrap;
            margin: 0;
        }

        .af-field-input input {
            flex-grow: 1;
            border: 1px solid #e8ecef;
            border-radius: 6px;
            padding: 7px 12px;
            font-size: 13px;
            font-family: 'Consolas', monospace;
            background: #fbfcfd;
        }

        .af-field-input input:focus {
            border-color: #d68910;
            outline: none;
            box-shadow: 0 0 0 3px rgba(214, 137, 16, 0.12);
        }

        @media (max-width: 640px) {
            .top-header h1 {
                font-size: 17px;
            }

            .card-box .body {
                padding: 14px;
            }

            .code-body pre {
                font-size: 11px;
                padding: 14px;
            }

            .type-chips {
                grid-template-columns: repeat(3, 1fr);
            }

            .auto-val-row .av-fields {
                grid-template-columns: 1fr;
            }

            .toggle-card {
                padding: 14px;
            }

            .toggle-card .switch-icon {
                width: 40px;
                height: 40px;
                font-size: 17px;
            }

            .toggle-card .switch-state {
                display: none;
            }

            .af-row {
                flex-direction: column;
                align-items: stretch;
                gap: 6px;
            }

            .af-row .af-name {
                min-width: auto;
            }
        }
    </style>
</head>
<body>

<div class="top-header">
    <div class="icon"><i class="fas fa-magic"></i></div>
    <h1>فرم‌ساز گام‌به‌گام</h1>
    <p>تابع شناسه + فیلتر خودکار + مقادیر خودکار — همه به هم متصل</p>
</div>

<div class="wrap">

    <?php if ($result !== null && !empty($result['error'])): ?>
        <div class="alert alert-danger"><?= h($result['error']) ?></div>
    <?php endif; ?>

    <?php if ($result !== null && !empty($result['php'])): ?>

        <div class="card-box">
            <div class="head"
                 style="background: linear-gradient(135deg, #16a085, #1abc9c); border-bottom-color:#0d7a5f;">
                <i class="fas fa-check-circle" style="color:#fff;"></i>
                کدها با موفقیت ساخته شدن
            </div>
            <div class="body">
                <p style="font-size:13px;color:#5a6b7a;margin:0 0 14px;">
                    <i class="fas fa-info-circle" style="color:var(--accent);"></i>
                    فایل PHP و فایل‌های کمکی رو در پوشه‌ی پنل قرار بدید و کد SQL رو در phpMyAdmin اجرا کنید.
                </p>
                <?php if (!empty($result['extras'])): ?>
                    <div class="note-box">
                        <i class="fas fa-circle-info"></i>
                        <div><strong>توجه:</strong> فایل‌های زیر رو هم <strong>در همان پوشه‌ی</strong> فایل اصلی قرار
                            بدید.
                        </div>
                    </div>
                <?php endif; ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="back_to_edit" value="1">
                    <input type="hidden" name="panel_title" value="<?= h($saved['panel_title']) ?>">
                    <input type="hidden" name="table_name" value="<?= h($saved['table_name']) ?>">
                    <input type="hidden" name="table_key" value="<?= h($saved['table_key']) ?>">
                    <input type="hidden" name="table_desc" value="<?= h($saved['table_desc']) ?>">
                    <input type="hidden" name="alow_edit" value="<?= $saved['alow_edit'] ? '1' : '' ?>">
                    <input type="hidden" name="alow_del" value="<?= $saved['alow_del'] ? '1' : '' ?>">
                    <input type="hidden" name="filters_enabled" value="<?= $saved['filters_enabled'] ? '1' : '' ?>">
                    <input type="hidden" name="auto_filters_enabled"
                           value="<?= $saved['auto_filters_enabled'] ? '1' : '' ?>">
                    <input type="hidden" name="auto_filter_field" value="<?= h($saved['auto_filter_field']) ?>">
                    <input type="hidden" name="auto_filter_show" value="<?= $saved['auto_filter_show'] ? '1' : '' ?>">
                    <input type="hidden" name="auto_filter_delete"
                           value="<?= $saved['auto_filter_delete'] ? '1' : '' ?>">
                    <input type="hidden" name="auto_filter_edit" value="<?= $saved['auto_filter_edit'] ? '1' : '' ?>">
                    <input type="hidden" name="where_show" value="<?= h($saved['where_show']) ?>">
                    <input type="hidden" name="where_delete" value="<?= h($saved['where_delete']) ?>">
                    <input type="hidden" name="where_edit" value="<?= h($saved['where_edit']) ?>">
                    <input type="hidden" name="auto_values_json" value="<?= json_h($saved['auto_values']) ?>">
                    <input type="hidden" name="fields_json" value="<?= json_h($saved['fields']) ?>">
                    <input type="hidden" name="id_func_enabled" value="<?= $saved['id_func']['enabled'] ? '1' : '' ?>">
                    <input type="hidden" name="id_func_name" value="<?= h($saved['id_func']['name']) ?>">
                    <input type="hidden" name="id_func_variable_name"
                           value="<?= h($saved['id_func']['variable_name']) ?>">
                    <input type="hidden" name="id_func_session_key" value="<?= h($saved['id_func']['session_key']) ?>">
                    <input type="hidden" name="id_func_table" value="<?= h($saved['id_func']['table']) ?>">
                    <input type="hidden" name="id_func_user_field" value="<?= h($saved['id_func']['user_field']) ?>">
                    <input type="hidden" name="id_func_extra_where" value="<?= h($saved['id_func']['extra_where']) ?>">
                    <button type="submit" class="btn-ghost">
                        <i class="fas fa-arrow-right"></i> بازگشت به ویرایش
                    </button>
                </form>
            </div>
        </div>

        <?php
        $idKey = null;
        foreach (($result['extras'] ?? []) as $hk => $hc) {
            if ($hk === '_id_function.php') {
                $idKey = $hk;
                break;
            }
        }
        ?>
        <?php if ($idKey !== null): ?>
            <div class="code-wrap">
                <div class="code-head">
                <span class="filename">
                    🔑 <?= h($idKey) ?>
                    <span class="badge-id">به calhead.php اضافه کن</span>
                </span>
                    <button class="btn-code" onclick="copyCode('idFuncCode', this)">
                        <i class="fas fa-copy"></i> کپی
                    </button>
                </div>
                <div class="code-body">
                    <pre id="idFuncCode"><?= h($result['extras'][$idKey]) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <div class="code-wrap">
            <div class="code-head">
                <span class="filename">📄 <?= h($result['file']) ?></span>
                <button class="btn-code" onclick="copyCode('phpCode', this)"><i class="fas fa-copy"></i> کپی</button>
            </div>
            <div class="code-body">
                <pre id="phpCode"><?= h($result['php']) ?></pre>
            </div>
        </div>

        <?php $ei = 0;
        foreach (($result['extras'] ?? []) as $hName => $hCode): if ($hName === '_id_function.php') continue;
            $ei++; ?>
            <div class="code-wrap">
                <div class="code-head">
                    <span class="filename">🧩 <?= h($hName) ?> <span class="badge-helper">فایل کمکی</span></span>
                    <button class="btn-code" onclick="copyCode('exCode<?= $ei ?>', this)"><i class="fas fa-copy"></i>
                        کپی
                    </button>
                </div>
                <div class="code-body">
                    <pre id="exCode<?= $ei ?>"><?= h($hCode) ?></pre>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="code-wrap">
            <div class="code-head">
                <span class="filename">🗄 <?= h($result['table']) ?>.sql</span>
                <button class="btn-code" onclick="copyCode('sqlCode', this)"><i class="fas fa-copy"></i> کپی</button>
            </div>
            <div class="code-body">
                <pre id="sqlCode"><?= h($result['sql']) ?></pre>
            </div>
        </div>

    <?php else: ?>

        <?php if ($edit): ?>
            <div class="edit-banner">
                <i class="fas fa-pen-to-square"></i>
                <div><strong>در حال ویرایش فرم:</strong> اطلاعات قبلی بازیابی شده — می‌تونی تغییر بدی و دوباره بسازی.
                </div>
            </div>
        <?php endif; ?>

        <form method="post" id="builderForm" onsubmit="return finalCheck()">
            <input type="hidden" name="finish" value="1">
            <input type="hidden" name="fields_json" value="" id="fieldsJson">
            <input type="hidden" name="auto_values_json" value="" id="autoValuesJson">

            <!-- Card 1 -->
            <div class="card-box">
                <div class="head"><span class="step-num">1</span> تنظیمات کلی فرم</div>
                <div class="body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">عنوان فرم</label>
                            <input type="text" name="panel_title" class="form-control" required
                                   value="<?= h($saved['panel_title']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام جدول دیتابیس</label>
                            <input type="text" name="table_name" class="form-control" required pattern="[a-zA-Z0-9_]+"
                                   value="<?= h($saved['table_name']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">کلید اصلی</label>
                            <input type="text" name="table_key" class="form-control"
                                   value="<?= h($saved['table_key']) ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">توضیح فرم</label>
                            <input type="text" name="table_desc" class="form-control"
                                   value="<?= h($saved['table_desc']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-check form-check-inline">
                                <input type="checkbox" name="alow_edit" class="form-check-input"
                                       value="1" <?= $saved['alow_edit'] ? 'checked' : '' ?>>
                                <span class="form-check-label">اجازه ویرایش</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input type="checkbox" name="alow_del" class="form-check-input"
                                       value="1" <?= $saved['alow_del'] ? 'checked' : '' ?>>
                                <span class="form-check-label">اجازه حذف</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: ID Function -->
            <div class="card-box">
                <div class="head">
                    <span class="step-num">2</span>
                    تعریف شناسه‌ی کاربر
                    <span class="step-opt"><i class="fas fa-cog"></i> اختیاری</span>
                </div>
                <div class="body">

                    <label class="form-check mb-3">
                        <input type="checkbox" name="id_func_enabled" id="id_func_enabled" class="form-check-input"
                               value="1"
                            <?= $saved['id_func']['enabled'] ? 'checked' : '' ?>
                               onchange="onIdFuncToggle()">
                        <span class="form-check-label">
                        می‌خواهم شناسه‌ی کاربر جاری (<code>get_..._id()</code>) ساخته شود
                    </span>
                    </label>

                    <div id="idFuncPanel" style="display:<?= $saved['id_func']['enabled'] ? 'block' : 'none' ?>;">
                        <div class="id-hint">
                            <i class="fas fa-lightbulb" style="color:#8e44ad;"></i>
                            این تابع یه <strong>متغیر PHP</strong> تولید می‌کنه که توی بخش فیلترها و مقادیر خودکار قابل
                            استفاده‌ست.
                        </div>

                        <div class="id-func-box">
                            <div class="filter-label"><i class="fas fa-key"></i> مشخصات تابع</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام تابع</label>
                                    <input type="text" name="id_func_name" id="id_func_name" class="form-control"
                                           placeholder="get_marketers_id" value="<?= h($saved['id_func']['name']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نام متغیر PHP</label>
                                    <input type="text" name="id_func_variable_name" id="id_func_variable_name"
                                           class="form-control" placeholder="$mid"
                                           value="<?= h($saved['id_func']['variable_name']) ?>">
                                    <div style="font-size:11px;color:#7f8c8d;margin-top:4px;">مثلاً: <code>$mid</code>
                                        یا <code>$cid</code></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">کلید سشن</label>
                                    <input type="text" name="id_func_session_key" id="id_func_session_key"
                                           class="form-control" placeholder="tel"
                                           value="<?= h($saved['id_func']['session_key']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">نام جدول</label>
                                    <input type="text" name="id_func_table" id="id_func_table" class="form-control"
                                           placeholder="marketers" value="<?= h($saved['id_func']['table']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">فیلد تطبیق در جدول</label>
                                    <input type="text" name="id_func_user_field" id="id_func_user_field"
                                           class="form-control" placeholder="tel"
                                           value="<?= h($saved['id_func']['user_field']) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">شرط اضافی (اختیاری)</label>
                                    <input type="text" name="id_func_extra_where" id="id_func_extra_where"
                                           class="form-control" placeholder="status=1"
                                           value="<?= h($saved['id_func']['extra_where']) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Filters -->
            <div class="card-box">
                <div class="head">
                    <span class="step-num">3</span>
                    فیلترها و مقادیر خودکار
                    <span class="step-opt"><i class="fas fa-cog"></i> اختیاری</span>
                </div>
                <div class="body">

                    <input type="checkbox" name="filters_enabled" id="filters_enabled" value="1"
                           style="display:none;"
                        <?= $saved['filters_enabled'] ? 'checked' : '' ?>>

                    <div class="toggle-card <?= $saved['filters_enabled'] ? 'active' : '' ?>"
                         id="filtersToggleCard"
                         onclick="toggleFilters()">

                        <div class="switch-icon"><i class="fas fa-sliders-h"></i></div>

                        <div class="switch-info">
                            <div class="switch-title">
                                <i class="fas fa-filter" style="color:#16a085;"></i>
                                فعال‌سازی فیلترها و مقادیر خودکار
                            </div>
                            <div class="switch-desc">
                                می‌تونی از <code>setwhere</code> <code>deletewhere</code> <code>set_where_edit</code>
                                <code>set_int_val</code> <code>set_str_val</code> استفاده کنی.
                            </div>
                        </div>

                        <div class="switch-state" id="filtersState">
                            <i class="fas fa-<?= $saved['filters_enabled'] ? 'check-circle' : 'circle' ?>"></i>
                            <?= $saved['filters_enabled'] ? 'فعال' : 'غیرفعال' ?>
                        </div>
                    </div>

                    <div class="collapse-panel <?= $saved['filters_enabled'] ? 'open' : '' ?>" id="filtersPanel">

                        <!-- ═══ Variable Chips Bar ═══ -->
                        <div class="var-chips-bar" id="varChipsBar"
                             style="display:<?= $saved['id_func']['enabled'] ? 'flex' : 'none' ?>;">
                        <span class="vc-label">
                            <i class="fas fa-code"></i>
                            متغیرهای در دسترس:
                        </span>
                            <div id="varChipsList" style="display:inline-flex;gap:8px;flex-wrap:wrap;"></div>
                            <span class="var-empty-msg" id="varEmptyMsg" style="display:none;">
                            ابتدا از بخش ۲ تابع شناسه را فعال کنید تا متغیرها اینجا نمایش داده شوند.
                        </span>
                        </div>

                        <!-- ═══ Auto Filter Box (NEW) ═══ -->
                        <div class="auto-filter-box" id="autoFilterBox"
                             style="display:<?= $saved['id_func']['enabled'] ? 'block' : 'none' ?>;">

                            <input type="checkbox" id="auto_filters_enabled" name="auto_filters_enabled" value="1"
                                   style="display:none;"
                                <?= $saved['auto_filters_enabled'] ? 'checked' : '' ?>>

                            <div class="toggle-card <?= $saved['auto_filters_enabled'] ? 'active' : '' ?>"
                                 id="autoFiltersToggleCard"
                                 style="background:<?= $saved['auto_filters_enabled'] ? 'linear-gradient(135deg, #fef0e6, #fde5c8)' : 'linear-gradient(135deg, #fff8ec, #fef3e2)' ?>;border-color:#f5cba7;"
                                 onclick="toggleAutoFilters()">

                                <div class="switch-icon" style="background:linear-gradient(135deg, #d68910, #f39c12);">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </div>

                                <div class="switch-info">
                                    <div class="switch-title" style="color:#8b5a1c;">
                                        <i class="fas fa-magic" style="color:#d68910;"></i>
                                        فیلتر خودکار بر اساس شناسه
                                    </div>
                                    <div class="switch-desc" style="color:#a06820;">
                                        فقط نام فیلد رو بده، خودش شرط کامل می‌سازه.
                                    </div>
                                </div>

                                <div class="switch-state" id="autoFiltersState"
                                     style="color:<?= $saved['auto_filters_enabled'] ? '#d68910' : '#95a5a6' ?>;">
                                    <i class="fas fa-<?= $saved['auto_filters_enabled'] ? 'check-circle' : 'circle' ?>"></i>
                                    <?= $saved['auto_filters_enabled'] ? 'فعال' : 'غیرفعال' ?>
                                </div>
                            </div>

                            <div class="collapse-panel <?= $saved['auto_filters_enabled'] ? 'open' : '' ?>"
                                 id="autoFiltersPanel"
                                 style="margin-top:16px;">

                                <div class="af-hint">
                                    <i class="fas fa-info-circle"></i>
                                    نام فیلد رو بنویس (مثلاً <code>marketer_id</code>) — سیستم خودش شرط رو با متغیر
                                    <code id="afVarPreview"><?= h($saved['id_func']['variable_name'] ?: '$mid') ?></code>
                                    می‌سازه.
                                </div>

                                <div class="af-field-input">
                                    <label><i class="fas fa-tag"></i> نام فیلد:</label>
                                    <input type="text" id="auto_filter_field" name="auto_filter_field"
                                           placeholder="marketer_id"
                                           value="<?= h($saved['auto_filter_field']) ?>"
                                           oninput="syncAutoFilters()">
                                </div>

                                <div class="af-row">
                                    <input type="checkbox" class="af-check" id="auto_filter_show"
                                           name="auto_filter_show" value="1"
                                        <?= $saved['auto_filter_show'] ? 'checked' : '' ?>
                                           onchange="syncAutoFilters()">
                                    <span class="af-name"><i class="fas fa-eye"></i> نمایش (setwhere)</span>
                                    <span class="af-preview" id="afPreviewShow">—</span>
                                </div>

                                <div class="af-row">
                                    <input type="checkbox" class="af-check" id="auto_filter_delete"
                                           name="auto_filter_delete" value="1"
                                        <?= $saved['auto_filter_delete'] ? 'checked' : '' ?>
                                           onchange="syncAutoFilters()">
                                    <span class="af-name"><i class="fas fa-trash"></i> حذف (deletewhere)</span>
                                    <span class="af-preview" id="afPreviewDelete">—</span>
                                </div>

                                <div class="af-row">
                                    <input type="checkbox" class="af-check" id="auto_filter_edit"
                                           name="auto_filter_edit" value="1"
                                        <?= $saved['auto_filter_edit'] ? 'checked' : '' ?>
                                           onchange="syncAutoFilters()">
                                    <span class="af-name"><i class="fas fa-pen"></i> ویرایش (set_where_edit)</span>
                                    <span class="af-preview" id="afPreviewEdit">—</span>
                                </div>

                            </div>
                        </div>

                        <!-- Manual Filters -->
                        <div class="filter-box">
                            <div class="filter-label"><i class="fas fa-filter"></i> فیلترها (دستی)</div>
                            <div style="font-size:11.5px;color:#7f8c9b;margin-bottom:10px;">
                                <i class="fas fa-info-circle"></i>
                                اگه فیلتر خودکار رو فعال کردی، این فیلدها خودکار پر می‌شن — می‌تونی دستی هم ویرایش کنی.
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">شرط نمایش — <code
                                                style="background:#eef2f5;padding:2px 6px;border-radius:4px;font-size:11px;">setwhere</code></label>
                                    <input type="text" name="where_show" id="where_show" class="form-control var-target"
                                           placeholder="مثلاً: marketer_id=$mid"
                                           value="<?= h($saved['where_show']) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">شرط حذف — <code
                                                style="background:#eef2f5;padding:2px 6px;border-radius:4px;font-size:11px;">deletewhere</code></label>
                                    <input type="text" name="where_delete" id="where_delete"
                                           class="form-control var-target"
                                           placeholder="مثلاً: marketer_id=$mid"
                                           value="<?= h($saved['where_delete']) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">شرط ویرایش — <code
                                                style="background:#eef2f5;padding:2px 6px;border-radius:4px;font-size:11px;">set_where_edit</code></label>
                                    <input type="text" name="where_edit" id="where_edit" class="form-control var-target"
                                           placeholder="مثلاً: marketer_id=$mid"
                                           value="<?= h($saved['where_edit']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="filter-box" style="margin-bottom:0;">
                            <div class="filter-label"><i class="fas fa-magic"></i> مقادیر خودکار (set_int_val /
                                set_str_val)
                            </div>
                            <div style="font-size:11.5px;color:#7f8c9b;margin-bottom:10px;">
                                مقادیری که هنگام ثبت به‌صورت خودکار در دیتابیس ذخیره می‌شن.
                            </div>
                            <div id="autoValuesList"></div>
                            <button type="button" class="btn-ghost" onclick="addAutoVal()" style="margin-top:6px;">
                                <i class="fas fa-plus"></i> افزودن مقدار خودکار
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Card 4: Fields -->
            <div class="card-box">
                <div class="head"><span class="step-num">4</span> افزودن فیلد جدید</div>
                <div class="body">

                    <label class="form-label">نوع ورودی</label>
                    <div class="type-chips" id="typeChips">
                        <div class="type-chip active" data-type="text">
                            <div class="chip-icon"><i class="fas fa-font"></i></div>
                            <div class="chip-title">متن</div>
                        </div>
                        <div class="type-chip" data-type="textarea">
                            <div class="chip-icon"><i class="fas fa-align-right"></i></div>
                            <div class="chip-title">متن بلند</div>
                        </div>
                        <div class="type-chip" data-type="number">
                            <div class="chip-icon"><i class="fas fa-hashtag"></i></div>
                            <div class="chip-title">عدد</div>
                        </div>
                        <div class="type-chip" data-type="password">
                            <div class="chip-icon"><i class="fas fa-key"></i></div>
                            <div class="chip-title">رمز عبور</div>
                        </div>
                        <div class="type-chip" data-type="select">
                            <div class="chip-icon"><i class="fas fa-list"></i></div>
                            <div class="chip-title">سلکت</div>
                        </div>
                        <div class="type-chip" data-type="date">
                            <div class="chip-icon"><i class="fas fa-calendar"></i></div>
                            <div class="chip-title">تاریخ</div>
                        </div>
                        <div class="type-chip" data-type="file">
                            <div class="chip-icon"><i class="fas fa-image"></i></div>
                            <div class="chip-title">فایل</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام فیلد (انگلیسی)</label>
                            <input type="text" id="f_name" class="form-control" placeholder="title">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">عنوان فارسی</label>
                            <input type="text" id="f_label" class="form-control" placeholder="عنوان">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Placeholder (اختیاری)</label>
                            <input type="text" id="f_placeholder" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مقدار پیش‌فرض (اختیاری)</label>
                            <input type="text" id="f_default" class="form-control">
                        </div>
                    </div>

                    <div id="selectExtras"
                         style="display:none;margin-top:20px;padding:16px;background:#fbfcfd;border:1.5px dashed #d6dbe1;border-radius:12px;">
                        <label class="form-label" style="font-size:13px;margin-bottom:8px;">
                            <i class="fas fa-database" style="color:var(--accent);"></i> منبع گزینه‌های سلکت
                        </label>
                        <div class="src-toggle">
                            <input type="radio" name="sel_source" id="src_manual" value="manual" checked>
                            <label for="src_manual"><i class="fas fa-keyboard"></i> دستی</label>
                            <input type="radio" name="sel_source" id="src_db" value="database">
                            <label for="src_db"><i class="fas fa-database"></i> از دیتابیس</label>
                        </div>
                        <div id="panelManual">
                            <div id="manualOptions"></div>
                            <button type="button" class="btn-ghost" onclick="addManualOpt()" style="margin-top:6px;">
                                <i class="fas fa-plus"></i> افزودن گزینه
                            </button>
                        </div>
                        <div id="panelDB" style="display:none;">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">نام جدول منبع</label>
                                    <input type="text" id="f_select_table" class="form-control"
                                           placeholder="categories">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">فیلد Value</label>
                                    <input type="text" id="f_select_value" class="form-control" value="id">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">فیلدهای نمایشی (با کاما)</label>
                                    <input type="text" id="f_select_display" class="form-control"
                                           placeholder="name,family">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">مرتب‌سازی</label>
                                    <input type="text" id="f_select_order" class="form-control" placeholder="title">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">شرط WHERE</label>
                                    <input type="text" id="f_select_where" class="form-control" placeholder="status=1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="fileExtras"
                         style="display:none;margin-top:16px;padding:14px 16px;background:#fef5e7;border:1.5px solid #f5cba7;border-radius:10px;font-size:12.5px;color:#8b5a1c;">
                        <i class="fas fa-lightbulb" style="color:#d68910;"></i>
                        <strong>توجه:</strong> برای نمایش تصویر در جدول، تیک «نمایش در جدول» رو بزن.
                    </div>

                    <div class="mt-3">
                        <label class="form-check form-check-inline">
                            <input type="checkbox" id="f_required" class="form-check-input">
                            <span class="form-check-label">اجباری</span>
                        </label>
                        <label class="form-check form-check-inline">
                            <input type="checkbox" id="f_in_table" class="form-check-input" checked>
                            <span class="form-check-label">نمایش در جدول</span>
                        </label>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn-accent" onclick="insertField()">
                            <i class="fas fa-plus"></i> درج فیلد
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 5: Fields List -->
            <div class="card-box">
                <div class="head">
                    <span class="step-num">5</span> فیلدهای اضافه شده
                    <span id="fieldsCountBadge"
                          style="margin-right:auto;background:rgba(255,255,255,0.15);color:#fff;padding:3px 12px;border-radius:20px;font-size:11px;">0 فیلد</span>
                    <span style="font-size:10.5px;font-weight:normal;opacity:0.85;"><i class="fas fa-hand-pointer"></i> با درگ جابجا کن</span>
                </div>
                <div class="body">
                    <div id="fieldsList"></div>
                    <div id="emptyState" class="empty-box">
                        <i class="fas fa-inbox"></i>
                        هنوز فیلدی اضافه نشده — از بالا یک فیلد بساز و «درج» رو بزن
                    </div>
                </div>
            </div>

            <!-- Card 6: Finish -->
            <div class="card-box">
                <div class="head"><span class="step-num">6</span> اتمام و ساخت کد</div>
                <div class="body text-center">
                    <p class="info-line justify-content-center mb-3">
                        <i class="fas fa-info-circle"></i>
                        با کلیک روی دکمه‌ی زیر، کد PHP و SQL ساخته می‌شن.
                    </p>
                    <button type="submit" class="btn-accent" style="padding:14px 40px;font-size:14px;">
                        <i class="fas fa-check-double"></i> اتمام و ساخت کد
                    </button>
                </div>
            </div>

        </form>

    <?php endif; ?>
</div>

<div class="toast-box" id="toast"><i class="fas fa-check-circle"></i> <span id="toastText">انجام شد</span></div>

<script>
    var fields = <?= json_encode($saved['fields'], JSON_UNESCAPED_UNICODE) ?>;
    var autoVals = <?= json_encode($saved['auto_values'], JSON_UNESCAPED_UNICODE) ?>;
    var currentType = 'text';
    var editIndex = -1;
    var lastVarTarget = null;

    /* ═══════ ID Function Toggle ═══════ */
    function onIdFuncToggle() {
        var enabled = document.getElementById('id_func_enabled').checked;
        document.getElementById('idFuncPanel').style.display = enabled ? 'block' : 'none';
        document.getElementById('autoFilterBox').style.display = enabled ? 'block' : 'none';
        refreshVarChips();
        if (enabled) autoFillIdFunc();
        syncAutoFilters();
    }

    function autoFillIdFunc() {
        var nameField = document.getElementById('id_func_name');
        if (nameField.value.trim() === '' && panelData.table_name) {
            var t = panelData.table_name.replace(/s$/, '');
            nameField.value = 'get_' + t + '_id';
        }
    }

    /* ═══════ Variable Chips ═══════ */
    function refreshVarChips() {
        var enabled = document.getElementById('id_func_enabled').checked;
        var bar = document.getElementById('varChipsBar');
        var list = document.getElementById('varChipsList');
        var emptyMsg = document.getElementById('varEmptyMsg');

        if (!enabled) {
            bar.style.display = 'none';
            return;
        }
        bar.style.display = 'flex';

        var varName = document.getElementById('id_func_variable_name').value.trim();
        var funcName = document.getElementById('id_func_name').value.trim();

        /* آپدیت پیش‌نمایش auto filter */
        var afVarPreview = document.getElementById('afVarPreview');
        if (afVarPreview) afVarPreview.innerText = varName || '$mid';

        list.innerHTML = '';
        if (varName === '') {
            emptyMsg.style.display = 'inline';
            return;
        }
        emptyMsg.style.display = 'none';

        var chip = document.createElement('span');
        chip.className = 'var-chip';
        chip.innerHTML = '<i class="fas fa-code"></i>' + esc(varName);
        chip.title = 'برای درج در فیلد فعلی کلیک کن';
        chip.onclick = function () {
            insertVar(varName);
        };
        list.appendChild(chip);

        if (funcName !== '') {
            var chip2 = document.createElement('span');
            chip2.className = 'var-chip';
            chip2.style.background = '#f0e6f7';
            chip2.innerHTML = '<i class="fas fa-key"></i>' + esc(funcName) + '()';
            chip2.onclick = function () {
                insertVar(funcName + '()');
            };
            list.appendChild(chip2);
        }
    }

    function insertVar(varName) {
        var target = document.activeElement;
        if (!target || !target.classList || !target.classList.contains('var-target')) {
            if (lastVarTarget) target = lastVarTarget;
            else {
                showToast('اول روی یک فیلد فیلتر کلیک کن', 'error');
                return;
            }
        }
        var startPos = target.selectionStart || target.value.length;
        var endPos = target.selectionEnd || target.value.length;
        var val = target.value;
        target.value = val.substring(0, startPos) + varName + val.substring(endPos);
        target.focus();
        target.selectionStart = target.selectionEnd = startPos + varName.length;
        showToast('متغیر ' + varName + ' درج شد');
    }

    document.addEventListener('focusin', function (e) {
        if (e.target && e.target.classList && e.target.classList.contains('var-target')) {
            lastVarTarget = e.target;
            document.querySelectorAll('.var-target').forEach(function (el) {
                el.classList.remove('last-target');
            });
            e.target.classList.add('last-target');
        }
    });

    /* ═══════ Toggle Filters ═══════ */
    function toggleFilters() {
        var checkbox = document.getElementById('filters_enabled');
        var card = document.getElementById('filtersToggleCard');
        var panel = document.getElementById('filtersPanel');
        var state = document.getElementById('filtersState');

        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            card.classList.add('active');
            panel.classList.add('open');
            state.innerHTML = '<i class="fas fa-check-circle"></i> فعال';
        } else {
            card.classList.remove('active');
            panel.classList.remove('open');
            state.innerHTML = '<i class="fas fa-circle"></i> غیرفعال';
        }
    }

    /* ═══════ Toggle Auto Filters ═══════ */
    function toggleAutoFilters() {
        var checkbox = document.getElementById('auto_filters_enabled');
        var card = document.getElementById('autoFiltersToggleCard');
        var panel = document.getElementById('autoFiltersPanel');
        var state = document.getElementById('autoFiltersState');

        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            card.classList.add('active');
            card.style.background = 'linear-gradient(135deg, #fef0e6, #fde5c8)';
            panel.classList.add('open');
            state.innerHTML = '<i class="fas fa-check-circle"></i> فعال';
            state.style.color = '#d68910';
            syncAutoFilters();
        } else {
            card.classList.remove('active');
            card.style.background = 'linear-gradient(135deg, #fff8ec, #fef3e2)';
            panel.classList.remove('open');
            state.innerHTML = '<i class="fas fa-circle"></i> غیرفعال';
            state.style.color = '#95a5a6';
        }
    }

    /* ═══════ Sync Auto Filters ═══════ */
    function syncAutoFilters() {
        var enabled = document.getElementById('auto_filters_enabled').checked;
        var field = document.getElementById('auto_filter_field').value.trim();
        var varName = document.getElementById('id_func_variable_name').value.trim() || '$mid';
        var idEnabled = document.getElementById('id_func_enabled').checked;

        var condition = '';
        if (enabled && field !== '' && idEnabled) {
            condition = field + '=' + varName;
        }

        /* آپدیت پیش‌نمایش‌ها */
        var prevShow = document.getElementById('afPreviewShow');
        var prevDelete = document.getElementById('afPreviewDelete');
        var prevEdit = document.getElementById('afPreviewEdit');

        var isShowOn = document.getElementById('auto_filter_show').checked;
        var isDeleteOn = document.getElementById('auto_filter_delete').checked;
        var isEditOn = document.getElementById('auto_filter_edit').checked;

        prevShow.innerText = (condition && isShowOn) ? condition : '— غیرفعال —';
        prevShow.classList.toggle('disabled', !(condition && isShowOn));
        prevDelete.innerText = (condition && isDeleteOn) ? condition : '— غیرفعال —';
        prevDelete.classList.toggle('disabled', !(condition && isDeleteOn));
        prevEdit.innerText = (condition && isEditOn) ? condition : '— غیرفعال —';
        prevEdit.classList.toggle('disabled', !(condition && isEditOn));

        /* اگه auto filter فعاله، مقادیر فیلترهای دستی رو هم ست کن */
        if (condition !== '') {
            if (isShowOn) document.getElementById('where_show').value = condition;
            if (isDeleteOn) document.getElementById('where_delete').value = condition;
            if (isEditOn) document.getElementById('where_edit').value = condition;
        }
    }

    /* ═══════ Type chips ═══════ */
    document.querySelectorAll('.type-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            document.querySelectorAll('.type-chip').forEach(function (c) {
                c.classList.remove('active');
            });
            this.classList.add('active');
            currentType = this.getAttribute('data-type');
            document.getElementById('selectExtras').style.display = (currentType === 'select') ? 'block' : 'none';
            document.getElementById('fileExtras').style.display = (currentType === 'file') ? 'block' : 'none';
            if (currentType === 'select' && document.getElementById('manualOptions').children.length === 0) {
                addManualOpt();
                addManualOpt();
            }
        });
    });

    document.getElementById('src_manual').addEventListener('change', function () {
        document.getElementById('panelManual').style.display = 'block';
        document.getElementById('panelDB').style.display = 'none';
    });
    document.getElementById('src_db').addEventListener('change', function () {
        document.getElementById('panelManual').style.display = 'none';
        document.getElementById('panelDB').style.display = 'block';
    });

    /* ═══════ Manual options ═══════ */
    function addManualOpt(val, lbl) {
        val = val || '';
        lbl = lbl || '';
        var wrap = document.getElementById('manualOptions');
        var html = '<div class="manual-opt">'
            + '<div class="opt-num">' + (wrap.children.length + 1) + '</div>'
            + '<div class="opt-fields">'
            + '<input type="text" class="opt-val" placeholder="value" value="' + escAttr(val) + '" style="width:30%;">'
            + '<input type="text" class="opt-lbl" placeholder="label" value="' + escAttr(lbl) + '" style="width:70%;">'
            + '</div>'
            + '<button type="button" class="opt-remove" onclick="removeManualOpt(this)"><i class="fas fa-times"></i></button>'
            + '</div>';
        wrap.insertAdjacentHTML('beforeend', html);
        renumberManualOpts();
    }

    function removeManualOpt(btn) {
        var r = btn.closest('.manual-opt');
        if (r) r.remove();
        renumberManualOpts();
    }

    function renumberManualOpts() {
        document.querySelectorAll('.manual-opt').forEach(function (o, i) {
            var n = o.querySelector('.opt-num');
            if (n) n.innerText = i + 1;
        });
    }

    /* ═══════ Auto Values ═══════ */
    function addAutoVal(name, type, value) {
        name = name || '';
        type = type || 'int';
        value = value || '';
        var wrap = document.getElementById('autoValuesList');
        var html = '<div class="auto-val-row">'
            + '<div class="av-num">' + (wrap.children.length + 1) + '</div>'
            + '<div class="av-fields">'
            + '<input type="text" class="av-name" placeholder="نام فیلد" value="' + escAttr(name) + '">'
            + '<select class="av-type">'
            + '<option value="int"' + (type === 'int' ? ' selected' : '') + '>عدد</option>'
            + '<option value="string"' + (type === 'string' ? ' selected' : '') + '>رشته</option>'
            + '</select>'
            + '<input type="text" class="av-value var-target" placeholder="مقدار (مثلاً: 1 یا $mid)" value="' + escAttr(value) + '">'
            + '</div>'
            + '<button type="button" class="av-remove" onclick="removeAutoVal(this)"><i class="fas fa-times"></i></button>'
            + '</div>';
        wrap.insertAdjacentHTML('beforeend', html);
        renumberAutoVals();
    }

    function removeAutoVal(btn) {
        var r = btn.closest('.auto-val-row');
        if (r) r.remove();
        renumberAutoVals();
    }

    function renumberAutoVals() {
        document.querySelectorAll('.auto-val-row').forEach(function (o, i) {
            var n = o.querySelector('.av-num');
            if (n) n.innerText = i + 1;
        });
    }

    function collectAutoVals() {
        var list = [];
        document.querySelectorAll('.auto-val-row').forEach(function (row) {
            var n = row.querySelector('.av-name').value.trim();
            var t = row.querySelector('.av-type').value;
            var v = row.querySelector('.av-value').value.trim();
            if (n !== '') list.push({name: n, type: t, value: v});
        });
        return list;
    }

    /* ═══════ Insert / Update Field ═══════ */
    function insertField() {
        var name = document.getElementById('f_name').value.trim();
        var label = document.getElementById('f_label').value.trim();
        var ph = document.getElementById('f_placeholder').value.trim();
        var def = document.getElementById('f_default').value.trim();
        var req = document.getElementById('f_required').checked;
        var tbl = document.getElementById('f_in_table').checked;
        if (!name) {
            showToast('نام فیلد الزامی است', 'error');
            return;
        }
        if (!/^[a-zA-Z0-9_]+$/.test(name)) {
            showToast('نام فیلد فقط حروف/عدد/_', 'error');
            return;
        }
        if (!label) {
            showToast('عنوان فارسی الزامی است', 'error');
            return;
        }
        var dup = fields.some(function (f, i) {
            return f.name === name && i !== editIndex;
        });
        if (dup) {
            showToast('این نام قبلاً استفاده شده', 'error');
            return;
        }

        var f = {
            name: name,
            label: label,
            type: currentType,
            required: req,
            in_table: tbl,
            placeholder: ph,
            default: def
        };

        if (currentType === 'select') {
            var source = document.querySelector('input[name="sel_source"]:checked').value;
            f.select_source = source;
            if (source === 'manual') {
                var opts = [];
                document.querySelectorAll('.manual-opt').forEach(function (o) {
                    var v = o.querySelector('.opt-val').value.trim();
                    var l = o.querySelector('.opt-lbl').value.trim();
                    if (l === '' && v !== '') l = v;
                    if (v === '' && l !== '') v = l;
                    if (l !== '') opts.push({value: v, label: l});
                });
                if (opts.length === 0) {
                    showToast('حداقل یک گزینه اضافه کن', 'error');
                    return;
                }
                f.manual_options = opts;
            } else {
                var t = document.getElementById('f_select_table').value.trim();
                var v = document.getElementById('f_select_value').value.trim();
                var d = document.getElementById('f_select_display').value.trim();
                if (!t) {
                    showToast('نام جدول منبع الزامی است', 'error');
                    return;
                }
                if (!v) {
                    showToast('فیلد value الزامی است', 'error');
                    return;
                }
                if (!d) {
                    showToast('حداقل یک فیلد نمایشی بده', 'error');
                    return;
                }
                f.select_table = t;
                f.select_value = v;
                f.select_display = d.split(',').map(function (s) {
                    return s.trim();
                }).filter(Boolean);
                f.select_order = document.getElementById('f_select_order').value.trim();
                f.select_where = document.getElementById('f_select_where').value.trim();
            }
        }

        if (editIndex >= 0) {
            fields[editIndex] = f;
            showToast('فیلد «' + label + '» بروزرسانی شد');
            editIndex = -1;
            cancelEdit();
        } else {
            fields.push(f);
            showToast('فیلد «' + label + '» اضافه شد');
        }
        renderFields();
        resetFieldForm();
    }

    function editField(i) {
        var f = fields[i];
        if (!f) return;
        editIndex = i;
        currentType = f.type;
        document.querySelectorAll('.type-chip').forEach(function (c) {
            c.classList.toggle('active', c.getAttribute('data-type') === f.type);
        });
        document.getElementById('f_name').value = f.name || '';
        document.getElementById('f_label').value = f.label || '';
        document.getElementById('f_placeholder').value = f.placeholder || '';
        document.getElementById('f_default').value = f.default || '';
        document.getElementById('f_required').checked = !!f.required;
        document.getElementById('f_in_table').checked = !!f.in_table;
        document.getElementById('selectExtras').style.display = (f.type === 'select') ? 'block' : 'none';
        document.getElementById('fileExtras').style.display = (f.type === 'file') ? 'block' : 'none';
        if (f.type === 'select') {
            if (f.select_source === 'manual') {
                document.getElementById('src_manual').checked = true;
                document.getElementById('panelManual').style.display = 'block';
                document.getElementById('panelDB').style.display = 'none';
                document.getElementById('manualOptions').innerHTML = '';
                (f.manual_options || []).forEach(function (o) {
                    addManualOpt(o.value, o.label);
                });
            } else {
                document.getElementById('src_db').checked = true;
                document.getElementById('panelManual').style.display = 'none';
                document.getElementById('panelDB').style.display = 'block';
                document.getElementById('f_select_table').value = f.select_table || '';
                document.getElementById('f_select_value').value = f.select_value || 'id';
                document.getElementById('f_select_display').value = (f.select_display || []).join(',');
                document.getElementById('f_select_order').value = f.select_order || '';
                document.getElementById('f_select_where').value = f.select_where || '';
            }
        }
        document.querySelector('.type-chips').scrollIntoView({behavior: 'smooth', block: 'center'});
        var ib = document.querySelector('button[onclick="insertField()"]');
        if (ib) ib.innerHTML = '<i class="fas fa-check"></i> ذخیره تغییرات';
        if (!document.getElementById('cancelEditBtn')) {
            var cb = document.createElement('button');
            cb.type = 'button';
            cb.id = 'cancelEditBtn';
            cb.className = 'btn-ghost';
            cb.style.marginRight = '8px';
            cb.innerHTML = '<i class="fas fa-times"></i> انصراف';
            cb.onclick = function () {
                cancelEdit();
            };
            ib.parentNode.insertBefore(cb, ib);
        }
        showToast('در حال ویرایش فیلد «' + f.label + '»');
    }

    function cancelEdit() {
        editIndex = -1;
        resetFieldForm();
        var ib = document.querySelector('button[onclick="insertField()"]');
        if (ib) ib.innerHTML = '<i class="fas fa-plus"></i> درج فیلد';
        var cb = document.getElementById('cancelEditBtn');
        if (cb) cb.remove();
    }

    /* ═══════ Render ═══════ */
    var typeIcons = {
        text: 'fa-font',
        textarea: 'fa-align-right',
        number: 'fa-hashtag',
        password: 'fa-key',
        select: 'fa-list',
        date: 'fa-calendar',
        file: 'fa-image'
    };
    var typeNames = {
        text: 'متن',
        textarea: 'متن بلند',
        number: 'عدد',
        password: 'رمز عبور',
        select: 'سلکت',
        date: 'تاریخ',
        file: 'فایل'
    };

    function renderFields() {
        var list = document.getElementById('fieldsList');
        var empty = document.getElementById('emptyState');
        var badge = document.getElementById('fieldsCountBadge');
        badge.innerText = fields.length + ' فیلد';
        if (fields.length === 0) {
            list.innerHTML = '';
            empty.style.display = 'block';
            return;
        }
        empty.style.display = 'none';
        var html = '';
        fields.forEach(function (f, i) {
            html += '<div class="field-item" draggable="true" data-idx="' + i + '">';
            html += '<div class="fi-handle" title="برای جابجایی بکش"><i class="fas fa-grip-vertical"></i></div>';
            html += '<div class="fi-icon"><i class="fas ' + typeIcons[f.type] + '"></i></div>';
            html += '<div class="fi-info">';
            html += '<div class="fi-name">' + esc(f.label) + ' <span style="color:#95a5a6;font-weight:normal;">(' + esc(f.name) + ')</span></div>';
            html += '<div class="fi-meta">';
            html += '<span><i class="fas fa-tag"></i> ' + typeNames[f.type] + '</span>';
            if (f.type === 'select') {
                if (f.select_source === 'manual') html += '<span style="color:#2980b9;"><i class="fas fa-keyboard"></i> دستی (' + (f.manual_options || []).length + ' گزینه)</span>';
                else html += '<span style="color:#8e44ad;"><i class="fas fa-database"></i> از ' + esc(f.select_table) + '</span>';
            }
            if (f.type === 'file' && f.in_table) html += '<span style="color:#d68910;"><i class="fas fa-image"></i> نمایش تصویر</span>';
            if (f.required) html += '<span style="color:#e74c3c;"><i class="fas fa-asterisk"></i> اجباری</span>';
            if (f.in_table) html += '<span style="color:#16a085;"><i class="fas fa-table"></i> در جدول</span>';
            html += '</div></div>';
            html += '<div class="fi-actions">';
            html += '<button type="button" class="fi-edit" onclick="editField(' + i + ')" title="ویرایش"><i class="fas fa-pen"></i></button>';
            html += '<button type="button" class="fi-remove" onclick="removeField(' + i + ')" title="حذف"><i class="fas fa-trash"></i></button>';
            html += '</div></div>';
        });
        list.innerHTML = html;
        attachDragDrop();
    }

    function removeField(i) {
        fields.splice(i, 1);
        renderFields();
        showToast('فیلد حذف شد');
    }

    function resetFieldForm() {
        document.getElementById('f_name').value = '';
        document.getElementById('f_label').value = '';
        document.getElementById('f_placeholder').value = '';
        document.getElementById('f_default').value = '';
        document.getElementById('f_required').checked = false;
        document.getElementById('f_in_table').checked = true;
        document.getElementById('src_manual').checked = true;
        document.getElementById('panelManual').style.display = 'block';
        document.getElementById('panelDB').style.display = 'none';
        document.getElementById('manualOptions').innerHTML = '';
        document.getElementById('f_select_table').value = '';
        document.getElementById('f_select_value').value = 'id';
        document.getElementById('f_select_display').value = '';
        document.getElementById('f_select_order').value = '';
        document.getElementById('f_select_where').value = '';
        document.getElementById('f_name').focus();
    }

    /* ═══════ Drag & Drop ═══════ */
    var dragSrcEl = null;

    function attachDragDrop() {
        document.querySelectorAll('.field-item').forEach(function (item) {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragover', handleDragOver);
            item.addEventListener('dragleave', handleDragLeave);
            item.addEventListener('drop', handleDrop);
            item.addEventListener('dragend', handleDragEnd);
        });
    }

    function handleDragStart(e) {
        dragSrcEl = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        try {
            e.dataTransfer.setData('text/plain', this.dataset.idx);
        } catch (ex) {
        }
    }

    function handleDragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        if (this !== dragSrcEl) this.classList.add('drag-over');
        return false;
    }

    function handleDragLeave(e) {
        this.classList.remove('drag-over');
    }

    function handleDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
        if (dragSrcEl && dragSrcEl !== this) {
            var fromIdx = parseInt(dragSrcEl.dataset.idx);
            var toIdx = parseInt(this.dataset.idx);
            var moved = fields.splice(fromIdx, 1)[0];
            fields.splice(toIdx, 0, moved);
            renderFields();
            showToast('ترتیب بروزرسانی شد');
        }
        return false;
    }

    function handleDragEnd(e) {
        this.classList.remove('dragging');
        document.querySelectorAll('.field-item').forEach(function (i) {
            i.classList.remove('drag-over');
        });
    }

    /* ═══════ Final ═══════ */
    function finalCheck() {
        if (fields.length === 0) {
            showToast('حداقل یک فیلد اضافه کن', 'error');
            return false;
        }
        document.getElementById('fieldsJson').value = JSON.stringify(fields);
        document.getElementById('autoValuesJson').value = JSON.stringify(collectAutoVals());
        return true;
    }

    function esc(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function escAttr(s) {
        return String(s).replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function showToast(msg, type) {
        var t = document.getElementById('toast');
        document.getElementById('toastText').innerText = msg;
        t.style.background = (type === 'error') ? '#e74c3c' : '#10b981';
        t.style.boxShadow = (type === 'error') ? '0 10px 30px rgba(231,76,60,0.4)' : '0 10px 30px rgba(16,185,129,0.4)';
        t.classList.add('show');
        setTimeout(function () {
            t.classList.remove('show');
        }, 2200);
    }

    function copyCode(id, btn) {
        var el = document.getElementById(id);
        if (!el) return;
        var text = el.innerText;
        var done = function () {
            btn.classList.add('copied');
            var orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> کپی شد';
            setTimeout(function () {
                btn.classList.remove('copied');
                btn.innerHTML = orig;
            }, 1800);
            showToast('کد کپی شد');
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(function () {
                fallbackCopy(text, done);
            });
        } else {
            fallbackCopy(text, done);
        }
    }

    function fallbackCopy(text, cb) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            cb();
        } catch (e) {
            showToast('کپی ناموفق', 'error');
        }
        document.body.removeChild(ta);
    }

    /* ═══════ Init ═══════ */
    document.addEventListener('DOMContentLoaded', function () {
        renderFields();
        autoVals.forEach(function (av) {
            addAutoVal(av.name, av.type, av.value);
        });
        refreshVarChips();
        syncAutoFilters();

        document.getElementById('id_func_variable_name').addEventListener('input', function () {
            refreshVarChips();
            syncAutoFilters();
        });
        document.getElementById('id_func_name').addEventListener('input', refreshVarChips);
    });

    var panelData = {table_name: '<?= h($saved['table_name']) ?>'};
</script>

</body>
</html>