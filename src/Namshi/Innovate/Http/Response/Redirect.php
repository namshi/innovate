<?php

namespace Namshi\Innovate\Http\Response;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect extends RedirectResponse
{
    protected $session;
    protected $paReq;

    public function __construct($url, $session, $paReq)
    {
        parent::__construct($url);
        
        $this->setPaReq($paReq);
        $this->setSession($session);
    }

    public function setPaReq($paReq)
    {
        $this->paReq = $paReq;
    }

    public function getPaReq()
    {
        return $this->paReq;
    }

    public function setSession($session)
    {
        $this->session = $session;
    }

    public function getSession()
    {
        return $this->session;
    }
}
