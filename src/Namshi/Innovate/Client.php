<?php

namespace Namshi\Innovate;

use Guzzle\Service\Client as BaseClient;
use Namshi\Innovate\Request\Factory as RequestFactory;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Exception\AuthFailed;
use Namshi\Innovate\Http\Response\Redirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP client tied to the Innovate API.
 */
class Client extends BaseClient
{
    const INNOVATE_URL                  = "https://secure.innovatepayments.com/gateway/remote.xml";
    const INNOVATE_MPI_URL              = "https://secure.innovatepayments.com/gateway/remote_mpi.xml";
    const STATUS_ERROR                  = 'E';
    const STATUS_ON_HOLD                = 'H';
    const STATUS_APPROVED               = 'A';
    const RESPONSE_ERROR_STATUS         = 400;
    const RESPONSE_SERVER_ERROR_STATUS  = 500;

    /**
     * @var array
     */
    protected $successfulPaymentStatusCodes = array(
        self::STATUS_APPROVED,
        self::STATUS_ON_HOLD,
    );

    /**
     * @var string
     */
    protected $storeId;
    /**
     * @var string
     */
    protected $key;
    /**
     * @var Transaction
     */
    protected $transaction;
    /**
     * @var Card
     */
    protected $card;
    /**
     * @var BillingInformation
     */
    protected $billingInformation;
    /**
     * @var Browser
     */
    protected $browser;

    /**
     * Constructor
     *
     * @param type $storeId
     * @param type $key
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     * @param string $baseUrl
     * @param array $config
     */
    public function __construct($storeId, $key, $baseUrl = '', $config = null)
    {
        parent::__construct($baseUrl, $config);

        $this->setStoreId($storeId);
        $this->setKey($key);
        $this->setRequestFactory(RequestFactory::getInstance());
    }

    /**
     * Sends a request to the Innovate API with all the information about the
     * payment to be performed.
     *
     * @return Response
     */
    public function performPayment(Transaction $transaction, Card $card, BillingInformation $billing, Browser $browser)
    {
        try {
            $this->setTransaction($transaction);
            $this->setCard($card);
            $this->setBillingInformation($billing);
            $this->setBrowser($browser);

            $mpi = $this->authorizeMpiRequest()->xml()->mpi;

            if (empty($mpi->acsurl)) {
                return $this->authorizeRemoteRequest(array($mpi->session));
            } else {
                return new Redirect($mpi->acsurl, $mpi->session, $mpi->pareq);
            }
        } catch(AuthFailed $e) {
            return new Response($e->getMessage(), static::RESPONSE_ERROR_STATUS);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), static::RESPONSE_SERVER_ERROR_STATUS);
        }
    }

    /**
     * Creates remote request and creates the body to be sent to innovate api.
     *
     * @param string $method
     * @param null $uri
     * @param null $headers
     * @param null $body
     * @param $mpiData
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function createRemoteRequest($method = 'GET', $uri = null, $headers = null, $body = null, $mpiData)
    {
        $request = parent::createRequest($method, $uri, $headers, $body);

        if (!$body) {
            $request->createBody($this->getStoreId(), $this->getKey(), $this->getTransaction(), $this->getCard(), $this->getBillingInformation(), $this->getBrowser(), $mpiData);
        }

        return $request;
    }

    /**
     * Creates mpi request as innovate api need it.
     *
     * @param string $method
     * @param string $uri
     * @param array $headers
     * @param string|resource|array|EntityBodyInterface $body
     * @return \Guzzle\Http\Message\RequestInterface
     */
    public function createMpiRequest($method = 'GET', $uri = null, $headers = null, $body = null)
    {
        $request = parent::createRequest($method, $uri, $headers, $body);

        if (!$body) {
            $request->createMpiBody($this->getStoreId(), $this->getKey(), $this->getTransaction(), $this->getCard(), $this->getBillingInformation(), $this->getBrowser());
        }

        return $request;
    }

    /**
     * Authorize mpi request by sending request to innovate api to do mpi authentication.
     *
     * @return array|\Guzzle\Http\Message\Response|null
     * @throws Exception\AuthFailed
     */
    protected function authorizeMpiRequest()
    {
        $response = $this->send($this->createMpiRequest('POST', self::INNOVATE_MPI_URL, null));

        if (empty($response) || !empty($response->xml()->error)) {
            throw new AuthFailed();
        }

        return $response;
    }

    /**
     * Authorize innovate remote request.
     *
     * @param \SimpleXMLElement $mpiData
     * @return array|\Guzzle\Http\Message\Response|null
     * @throws Exception\AuthFailed
     */
    public function authorizeRemoteRequest($mpiData)
    {
        $response   = $this->send($this->createRemoteRequest('POST', self::INNOVATE_URL, null, null, $mpiData));

        if (!$response || !isset($response) ) {
            return new Response('Authentication Failed', self::RESPONSE_ERROR_STATUS);
        }

        if(!in_array($response->xml()->auth->status, $this->successfulPaymentStatusCodes)) {
            return new Response($response->getBody(), self::RESPONSE_ERROR_STATUS, $response->getHeaders()->toArray());
        }

        return new Response($response->getBody(), $response->getStatusCode(), $response->getHeaders()->toArray());
    }


    /**
     * @return string
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param string $storeId
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return Namshi\Innovate\Payment\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @param Payment\Transaction $transaction
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param Payment\Card $card
     */
    public function setCard(Card $card)
    {
        $this->card = $card;
    }

    /**
     * @return Payment\Card
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @param $browser
     */
    public function setBrowser($browser)
    {
        $this->browser = $browser;
    }

    /**
     * @return Payment\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @param $billingInformation
     */
    public function setBillingInformation($billingInformation)
    {
        $this->billingInformation = $billingInformation;
    }

    /**
     * @return Payment\BillingInformation
     */
    public function getBillingInformation()
    {
        return $this->billingInformation;
    }
}
