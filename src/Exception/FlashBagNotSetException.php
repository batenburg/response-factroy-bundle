<?php

namespace Batenburg\ResponseFactoryBundle\Exception;

use Exception;
use Throwable;

class FlashBagNotSetException extends Exception
{

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'Flash bag is not set.', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
