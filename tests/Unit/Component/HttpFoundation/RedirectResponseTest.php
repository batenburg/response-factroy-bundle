<?php

namespace Tests\Batenburg\ResponseFactoryBundle\Unit\Component\HttpFoundation;

use Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse
 */
class RedirectResponseTest extends TestCase
{

    /**
     * @var RedirectResponse
     */
    private $redirectResponse;

    public function setUp(): void
    {
        parent::setUp();

        $this->redirectResponse = new RedirectResponse('redirect');
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse
     */
    public function testARedirectResponseExtendsTheSymfonyRedirectResponse(): void
    {
        // Validate
        $this->assertInstanceOf(SymfonyRedirectResponse::class, $this->redirectResponse);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::setFlashBag
     */
    public function testAFlashBagCanBeInjected(): void
    {
        // Setup
        $flashBag = $this->createMock(FlashBagInterface::class);
        // Execute
        $result = $this->redirectResponse->setFlashBag($flashBag);
        // Validate
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::withMessage
     * @throws FlashBagNotSetException
     */
    public function testWhenWeTryToSetAMessageWithoutAFlashBagWeExpectAnException(): void
    {
        // Expectations
        $this->expectException(FlashBagNotSetException::class);
        // Execute
        $this->redirectResponse->withMessage('error', 'not set');
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::withMessage
     * @throws FlashBagNotSetException
     */
    public function testAMessageCanBeSetWhenAFlashBagIsSet(): void
    {
        // Setup
        $type     = 'success';
        $message  = 'we can pass a message';
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->redirectResponse->setFlashBag($flashBag);
        // Expectations
        $flashBag->expects($this->once())
            ->method('add')
            ->with($type, $message);
        // Execute
        $result = $this->redirectResponse->withMessage($type, $message);
        // Validation
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::withInput
     * @throws FlashBagNotSetException
     */
    public function testWhenTryToSetTheOldInputAndTheFlashBagIsNotSetItThrowsAnException(): void
    {
        // Expectations
        $this->expectException(FlashBagNotSetException::class);
        // Execute
        $this->redirectResponse->withInput(['name' => 'Skepp B.V.']);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::withInput
     * @throws FlashBagNotSetException
     */
    public function testWeCanSetTheOldInputToTheFlashBag(): void
    {
        // Setup
        $input    = ['name' => 'Skepp B.V.'];
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->redirectResponse->setFlashBag($flashBag);
        // Expectations
        $flashBag->expects($this->once())
            ->method('add')
            ->with('_old_inputs', $input);
        // Execute
        $result = $this->redirectResponse->withInput($input);
        // Validate
        $this->assertInstanceOf(RedirectResponse::class, $result);
    }

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse::withCookie
     */
    public function testACookieCanBeSet(): void
    {
        // Setup
        $name   = 'test.cookie';
        $value  = 'valid';
        $cookie = Cookie::create($name, $value);
        // Execute
        $result = $this->redirectResponse->withCookie($cookie);
        // Validate
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertTrue($result->headers->has('set-cookie'));
        $this->assertCount(1, $result->headers->getCookies());
        $this->assertEquals($name, $result->headers->getCookies()[0]->getName());
        $this->assertEquals($value, $result->headers->getCookies()[0]->getValue());
    }
}
