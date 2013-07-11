<?php

namespace Namshi\Innovate\Payment\Billing;

use InvalidArgumentException;

/**
 * This class represents a address as detailed as Innovate needs it.
 */
class Address
{
    protected $line1;
    protected $line2;
    protected $line3;
    protected $city;
    protected $region;
    protected $country;
    protected $zip;

    public function __construct($line1, $line2 = null, $line3 = null, $city, $region = null, $country, $zip = null)
    {
        $this->setLine1($line1);
        $this->setLine2($line2);
        $this->setLine3($line3);
        $this->setCity($city);
        $this->setRegion($region);
        $this->setCountry($country);
        $this->setZip($zip);
    }

    public function setCity($city)
    {
        if (!$city)
        {
            throw new InvalidArgumentException("The city parameter can't be empty.");
        }
        $this->city = $city;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function setCountry($country)
    {
        if (!$country)
        {
            throw new InvalidArgumentException("The country parameter can't be empty.");
        }
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setLine1($line1)
    {
        if (!$line1)
        {
            throw new InvalidArgumentException("The address line1 parameter can't be empty.");
        }
        $this->line1 = $line1;
    }

    public function getLine1()
    {
        return $this->line1;
    }

    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    public function getLine2()
    {
        return $this->line2;
    }

    public function setLine3($line3)
    {
        $this->line3 = $line3;
    }

    public function getLine3()
    {
        return $this->line3;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setZip($zip)
    {
        if ($zip && (!preg_match("/^[0-9]{5}$/", $zip))) {
            throw new InvalidArgumentException("The zip code parameter doesn't match zip code format.");
        }
        $this->zip = $zip;
    }

    public function getZip()
    {
        return $this->zip;
    }

    public function toArray()
    {
        return array(
            'line1'     => $this->getLine1(),
            'line2'     => $this->getLine2(),
            'line3'     => $this->getLine3(),
            'city'      => $this->getCity(),
            'region'    => $this->getRegion(),
            'country'   => $this->getCountry(),
            'zip'       => $this->getZip(),
        );
    }
}
