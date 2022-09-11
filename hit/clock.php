<?php
require_once("constants.php");
require_once("result.php");
require_once("request.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("account_util.php");
require_once("jb_util.php");
require_once("clock_util.php");

try {

    $content = file_get_contents('php://input');
    $request = new Request($content);

    hit_ptl_check_version($request->version);

    $conn = hit_sql_connect();
    $login_res = login($conn, $request->token);
    $user_info = $login_res->res;

    switch($request->action) {
        case HIT_ACT_CLOCK:
            switch($request->attr) {
                case HIT_ACT_ATTR_CLOCK_FETCH_CLOCK:
                    if($user_info->priority <= 0) {
                        throw new PriorityException();
                    }
                    $avail = hit_clock_fetch($conn, $user_info->uid);
                    echo json_encode(new Result(0, "", array("avail" => $avail)));
                    break;
                case HIT_ACT_ATTR_CLOCK_CLOCK:
                    if($user_info->priority <= 0) {
                        throw new PriorityException();
                    }
                    $avail = hit_clock_fetch($conn, $user_info->uid);
                    if(!$avail) {
                        throw new AlreadyClockedException();
                    }
                    $count = hit_clock_action($conn, $user_info->uid);
                    echo json_encode(new Result(0, "", array(
                        "count" => $count,
                        "jb" => hit_jb_fetch($conn, $user_info->uid)
                    )));
                    break;
                default:
                    throw new InvalidRequestException();
            }
            break;
        default:
            throw new InvalidRequestException();
    }
}
catch(HitRuntimeException $exception) {
    echo json_encode($exception->toResult());
}
catch(Exception | Error $exception) {
    $result = new Result(HIT_ERR_SCRIPT_ERROR, sprintf(HIT_ERS_SCRIPT_ERROR, $exception->getMessage()));
    echo json_encode($result);
}
finally {
    if(isset($conn))
        hit_sql_close($conn);
}
