<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");

class Release {
    public function __construct(string $version_string, int $build, string $release_level, string $update_url, int $size,
                            string $description) {
        $this->version_string = $version_string;
        $this->build = $build;
        $this->release_level = $release_level;
        $this->update_url = $update_url;
        $this->size = $size;
        $this->description = $description;
    }

    public string $version_string;
    public int $build;
    public int $release_level;
    public string $update_url;
    public int $size;
    public string $description;
}

/**
 * @throws SQLException
 */
function hit_upd_fetch_latest_release(mysqli $conn, int $channel): ?Release {
    $query_str = "select * from nul_update where Build=(select max(Build) from nul_update where ReleaseLevel<=$channel);";
    $result = hit_sql_assert_query($conn, mysqli_query($conn, $query_str));

    if(mysqli_num_rows($result) == 0) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return new Release($row['Version'], $row['Build'], $row['ReleaseLevel'], $row['UpdateUrl'], $row['Size'], $row['Description']);
}

/**
 * @throws SQLException
 */
function hit_upd_fetch_user_update_level(mysqli $conn, int $uid): int {
    $query_str = "select * from nul_update_channels where UID=$uid;";
    $result = hit_sql_assert_select(hit_sql_assert_query($conn, mysqli_query($conn, $query_str)));
    $row = mysqli_fetch_assoc($result);
    return $row['UpdateLevel'];
}

/**
 * @throws SQLException
 */
function hit_upd_set_user_update_level(mysqli $conn, int $uid, int $level): void {
    $query_str = "update nul_update_users set UpdateLevel=$level where UID=$uid;";
    hit_sql_assert_query($conn, mysqli_query($conn, $query_str));
}
