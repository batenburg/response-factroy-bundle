<?php

namespace Batenburg\ResponseFactoryBundle\Test\Unit;

use Batenburg\ResponseFactoryBundle\ResponseFactoryBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @covers \Batenburg\ResponseFactoryBundle\ResponseFactoryBundle
 */
class ResponseFactoryBundleTest extends TestCase
{

    /**
     * @covers \Batenburg\ResponseFactoryBundle\ResponseFactoryBundle
     */
    public function testIsBundle(): void
    {
        // Setup
        $result = new ResponseFactoryBundle();
        // Validate
        $this->assertInstanceOf(ResponseFactoryBundle::class, $result);
        $this->assertInstanceOf(Bundle::class, $result);
    }
}
