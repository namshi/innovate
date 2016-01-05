<?php

namespace Namshi\Innovate;

use Guzzle\Http\Client as HttpClient;
use Namshi\Innovate\CreditCardInterface;
use Namshi\Innovate\CustomerInformationInterface;
use Namshi\Innovate\Exception\InnovateException;
use Namshi\Innovate\Request\Factory as RequestFactory;
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Tokenized\Card as TokenizedCard;
use Namshi\Innovate\Tokenized\BillingInformation as TokenizedBillingInfo;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Exception\AuthFailed;
use Namshi\Innovate\Http\Response\Redirect;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTP client tied to the Innovate API.
 */
class Client
{
    const INNOVATE_URL                     = "https://secure.innovatepayments.com/gateway/remote.xml";
    const INNOVATE_MPI_URL                 = "https://secure.innovatepayments.com/gateway/remote_mpi.xml";
    const INNOVATE_BASE_URL                = "https://secure.innovatepayments.com";
    const INNOVATE_GENERATE_CARD_TOKEN_URL = "https://secure.innovatepayments.com/gateway/tokenize.xml";
    const INNOVATE_SEARCH_BY_CARTID_URL    = "https://secure.innovatepayments.com/tools/api/xml/transaction/%s/cart";
    const STATUS_ERROR                     = 'E';
    const STATUS_ON_HOLD                   = 'H';
    const STATUS_APPROVED                  = 'A';
    const RESPONSE_ERROR_STATUS            = 400;
    const RESPONSE_SERVER_ERROR_STATUS     = 500;

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
     * @var CreditCardInterface
     */
    protected $card;

    /**
     * @var CustomerInformationInterface
     */
    protected $billingInformation;

    /**
     * @var Browser
     */
    protected $browser;

    /**
     * @var string
     */
    protected $merchantId;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * Constructor
     *
     * @param string $storeId
     * @param string $key
     * @param \Namshi\Innovate\Payment\Transaction $transaction
     * @param string $baseUrl
     * @param array $config
     * @param \Guzzle\Service\Client|null $guzzleClient
     */
    public function __construct(
        $storeId,
        $merchantId,
        $key,
        $searchKey,
        $baseUrl = '',
        $config = null,
        \Guzzle\Service\Client $guzzleClient = null)
    {
        $this->guzzleClient = $guzzleClient ?: new \Guzzle\Service\Client($baseUrl, $config);
        $this->storeId = $storeId;
        $this->merchantId = $merchantId;
        $this->searchKey = $searchKey;
        $this->key = $key;
        $this->requestFactory = new RequestFactory();
    }

    /**
     * Sends a request to the Innovate API with all the information about the
     * payment to be performed.
     *
     * @return Response
     */
    public function performPayment(Transaction $transaction, CreditCardInterface $card, CustomerInformationInterface $billing, Browser $browser)
    {
        try {
            $this->setTransactionDetails($transaction, $card, $billing, $browser);

            $mpi = $this->authorizeMpiRequest()->xml()->mpi;

            if (empty($mpi->acsurl)) {
                return $this->authorizeRemoteRequest(array($mpi->session));
            } else {
                return new Redirect($mpi->acsurl->__toString(), $mpi->session->__toString(), $mpi->pareq->__toString());
            }
        } catch (AuthFailed $e) {
            return new Response($e->getMessage(), static::RESPONSE_ERROR_STATUS);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), static::RESPONSE_SERVER_ERROR_STATUS);
        }
    }

    /**
     * Sends a request to the Innovate API with all the information about the
     * 3D secure payment to be performed.
     *
     * @param Transaction                  $transaction
     * @param CreditCardInterface          $card
     * @param CustomerInformationInterface $billing
     * @param Browser                      $browser
     * @param array                        $mpiData
     * @return Response
     */
    public function perform3DSecurePayment(Transaction $transaction, CreditCardInterface $card, CustomerInformationInterface $billing, Browser $browser, array $mpiData)
    {
        try {
            $this->setTransactionDetails($transaction, $card, $billing, $browser);

            return $this->authorizeRemoteRequest($mpiData);
        } catch (AuthFailed $e) {
            return new Response($e->getMessage(), static::RESPONSE_ERROR_STATUS);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), static::RESPONSE_SERVER_ERROR_STATUS);
        }
    }

    /**
     * Given a cart id reference will search for transactions for that cart id on innovate
     *
     * @param $ref
     * @return \SimpleXMLElement
     * @throws InnovateException
     */
    public function searchTransactionsByCartId($cartId)
    {
        $request = $this->requestFactory->createSearchByCartIdRequest(
            $this->guzzleClient,
            $cartId,
            $this->merchantId,
            $this->searchKey
        );

        $response = $this->guzzleClient->send($request);

        if ( ! $response instanceof \Guzzle\Http\Message\Response) {
            throw new InnovateException('Error while connecting to innovate. Transactions for '.$cartId.' could not be fetched.');
        }

        $xml = $response->xml();

        if($xml->trancount <= 0) {
            throw new InnovateException("No transaction found for {$cartId}");
        }

        return $xml;
    }

    /**
     * Make a request ro the tokenize api endpoint.
     *
     * @param  TokenizedCard        $card
     * @param  TokenizedBillingInfo $billing
     * @return Guzzle\Http\Message\Response
     */
    public function tokenize(TokenizedCard $card, TokenizedBillingInfo $billing)
    {
        $request = $this->requestFactory->createTokenizeRequest(
            $this->guzzleClient,
            $this->getStoreId(),
            $this->getKey(),
            $card,
            $billing
        );

        return $this->guzzleClient->send($request);
    }

    /**
     * Authorize mpi request by sending request to innovate api to do mpi authentication.
     *
     * @return array|\Guzzle\Http\Message\Response|null
     * @throws Exception\AuthFailed
     */
    protected function authorizeMpiRequest()
    {
        $request  = $this->requestFactory->createMpiRequest(
            $this->guzzleClient,
            'POST',
            self::INNOVATE_MPI_URL,
            null,
            $this->getStoreId(),
            $this->getKey(),
            $this->getTransaction(),
            $this->getCard(),
            $this->getBillingInformation()
        );

        $response = $this->guzzleClient->send($request);

        if (empty($response)) {
            throw new AuthFailed("Innovate authorization request timeout");
        }

        if (!($response->xml() instanceof \SimpleXMLElement)) {
            throw new AuthFailed("Invalid Innovate authorization request");
        }

        if (!empty((string)$response->xml()->error)) {
            throw new AuthFailed((string)$response->getBody());
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
    protected function authorizeRemoteRequest($mpiData)
    {
        $request = $this->requestFactory->createRemoteRequest(
            $this->guzzleClient,
            'POST',
            self::INNOVATE_URL,
            null,
            $this->getStoreId(),
            $this->getKey(),
            $this->getTransaction(),
            $this->getCard(),
            $this->getBillingInformation(),
            $this->getBrowser(),
            $mpiData
        );

        $response   = $this->guzzleClient->send($request);

        if (!$response || !isset($response) || !($response->xml() instanceof \SimpleXMLElement)) {
            return new Response('Authentication Failed', self::RESPONSE_ERROR_STATUS);
        }

        if (!in_array($response->xml()->auth->status, $this->successfulPaymentStatusCodes)) {
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return Namshi\Innovate\Payment\Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return CreditCardInterface
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * @return Payment\Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * @return CustomerInformationInterface
     */
    public function getBillingInformation()
    {
        return $this->billingInformation;
    }

    /**
     * Sets transactions details for the innovate api call
     *
     * @param Transaction                  $transaction
     * @param CreditCardInterface          $card
     * @param CustomerInformationInterface $billing
     * @param Browser                      $browser
     */
    protected function setTransactionDetails(
        Transaction $transaction,
        CreditCardInterface $card,
        CustomerInformationInterface $billing,
        Browser $browser = null
    ) {
        $this->transaction = $transaction;
        $this->card = $card;
        $this->billingInformation = $billing;
        $this->browser = $browser;
    }
}
