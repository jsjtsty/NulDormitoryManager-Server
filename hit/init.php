<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");

/**
 * @throws SQLException
 */
function hit_add_user(mysqli $conn, string $user_name, string $pass_hash, int $priority): int {
    $query_str = "insert into nul_users (UserName, PassHash, Priority) values('$user_name', '$pass_hash', $priority);";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));

    return hit_sql_fetch_value($conn, 'nul_users', 'UID', "UserName='$user_name'");
}

/**
 * @throws SQLException
 */
function hit_add_jb(mysqli $conn, int $uid): void {
    $query_str = "insert into nul_jb (UID, JB) values($uid, 0);";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));
}


/**
 * @throws SQLException
 */
function hit_add_clock_info(mysqli $conn, int $uid): void {
    $query_str = "insert into nul_clock (UID, Time) values($uid, 0);";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));
}

/**
 * @throws SQLException
 */
function hit_add_update_channel(mysqli $conn, int $uid, int $priority): void {
    $query_str = "insert into nul_update_channels (UID, UpdateLevel) values($uid, "
        .HIT_UPD_PRIORITY_DEFAULT_CHANNEL[$priority].");";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));
}
