<?php

namespace Namshi\Innovate\Test;

use Namshi\Innovate\Client;
use Guzzle\Http\Message\Response;

class ClientStub extends Client
{
  /**
  * Instead of sending the request to Innovate and returning the response, we
  * just return the Request itself
  *
  * @param \SimpleXMLElement $mpiData
  * @return \Guzzle\Http\Message\RequestInterface
  */
  public function authorizeRemoteRequest($mpiData)
  {
    return $this->createRemoteRequest('POST', self::INNOVATE_URL, null, null, $mpiData);
  }

  /**
  * Instead of sending a requesto to Innovate we directly return a valid response
  * @return Response
  */
  public function  authorizeMpiRequest()
  {

      $xmlBody = '
      <result>
        <mpi>
          <session>xyx</session>
          <acsurle>http://acsurl.com</acsurle>
          <pareq>pAreQ</pareq>
        </mpi>
      </result>
      ';

      return new Response(200, null, $xmlBody);
  }
}