<?php

namespace ThisData\Api\Endpoint;

use ThisData\Api\ThisData;
use ThisData\Api\Builder;

/**
 * Events Endpoint
 *
 * Track events related to user security, and send them to ThisData.
 * Also get those events back out again.
 *
 * @see http://help.thisdata.com/docs/apiv1events Documentation for POST /events
 * @see http://help.thisdata.com/docs/v1getevents Documentation for  GET /events
 */
class EventsEndpoint extends AbstractEndpoint
{

    /**
     * Track the successful authentication of a client.
     *
     * @param string $ip             The IP address of the client logging in
     * @param array  $user           An array containing id, and optionally name, email, mobile
     * @param string|null $userAgent The browser user agent of the client logging in
     * @param array|null $source     Source details (e.g. for multi-tenanted applications)
     * @param array|null $session    Extra information that provides useful context about the session, for example the session ID, or some cookie information
     * @param array|null $device     Information about the device being used
     */
    public function trackLogIn($ip, array $user, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $this->trackEvent(self::VERB_LOG_IN, $ip, $user, $userAgent, $source, $session, $device);
    }

    /**
     * Track the unsuccessful authentication of a client.
     *
     * @param string $ip             The IP address of the client logging in
     * @param array|null $user       An optional array containing id, and optionally name, email, mobile.
     * @param string|null $userAgent The browser user agent of the client logging in
     * @param array|null $source     Source details (e.g. for multi-tenanted applications)
     * @param array|null $session    Extra information that provides useful context about the session, for example the session ID, or some cookie information
     * @param array|null $device     Information about the device being used
     */
    public function trackLogInDenied($ip, array $user = null, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $this->trackEvent(self::VERB_LOG_IN_DENIED, $ip, $user, $userAgent, $source, $session, $device);
    }

    /**
     * Tracks an event using the ThisData API.
     * @param string $verb           Describes what the User did, using an English present tense verb
     * @param string $ip             The IP address of the client logging in
     * @param array|null $user       An optional array containing id, and optionally name, email, mobile.
     * @param string|null $userAgent The browser user agent of the client logging in
     * @param array|null $source     Source details (e.g. for multi-tenanted applications)
     * @param array|null $session    Extra information that provides useful context about the session, for example the session ID, or some cookie information
     * @param array|null $device     Information about the device being used
     * @see AbstractEndpoint         Predefined VERB_ constants
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

    /**
     * Fetches events from the ThisData API.
     * @see http://help.thisdata.com/docs/v1getevents for request parameters
     * @return array An array of arrays
     */
    public function getEvents($options = null)
    {
        $url = ThisData::ENDPOINT_EVENTS . '?' . http_build_query($options);
        $response = $this->synchronousExecute('GET', $url);
        return json_decode($response->getBody(), TRUE);
    }
}
