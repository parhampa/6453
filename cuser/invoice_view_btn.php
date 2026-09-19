<?php
// فایل: client/invoice_view_btn.php
// این فایل داخل حلقه نمایش جدول makeform include می‌شود
// متغیرهای $tmpkey و $restbl در دسترس هستند
$restbl .= "<a href='invoice_print.php?id=" . $tmpkey . "' target='_blank'>";
$restbl .= "<input type='button' class='btn btn-sm btn-success m-1' value='مشاهده فاکتور'>";
$restbl .= "</a>";
?>