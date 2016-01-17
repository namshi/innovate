# NAMSHI | Innovate

[![Build Status](https://travis-ci.org/namshi/innovate.svg?branch=tests-on-hhvm)](https://travis-ci.org/namshi/innovate)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3ebdeda0-ce10-4c4e-8917-a93290f4e2a8/mini.png)](https://insight.sensiolabs.com/projects/3ebdeda0-ce10-4c4e-8917-a93290f4e2a8)

This library provides support for the [Innovate](http://www.innovatepayments.com/) payment gateway using [Guzzle](https://github.com/guzzle/guzzle).

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

You can install this library via composer: have a look at the [package on packagist](https://packagist.org/packages/namshi/innovate).

Then include it into your `composer.json`:

```
"namshi/innovate": "1.0.*",
```

Pick major and minor version according to your needs.

## Usage

To use the library you will need an instance of the `Namshi\Innovate\Client` class.
This library will provide also helper classes that describe the data needed by the Innovate api.

``` php
use Namshi\Innovate\Payment\Transaction;
use Namshi\Innovate\Payment\BillingInformation;
use Namshi\Innovate\Payment\Browser;
use Namshi\Innovate\Payment\Billing\Customer;
use Namshi\Innovate\Payment\Billing\Address;
use Namshi\Innovate\Payment\Card;
use Namshi\Innovate\Client;

$client      = new Client('storeid', 'merchantid', 'key', 'searchkey'); // get them from your innovate account

$transaction = new Transaction('sale', 'ecom', true, 'ORDER_NUMBER', 'DESCRIPTION', 'USD', 40, 'AN OPTIONAL REFERENCE TO YOUR TRANSACTION');
$card        = new Card('1234123412341234', '111', new \DateTime($cardExpiryDate));
$customer    = new Customer('Mr', 'John', 'Doe');
$address     = new Address('My address info 1', 'My address info 2', 'My address info 3', 'San Francisco', 'California', 'US', '00000');
$billing     = new BillingInformation($customer, $address, "customers's-email@gmail.com", $customerIpAddress);
$browser     = new Browser($customerUserAgent, $requestAcceptHeader);

$response    = $client->performPayment($transaction, $card, $billing, $browser);
```

If you want you can also inject your own Guzzle client instance (as long as it extendes `\Guzzle\Service\Client`)

```php
$myGuzzleClient = MyGuzzleClient(...);

$client = new Client('storeid', 'merchantid', 'key', 'searchkey', $myGuzzleClient);
```

## How It Works

There are two types of transactions:

* [3-D_Secure](http://en.wikipedia.org/wiki/3-D_Secure) transaction
* Normal transaction

We do not have a way to understand which is which beforehand.
The way we can understand if we are dealing with a 3-D_Secure transaction is by checking the response from Innovate.

```php
use Namshi\Innovate\Http\Response\Redirect;

$response = $client->performPayment($transaction, $card, $billing, $browser);

if ($response instanceOf Redirect) {
    // 3D secure transactions
} else {
    // Normal transactions
}

```

## Authorization Statuses

Any payment will perform an authorization request first and then the payment request.

If the authorization is denied, you will receive a response with a failure message and the [400 Bad Request](http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.1) status code .

## Normal transaction

A normal transaction will follow only the authorization and payment steps already described.
On your end you will only need to check that the status code of the response is a `200`. The library will take care of the rest.

```php
$response = $client->performPayment($transaction, $card, $billing, $browser);

if ($response->getStatusCode() === 200) {
    // payment done
} else {
    // transaction failed
}
```

## 3D secure transaction

A 3D secure transaction requires an additional step compared to the normal transaction.

When the library requests the authorization for a 3D secure transaction, Innovate will answer with a url where the customer should be redirect. The redirection should be done by your application (as described below).

Once the customer is done with the 3D secure step, she'll be redirected back to the application and there you can use this library to perform the actual payment.

*Note*: The library will return an instance of  ['Namshi\Innovate\Http\Response\Redirect'](https://github.com/namshi/innovate/blob/master/src/Namshi/Innovate/Http/Response/Redirect.php) when the transaction is a 3D secure one.

The following example describes the code to perform a 3D secure payment:

```php
use Namshi\Innovate\Http\Response\Redirect;

$response = $client->performPayment($transaction, $card, $billing, $browser);

if ($response instanceof Redirect) {
    // build a form
}

```

The response object will contain the following values:

- `targetUrl`
- `session`
- `paReq`

You will need those as hidden fields to perform the redirect to Innovate:

```php
$targetUrl = $response->getTargetUrl();
$session   = $response->getSession();
$pareq     = $response->getPareq();
```

One simple way to redirect the customer with the right parameters is to build a form and add those values as hidden fields.

The must be sent to the `targetUrl`:

```html
<form name="acsform" action="[targetUrl from the response]" method="post">
    <input type="hidden" name="PaReq" value="[The pareq data from response]">
    <input type="hidden" name="MD" value="[Value that identifies the transaction on our end. It will be sent back unchanged in the the 3d secure response. (i.e.: you could use the session id to complete the transaction]">
    <input type="hidden" name="TermUrl" value="[return URL on your site]">
    <noscript><input type="Submit"></noscript>
</form>
```

*Note*: `termUrl` is  the url where Innovate will redirect the customer after the 3D secure step.

An example of this form is here: [ACS (Access Control Server) Form](https://github.com/namshi/innovate/tree/innovate-readme/examples/3d-secured/ACSForm.php)

The easiest way to submit the form is with Javascript (without asking the customer to do it):
```
<script>
    function autosub() {
        document.forms['acsform'].submit();
    }
    document.onload=autosub;
</script>
```

After the form is submitted the customer will be redirected to the 3D-Secure page which asks for extra credentials. From there she gets redirected to `termUrl` with two values ('PaRes', 'MD'):

We can use this values to perfom the actual payment request:

``` php
$mpiData = array(
    'PaRes'     => $request->get('PaRes'),
    'session'   => $request->get('MD'),
);

$finalResponse = $client->perform3DSecurePayment($transaction, $card, $billingInformation, $browser, $mpiData);
```

The final response lets us check if the payment is successful or denied:

```php

if ($finalResponse->getStatusCode() === 200) {
    // payment done
} else {
    // payment failed
}
```

## Fetching transaction related to a cart id

From 3.0.0 version there is a new method ```Namshi\Innovate\Client::searchTransactionsByCartId``` that let you fetch the transaction given the innovate cart id.

This method will return a ```\SimpleXMLElement``` object containing the answer from innovate or throw an exception (```Namshi\Innovate\Exception\InnovateException```).


## Tokenization of a credit card

With 4.0.0 version we add the ability to:

- request a token from a credit card
- issue a normal transaction using a token instead of a credit card
- issue a 3D Secure transaction using a token instead of a credit card

To request a token you should use the following method

```php

$client = new Client('storeid', 'merchantid', 'key', 'searchkey');

$card = new TokenizedCard('4000000000000002', new \DateTime('2025-5'));
$billing = new TokenizedBillingInfo(
    new Customer('Forenames', 'Surname', 'Mr'),
    new Address('STREET_ADDRESS_LINE_1', 'STREET_ADDRESS_LINE_2', 'STREET_ADDRESS_LINE_3', 'CITY', 'REGION', 'COUNTRY', '12345'),
    'test@namshi.com'
);

$response = $client->tokenize($card, $billing);

/* response body
<?xml version="1.0" encoding="UTF-8"?>
<remote>
  <result>1</result>
  <token>
    <ref>123400012340002</ref>
    <description>Visa Credit ending 0002</description>
  </token>
</remote>
*/
```

To perform a normal transaction or a 3D Secure transaction with a token, the flow is the same described above, the only difference is:

- use an instrance of `Namshi\Innovate\Tokenized\Token` instead of `Namshi\Innovate\Payment\Card`
- use an instrance of `Namshi\Innovate\Tokenized\CustomerInformation` instead of `Namshi\Innovate\Payment\BillingInformation`

## Tests

You can run the test suite by first installing the dependencies and then running PHPUnit:

```
php composer.phar install

./vendor/bin/phpunit -c .
```

There is an integration test that actually verify that the library works flawlessly.

To run it, you need to create a file called `.innovate.config` in your your project directory with 4 parameters

``` php
<?php

$configs = array(
    'storeId'           => 'xxxxx',  // your store Id in Innovate
    'merchantId'        => 'xxxxx',  // your merchant Id in Innovate
    'authenticationKey' => 'xxxxxxxxxxx',  // your authentication key
    'searchKey'         => 'xxxxxxxxxxx',  // your search key
);

// Card info
$cardInfo = array(
    'number'    => '111111111111111',
    'cvv'       => 'XXX',
);

// The card which need redirection for 3d secured
$redirectUrlCardInfo = array(
    'number'    => '111111111111111',
    'cvv'       => 'XXX',

);

// your ip and should be in Innovate white list
$ip = 'xxx.xxx.xxx.xxx';
```

and then run

```
phpunit tests/Namshi/Innovate/Test/Integration/ServiceTest.php
```
