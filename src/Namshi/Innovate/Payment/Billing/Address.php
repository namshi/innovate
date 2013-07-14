<?php

namespace Namshi\Innovate\Payment\Billing;

use InvalidArgumentException;

/**
 * This class represents a address as detailed as Innovate needs it.
 */
class Address
{
    /**
     * Address line 1
     *
     * @var string
     */
    protected $line1;

    /**
     * Address line 2
     *
     * @var string
     */
    protected $line2;

    /**
     * Address line 3
     *
     * @var string
     */
    protected $line3;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $zip;

    /**
     * Constructor
     *
     * @param string $line1
     * @param string|null $line2
     * @param string|null $line3
     * @param string $city
     * @param string $region
     * @param string $country
     * @param null|string $zip
     */
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

    /**
     * @param $city
     * @throws \InvalidArgumentException
     */
    public function setCity($city)
    {
        if (!$city) {
            throw new InvalidArgumentException("The city parameter can't be empty.");
        }
        
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param $country
     * @throws \InvalidArgumentException
     */
    public function setCountry($country)
    {
        if (!$country) {
            throw new InvalidArgumentException("The country parameter can't be empty.");
        }
        
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets address line 1
     *
     * @param $line1
     * @throws \InvalidArgumentException
     */
    public function setLine1($line1)
    {
        if (!$line1) {
            throw new InvalidArgumentException("The address line1 parameter can't be empty.");
        }
        
        $this->line1 = $line1;
    }

    /**
     * @return string
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return string
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param $line3
     */
    public function setLine3($line3)
    {
        $this->line3 = $line3;
    }

    /**
     * @return string
     */
    public function getLine3()
    {
        return $this->line3;
    }

    /**
     * @param $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param $zip
     * @throws \InvalidArgumentException
     */
    public function setZip($zip)
    {
        if ($zip && (!preg_match("/^[0-9]{5}$/", $zip))) {
            throw new InvalidArgumentException("The zip code parameter doesn't match zip code format.");
        }
        
        $this->zip = $zip;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Converts the current object to an array.
     *
     * @return array
     */
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
