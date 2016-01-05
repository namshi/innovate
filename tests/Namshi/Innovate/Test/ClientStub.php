<?php

namespace Namshi\Innovate\Test;

use Namshi\Innovate\Client as NamshiClient;
use Guzzle\Service\Client;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\EntityEnclosingRequest;

class ClientStub extends Client
{
    protected $mpiResponseBody = <<<XML
<?xml version="1.0"?>
<result>
    <mpi>
        <session>xyz</session>
        <acsurle>http://acsurl.com</acsurle>
        <pareq>pAreQ</pareq>
    </mpi>
</result>
XML;

    protected $mpiErrorResponseBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<remote>
    <error>Invalid Request</error>
    <mpi>
        <trace>4000/11858</trace>
    </mpi>
</remote>
XML;

    protected $captureResponseBody = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<remote>
    <auth>
        <status>A</status>
        <code>916358</code>
        <message>Authorised</message>
        <tranref>029818836006</tranref>
        <cvv>Y</cvv>
        <avs>X</avs>
        <trace>4000/1635st7</trace>
    </auth>
</remote>
XML;

    public function send($request)
    {
        if ($this->isTokenRequest($request)) {
            $responseBody = file_get_contents(__DIR__ . '/../../../fixtures/exmaple_reasponse_tokenize.xml');

            return new Response(200, null, trim($responseBody));
        }

        if ($this->isSearchByCartIdRequest($request)) {
            $responseBody = file_get_contents(__DIR__ . '/../../../fixtures/exmaple_reasponse_search_by_cart_id.xml');

            return new Response(200, null, trim($responseBody));
        }

        $xml = new \SimpleXMLElement($request->getBody());

        if ($this->isAuthorizationRequest($request)) {

            if ((string)$xml->card->cvv === '999') {
                return new Response(200, null, trim($this->mpiErrorResponseBody));
            }

            $expected = simplexml_load_file(__DIR__ . '/../../../fixtures/example.xml');
            if ((string)$xml->card->number === '1234123412340002') {
                $expected = simplexml_load_file(__DIR__ . '/../../../fixtures/example_token_request.xml');
            }

            assert((string)$expected->key == (string)$xml->key, 'MPI Authorization request: key is not the same');
            assert((string)$expected->tran->type == (string)$xml->tran->type, 'MPI Authorization request: type is not the same');
            assert((string)$expected->tran->currency == (string)$xml->tran->currency, 'MPI Authorization request: currency is not the same');
            assert((string)$expected->card->number == (string)$xml->card->number, 'MPI Authorization request: number is not the same');
            assert((string)$expected->card->cvv == (string)$xml->card->cvv, 'MPI Authorization request: cvv is not the same');
            assert((string)$expected->card->expiry->month == (string)$xml->card->expiry->month, 'MPI Authorization request: expiry->month is not the same');
            assert((string)$expected->card->expiry->year == (string)$xml->card->expiry->year, 'MPI Authorization request: expiry->year is not the same');

            return new Response(200, null, trim($this->mpiResponseBody));
        }

        $expected = simplexml_load_file(__DIR__ . '/../../../fixtures/example_capture_request.xml');
        if ((string)$xml->card->number === '1234123412340002') {
            $expected = simplexml_load_file(__DIR__ . '/../../../fixtures/example_capture_token_request.xml');
        }

        if (!empty((string)$xml->mpi->pares)) {
           assert((string)$expected->mpi->pares == (string)$xml->mpi->pares, 'Capture request: mpi->pares is not the same');
        }
        assert((string)$expected->mpi->session == (string)$xml->mpi->session, 'Capture request: mpi->session is not the same');
        assert((string)$expected->store == (string)$xml->store, 'Capture request: store is not the same');
        assert((string)$expected->key == (string)$xml->key, 'Capture request: key is not the same');
        assert((string)$expected->tran->type == (string)$xml->tran->type, 'Capture request: tran->type is not the same');
        assert((string)$expected->tran->currency == (string)$xml->tran->currency, 'Capture request: tran->currency is not the same');
        assert((string)$expected->card->number == (string)$xml->card->number, 'Capture request: card->number is not the same');
        assert((string)$expected->card->cvv == (string)$xml->card->cvv, 'Capture request: card->cvv is not the same');
        assert((string)$expected->card->expiry->month == (string)$xml->card->expiry->month, 'Capture request: card->expiry->month is not the same');
        assert((string)$expected->card->expiry->year == (string)$xml->card->expiry->year, 'Capture request: card->expiry->year is not the same');

        if ((string)$xml->card->number !== '1234123412340002') {
            assert((string)$expected->billing->address->line1 == (string)$xml->billing->address->line1, 'Capture request: billing->address->line1 is not the same');
            assert((string)$expected->billing->address->line2 == (string)$xml->billing->address->line2, 'Capture request: billing->address->line2 is not the same');
            assert((string)$expected->billing->address->line3 == (string)$xml->billing->address->line3, 'Capture request: billing->address->line3 is not the same');
            assert((string)$expected->billing->address->city == (string)$xml->billing->address->city, 'Capture request: billing->address->city is not the same');
            assert((string)$expected->billing->address->region == (string)$xml->billing->address->region, 'Capture request: billing->address->region is not the same');
            assert((string)$expected->billing->address->country == (string)$xml->billing->address->country, 'Capture request: billing->address->country is not the same');
            assert((string)$expected->billing->address->zip == (string)$xml->billing->address->zip, 'Capture request: billing->address->zip is not the same');
        }

        assert((string)$expected->billing->email == (string)$xml->billing->email, 'Capture request: billing->email is not the same');
        assert((string)$expected->billing->ip == (string)$xml->billing->ip, 'Capture request: billing->ip is not the same');

        return new Response(200, null, trim($this->captureResponseBody));
    }

    protected function isTokenRequest($request)
    {
        return NamshiClient::INNOVATE_GENERATE_CARD_TOKEN_URL == $request->getUrl();
    }

    protected function isSearchByCartIdRequest($request)
    {
        return sprintf(NamshiClient::INNOVATE_SEARCH_BY_CARTID_URL, 'cartIDcart') == $request->getUrl();
    }

    protected function isAuthorizationRequest($request)
    {
        return NamshiClient::INNOVATE_MPI_URL == $request->getUrl();
    }
}
