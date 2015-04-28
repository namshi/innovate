# NAMSHI | Innovate

[![Build Status](https://travis-ci.org/namshi/innovate.svg?branch=tests-on-hhvm)](https://travis-ci.org/namshi/innovate)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3ebdeda0-ce10-4c4e-8917-a93290f4e2a8/mini.png)](https://insight.sensiolabs.com/projects/3ebdeda0-ce10-4c4e-8917-a93290f4e2a8)

This library provides support for the [Innovate](http://www.innovatepayments.com/)
payment gateway using [Guzzle](https://github.com/guzzle/guzzle).

## Version

From the 3.0.0 version, the client constructor has been changed. Now it accepts two more parameters:

```
/* 2.x version */
$client = new Namshi\Innovate\Client('storeid', 'key');
```

```
/* 3.x version */
$client = new Namshi\Innovate\Client('storeid', 'merchantid', 'key', 'searchkey');
```

The added paramets are used when fetching the transactions related to a cart id (see below).


## Installation

You can install this library via composer: have a look
at the [package on packagist](https://packagist.org/packages/namshi/innovate).

Then include it into your `composer.json`:

```
"namshi/innovate": "1.0.*",
```

Pick major and minor version according to your needs.

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
$customer     = new Customer('Mr', 'John', 'Doe');
$address      = new Address('My address info 1', 'My address info 2', 'My address info 3', 'San Francisco', 'California', 'US', '00000');
$billing      = new BillingInformation($customer, $address, "customers's-email@gmail.com", $customerIpAddress);
$browser      = new Browser($customerUserAgent, $requestAcceptHeader);
$response     = $client->performPayment($transaction, $card, $billing, $browser);
```

## How Its Working

There are two types of transactions ([3-D_Secure](http://en.wikipedia.org/wiki/3-D_Secure) transaction, Normal transaction) depending on the 
type of the response, the proper transaction method can be determined:

```php
use use Namshi\Innovate\Http\Response\Redirect;

$response = $client->performPayment($transaction, $card, $billing, $browser);

if ($response instanceOf Redirect) {
    // 3D secure transactions
} else {
    // Normal transactions
}

```

## Authorization Statuses

A request is sent, the authorization is granted, and **then** the payment request is sent, you will receive the
response [Http status code: 200](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.1)

If the authorization is denied, you will receive a response with [Http status code: 400](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.1)

## Normal transactions

A normal transaction follows a very simple flow, as it just requires authentication through Innovate, and 
the library will perform the payment:

```php
$response = $this->client->performPayment($transaction, $card, $billing, $browser);

if ($response->getStatusCode() === 200) {
    // payment done
} else {
    // Authentication failed
}
```

## 3D secure transactions

A 3D secure transaction require 2 requests to innovate and 1 request for [3-D_Secure](http://en.wikipedia.org/wiki/3-D_Secure) to 
additional security layer to perform the payment.
The first request is already sent to innovate and if its 3d secure transaction it returns new instance 
of ['Namshi\Innovate\Http\Response\Redirect'](https://github.com/namshi/innovate/blob/master/src/Namshi/Innovate/Http/Response/Redirect.php)

```php
use Namshi\Innovate\Http\Response\Redirect;

if ($response instanceof Redirect) {
    // build a form
}

```

The response object contains the values ('targetUrl', 'session', 'paReq') which is needed to build the form:

```php
$targetUrl = $response->getTargetUrl();
$session   = $response->getSession();
$pareq     = $response->getPareq();
```

Now, after receiving the previous values, they will be used to build a form as hidden values.


The form will be sent to the `$targetUrl` which is the 3D-secure page as follows:

```html
<form name="acsform" action="[targetUrl from the response]" method="post">
    <input type="hidden" name="PaReq" value="[The pareq data from response]">
    <input type="hidden" name="MD" value="[Any value can be stored here which will be sent back unchanged in the the 3d secure response e.g to preserve the session id which we can send back to complete the transaction]">
    <input type="hidden" name="TermUrl" value="[return URL on your site]">
    <noscript><input type="Submit"></noscript>
</form>
```
'termUrl': the url that we need to direct the user to it after he submit the form.

See an example [ACS (Access Control Server) Form](https://github.com/namshi/innovate/tree/innovate-readme/examples/3d-secured/ACSForm.php)

We need to submit the form with JS:
```
<script>
    function autosub() {
        document.forms['acsform'].submit();
    }
    document.onload=autosub;
</script>
```

Then after the form is submitted the user will be redirected to 3D-Secure page which asks for extra credentials,
then the user submit the 3-D_secure form and gets redirected to 'termUrl' page with two hidden values ('PaRes', 'MD')
which are used to create the final request to innovate:


``` php
$extraData   = array(
    'PaRes'     => 'Authentication response', // we get this value from hidden fields after redirection to termUrl
    'session'   => 'MD', // we get this value from hidden fields after redirection to termUrl
);

$finalResponse   = $client->send($client->createRemoteRequest('POST', Client::INNOVATE_URL, null, null, $extraData));
```

Now we got the final response and we can check if the payment performed or denied:
```php

if ($finalResponse->getStatusCode() === 200) {
    // payment done
} else {
    // Authentication failed
}
```

## Fetching transaction related to a cart id

From 3.0.0 version there is a new method ```Namshi\Innovate\Client::searchTransactionsByCartId``` that let you fetch the transaction given the innovate cart id.

This method will return a ```\SimpleXMLElement``` object containing the answer from innovate or throw an exception (```Namshi\Innovate\Exception\InnovateException```).


## Tests

You can run the test suite by first installing the
dependencies and running PHPUnit:

```
php composer.phar update

phpunit
```

There are a couple integration tests that actually verify that the library
works flawless.
You need  valid credentials for that, just create a file called `.innovate.config`
in your your project directory with 4 parameters:

``` php
<?php

$configs = array(
	'storeId' 		    => 'xxxxx',  // your store Id in Innovate
	'merchantId' 		=> 'xxxxx',  // your merchant Id in Innovate
	'authenticationKey' => 'xxxxxxxxxxx',  // your authentication key
	'searchKey'         => 'xxxxxxxxxxx',  // your search key
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
**P.S. There are no tests for 3d secure transactions.**
