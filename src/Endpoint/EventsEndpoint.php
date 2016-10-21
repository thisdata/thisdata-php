<?php

namespace ThisData\Api\Endpoint;

use ThisData\Api\ThisData;
use ThisData\Api\Builder;

/**
 * Events Endpoint
 *
 * Track events related to user security, and send them to ThisData.
 *
 * @see http://help.thisdata.com/docs/apiv1events
 */
class EventsEndpoint extends AbstractEndpoint
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

    /**
     * Track the successful authentication of a client.
     *
     * @param string $ip              The IP address of the client logging in
     * @param array  $user            An array containing id, and optionally name, email, mobile
     * @param string|null  $userAgent The browser user agent of the client logging in
     */
    public function trackLogIn($ip, array $user = null, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $this->trackEvent(self::VERB_LOG_IN, $ip, $user, $userAgent, $source, $session, $device);
    }

    /**
     * Track the unsuccessful authentication of a client.
     *
     * @param string $ip              The IP address of the client logging in
     * @param array  $user            An array containing id, and optionally name, email, mobile
     * @param string|null  $userAgent The browser user agent of the client logging in
     */
    public function trackLogInDenied($ip, array $user = null, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $this->trackEvent(self::VERB_LOG_IN_DENIED, $ip, $user, $userAgent, $source, $session, $device);
    }

    /**
     * @param string $verb
     * @param string $ip
     * @param array $user
     * @param string|null $userAgent
     */
    public function trackEvent($verb, $ip, array $user = null, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $event = array(
            self::PARAM_VERB => $verb,
            self::PARAM_IP   => $ip
        );

        if (!is_null($userAgent)) {
            $event[self::PARAM_USER_AGENT] = $userAgent;
        }
        if (!is_null($user)) {
            $event[self::PARAM_USER] = array_filter([
                self::PARAM_USER__ID     => $this->findValue(self::PARAM_USER__ID, $user),
                self::PARAM_USER__NAME   => $this->findValue(self::PARAM_USER__NAME, $user),
                self::PARAM_USER__EMAIL  => $this->findValue(self::PARAM_USER__EMAIL, $user),
                self::PARAM_USER__MOBILE => $this->findValue(self::PARAM_USER__MOBILE, $user),
                self::PARAM_USER__AUTHENTICATED => $this->findValue(self::PARAM_USER__AUTHENTICATED, $user),
            ]);
        }
        if (!is_null($source)) {
            $event[self::PARAM_SOURCE] = array_filter([
                self::PARAM_SOURCE__NAME     => $this->findValue(self::PARAM_SOURCE__NAME, $source),
                self::PARAM_SOURCE__LOGO_URL => $this->findValue(self::PARAM_SOURCE__LOGO_URL, $source)
            ]);
        }

        // Add information about the session
        // First, the session ID if it's passed
        $event[self::PARAM_SESSION] = array_filter([
            self::PARAM_SESSION__ID  => $this->findValue(self::PARAM_SESSION__ID, $session)
        ]);
        // Then pull the TD cookie if its present
        if(isset($_COOKIE[ThisData::TD_COOKIE_NAME])) {
            $event[self::PARAM_SESSION][self::PARAM_SESSION__TD_COOKIE_ID] = $_COOKIE[ThisData::TD_COOKIE_NAME];
        }
        // Then whether we expect the JS Cookie at all
        if($this->configuration[Builder::CONF_EXPECT_JS_COOKIE]) {
            $event[self::PARAM_SESSION][self::PARAM_SESSION__TD_COOKIE_EXPECTED] = $this->configuration[Builder::CONF_EXPECT_JS_COOKIE];
        }

        if (!is_null($device)) {
            $event[self::PARAM_DEVICE] = array_filter([
                self::PARAM_DEVICE__ID => $this->findValue(self::PARAM_DEVICE__ID, $device)
            ]);
        }

        $this->execute('POST', ThisData::ENDPOINT_EVENTS, array_filter($event));
    }
}
