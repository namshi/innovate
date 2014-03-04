<?php

namespace Namshi\Innovate\Exception;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;

/**
 * Exception responsible to report an expired date for the card
 */
class ExpiredCard extends InvalidArgumentException
{
}
