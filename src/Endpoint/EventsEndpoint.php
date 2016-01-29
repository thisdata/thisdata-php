<?php

namespace ThisData\Api\Endpoint;

use ThisData\Api\ThisData;

class EventsEndpoint extends AbstractEndpoint
{
    const VERB_LOG_IN        = 'log-in';
    const VERB_LOG_IN_DENIED = 'log-in-denied';

    const PARAM_VERB        = 'verb';
    const PARAM_IP          = 'ip';
    const PARAM_USER        = 'user';
    const PARAM_USER__ID    = 'id';
    const PARAM_USER__NAME  = 'name';
    const PARAM_USER__EMAIL = 'email';
    const PARAM_USER_AGENT  = 'user_agent';

    public function trackLogIn($ip, array $user, $userAgent = null)
    {
        $this->trackLogInAttempt(self::VERB_LOG_IN, $ip, $user, $userAgent);
    }

    public function trackLogInDenied($ip, array $user, $userAgent = null)
    {
        $this->trackLogInAttempt(self::VERB_LOG_IN_DENIED, $ip, $user, $userAgent);
    }

    protected function trackLogInAttempt($verb, $ip, array $user, $userAgent = null)
    {
        $this->execute('POST', ThisData::ENDPOINT_EVENTS, [
            self::PARAM_VERB => $verb,
            self::PARAM_IP   => $ip,
            self::PARAM_USER => [
                self::PARAM_USER__ID    => $this->findValue(self::PARAM_USER__ID, $user),
                self::PARAM_USER__NAME  => $this->findValue(self::PARAM_USER__NAME, $user),
                self::PARAM_USER__EMAIL => $this->findValue(self::PARAM_USER__EMAIL, $user),
            ],
            self::PARAM_USER_AGENT => $userAgent
        ]);
    }
}
