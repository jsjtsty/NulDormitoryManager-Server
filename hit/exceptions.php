<?php

use JetBrains\PhpStorm\Pure;

require_once("constants.php");
require_once("err_strings.php");
require_once("result.php");

class HitRuntimeException extends Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getProtocolVersion(): int {
        return HIT_PTL_VERSION_OUT;
    }

    public function toResult(): Result {
        $result = new Result();
        $result->version = $this->getProtocolVersion();
        $result->status = $this->getCode();
        $result->errmsg = $this->getMessage();
        return $result;
    }
}

class InvalidRequestException extends HitRuntimeException {
    public function __construct($message = HIT_ERS_INVALID_REQUEST
        , $code = HIT_ERR_INVALID_REQUEST, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class SQLException extends HitRuntimeException {
    public function __construct($message = "", $code = HIT_ERR_SQL_EXCEPTION, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class InvalidInviteCodeException extends HitRuntimeException {
    public function __construct($message = "", $code = HIT_ERR_INVALID_INVITE_CODE, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class InvalidTokenException extends HitRuntimeException {
    public function __construct($message = "", $code = HIT_ERR_INVALID_TOKEN, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class InvalidLoginException extends HitRuntimeException {
    public function __construct($message = "", $code = HIT_ERR_INVALID_USERNAME, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class ProtocolException extends HitRuntimeException {
    public function __construct($message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class LowProtocolException extends ProtocolException {
    public function __construct($message = "", $code = HIT_ERR_LOW_PROTOCOL_VERSION, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class HighProtocolException extends ProtocolException {
    public function __construct($message = "", $code = HIT_ERR_HIGH_PROTOCOL_VERSION, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class PriorityException extends ProtocolException {
    public function __construct($message = HIT_ERS_PRIORITY, $code = HIT_ERR_PRIORITY, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

class AlreadyClockedException extends HitRuntimeException {
    public function __construct($message = HIT_ERS_ALREADY_CLOCKED, $code = HIT_ERR_ALREADY_CLOCKED,
                                Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
