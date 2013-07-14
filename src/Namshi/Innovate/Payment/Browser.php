<?php

namespace Namshi\Innovate\Payment;

/**
 * This class represents a browser as detailed as Innovate needs it.
 */
class Browser
{
    /**
     * @var string
     */
    protected $agent;

    /**
     * @var string
     */
    protected $accept;

    /**
     * @param string $agent
     * @param string $accept
     */
    public function __construct($agent, $accept)
    {
        $this->setAgent($agent);
        $this->setAccept($accept);
    }

    /**
     * @param $accept
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    /**
     * @return string
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * @param $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Converts the current object to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            'agent'     => $this->getAgent(),
            'accept'    => $this->getAccept(),
        );
    }
}
