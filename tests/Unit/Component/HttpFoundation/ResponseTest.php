<?php

namespace Tests\Batenburg\ResponseFactoryBundle\Unit\Component\HttpFoundation;

use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response
 */
class ResponseTest extends TestCase
{
    /**
     * @var Response
     */
    private $response;

    public function setUp(): void
    {
        $this->response = new Response();
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response
     */
    public function testItExtendsTheSymfonyResponse(): void
    {
        // Validate
        $this->assertInstanceOf(SymfonyResponse::class, $this->response);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response::withCookie
     */
    public function testACookieCanBeSet(): void
    {
        // Setup
        $name   = 'test.cookie';
        $value  = 'valid';
        $cookie = Cookie::create($name, $value);
        // Execute
        $result = $this->response->withCookie($cookie);
        // Validate
        $this->assertInstanceOf(Response::class, $result);
        $this->assertTrue($result->headers->has('set-cookie'));
        $this->assertCount(1, $result->headers->getCookies());
        $this->assertEquals($name, $result->headers->getCookies()[0]->getName());
        $this->assertEquals($value, $result->headers->getCookies()[0]->getValue());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response::withHeader
     */
    public function testAHeaderCanBeSet(): void
    {
        // Setup
        $name  = 'status';
        $value = 200;
        // Execute
        $result = $this->response->withHeader($name, $value);
        // Validate
        $this->assertInstanceOf(Response::class, $result);
        $this->assertTrue($result->headers->has($name));
        $this->assertEquals($value, $result->headers->get($name));
    }
}
