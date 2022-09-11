<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");
require_once("account_util.php");
require_once("protocol.php");
require_once("request.php");
require_once("account_util.php");

try {

    $content = file_get_contents('php://input');
    $request = new Request($content);

    hit_ptl_check_version($request->version);

    $conn = hit_sql_connect();
    $login_res = login($conn, $request->token);
    $user_info = $login_res->res;

    switch($request->action) {
        case HIT_ACT_ACCOUNT:
            switch($request->attr) {
                case HIT_ACT_ATTR_ACCOUNT_ADD_INVITE_CODE:
                    if($user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }
                    $inv_req = new AddInviteCodeRequest($request->request);
                    $code = hit_generate_invite_code($conn, $inv_req->priority, $inv_req->remaining);
                    echo json_encode(new Result(0, "", array("code" => $code)));
                    break;
                case HIT_ACT_ATTR_ACCOUNT_BLOCK_INVITE_CODE:
                    if($user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }
                    $blk_req = new BlockInviteCodeRequest($request->request);
                    hit_block_invite_code($conn, $blk_req->code, $blk_req->block);
                    echo json_encode(new Result());
                    break;
                case HIT_ACT_ATTR_ACCOUNT_FETCH_INVITE_CODE:
                    if($user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }
                    $req_fetch = new FetchInviteCodeRequest($request->request);
                    $code = hit_fetch_invite_code($conn, $req_fetch->code);
                    if($code == null) {
                        throw new InvalidInviteCodeException(sprintf(HIT_ERS_INVALID_INVITE_CODE,
                            $code->getCode()));
                    }
                    echo json_encode(new Result(0, "", $code));
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
