<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");

/**
 * @throws SQLException
 */
function hit_sql_connect(): mysqli {
    $conn = mysqli_connect("127.0.0.1", "root", "Sty@20030209", "hit_340");
    if (!$conn)
        throw new SQLException(HIT_ERS_SQL_CONNECTION_FAILURE, HIT_ERR_INVALID_SQL_CONNECTION);
    return $conn;
}

function hit_sql_close(mysqli $conn): bool {
    return mysqli_close($conn);
}

function hit_sql_count(mysqli $conn, string $table, string $condition): int {
    $query_str = "select count(*) from $table where $condition;";
    $result = mysqli_query($conn, $query_str);
    return $result->num_rows;
}

/**
 * @throws SQLException
 */
function hit_sql_assert_query(mysqli $conn, bool|mysqli_result $res): bool|mysqli_result {
    if($res == false) {
        throw new SQLException(sprintf(HIT_ERS_SQL_EXCEPTION, mysqli_error($conn)));
    }
    return $res;
}

/**
 * @throws SQLException
 */
function hit_sql_assert_select(mysqli_result $res): mysqli_result {
    if(mysqli_num_rows($res) == 0) {
        throw new SQLException(HIT_ERS_SQL_SELECT_ASSERT_EXCEPTION, HIT_ERR_SQL_EXCEPTION);
    }
    return $res;
}

/**
 * @throws SQLException
 */
function hit_sql_fetch_value(mysqli $conn, string $table, string $column, string $condition) {
    $query_str = "select $column from $table where $condition;";
    hit_sql_assert_select($res = mysqli_query($conn, $query_str));
    return mysqli_fetch_assoc($res)[$column];
}

function hit_sql_update_value(mysqli $conn, string $table, string $column, $val, string $condition): bool {
    $query_str = "update $table set $column=$val where $condition;";
    return mysqli_query($conn, $query_str);
}