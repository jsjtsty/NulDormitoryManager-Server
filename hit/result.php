<?php
require_once("constants.php");

class Result {
    public function __construct(int $status = 0, string $errmsg = "", $res = null) {
        $this->status = $status;
        $this->errmsg = $errmsg;
        $this->res = $res;
    }

    public int $version = HIT_PTL_VERSION_OUT;
    public int $status = 0;
    public string $errmsg = "";
    public mixed $res;
}
