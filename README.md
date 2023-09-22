# Fansipan Mock HTTP Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Github Actions][ico-gh-actions]][link-gh-actions]
[![Codecov][ico-codecov]][link-codecov]
[![Total Downloads][ico-downloads]][link-downloads]
[![Software License][ico-license]](LICENSE.md)

Fansipan mock HTTP client is [PSR-18 Client](https://www.php-fig.org/psr/psr-18/) implementation that provides ability send test requests with fake responses.

The `MockClient` accepts `Psr\Http\Message\ResponseInterface` which when used on a request, will respond with a fake response without actually sending a real request to the web. This helps speed up tests massively and can help you test your application for different API response scenarios, like a 404 error or 500 error.

## Installation

You can install the package via composer:

```bash
composer require fansipan/mock-client
```

## Usage

### Creating Mock Client

```php
use Fansipan\Mock\MockClient;

$client = new MockClient();
$response = $client->sendRequest($request);
```

By default `MockClient` will always return `200 - OK` status code with empty body. If you want to return a different response, create a `Psr\Http\Message\ResponseInterface` instance and pass it as constructor argument. You can use `MockResponse` to quickly create a fake response.

```php
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;

$client = new MockClient(MockResponse::create('', 500));
```

### Faking Response

The `MockResponse` class is used to create fake responses. It can accept a body, status, and headers. These properties will be populated in the fake response. The response body accepts an array for a JSON body or plain strings to simulate other responses, like XML.

```php
use Fansipan\Mock\MockResponse;

MockResponse::create(['name' => 'John', 'age' => 30], 201, ['X-Custom-Header' => 'foo']);
```

> You don't have to add `['Content-Type' => 'application/json']` header if your body is array.

If you have fixture data and don't want to create response manually, you can also use `fixture` method to create a response.

```php
use Fansipan\Mock\MockResponse;

MockResponse::fixture(__DIR__.'/fixtures/user.json');
```

> If your fixture is a JSON or XML file, there's no need to add the `Content-Type` header manually.

#### Faking Response Sequences

Sequence faking allows you to define a number of fake responses in a specific order. It will pull out the next response in the sequence, removing it from the sequence. Each response can only be consumed once. When all the responses in a response sequence have been consumed, any further requests will cause the response sequence to throw an exception.

```php
use Fansipan\Mock\MockClient;
use Fansipan\Mock\MockResponse;

$client = new MockClient([
    MockResponse::make(['name' => 'foo'], 200),
    MockResponse::make(['name' => 'bar'], 201),
    MockResponse::make(['error' => 'Server Error'], 500),
]);

$client->sendRequest($firstRequest); // Will return with `['name' => 'foo']` and status `200`
$client->sendRequest($secondRequest); // Will return with `['name' => 'bar']` and status `200`
$client->sendRequest($thirdRequest); // Will return with `['error' => 'Server Error']` and status `500`
```
### Faking Specific URLs

Alternatively, you may use `ScopingMockClient` and pass an array to the constructor argument. The array's keys should represent URL patterns that you wish to fake and their associated responses. The `*` character may be used as a wildcard character. Any requests made to URLs that have not been faked will actually be executed.


```php
use Fansipan\Mock\MockResponse;
use Fansipan\Mock\ScopingMockClient;

new ScopingMockClient([
    // Stub a JSON response for GitHub endpoints...
    'github.com/*' => MockResponse::create(['foo' => 'bar'], 200),

    // Stub a string response for Google endpoints...
    'google.com/*' => MockResponse::create('Hello World', 200, $headers),

    // Stub a string response for all other endpoints...
    '*' => MockResponse::create('Hello World', 200, $headers),
]);
```

[Sequence Faking](#faking-response-sequences) also works with `ScopingMockClient`

```php
use Fansipan\Mock\MockResponse;
use Fansipan\Mock\ScopingMockClient;

new ScopingMockClient([
    // Stub sequence JSON responses for GitHub endpoints...
    'github.com/*' => [
        MockResponse::create(['foo' => 'bar']),
        MockResponse::create(['error' => 'Server Error'], 500),
    ],

    // Stub sequence responses for Google endpoints...
    'google.com/*' => [
        MockResponse::create('Hello World', 200, $headers),
        MockResponse::create(['baz' => 'qux']),
    ],
]);
```

### Adding Expectations

When using faking responses, it's important to be able to check that a specific make request was sent and with the correct data, and headers. `MockClient` & `ScopingMockClient` provide you with various ways to add expectations to your tests.

#### Available Expectations
- `assertSent`
- `assetNotSend`
- `assertNothingSent`
- `assertSentCount`

The `assertSent` / `assertNotSent` are the two most powerful expectation methods. They can accept a URL pattern or even a closure where you define if a request/response is what you expect.

```php
use Fansipan\Mock\MockClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

$client = new MockClient();

$request = $requestFactory->createRequest('GET', 'http://example.com/users/1');
$client->sendRequest($request);

$client->assertSent('users/*');

$client->assertSent(function (RequestInterface $request, ResponseInterface $response): bool {
    return $request->getMethod() === 'GET'
        && (string) $request->getUri() === 'http://example.com/users/1'
        && $response->getStatusCode() === 200;
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email jenky.w0w@gmail.com instead of using the issue tracker.

## Credits

- [Lynh](https://github.com/jenky)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/fansipan/mock-client.svg?style=for-the-badge
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge
[ico-gh-actions]: https://img.shields.io/github/actions/workflow/status/phanxipang/mock-client/testing.yml?branch=main&label=actions&logo=github&style=for-the-badge
[ico-codecov]: https://img.shields.io/codecov/c/github/phanxipang/mock-client?logo=codecov&style=for-the-badge
[ico-downloads]: https://img.shields.io/packagist/dt/fansipan/mock-client.svg?style=for-the-badge

[link-packagist]: https://packagist.org/packages/fansipan/mock-client
[link-gh-actions]: https://github.com/jenky/phanxipang/mock-client/actions
[link-codecov]: https://codecov.io/gh/phanxipang/mock-client
[link-downloads]: https://packagist.org/packages/fansipan/mock-client

