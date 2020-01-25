<?php

namespace Batenburg\ResponseFactoryBundle\Component\HttpFoundation;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Response extends BaseResponse
{

    /**
     * @param Cookie $cookie
     * @return $this
     */
    public function withCookie(Cookie $cookie): self
    {
        $this->headers->setCookie($cookie);

        return $this;
    }

    /**
     * @param string $key
     * @param string|int|float $value
     * @return $this
     */
    public function withHeader(string $key, $value): self
    {
        $this->headers->set($key, $value);

        return $this;
    }
}
