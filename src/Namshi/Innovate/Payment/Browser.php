<?php

namespace Namshi\Innovate\Payment;

/**
 * This class represents a browser as detailed as Innovate needs it.
 */
class Browser
{
    protected $agent;
    protected $accept;

    public function __construct($agent, $accept)
    {
        $this->setAgent($agent);
        $this->setAccept($accept);
    }

    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    public function getAccept()
    {
        return $this->accept;
    }

    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    public function getAgent()
    {
        return $this->agent;
    }

    public function toArray()
    {
        return array(
            'agent'     => $this->getAgent(),
            'accept'    => $this->getAccept(),
        );
    }
}
