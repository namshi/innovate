<?php

namespace Namshi\Innovate\Tokenized;

use Namshi\Innovate\AbstractCustomerInformation;

class CustomerInformation extends AbstractCustomerInformation
{
    public function toArray()
    {
        return [
            'email' => $this->getEmail(),
            'ip'    => $this->getIp(),
        ];
    }
}
