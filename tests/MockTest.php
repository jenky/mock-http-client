<?php

declare(strict_types=1);

namespace Jenky\Atlas\Tests;

use Jenky\Atlas\Mock\MockClient;
use Jenky\Atlas\Mock\MockResponse;
use Jenky\Atlas\Mock\ScopingMockClient;
use Jenky\Atlas\Mock\Uri;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

final class MockTest extends TestCase
{
    /**
     * @var \Psr\Http\Message\factoryInterface
     */
    private $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Psr17Factory();
    }

    public function test_fake_default_response(): void
    {
        $client = new MockClient();

        $request = $this->factory->createRequest('GET', 'https://example.com');

        $response = $client->sendRequest($request);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_fake_failed_response(): void
    {
        $client = new MockClient($this->factory->createResponse(500));

        $request = $this->factory->createRequest('GET', 'https://example.com');

        $response = $client->sendRequest($request);

        $this->assertSame(500, $response->getStatusCode());
    }

    public function test_fake_sequence_responses(): void
    {
        $client = new MockClient([
            MockResponse::create(['ok' => true]),
            MockResponse::create(['error' => 'Unauthenticated'], 401),
            $this->factory->createResponse(502),
        ]);

        $request1 = $this->factory->createRequest('GET', 'https://example.com');
        $request2 = $this->factory->createRequest('POST', 'https://github.com');
        $request3 = $this->factory->createRequest('PUT', 'https://google.com');

        $response1 = $client->sendRequest($request1);
        $response2 = $client->sendRequest($request2);

        $body1 = json_decode((string) $response1->getBody(), true);
        $body2 = json_decode((string) $response2->getBody(), true);

        $this->assertSame(200, $response1->getStatusCode());
        $this->assertTrue($body1['ok'] ?? null);

        $this->assertSame(401, $response2->getStatusCode());
        $this->assertSame('Unauthenticated', $body2['error'] ?? '');

        $this->assertSame(502, $client->sendRequest($request3)->getStatusCode());

        $client->assertSent('https://example.com');
        $client->assertSent('https://github.com');
        $client->assertSent('https://google.com');
    }

    public function test_fake_conditional_responses(): void
    {
        $client = new ScopingMockClient([
            'jsonplaceholder.typicode.com/users/*' => MockResponse::fixture(__DIR__.'/fixtures/user.json'),
            'postman-echo.com/cookies*' => MockResponse::create('', 400),
            '*' => MockResponse::create('', 200),
        ]);

        $request = $this->factory->createRequest('GET', 'https://postman-echo.com/get');
        $this->assertSame(200, $client->sendRequest($request)->getStatusCode());

        $request = $this->factory->createRequest('GET', 'https://postman-echo.com/cookies');
        $this->assertSame(400, $client->sendRequest($request)->getStatusCode());

        $client->assertSent(function (RequestInterface $request): bool {
            return $request->getMethod() === 'GET' && Uri::matches('/cookies', (string) $request->getUri());
        });

        $client->assertNotSent('/users/*');

        $request = $this->factory->createRequest('GET', 'jsonplaceholder.typicode.com/users/1');
        $response = $client->sendRequest($request);
        $body = json_decode((string) $response->getBody(), true);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Leanne Graham', $body['name'] ?? '');
        $this->assertSame('Bret', $body['username'] ?? '');
    }
}
