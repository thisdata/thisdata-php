# ThisData API Client [![Build Status](https://travis-ci.org/thisdata/thisdata-php.png?branch=master)](https://travis-ci.org/thisdata/thisdata-php)

Consume the [ThisData.com](https://thisdata.com/) REST API using this client. Requires at least PHP5.5.

## Install

Using composer:

```
composer require thisdata/api
```

## Quick Start

Use the factory to return an instance of `ThisData\Api\ThisData`, configured with default settings.

```php
use ThisData\Api\ThisData;

$apiKey = '<API_KEY>';

$thisData = ThisData::create($apiKey);
```

> :warning: Don't commit your API key to source control! Use an environment
  variable, or perhaps you have a configuration solution that allows you to
  store secrets in local configuration without being shared.

Use the `$thisData` instance to get an instance of an endpoint, e.g. [events](http://help.thisdata.com/docs/apiv1events).

```php
$events = $thisData->getEventsEndpoint();
```

Each endpoint will have different methods, depending on the functionality the endpoint provides. For instance, the
events endpoint can track successful login attempts.

```php
$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => 'johntitor',
    'name' => 'John Titor',
    'email' => 'john.titor@thisdata.com',
    'mobile' => '+64270000001'
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$events->trackLogIn($ip, $user, $userAgent);

$verb = 'reset-password-attempt';
$events->trackEvent($verb, $ip, $user, $userAgent);
```

### API Documentation

Documentation for API endpoints can be found here:

- [Events](http://help.thisdata.com/docs/apiv1events)

## Advanced

There are several configuration options that modify the way the library behaves.
By using the Builder you can set these configuration options.

```php
use ThisData\Api\Builder;
$builder = new Builder($apiKey);

// Configure the builder here. See below for more details.
// ...
// e.g. $builder->setAsync(false);
// ...

$thisData = $builder->build();
```

### Synchronous Requests

By default, requests are sent to the server asynchronously. If this is not required, or synchronous communication is
preferred, configure your builder to use synchronous requests.

```php
$builder->setAsync(false);
```

### Network Configuration

If you require more control of your network settings, such as using a proxy, configure the client settings when
building.

```php
$builder->setClientOption('proxy', 'tcp://localhost:8125');
```

If you want to see the verbose output of the HTTP request, enable debug mode in Guzzle.

```php
$builder->setClientOption('debug', true);
```

All settings supported by the [Guzzle HTTP Client](http://docs.guzzlephp.org/en/latest) can be configured here,
including [curl options](http://docs.guzzlephp.org/en/latest/faq.html#how-can-i-add-custom-curl-options).

### Direct Access to the HTTP client

If you require access to directly interact with the Guzzle HTTP client to send custom requests, the configured instance
can be retrieved from an endpoint.

```php
/** @var GuzzleHttp\Client $guzzle */
$guzzle = $events->getClient();
```

Alternatively, you can instantiate the client manually, without the added endpoint abstration layers.

```php
$client = new ThisData\Api\Client('<API_KEY>'); // An extension of the GuzzleHttp\Client class
$client->post('events', ['body' => '{"ip":"127.0.0.1"}']);
```

## Contributing

Thanks for helping! Start by forking the repository. Then make sure the tests pass:

```
composer install
./vendor/bin/phpunit
```

Make your changes, add test coverage, and check the tests are still passing.
Then open a PR! :)