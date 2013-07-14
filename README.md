# NAMSHI | Innovate

This library provides support for Innovate using "Guzzle" client

## Usage

Using this library is easy, you need to define client and send the required data
to perform the payment:
```
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Client;

$client       = new Client($storeId, $authenticationKey);
$transaction  = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'AED', 40, '');
$card         = new Card($this->cardInfo['number'], $this->cardInfo['cvv'], (new DateTime())->add(new DateInterval('P1M')));
$customer     = new Customer('Mr', 'Ayham', 'Alzoubi');
$address      = new Address('alqouz', 'gdp', 'byuilding 3', 'dubai', 'gcc', 'AE', '00971');
$billing      = new BillingInformation($this->customer, $this->address, 'test+test@namshi.com', '192.0.0.1');
$browser      = new Browser('agent', 'accept');
$response     = $client->performPayment($this->transaction, $this->card, $this->billing, $this->browser);
```
we have to check if the response is instance of 'Namshi\Innovate\Http\Response\Redirect' it needs 3d secured
the response contains required data to redirect the user and continue the payment
otherwise the payment are done.
```
if ($response instanceof Redirect) {
    $extraData = array(
        'PaRes'     => $xpath->query("//input[@name='PaRes']")->item(0)->getAttribute('value'),
        'session'   => $xpath->query("//input[@name='MD']")->item(0)->getAttribute('value'),
    );

    $response   = $this->client->send($this->client->createRemoteRequest('POST', Client::INNOVATE_URL, null, null, $extraData));
}
```
we get $extraData from redirect url after we redirect the user it will be added as hidden fields
to the form.

and then we send the last request to continue the payment.

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
