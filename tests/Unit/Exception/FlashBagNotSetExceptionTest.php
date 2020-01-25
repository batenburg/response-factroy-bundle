<?php

namespace Batenburg\ResponseFactoryBundle\Unit\Test\Exception;

use Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException;
use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * @covers \Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException
 */
class FlashBagNotSetExceptionTest extends TestCase
{

    /**
     * @covers \Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException
     */
    public function testTheDefaultException(): void
    {
        // Setup
        $result = new FlashBagNotSetException();
        // Validate
        $this->assertInstanceOf(FlashBagNotSetException::class, $result);
        $this->assertInstanceOf(Exception::class, $result);
        $this->assertSame('Flash bag is not set.', $result->getMessage());
        $this->assertSame(0, $result->getCode());
        $this->assertNull($result->getPrevious());
    }

    /**
     * @dataProvider modelNotFoundExceptionScenarioProvider
     * @covers \Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function testAModelNotFoundExceptionIsSeededProperly(
        string $message = 'Flash bag is not set.',
        int $code = 0,
        Throwable $previous = null
    ): void {
        // Setup
        $result = new FlashBagNotSetException($message, $code, $previous);
        // Validate
        $this->assertInstanceOf(FlashBagNotSetException::class, $result);
        $this->assertInstanceOf(Exception::class, $result);
        $this->assertSame($message, $result->getMessage());
        $this->assertSame($code, $result->getCode());
        $this->assertSame($previous, $result->getPrevious());
    }

    /**
     * @return array
     */
    public function modelNotFoundExceptionScenarioProvider(): array
    {
        return [
            'An exception with a custom message' => [
                'A custom message'
            ],
            'A custom code' => [
                'Flash bag is not set.',
                200
            ],
            'A custom previous' => [
                'Flash bag is not set.',
                0,
                $this->createMock(Throwable::class)
            ]
        ];
    }
}
