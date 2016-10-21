<?php

namespace ThisData\Api\Endpoint;

class EndpointConstants
{
    // Some pre-defiend verbs
    // @see http://help.thisdata.com/docs/verbs

    const VERB_LOG_IN                = 'log-in';
    const VERB_LOG_OUT               = 'log-out';
    const VERB_LOG_IN_DENIED         = 'log-in-denied';
    const VERB_LOG_IN_CHALLENGE      = 'log-in-challenge';

    const VERB_ACCESS                = 'access';

    const VERB_EMAIL_UPDATE          = 'email-update';
    const VERB_PASSWORD_UPDATE       = 'password-update';

    const VERB_PASSWORD_RESET_REQUEST  = 'password-reset-request';
    const VERB_PASSWORD_RESET          = 'password-reset';
    const VERB_PASSWORD_RESET_FAIL     = 'password-reset-fail';

    const VERB_AUTHENTICATION_CHALLENGE      = 'authentication-challenge';
    const VERB_AUTHENTICATION_CHALLENGE_PASS = 'authentication-challenge-pass';
    const VERB_AUTHENTICATION_CHALLENGE_FAIL = 'authentication-challenge-fail';

    const VERB_TWO_FACTOR_DISABLE = 'two-factor-disable';

    // Constants for the parameters the API supports

    const PARAM_VERB = 'verb';
    const PARAM_IP   = 'ip';

    const PARAM_USER                = 'user';
    const PARAM_USER__ID            = 'id';
    const PARAM_USER__NAME          = 'name';
    const PARAM_USER__EMAIL         = 'email';
    const PARAM_USER__MOBILE        = 'mobile';
    const PARAM_USER__AUTHENTICATED = 'authenticated';
    const PARAM_USER_AGENT          = 'user_agent';

    const PARAM_SOURCE           = 'source';
    const PARAM_SOURCE__NAME     = 'name';
    const PARAM_SOURCE__LOGO_URL = 'logo_url';

    const PARAM_SESSION                     = 'session';
    const PARAM_SESSION__ID                 = 'id';
    const PARAM_SESSION__TD_COOKIE_ID       = 'td_cookie_id';
    const PARAM_SESSION__TD_COOKIE_EXPECTED = 'td_cookie_expected';

    const PARAM_DEVICE     = 'device';
    const PARAM_DEVICE__ID = 'id';
}