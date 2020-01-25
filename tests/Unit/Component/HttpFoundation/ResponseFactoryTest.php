<?php

namespace Tests\Batenburg\ResponseFactoryBundle\Unit\Component\HttpFoundation;

use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Contract\ResponseFactoryInterface;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory;
use Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\LoaderInterface;

/**
 * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory
 */
class ResponseFactoryTest extends TestCase
{

    /**
     * @var MockObject|Environment
     */
    private $twig;

    /**
     * @var MockObject|UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var MockObject|FlashBagInterface
     */
    private $flashBag;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function setUp(): void
    {
        parent::setUp();

        $this->twig            = $this->createMock(Environment::class);
        $this->urlGenerator    = $this->createMock(UrlGeneratorInterface::class);
        $this->flashBag        = $this->createMock(FlashBagInterface::class);
        $this->responseFactory = new ResponseFactory($this->twig, $this->urlGenerator, $this->flashBag);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory
     */
    public function testAResponseFactoryImplementTheResponseFactoryInterface(): void
    {
        // Validate
        $this->assertInstanceOf(ResponseFactoryInterface::class, $this->responseFactory);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::content
     */
    public function testWeRetrieveAResponseFromTheContent(): void
    {
        // Setup
        $content = 'Some awesome content!';
        // Execute
        $result = $this->responseFactory->content($content);
        // Validate
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals($content, $result->getContent());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::content
     */
    public function testWeCanSetACustomResponseCodeOnContent(): void
    {
        // Setup
        $content      = 'Some awesome content!';
        $responseCode = 418;
        // Execute
        $result = $this->responseFactory->content($content, $responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::content
     */
    public function testWeCanSetACustomHeaderOnContent(): void
    {
        // Setup
        $content      = 'Some awesome content!';
        $responseCode = 200;
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        // Execute
        $result = $this->responseFactory->content($content, $responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::noContent
     */
    public function testWhenCallingNoContentWeGetAResponseWithNoContent(): void
    {
        // Execute
        $result = $this->responseFactory->noContent();
        // Validate
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(204, $result->getStatusCode());
        $this->assertEquals('', $result->getContent());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::noContent
     */
    public function testWeCanSetACustomResponseCodeOnNoContent(): void
    {
        // Setup
        $responseCode = 418;
        // Execute
        $result = $this->responseFactory->noContent($responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::noContent
     */
    public function testWeCanSetACustomHeaderOnNoContent(): void
    {
        // Setup
        $responseCode = 204;
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        // Execute
        $result = $this->responseFactory->noContent($responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::exists
     */
    public function testExists(): void
    {
        // Setup
        $view = 'template.html.twig';
        $loader = $this->createMock(LoaderInterface::class);
        $this->twig->expects($this->once())
            ->method('getLoader')
            ->willReturn($loader);
        $loader->expects($this->once())
            ->method('exists')
            ->with($view)
            ->willReturn(true);
        // Execute
        $result = $this->responseFactory->exists($view);
        // Validate
        $this->assertTrue($result);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::render
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testWeCanRenderTheContent(): void
    {
        // Setup
        $view         = 'launchdesk.index';
        $parameters   = [];
        $twigResponse = 'Some awesome content!';
        $this->twig->expects($this->once())
            ->method('render')
            ->with($view, $parameters)
            ->willReturn($twigResponse);
        // Execute
        $result = $this->responseFactory->render($view, $parameters);
        // Validate
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals($twigResponse, $result->getContent());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::render
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testWeCanSetACustomResponseCodeOnRender(): void
    {
        // Setup
        $responseCode = 418;
        $view         = 'launchdesk.index';
        $parameters   = [];
        $twigResponse = 'Some awesome content!';
        $this->twig->expects($this->once())
            ->method('render')
            ->with($view, $parameters)
            ->willReturn($twigResponse);
        // Execute
        $result = $this->responseFactory->render($view, $parameters, $responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::render
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function testWeCanSetACustomHeaderOnRender(): void
    {
        // Setup
        $responseCode = 200;
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        $view         = 'launchdesk.index';
        $parameters   = [];
        $twigResponse = 'Some awesome content!';
        $this->twig->expects($this->once())
            ->method('render')
            ->with($view, $parameters)
            ->willReturn($twigResponse);
        // Execute
        $result = $this->responseFactory->render($view, $parameters, $responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirect
     */
    public function testWeCanRedirectARequest(): void
    {
        // Setup
        $url = 'https://admin.launchdesk.dev';
        // Execute
        $result = $this->responseFactory->redirect($url);
        // Validate
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals($url, $result->getTargetUrl());
        $this->assertEquals(302, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirect
     */
    public function testWeCanSetACustomResponseCodeOnRedirect(): void
    {
        // Setup
        $responseCode = 301;
        $url          = 'https://admin.launchdesk.dev';
        // Execute
        $result = $this->responseFactory->redirect($url, $responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirect
     */
    public function testWeCanNotSetACustomResponseWhichIsNotARedirect(): void
    {
        // Expectations
        $this->expectException(InvalidArgumentException::class);
        // Setup
        $responseCode = 418;
        $url          = 'https://admin.launchdesk.dev';
        // Execute
        $this->responseFactory->redirect($url, $responseCode);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirect
     */
    public function testWeCanSetACustomHeaderOnRedirect(): void
    {
        // Setup
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        $responseCode = 302;
        $url          = 'https://admin.launchdesk.dev';
        // Execute
        $result = $this->responseFactory->redirect($url, $responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirect
     * @throws FlashBagNotSetException
     */
    public function testWeCanSetAMessageOnARedirect(): void
    {
        // Setup
        $type    = 'success';
        $message = 'this test passed!';
        $url     = 'https://admin.launchdesk.dev';
        // Expectations
        $this->flashBag->expects($this->once())
            ->method('add')
            ->with($type, $message);
        // Execute
        $this->responseFactory->redirect($url)
            ->withMessage($type, $message);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirectToRoute
     */
    public function testWeCanRedirectToRoute(): void
    {
        // Setup
        $route      = 'dashboard';
        $parameters = [];
        $url        = 'https://admin.launchdesk.dev/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn($url);
        // Execute
        $result = $this->responseFactory->redirectToRoute($route, $parameters);
        // Validate
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertEquals($url, $result->getTargetUrl());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirectToRoute
     */
    public function testWeCanSetACustomResponseCodeOnRedirectToRoute(): void
    {
        // Setup
        $route        = 'dashboard';
        $parameters   = [];
        $responseCode = 301;
        $url          = 'https://admin.launchdesk.dev/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn($url);
        // Execute
        $result = $this->responseFactory->redirectToRoute($route, $parameters, $responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirectToRoute
     */
    public function testWeCanNotSetACustomResponseWhichIsNotARedirectToRoute(): void
    {
        // Expectations
        $this->expectException(InvalidArgumentException::class);
        // Setup
        $route        = 'dashboard';
        $parameters   = [];
        $responseCode = 418;
        $url          = 'https://admin.launchdesk.dev/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn($url);
        // Execute
        $this->responseFactory->redirectToRoute($route, $parameters, $responseCode);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirectToRoute
     */
    public function testWeCanSetACustomHeaderOnRedirectToRoute(): void
    {
        // Setup
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        $responseCode = 302;
        $route        = 'dashboard';
        $parameters   = [];
        $url          = 'https://admin.launchdesk.dev/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn($url);
        // Execute
        $result = $this->responseFactory->redirectToRoute($route, $parameters, $responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::redirectToRoute
     * @throws FlashBagNotSetException
     */
    public function testWeCanSetAMessageOnARedirectToRoute(): void
    {
        // Setup
        $type       = 'success';
        $message    = 'this test passed!';
        $route      = 'dashboard';
        $parameters = [];
        $url        = 'https://admin.launchdesk.dev/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route, $parameters)
            ->willReturn($url);
        // Expectations
        $this->flashBag->expects($this->once())
            ->method('add')
            ->with($type, $message);
        // Execute
        $this->responseFactory->redirectToRoute($route, $parameters)
            ->withMessage($type, $message);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::json
     */
    public function testWeCanGetAJsonResponse(): void
    {
        // Setup
        $data               = ['json' => 'some json content'];
        $expectedJsonString = '{"json":"some json content"}';
        // Execute
        $result = $this->responseFactory->json($data);
        // Validate
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals($expectedJsonString, $result->getContent());
        $this->assertEquals(200, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::json
     */
    public function testWeCanSetACustomResponseOnJson(): void
    {
        // Setup
        $data         = ['json' => 'some json content'];
        $responseCode = 418;
        // Execute
        $result = $this->responseFactory->json($data, $responseCode);
        // Validate
        $this->assertEquals($responseCode, $result->getStatusCode());
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\ResponseFactory::json
     */
    public function testWeCanSetACustomHeaderOnJson(): void
    {
        // Setup
        $data         = ['json' => 'some json content'];
        $responseCode = 200;
        $headerKey    = 'custom';
        $headerValue  = 'header';
        $headers      = [$headerKey => $headerValue];
        // Execute
        $result = $this->responseFactory->json($data, $responseCode, $headers);
        // Validate
        $this->assertArrayHasKey($headerKey, $result->headers->all());
        $this->assertEquals($headerValue, $result->headers->get($headerKey));
    }
}
