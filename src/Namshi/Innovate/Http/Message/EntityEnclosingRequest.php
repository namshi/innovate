<?php

namespace Namshi\Innovate\Http\Message;

use Guzzle\Http\Message\EntityEnclosingRequest as BaseRequest;
use Namshi\Innovate\Payment\Transaction;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;

/**
 * @inheritDoc
 */
class EntityEnclosingRequest extends BaseRequest
{
    protected $xmlBody;

    /**
     * Constructor
     *
     * @param string $method
     * @param \Guzzle\Http\Url|string $url
     * @param array $headers
     */
    public function __construct($method, $url, $headers = array())
    {
        parent::__construct($method, $url, $headers);
        
        $this->xmlBody = array();
    }

    /**
     * Creates http request body formatted in xml
     *
     * @param string $storeId
     * @param string $key
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     * @param \Namshi\Innovate\Payment\Card $card
     * @param \Namshi\Innovate\Payment\BillingInformation $billing
     * @param \Namshi\Innovate\Payment\Browser $browser
     * @param array $mpiData
     */
    public function createBody($storeId, $key, Transaction $transaction, Card $card, BillingInformation $billing, Browser $browser, array $mpiData)
    {
        $this->xmlBody = array();
        $this->addStoreId($storeId);
        $this->addKey($key);
        $this->addTransaction($transaction);
        $this->addCard($card);
        $this->addBilling($billing);
        $this->addBrowser($browser);

        if (!empty($mpiData)) {
            $this->addMpiData($mpiData);
        }
        
        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));

        $xmlBody = $serializer->serialize($this->xmlBody, 'xml');
        file_put_contents (sys_get_temp_dir().'/innovate.txt' , $xmlBody, FILE_APPEND);

        $this->setBody($xmlBody);
    }

    /**
     * Creates mpi request formatted in xml
     *
     * @param string $storeId
     * @param string $key
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     * @param \Namshi\Innovate\Payment\Card $card
     */
    public function createMpiBody($storeId, $key, Transaction $transaction, Card $card)
    {
        $this->xmlBody  = array();
        $this->addStoreId($storeId);
        $this->addKey($key);
        $this->addTransaction($transaction);
        $this->addCard($card);
        $encoder        = new XmlEncoder('remote');
        $serializer     = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
        $this->setBody($serializer->serialize($this->xmlBody, 'xml'));
    }

    /**
     * Adds the value of storeId to the body
     *
     * @param $id
     */
    protected function addStoreId($id)
    {
        $this->xmlBody['store'] = $id;
    }

    /**
     * Adds the value of authentication Key to the body
     *
     * @param $key
     */
    protected function addKey($key)
    {
        $this->xmlBody['key'] = $key;
    }

    /**
     * Adds the value of mpi data to the body
     *
     * @param $mpiData
     */
    public function addMpiData(array $mpiData)
    {
        $this->xmlBody['mpi'] = $mpiData;
        file_put_contents (sys_get_temp_dir().'/innovate.txt' , json_encode($this->xmlBody), FILE_APPEND );
    }

    /**
     * Adds the value of transaction data to the body
     *
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     */
    protected function addTransaction(Transaction $transaction)
    {
        $this->xmlBody['tran'] = $transaction->toArray();
    }

    /**
     * Adds the value of card data to the body
     *
     * @param \Namshi\Innovate\Payment\Card $card
     */
    protected function addCard(Card $card)
    {
        $this->xmlBody['card'] = $card->toArray();
    }

    /**
     * Adds the value of billing information to the body
     *
     * @param \Namshi\Innovate\Payment\BillingInformation $card
     */
    protected function addBilling(BillingInformation $card)
    {
        $this->xmlBody['billing'] = $card->toArray();
    }

    /**
     * Adds the value of browser data to the body
     *
     * @param \Namshi\Innovate\Payment\Browser $browser
     */
    protected function addBrowser(Browser $browser)
    {
        $this->xmlBody['browser'] = $browser->toArray();
    }
}
