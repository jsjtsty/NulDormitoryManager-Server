<?php

class Request {
    public int $version;
    public string $token;
    public int $action;
    public int $attr;
    public $request;

    public function __construct(string $content) {
        $json = json_decode($content);
        $this->version = $json->version;
        $this->token = $json->token;
        $this->action = $json->action;
        $this->attr = $json->attr;
        $this->request = $json->request;
    }
}

class RegisterRequest {
    public string $invite_code;

    public function __construct($request) {
        $this->invite_code = $request->invite_code;
    }
}

class AddInviteCodeRequest {
    public int $priority;
    public int $remaining;
    public bool $blocked;

    public function __construct($request) {
        $this->priority = $request->code;
        $this->blocked = $request->blocked;
        $this->remaining = $request->remaining;
    }
}

class UpdateRequest {
    public int $channel;

    public function __construct($request) {
        $this->channel = $request->channel;
    }
}

class BlockInviteCodeRequest {
    public string $code;
    public bool $block;

    public function __construct($request) {
        $this->code = $request->code;
        $this->block = $request->block;
    }
}

class FetchInviteCodeRequest {
    public string $code;

    public function __construct($request) {
        $this->code = $request->code;
    }
}

class AddJBRequest {
    public int $uid;
    public int $count;
    public string $comment;

    public function __construct($request) {
        $this->count = $request->count;
        $this->uid = $request->uid;
        $this->comment = $request->comment;
    }
}

class FetchJBRequest {
    public int $uid;

    public function __construct($request) {
        $this->uid = $request->uid;
    }
}

class UseJBRequest {
    public int $uid;
    public int $count;
    public string $comment;

    public function __construct($request) {
        $this->count = $request->count;
        $this->uid = $request->uid;
        $this->comment = $request->comment;
    }
}

class UpdateJBRequest {
    public int $uid;
    public int $count;
    public string $comment;

    public function __construct($request) {
        $this->count = $request->count;
        $this->uid = $request->uid;
        $this->comment = $request->comment;
    }
}

class TransferJBRequest {
    public int $from;
    public int $to;
    public int $count;
    public string $comment;

    public function __construct($request) {
        $this->count = $request->count;
        $this->from = $request->from;
        $this->to = $request->to;
        $this->comment = $request->comment;
    }
}
