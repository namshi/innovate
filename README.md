# NAMSHI | Innovate

This library provides support for Innovate using "Guzzle" client

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
