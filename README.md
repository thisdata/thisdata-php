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


Each endpoint will have different methods, depending on the functionality the endpoint provides. For instance, the
events endpoint can track successful login attempts.

### API Documentation

Documentation for API endpoints can be found here:

- [POST Events](http://help.thisdata.com/docs/apiv1events)
- [POST Verify](http://help.thisdata.com/docs/apiv1verify)
- [GET Events](http://help.thisdata.com/docs/v1getevents)

### Tracking Events

Use the `$thisData` instance to get an instance of the events endpoint.

```php
$endpoint = $thisData->getEventsEndpoint();
```

Then track events:

```php
$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => 'johntitor',
    'name' => 'John Titor',
    'email' => 'john.titor@thisdata.com',
    'mobile' => '+64270000001'
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$endpoint->trackLogIn($ip, $user, $userAgent);

$verb = 'my-custom-verb';
$endpoint->trackEvent($verb, $ip, $user, $userAgent);
```

When the login attempt is unsuccessful:

```php
$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => 'johntitor',
    'authenticated' => false
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$endpoint->trackEvent(EventsEndpoint::VERB_LOG_IN_DENIED, $ip, $user, $userAgent);
```

When you're using a multi-tenanted app:

```php
$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => 'johntitor'
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$source = [
    'name' => 'SubCustomer 1'
]
$endpoint->trackLogIn($ip, $user, $userAgent, $source);
```

### Verifying a User

When a sensitive action is about to occur, perhaps before finalizing the
log-in process, you can verify that the user is who they say they are, and check
the risk that their account is being impersonated by an attacker.
If they present a medium or high risk, force them to provide a two-factor
authentication code.


```php
$endpoint = $thisData->getVerifyEndpoint();

$ip = $_SERVER['REMOTE_ADDR'];
$user = [
    'id' => 'johntitor'
];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$response = $endpoint->verify($ip, $user, $userAgent);

if ($response['risk_level'] != "green") {
    # Ask for Two Factor Authentication code
} else {
    # Everything is OK
}
```

### Getting a list of Events (Audit Log)

You can fetch a paginated, filterable array of Events from the API. The method
accepts any of the parameters described in the documentation: http://help.thisdata.com/docs/v1getevents

e.g. returning the 10 most recent `log-in` events for a user

```php
$endpoint = $thisData->getEventsEndpoint();
$events = $endpoint->getEvents(["verb" => "log-in", "user_id" => 112233, "limit" => 10]);
```


## Advanced

There are several configuration options that modify the way the library behaves.
By using the Builder you can set these configuration options.

```php
use ThisData\Api\Builder;
$builder = new Builder($apiKey);

// Configure the builder here. See below for more details.
// ...
// e.g. $builder->setAsync(false);
// $builder->setExpectJsCookie(true);
// ...

$thisData = $builder->build();
```

### Synchronous Requests

By default, requests are sent to the server asynchronously. If this is not required, or synchronous communication is
preferred, configure your builder to use synchronous requests.

```php
$builder->setAsync(false);
```

### ThisData's Javascript library

ThisData's Javascript tracking code is optional, but when included in a page
will add a cookie called `__tdli`. If you're using the optional javascript
library, this library will automatically pick up that cookie.
You should also tell the API that each request _should_ come with a cookie:

```php
$builder->setExpectJsCookie(true);
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