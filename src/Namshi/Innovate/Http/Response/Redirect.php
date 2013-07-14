<?php

namespace Namshi\Innovate\Http\Response;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @inheritDoc
 */
class Redirect extends RedirectResponse
{
    /**
     * @var string
     */
    protected $session;

    /**
     * @var string
     */
    protected $paReq;

    /**
     * Constructor
     *
     * @param string $url
     * @param int $session
     * @param array $paReq
     */
    public function __construct($url, $session, $paReq)
    {
        parent::__construct($url);
        
        $this->setPaReq($paReq);
        $this->setSession($session);
    }

    /**
     * @param $paReq
     */
    public function setPaReq($paReq)
    {
        $this->paReq = $paReq;
    }

    /**
     * @return string
     */
    public function getPaReq()
    {
        return $this->paReq;
    }

    /**
     * @param $session
     */
    public function setSession($session)
    {
        $this->session = $session;
    }

    /**
     * @return string
     */
    public function getSession()
    {
        return $this->session;
    }
}
