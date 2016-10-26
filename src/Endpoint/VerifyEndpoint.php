<?php

namespace ThisData\Api\Endpoint;

use ThisData\Api\ThisData;
use ThisData\Api\Builder;

/**
 * Verify Endpoint
 *
 * Verify your current user, and make use of their current risk score.
 *
 * When a sensitive action is about to occur, e.g. before finalizing the
 * log-in process, you can verify that the user is who they say they are, and
 * check the risk that their account is being impersonated by an attacker.
 * If they present a medium or high risk, force them to provide a two-factor
 * authentication code.
 *
 * @see http://help.thisdata.com/docs/apiv1verify
 */
class VerifyEndpoint extends AbstractEndpoint
{

    /**
     * Verify the current user to get the risk that their account is being
     * hijacked.
     *
     * @param string $ip             The IP address of the client logging in
     * @param array $user            Array containing id, and optionally name, email, mobile.
     * @param string|null $userAgent The browser user agent of the client logging in
     * @param array|null $source     Source details (e.g. for multi-tenanted applications)
     * @param array|null $session    Extra information that provides useful context about the session, for example the session ID, or some cookie information
     * @param array|null $device     Information about the device being used
     *
     * @return an Array with details on the current risk (a JSON representation of the API response).
     * @see http://help.thisdata.com/docs/apiv1verify
     */
    public function verify($ip, array $user, $userAgent = null, array $source = null, array $session = null, array $device = null)
    {
        $event = [
            self::PARAM_IP   => $ip
        ];

        if (!is_null($userAgent)) {
            $event[self::PARAM_USER_AGENT] = $userAgent;
        }

        $event[self::PARAM_USER] = array_filter([
            self::PARAM_USER__ID     => $this->findValue(self::PARAM_USER__ID, $user),
            self::PARAM_USER__NAME   => $this->findValue(self::PARAM_USER__NAME, $user),
            self::PARAM_USER__EMAIL  => $this->findValue(self::PARAM_USER__EMAIL, $user),
            self::PARAM_USER__MOBILE => $this->findValue(self::PARAM_USER__MOBILE, $user),
            self::PARAM_USER__AUTHENTICATED => $this->findValue(self::PARAM_USER__AUTHENTICATED, $user),
        ]);

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
        if (isset($_COOKIE[ThisData::TD_COOKIE_NAME])) {
            $event[self::PARAM_SESSION][self::PARAM_SESSION__TD_COOKIE_ID] = $_COOKIE[ThisData::TD_COOKIE_NAME];
        }
        // Then whether we expect the JS Cookie at all
        if ($this->configuration[Builder::CONF_EXPECT_JS_COOKIE]) {
            $event[self::PARAM_SESSION][self::PARAM_SESSION__TD_COOKIE_EXPECTED] = $this->configuration[Builder::CONF_EXPECT_JS_COOKIE];
        }

        if (!is_null($device)) {
            $event[self::PARAM_DEVICE] = array_filter([
                self::PARAM_DEVICE__ID => $this->findValue(self::PARAM_DEVICE__ID, $device)
            ]);
        }

        $response = $this->synchronousExecute('POST', ThisData::ENDPOINT_VERIFY, array_filter($event));
        return json_decode($response->getBody(), TRUE);
    }
}
