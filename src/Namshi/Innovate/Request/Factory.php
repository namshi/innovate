<?php

namespace Namshi\Innovate\Request;

use Namshi\Innovate\CreditCardInterface;
use Namshi\Innovate\CustomerInformationInterface;
use Namshi\Innovate\Tokenized\Card as TokenizedCard;
use Namshi\Innovate\Tokenized\BillingInformation as TokenizedBillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Payment\Transaction;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Guzzle\Http\Client as HttpClient;
use Namshi\Innovate\Client;

/**
 * Request factory that overrides the base Guzzle factory in order to specify
 * an own class for assembling HTTP requests.
 */
class Factory
{
    /**
     * creare a request to obtain mpi data
     *
     * @param  Client      $client
     * @param  string      $method
     * @param  string      $uri
     * @param  array       $headers
     * @param  string      $storeId
     * @param  string      $key
     * @param  Transaction $transaction
     * @param  CreditCardInterface $card
     * @return Guzzle\Http\Message\Response
     */
    public function createMpiRequest(
        HttpClient $client,
        $method,
        $uri,
        $headers,
        $storeId,
        $key,
        Transaction $transaction,
        CreditCardInterface $card,
        CustomerInformationInterface $billing
    ) {
        $request = $client->createRequest($method, $uri, $headers);

        $body            = [];
        $body['store']   = $storeId;
        $body['key']     = $key;
        $body['tran']    = $transaction->toArray();
        $body['card']    = $card->toArray();
        $body['billing'] = $billing->toArray();

        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
        $request->setBody($serializer->serialize($body, 'xml'));

        return $request;
    }

    /**
     * create a request for the remote API when providing mpi data
     *
     * @param  Client                    $client
     * @param  string                    $method
     * @param  string                    $uri
     * @param  array                     $headers
     * @param  string                    $storeId
     * @param  string                    $key
     * @param  Transaction               $transaction
     * @param  CreditCardInterface               $card
     * @param  CustomerInformationInterface $billing
     * @param  Browser                   $browser
     * @param  array                     $mpiData
     * @return [type]
     */
    public function createRemoteRequest(
        HttpClient $client,
        $method,
        $uri,
        $headers,
        $storeId,
        $key,
        Transaction $transaction,
        CreditCardInterface $card,
        CustomerInformationInterface $billing,
        Browser $browser,
        array $mpiData
    ) {
        $request = $client->createRequest($method, $uri, $headers);

        $body            = [];
        $body['store']   = $storeId;
        $body['key']     = $key;
        $body['tran']    = $transaction->toArray();
        $body['card']    = $card->toArray();
        $body['billing'] = $billing->toArray();
        $body['browser'] = $browser->toArray();
        $body['mpi']     = $mpiData;

        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));
        $request->setBody($serializer->serialize($body, 'xml'));

        return $request;
    }

    /**
     * Create a request to tokenize a credit card
     *
     * @param  HttpClient                  $client
     * @param  string                      $storeId
     * @param  string                      $key
     * @param  TokenizedCard               $card
     * @param  TokenizedBillingInformation $billing
     * @return Guzzle\Http\Message\Response
     */
    public function createTokenizeRequest(HttpClient $client, $storeId, $key, TokenizedCard $card, TokenizedBillingInformation $billing)
    {
        $encoder    = new XmlEncoder('remote');
        $serializer = new Serializer(array(new GetSetMethodNormalizer()), array($encoder));

        $body = [
            'store' => $storeId,
            'key' => $key,
            'card' => $card->toArray(),
            'billing' => $billing->toArray(),
        ];

        return $client->post(sprintf(Client::INNOVATE_GENERATE_CARD_TOKEN_URL), [], $serializer->serialize($body, 'xml'), array(
            'timeout'         => 5,
            'connect_timeout' => 5
        ));
    }

    /**
     * Create a request to list transactions by cart id
     *
     * @param  HttpClient $client
     * @param  string     $cartId
     * @param  string     $merchantIdr
     * @param  string     $searchKey
     * @return \SimpleXMLElement
     */
    public function createSearchByCartIdRequest(HttpClient $client, $cartId, $merchantId, $searchKey)
    {
        $request = $client->get(
            sprintf(Client::INNOVATE_SEARCH_BY_CARTID_URL, $cartId),
            [],
            [ 'auth' => [$merchantId, $searchKey, 'Basic']],
            [
                'timeout'         => 5,
                'connect_timeout' => 5,
            ]
        );

        return $request;
    }
}
