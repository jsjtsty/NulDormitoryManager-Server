<?php

// 1. Error code.

const HIT_ERR_OK = 0;

const HIT_ERR_SCRIPT_ERROR = 1;
const HIT_ERR_INVALID_REQUEST = 2;
const HIT_ERR_PRIORITY = 3;

const HIT_ERR_LOW_PROTOCOL_VERSION = 5;
const HIT_ERR_HIGH_PROTOCOL_VERSION = 6;
const HIT_ERR_UNSUPPORTED_VERSION = 7;

const HIT_ERR_INVALID_SQL_CONNECTION = 10;
const HIT_ERR_SQL_EXCEPTION = 11;

const HIT_ERR_INVALID_USERNAME = 20;
const HIT_ERR_USER_NOT_EXIST = 21;
const HIT_ERR_WRONG_PASSWORD = 22;
const HIT_ERR_INVALID_TOKEN = 23;
const HIT_ERR_USER_ALREADY_EXIST = 24;
const HIT_ERR_INVALID_INVITE_CODE = 30;
const HIT_ERR_INVITE_CODE_USED_UP = 31;
const HIT_ERR_INVITE_CODE_BLOCKED = 32;

const HIT_ERR_ALREADY_CLOCKED = 100;

// 2. Protocol Version.

const HIT_PTL_VERSION_IN = 2;
const HIT_PTL_VERSION_OUT = 2;

// 3. Action code.

const HIT_ACT_LOGIN = 1;
const HIT_ACT_ATTR_LOGIN_LOGIN = 100;
const HIT_ACT_ATTR_LOGIN_REGISTER = 101;

const HIT_ACT_JB = 2;
const HIT_ACT_ATTR_JB_FETCH = 200;
const HIT_ACT_ATTR_JB_ADD = 201;
const HIT_ACT_ATTR_JB_USE = 202;
const HIT_ACT_ATTR_JB_UPDATE = 203;
const HIT_ACT_ATTR_JB_TRANSFER = 204;

const HIT_ACT_ACCOUNT = 3;
const HIT_ACT_ATTR_ACCOUNT_FETCH_INVITE_CODE = 300;
const HIT_ACT_ATTR_ACCOUNT_UPDATE_INVITE_CODE = 301;
const HIT_ACT_ATTR_ACCOUNT_ADD_INVITE_CODE = 302;
const HIT_ACT_ATTR_ACCOUNT_BLOCK_INVITE_CODE = 303;
const HIT_ACT_ATTR_ACCOUNT_FETCH_USER = 304;
const HIT_ACT_ATTR_ACCOUNT_FETCH_ALL_INVITE_CODE = 305;

const HIT_ACT_CLOCK = 4;
const HIT_ACT_ATTR_CLOCK_CLOCK = 400;
const HIT_ACT_ATTR_CLOCK_FETCH_CLOCK = 401;

const HIT_ACT_UPDATE = 5;
const HIT_ACT_ATTR_UPDATE_FETCH_CUR_CHANNEL = 500;
const HIT_ACT_ATTR_UPDATE_FETCH_CUR_UPDATE = 501;
const HIT_ACT_ATTR_UPDATE_FETCH_UPDATE_WITHOUT_TOKEN = 502;
const HIT_ACT_ATTR_UPDATE_FETCH_UPDATE = 503;
const HIT_ACT_ATTR_UPDATE_CHANGE_CUR_CHANNEL = 504;
const HIT_ACT_ATTR_UPDATE_CHANGE_CHANNEL = 505;

// 4. JB Details.
const HIT_JB_TAX_RATE = 0.2;

// 5. User Priority.
const HIT_USP_BANNED = 0;
const HIT_USP_ORDINARY_USER = 1;
const HIT_USP_HIGH_PRIORITY_USER = 2;
const HIT_USP_INSIDER = 3;
const HIT_USP_ADMINISTRATOR = 4;
const HIT_USP_DEVELOPER = 5;

// 6. Application Update Util.
const HIT_UPD_RELEASE_RELEASE = 0;
const HIT_UPD_RELEASE_RC = 1;
const HIT_UPD_RELEASE_BETA = 2;
const HIT_UPD_RELEASE_ALPHA = 3;
const HIT_UPD_RELEASE_INSIDER_PREVIEW = 4;
const HIT_UPD_RELEASE_TECHNICAL_PREVIEW = 5;
const HIT_UPD_RELEASE_DEVELOPMENT = 6;

const HIT_UPD_CHANNEL_RELEASE = 0;
const HIT_UPD_CHANNEL_RC = 1;
const HIT_UPD_CHANNEL_BETA = 2;
const HIT_UPD_CHANNEL_ALPHA = 3;
const HIT_UPD_CHANNEL_INSIDER_PREVIEW = 4;
const HIT_UPD_CHANNEL_TECHNICAL_PREVIEW = 5;
const HIT_UPD_CHANNEL_DEVELOPMENT = 6;

const HIT_UPD_PRIORITY_MAX_CHANNEL = array(
    HIT_USP_BANNED => HIT_UPD_CHANNEL_RELEASE,
    HIT_USP_ORDINARY_USER => HIT_UPD_CHANNEL_RC,
    HIT_USP_HIGH_PRIORITY_USER => HIT_UPD_CHANNEL_ALPHA,
    HIT_USP_INSIDER => HIT_UPD_CHANNEL_TECHNICAL_PREVIEW,
    HIT_USP_ADMINISTRATOR => HIT_UPD_CHANNEL_DEVELOPMENT,
    HIT_USP_DEVELOPER => HIT_UPD_CHANNEL_DEVELOPMENT
);

const HIT_UPD_PRIORITY_DEFAULT_CHANNEL = array(
    HIT_USP_BANNED => HIT_UPD_CHANNEL_RELEASE,
    HIT_USP_ORDINARY_USER => HIT_UPD_CHANNEL_RELEASE,
    HIT_USP_HIGH_PRIORITY_USER => HIT_UPD_CHANNEL_RELEASE,
    HIT_USP_INSIDER => HIT_UPD_CHANNEL_TECHNICAL_PREVIEW,
    HIT_USP_ADMINISTRATOR => HIT_UPD_CHANNEL_TECHNICAL_PREVIEW,
    HIT_USP_DEVELOPER => HIT_UPD_CHANNEL_DEVELOPMENT
);
