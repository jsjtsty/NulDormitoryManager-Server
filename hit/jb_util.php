<?php
require_once("constants.php");
require_once("result.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");
require_once("account_util.php");
require_once("protocol.php");

/**
 * @throws SQLException
 */
function hit_jb_log_add(mysqli $conn, int $uid, int $origin, int $end, string $action, string $comment = ""): void {
    $query_str =
        "insert into nul_jb_log(UID, Time, Origin, End, Modification, Action, Comment) values($uid, now(), $origin, $end, "
        .($end - $origin).", '$action', '$comment');";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));
}

/**
 * @throws SQLException
 */
function hit_jb_fetch(mysqli $conn, int $uid): int {
    return hit_sql_fetch_value($conn, 'nul_jb', 'JB', "UID=$uid");
}

/**
 * @throws SQLException
 */
function hit_jb_add(mysqli $conn, int $uid, int $count, string $action = "add", string $comment = ""): int {
    $origin = hit_jb_fetch($conn, $uid);
    hit_sql_assert_query($conn, hit_sql_update_value($conn, 'nul_jb', 'JB', $origin + $count
        , "UID=$uid"));
    hit_jb_log_add($conn, $uid, $origin, $origin + $count, $action, $comment);
    return $origin + $count;
}
