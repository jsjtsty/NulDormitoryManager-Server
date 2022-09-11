<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");
require_once("account_util.php");
require_once("protocol.php");
require_once("request.php");

try {

    $content = file_get_contents('php://input');
    $request = new Request($content);

    hit_ptl_check_version($request->version);

    $conn = hit_sql_connect();

    switch($request->action) {
        case HIT_ACT_LOGIN:
            switch($request->attr) {
                case HIT_ACT_ATTR_LOGIN_LOGIN:
                    echo json_encode(login($conn, $request->token));
                    break;
                case HIT_ACT_ATTR_LOGIN_REGISTER:
                    $reg_req = new RegisterRequest($request->request);
                    echo json_encode(register($conn, $request->token, $reg_req->invite_code));
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
