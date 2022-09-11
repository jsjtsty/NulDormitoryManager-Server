<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");
require_once("init.php");

class InviteCode {
    protected int $remaining;
    protected int $priority;
    protected string $code;
    protected bool $blocked;

    public function __construct(string $code, int $priority, int $remaining, bool $blocked) {
        $this->code = $code;
        $this->priority = $priority;
        $this->remaining = $remaining;
        $this->blocked = $blocked;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getRemaining(): int {
        return $this->remaining;
    }

    public function isBlocked(): bool {
        return $this->blocked;
    }
}

class TokenAnalyser {
    protected string $user_name;
    protected string $pass_hash;

    /**
     * @throws InvalidTokenException
     */
    public function __construct(string $token) {
        $decode = base64_decode($token);
        $pass_hash = strstr($decode, "|");
        if($pass_hash == false) {
            throw new InvalidTokenException(sprintf(HIT_ERS_INVALID_TOKEN, $token));
        }
        $this->pass_hash = substr($pass_hash, 1, strlen($pass_hash) - 1);
        $this->user_name = substr($decode,0, strlen($decode) - strlen($pass_hash));
    }

    public function getUserName(): string {
        return $this->user_name;
    }

    public function getPassHash(): string {
        return $this->pass_hash;
    }
}

class UserInfo {
    public function __construct(int $uid = 0, string $user_name = "", int $priority = 0) {
        $this->uid = $uid;
        $this->user_name = $user_name;
        $this->priority = $priority;
    }

    public int $priority;
    public int $uid;
    public string $user_name;
}

function hit_acc_user_existence(mysqli $conn, string $user_name): bool {
    return hit_sql_count($conn, "nul_users", "UserName='$user_name'");
}

/**
 * @throws SQLException
 */
function hit_generate_invite_code(mysqli $conn, int $priority, int $avail_times): string {
    function generate_rand_str(int $length): string {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $rand_str = '';
        for ($i = 0; $i < $length; ++$i) {
            $num = mt_rand(0, $len);
            $rand_str .= $str[$num];
        }
        return $rand_str;
    }

    while(true) {
        $code = generate_rand_str(6);
        if(hit_sql_count($conn, "nul_invite", "InviteCode='$code'")) {
            break;
        }
    }

    $query_str = "insert into nul_invite (InviteCode, Remaining, Priority, Blocked) values('$code', $avail_times, $priority, 0);";
    $res = mysqli_query($conn, $query_str);

    if(!$res) {
        throw new SQLException(HIT_ERS_SQL_CONNECTION_FAILURE, HIT_ERR_INVALID_SQL_CONNECTION);
    }

    return $code;
}

function hit_fetch_invite_code(mysqli $conn, string $code): ?InviteCode {
    $query_str = "select Priority, Remaining, Blocked from nul_invite where InviteCode='$code';";
    $res = mysqli_query($conn, $query_str);

    if(mysqli_num_rows($res) == 0) {
        return null;
    }
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return new InviteCode($code, $row['Priority'], $row['Remaining'], $row['Blocked'] != 0);
}

/**
 * @throws SQLException
 */
function hit_update_invite_code(mysqli $conn, string $code, InviteCode $info): void {
    $query_str = "update nul_invite set InviteCode='".$info->getCode()."', Remaining=".$info->getRemaining()
        .", Priority=".$info->getPriority().", Blocked=".($info->isBlocked() ? 1 : 0)." where InviteCode='$code';";
    $res = mysqli_query($conn, $query_str);
    if($res != true) {
        throw new SQLException(mysqli_error($conn), HIT_ERR_SQL_EXCEPTION);
    }
}

/**
 * @throws SQLException
 * @throws InvalidInviteCodeException
 */
function hit_use_invite_code(mysqli $conn, string $code): InviteCode {
    if(($code_info = hit_fetch_invite_code($conn, $code)) == null) {
        throw new InvalidInviteCodeException(sprintf(HIT_ERS_INVALID_INVITE_CODE, $code),
            HIT_ERR_INVALID_INVITE_CODE);
    }

    if($code_info->getRemaining() <= 0) {
        throw new InvalidInviteCodeException(sprintf(HIT_ERS_INVITE_CODE_USED_UP, $code),
            HIT_ERR_INVITE_CODE_USED_UP);
    }

    if($code_info->isBlocked()) {
        throw new InvalidInviteCodeException(sprintf(HIT_ERS_INVITE_CODE_BLOCKED, $code),
            HIT_ERR_INVITE_CODE_BLOCKED);
    }

    $res = new InviteCode($code, $code_info->getPriority(), $code_info->getRemaining() - 1,
        $code_info->isBlocked());
    hit_update_invite_code($conn, $code, $res);

    return $res;
}

/**
 * @throws SQLException
 */
function hit_block_invite_code(mysqli $conn, string $code, bool $blocked): void {
    $query_str = "update nul_invite set Blocked=".($blocked ? 1 : 0)." where InviteCode=$code;";
    $res = mysqli_query($conn, $query_str);
    if($res != true) {
        throw new SQLException(mysqli_error($conn), HIT_ERR_SQL_EXCEPTION);
    }
}

/**
 * @throws InvalidTokenException
 * @throws SQLException
 * @throws InvalidLoginException
 */
function login(mysqli $conn, string $token): Result {
    $anal = new TokenAnalyser($token);

    $query_str = "select * from nul_users where UserName='".$anal->getUserName()."';";
    $info_res = hit_sql_assert_query($conn, mysqli_query($conn, $query_str));

    if(mysqli_num_rows($info_res) == 0) {
        throw new InvalidLoginException(sprintf(HIT_ERS_USER_NOT_EXIST, $anal->getUserName())
            ,HIT_ERR_USER_NOT_EXIST);
    }

    $row = mysqli_fetch_assoc($info_res);
    $info = new UserInfo($row['UID'], $row['UserName'], $row['Priority']);
    $pass_hash = $row['PassHash'];
    mysqli_free_result($info_res);

    if($pass_hash != $anal->getPassHash()) {
        throw new InvalidLoginException(HIT_ERS_WRONG_PASSWORD,HIT_ERR_WRONG_PASSWORD);
    }

    return new Result(0, "", $info);
}

/**
 * @throws InvalidInviteCodeException
 * @throws InvalidTokenException
 * @throws SQLException
 * @throws InvalidLoginException
 */
function register(mysqli $conn, string $token, string $invite_code): Result {
    $anal = new TokenAnalyser($token);
    $query_str = "select * from nul_users where UserName='".$anal->getUserName()."';";
    $info_res = hit_sql_assert_query($conn, mysqli_query($conn, $query_str));

    if(mysqli_num_rows($info_res) > 0) {
        throw new InvalidLoginException(HIT_ERS_USER_ALREADY_EXIST,HIT_ERR_USER_ALREADY_EXIST);
    }

    $code = hit_use_invite_code($conn, $invite_code);

    $uid = hit_add_user($conn, $anal->getUserName(), $anal->getPassHash(), $code->getPriority());

    hit_add_jb($conn, $uid);

    hit_add_clock_info($conn, $uid);

    hit_add_update_channel($conn, $uid, $code->getPriority());

    return new Result(0, "", new UserInfo($uid, $anal->getUserName(), $code->getPriority()));
}
