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
if (!function_exists('get_marketers_id')) {
    function get_marketers_id()
    {
        if (!isset($_SESSION['tel'])) return 0;

        $key = $_SESSION['tel'];

        $db = new database();
        $db->connect();

        $fm_tmp = new makeform();
        $key_safe = $fm_tmp->sqlstr($key);

        $db->query("SELECT `id` FROM `marketers` WHERE `tel`='$key_safe' LIMIT 1");
        if (mysqli_num_rows($db->res) == 0) return 0;

        $row = mysqli_fetch_assoc($db->res);
        return (int)$row['id'];
    }
}
?>