<?php

declare(strict_types=1);

namespace Jenky\Atlas\Mock;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ScopingMockClient implements ClientInterface
{
    use AssertTrait;

    /**
     * @var null|iterable<ResponseInterface>|ResponseInterface|ResponseInterface[]
     */
    private $defaultResponse = null;

    /**
     * @var array<string, mixed>
     */
    private $conditionalResponses = [];

    /**
     * @var array<string, ClientInterface>
     */
    private $cachedClients = [];

    public function __construct(iterable $responses)
    {
        $this->setResponses($responses);
    }

    /**
     * Set the responses.
     */
    public function setResponses(iterable $responses): void
    {
        foreach ($responses as $key => $response) {
            if (! is_string($key)) {
                continue;
            }

            $this->addResponse($key, $response);
        }
    }

    /**
     * Add an response with a condition.
     *
     * @param  iterable<ResponseInterface>|ResponseInterface|ResponseInterface[] $response
     */
    public function addResponse(string $condition, $response): void
    {
        if ($condition === '*') {
            $this->defaultResponse = $response;
        } else {
            $this->conditionalResponses[$condition] = $response;
        }
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        foreach ($this->conditionalResponses as $scope => $responseFactory) {
            if (Uri::matches($scope, (string) $request->getUri())) {
                return $this->sendAndRecord($request, $responseFactory, $scope);
            }
        }

        return $this->sendAndRecord($request, $this->defaultResponse);
    }

    /**
     * @param  null|iterable<ResponseInterface>|ResponseInterface|ResponseInterface[] $responseFactory
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    private function sendAndRecord(RequestInterface $request, $responseFactory, ?string $scope = null): ResponseInterface
    {
        if (is_null($scope)) {
            $client = $this->createMockClient($responseFactory);
        } else {
            if (empty($this->cachedClients[$scope])) {
                $this->cachedClients[$scope] = $this->createMockClient($responseFactory);
            }

            $client = $this->cachedClients[$scope];
        }

        $response = $client->sendRequest($request);

        $this->record($request, $response);

        return $response;
    }

    /**
     * Create a mock client.
     *
     * @param  null|iterable<ResponseInterface>|ResponseInterface|ResponseInterface[] $response
     */
    private function createMockClient($response): MockClient
    {
        return new MockClient($response);
    }
}
