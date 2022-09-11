<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("sql_util.php");
require_once("account_util.php");
require_once("protocol.php");
require_once("request.php");
require_once("jb_util.php");

try {

    $content = file_get_contents('php://input');
    $request = new Request($content);

    hit_ptl_check_version($request->version);

    $conn = hit_sql_connect();
    $login_res = login($conn, $request->token);
    $user_info = $login_res->res;

    switch($request->action) {
        case HIT_ACT_JB:
            switch($request->attr) {
                case HIT_ACT_ATTR_JB_FETCH:
                    $req_fetch = new FetchJBRequest($request->request);
                    $jb = hit_jb_fetch($conn, $req_fetch->uid);
                    if($req_fetch->uid != $user_info->uid && $user_info->priority <= 0) {
                        throw new PriorityException();
                    }
                    echo json_encode(new Result(0, "", array('jb' => $jb)));
                    break;
                case HIT_ACT_ATTR_JB_ADD:
                    if($user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }
                    $req_add = new AddJBRequest($request->request);
                    $jb = hit_jb_add($conn, $user_info->uid, $req_add->count, "admin_add", $req_add->comment);
                    echo json_encode(new Result(0, "", array('jb' => $jb)));
                    break;
                case HIT_ACT_ATTR_JB_USE:
                    if($user_info->priority <= HIT_USP_BANNED) {
                        throw new PriorityException();
                    }
                    $req_add = new UseJBRequest($request->request);
                    $jb = hit_jb_add($conn, $user_info->uid, -$req_add->count,"use", $req_add->comment);
                    echo json_encode(new Result(0, "", array('jb' => $jb)));
                    break;
                case HIT_ACT_ATTR_JB_UPDATE:
                    if($user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }
                    $req_update = new UpdateJBRequest($request->request);
                    $origin = hit_jb_fetch($conn, $req_update->uid);
                    $jb = hit_jb_add($conn, $user_info->uid, $req_update->count - $origin,
                        "update", $req_update->comment);
                    break;
                case HIT_ACT_ATTR_JB_TRANSFER:
                    if($user_info->priority <= HIT_USP_BANNED) {
                        throw new PriorityException();
                    }
                    $req_trans = new TransferJBRequest($request->request);
                    if($user_info->uid != $req_trans->from && $user_info->priority < HIT_USP_ADMINISTRATOR) {
                        throw new PriorityException();
                    }

                    $jb = hit_jb_add($conn, $req_trans->from, -$req_trans->count, "transfer", $req_trans->comment);
                    $to_jb = hit_jb_add($conn, $req_trans->to, (int)($req_trans->count * (1 - HIT_JB_TAX_RATE))
                        ,"transfer", $req_trans->comment);

                    echo json_encode(new Result(0, "", array(
                        'jb' => $jb,
                        'to_jb' => $to_jb,
                        'transfer' => (int)($req_trans->count * (1 - HIT_JB_TAX_RATE))
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
