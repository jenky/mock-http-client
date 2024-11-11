<?php

declare(strict_types=1);

namespace Fansipan\Mock;

use Fansipan\RequestMatcher\RequestMatcherInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

if (! \interface_exists(RequestMatcherInterface::class)) {
    throw new \LogicException('You cannot use the "Fansipan\Mock\RequestMatcher" as the "fansipan/request-matcher" package is not installed. Try running "composer require fansipan/request-matcher".');
}

final class RequestMatcher
{
    /**
     * @var RequestMatcherInterface
     */
    private $matcher;

    public function __construct(RequestMatcherInterface $matcher)
    {
        $this->matcher = $matcher;
    }

    public function __invoke(RequestInterface $request, ResponseInterface $response): bool
    {
        return $this->matcher->matches($request);
    }
}
