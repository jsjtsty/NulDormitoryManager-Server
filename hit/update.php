<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");
require_once("protocol.php");
require_once("account_util.php");
require_once("result.php");
require_once("request.php");
require_once("update_util.php");

try {
    $content = file_get_contents('php://input');
    $request = new Request($content);

    hit_ptl_check_version($request->version);

    $conn = hit_sql_connect();

    switch($request->action) {
        case HIT_ACT_UPDATE:
            switch($request->attr) {
                case HIT_ACT_ATTR_UPDATE_FETCH_UPDATE_WITHOUT_TOKEN:
                    $release = hit_upd_fetch_latest_release($conn, HIT_UPD_CHANNEL_RELEASE);
                    echo json_encode(new Result(0, "", $release));
                    break;
                default:
                    $user_info = login($conn, $request->token)->res;
                    switch($request->attr) {
                        case HIT_ACT_ATTR_UPDATE_FETCH_CUR_CHANNEL:
                            echo json_encode(new Result(0, "", array(
                                'channel' => hit_upd_fetch_user_update_level($conn, $user_info->uid)
                            )));
                            break;
                        case HIT_ACT_ATTR_UPDATE_FETCH_CUR_UPDATE:
                            $channel = hit_upd_fetch_user_update_level($conn, $user_info->uid);
                            echo json_encode(new Result(0, "", hit_upd_fetch_latest_release($conn, $channel)));
                            break;
                        case HIT_ACT_ATTR_UPDATE_FETCH_UPDATE:
                            $req_upd = new UpdateRequest($request->request);
                            $max_request = HIT_UPD_PRIORITY_MAX_CHANNEL[$user_info->priority];
                            if($req_upd->channel > $max_request) {
                                throw new PriorityException();
                            }
                            $release = hit_upd_fetch_latest_release($conn, $req_upd->channel);
                            echo json_encode(new Result(0, "", $release));
                            break;
                        case HIT_ACT_ATTR_UPDATE_CHANGE_CHANNEL:
                            $req_upd = new UpdateRequest($request->request);
                            $max_request = HIT_UPD_PRIORITY_MAX_CHANNEL[$user_info->priority];
                            if($req_upd->channel > $max_request) {
                                throw new PriorityException();
                            }
                            hit_upd_set_user_update_level($conn, $user_info->uid, $req_upd->channel);
                            echo json_encode(new Result(0, ""));
                            break;
                        default:
                            throw new InvalidRequestException();
                    }
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
    echo json_encode(new Result(HIT_ERR_SCRIPT_ERROR, $exception->getMessage()));
}
finally {
    if(isset($conn)) {
        hit_sql_close($conn);
    }
}
