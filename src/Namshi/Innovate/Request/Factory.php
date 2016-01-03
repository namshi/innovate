<?php

namespace Namshi\Innovate\Request;

use Guzzle\Http\Message\RequestFactory;
use Namshi\Innovate\Tokenized\Card;
use Namshi\Innovate\Tokenized\BillingInformation;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Guzzle\Http\Client as HttpClient;
use Namshi\Innovate\Client;

/**
 * Request factory that overrides the base Guzzle factory in order to specify
 * an own class for assembling HTTP requests.
 */
class Factory extends RequestFactory
{
    protected $entityEnclosingRequestClass = 'Namshi\\Innovate\\Http\\Message\\EntityEnclosingRequest';
    protected static $instance;

    public function createTokenizeRequest(HttpClient $client, $storeId, $key, Card $card, BillingInformation $billing)
    {
        $body = $this->buildTokenizeRequestBody($storeId, $key, $card, $billing);

        return $client->post(sprintf(Client::INNOVATE_GENERATE_CARD_TOKEN_URI), array(), $body, array(
            'timeout'         => 5,
            'connect_timeout' => 5
        ));
    }

    /**
     * Transform the card and billing info to xml format needed for a tokenize request
     *
     * @param  Card               $card
     * @param  BillingInformation $billing
     * @return string
     */
    protected function buildTokenizeRequestBody($storeId, $key, Card $card, BillingInformation $billing)
    {
        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));

        $body = [
            'store' => $storeId,
            'key' => $key,
            'card' => $card->toArray(),
            'billing' => $billing->toArray(),
        ];

        return $serializer->serialize($body, 'xml');
    }
}
