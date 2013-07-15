# NAMSHI | Innovate

This library provides support for Innovate using "Guzzle" client

## Usage

Using this library is easy, you need to define client and send the required data
to perform the payment

``` php
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Client;

$client       = new Client($storeId, $authenticationKey); // retrieve them from innovate
$transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40, 'AN OPTIONAL REFERENCE TO YOUR TRANSACTION');
$card         = new Card('1234123412341234', '111', new \DateTime($cardExpiryDate));
$customer     = new Customer('Mr', 'Ayham', 'Alzoubi');
$address      = new Address('My address info 1', 'My address info 2', 'My address info 3', 'San Francisco', 'California', 'US', '00000');
$billing      = new BillingInformation($this->customer, $this->address, 'customers's-email@gmail.com', $customerIpAddress);
$browser      = new Browser($customerUserAgent, $requestAcceptHeader);
$response     = $client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
```

## Normal transactions

## 3D secure transactions

http://en.wikipedia.org/wiki/3-D_Secure

We have to check if the response is instance of 'Namshi\Innovate\Http\Response\Redirect' it needs **3d-secured**
verification, meaning that the user will provide additional credentials before authorizing the payment.

The response will contain the URL that you will need to redirect the user, with a form, via POST:

``` php
if ($response instanceof Redirect) {
    // build a form
    // add hidden PaReq, MD and termUrl
    // termUrl is the URL to which the user will be redirected after
    // he enters the 3d secured credentials on
    // the innovate portal

    // submit the form via JS so that the user gets redirected to
    // the Innovate webservice
}
```

See an example [ACS From](https://github.com/namshi/innovate/tree/innovate-readme/examples/3d-secured/ACSForm.php)

After you submit the form you will get hidden values `pares`, `session` and you need this value to continue:

```
$extraData   = array(
    'PaRes'     => 'Authentication response', // we get this value from hidden fields after we submit the form
    'session'   => 'MPI Session ID', // we get this value from hidden fields after we submit the form
);

$response   = $client->send($this->client->createRemoteRequest('POST', Client::INNOVATE_URL, null, null, $extraData));
```

## Tests

You can run the test suite by first installing the
dependencies and running PHPUnit:

```
php composer.phar update

phpunit
```

There are a couple integration tests that actually verify that the library
works flawless.
You need  valid credentials for that, just create a file called `innovate.config`
in your system's temporary folder (`sys_get_temp_dir()`) with 4 parameters:

``` php
<?php

$configs = array(
	'storeId' 		    => 'xxxxx',  // your store Id in Innovate
	'authenticationKey' => 'xxxxxxxxxxx',  // your authentication key
);

// Card info
$cardInfo = array(
	'number'	=> '111111111111111',
	'cvv'		=> 'XXX',
);

// The card which need redirection for 3d secured
$redirectUrlCardInfo = array(
	'number'	=> '111111111111111',
	'cvv'		=> 'XXX',

);

// your ip and should be in Innovate white list
$ip = 'xxx.xxx.xxx.xxx';
```

To run integration test you need to run:
```
phpunit tests/Namshi/Innovate/Test/Integration/ServiceTest.php
```
