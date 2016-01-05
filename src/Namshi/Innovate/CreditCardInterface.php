<?php

namespace Namshi\Innovate;

interface CreditCardInterface
{
    public function getNumber();

    public function getCvv();

    public function toArray();
}
