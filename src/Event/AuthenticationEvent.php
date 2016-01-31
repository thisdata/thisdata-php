<?php

namespace ThisData\Api\Event;

use ThisData\Api\Model\UserInterface;

class AuthenticationEvent
{
    /**
     * @var string
     */
    private $ip;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * AuthenticationEvent constructor.
     * @param string $ip
     * @param UserInterface $user
     * @param string|null $userAgent
     */
    public function __construct($ip, UserInterface $user, $userAgent = null)
    {
        $this->setIp($ip);
        $this->setUser($user);
        $this->setUserAgent($userAgent);
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @return AuthenticationEvent
     */
    public function setIp($ip)
    {
        $validatedIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 & FILTER_FLAG_NO_PRIV_RANGE);

        if (false === $validatedIp) {
            throw new \InvalidArgumentException(sprintf('IP address "%s" is not valid.', $ip));
        }

        $this->ip = $validatedIp;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return AuthenticationEvent
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return AuthenticationEvent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }
}
