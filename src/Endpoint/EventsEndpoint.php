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

    /**
     * Track the successful authentication of a client.
     *
     * @param string $ip              The IP address of the client logging in
     * @param array  $user            An array containing id, and optionally name, email, mobile
     * @param string|null  $userAgent The browser user agent of the client logging in
     */
    public function trackLogIn($ip, array $user = null, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $this->trackEvent(EndpointConstants::VERB_LOG_IN, $ip, $user, $userAgent, $source, $session, $device);
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
        $this->trackEvent(EndpointConstants::VERB_LOG_IN_DENIED, $ip, $user, $userAgent, $source, $session, $device);
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
            EndpointConstants::PARAM_VERB => $verb,
            EndpointConstants::PARAM_IP   => $ip
        );

        if (!is_null($userAgent)) {
            $event[EndpointConstants::PARAM_USER_AGENT] = $userAgent;
        }
        if (!is_null($user)) {
            $event[EndpointConstants::PARAM_USER] = array_filter([
                EndpointConstants::PARAM_USER__ID     => $this->findValue(EndpointConstants::PARAM_USER__ID, $user),
                EndpointConstants::PARAM_USER__NAME   => $this->findValue(EndpointConstants::PARAM_USER__NAME, $user),
                EndpointConstants::PARAM_USER__EMAIL  => $this->findValue(EndpointConstants::PARAM_USER__EMAIL, $user),
                EndpointConstants::PARAM_USER__MOBILE => $this->findValue(EndpointConstants::PARAM_USER__MOBILE, $user),
                EndpointConstants::PARAM_USER__AUTHENTICATED => $this->findValue(EndpointConstants::PARAM_USER__AUTHENTICATED, $user),
            ]);
        }
        if (!is_null($source)) {
            $event[EndpointConstants::PARAM_SOURCE] = array_filter([
                EndpointConstants::PARAM_SOURCE__NAME     => $this->findValue(EndpointConstants::PARAM_SOURCE__NAME, $source),
                EndpointConstants::PARAM_SOURCE__LOGO_URL => $this->findValue(EndpointConstants::PARAM_SOURCE__LOGO_URL, $source)
            ]);
        }

        // Add information about the session
        // First, the session ID if it's passed
        $event[EndpointConstants::PARAM_SESSION] = array_filter([
            EndpointConstants::PARAM_SESSION__ID  => $this->findValue(EndpointConstants::PARAM_SESSION__ID, $session)
        ]);
        // Then pull the TD cookie if its present
        if(isset($_COOKIE[ThisData::TD_COOKIE_NAME])) {
            $event[EndpointConstants::PARAM_SESSION][EndpointConstants::PARAM_SESSION__TD_COOKIE_ID] = $_COOKIE[ThisData::TD_COOKIE_NAME];
        }
        // Then whether we expect the JS Cookie at all
        if($this->configuration[Builder::CONF_EXPECT_JS_COOKIE]) {
            $event[EndpointConstants::PARAM_SESSION][EndpointConstants::PARAM_SESSION__TD_COOKIE_EXPECTED] = $this->configuration[Builder::CONF_EXPECT_JS_COOKIE];
        }

        if (!is_null($device)) {
            $event[EndpointConstants::PARAM_DEVICE] = array_filter([
                EndpointConstants::PARAM_DEVICE__ID => $this->findValue(EndpointConstants::PARAM_DEVICE__ID, $device)
            ]);
        }

        $this->execute('POST', ThisData::ENDPOINT_EVENTS, array_filter($event));
    }
}
