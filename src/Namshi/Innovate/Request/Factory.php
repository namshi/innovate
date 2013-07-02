<?php

namespace Namshi\Innovate\Request;

use Guzzle\Http\Message\RequestFactory;

/**
 * Request factory that overrides the base Guzzle factory in order to specify
 * an own class for assembling HTTP requests.
 */
class Factory extends RequestFactory
{
    protected $entityEnclosingRequestClass = 'Namshi\\Innovate\\Http\\Message\\EntityEnclosingRequest';
    protected static $instance;
}