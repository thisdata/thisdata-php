# ThisData API Client

Consume the [ThisData.com](https://thisdata.com/) REST API using this client. Requires at least PHP5.5.

## Install

Using composer:

```
composer require thisdata/api
```

## Quick Start

Use the builder to return an instance of `ThisData\Api\ThisData`, configured with default settings.

```php
use ThisData\Api\Builder;

$apiKey = '<API_KEY>';

$thisData = Builder::create($apiKey);
```

Alternatively, you can provide more customisation by using an instance of the builder.

```
$builder = new Builder($apiKey);
// Configure the builder here. See the Advanced section below for more details.
$thisData = $builder->build();
```

Use the `$thisData` instance to get an instance of an endpoint, e.g. [events](http://help.thisdata.com/docs/apiv1events).

```php
$events = $thisData->getEventsEndpoint();
```

Each endpoint will have different methods, depending on the functionality the endpoint provides. For instance, the
events endpoint can track successful and failed login attempts.

```php
$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => '86',
    'name' => 'Maxwell Smart',
    'email' => 'max@control.com'
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$events->trackLogIn($ip, $user, $userAgent);
$events->trackLogInDenied($ip, $user, $userAgent);
```

Current endpoints supported are:

- [Events](http://help.thisdata.com/docs/apiv1events)

## Advanced

There are several configuration changes that modify the way the library behaves.

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
