<?php
require_once("constants.php");
require_once("err_strings.php");
require_once("exceptions.php");

/**
 * @throws LowProtocolException
 * @throws HighProtocolException
 */
function hit_ptl_check_version(int $version): void {
    if($version < HIT_PTL_VERSION_IN) {
        throw new LowProtocolException(HIT_ERS_LOW_PROTOCOL_VERSION);
    }
    else if($version > HIT_PTL_VERSION_IN) {
        throw new HighProtocolException(HIT_ERS_HIGH_PROTOCOL_VERSION);
    }
}
