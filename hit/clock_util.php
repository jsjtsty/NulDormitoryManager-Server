<?php
require_once("constants.php");
require_once("result.php");
require_once("request.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("account_util.php");
require_once("jb_util.php");

function hit_clock_generate(): int {
    $prev = mt_rand(1, 10000);
    if($prev == 1) {
        return 100000;
    }
    else if($prev <= 100) {
        return 10000;
    }
    else if($prev <= 200) {
        return 1000;
    }
    else if($prev <= 1000) {
        return 500;
    }
    else if($prev <= 2500) {
        return 100;
    }
    else if($prev <= 4500) {
        return 50;
    }
    else if($prev <= 9000) {
        return mt_rand(2, 49);
    }
    else if($prev <= 9500) {
        return 1;
    }
    else {
        return 0;
    }
}

/**
 * @throws SQLException
 */
function hit_clock_fetch(mysqli $conn, int $uid): bool {
    return true;
    /*
    $query_str = "select Time from nul_clock where UID=$uid;";
    $result = hit_sql_assert_select(hit_sql_assert_query($conn, mysqli_query($conn, $query_str)));
    $row = mysqli_fetch_assoc($result);
    $time = $row['Time'];
    $cur = (int)date("Ymd");
    mysqli_free_result($result);
    return $time != $cur;
    */
}

/**
 * @throws SQLException
 */
function hit_clock_action(mysqli $conn, int $uid): int {
    $cur = (int)date("Ymd");
    hit_sql_assert_query($conn,
        hit_sql_update_value($conn, 'nul_clock', 'Time', $cur, "UID=$uid"));
    $gen = hit_clock_generate();

    hit_jb_add($conn, $uid, $gen, "clock");
    return $gen;
}
