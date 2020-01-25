<?php

namespace Batenburg\ResponseFactoryBundle\Component\HttpFoundation;

use Batenburg\ResponseFactoryBundle\Exception\FlashBagNotSetException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class RedirectResponse extends BaseRedirectResponse
{

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @param FlashBagInterface $flashBag
     * @return $this
     */
    public function setFlashBag(FlashBagInterface $flashBag): self
    {
        $this->flashBag = $flashBag;

        return $this;
    }

    /**
     * @param string $type
     * @param mixed $message
     * @return $this
     * @throws FlashBagNotSetException
     */
    public function withMessage(string $type, $message): self
    {
        if (is_null($this->flashBag)) {
            throw new FlashBagNotSetException();
        }

        $this->flashBag->add($type, $message);

        return $this;
    }

    /**
     * @param array $input
     * @return $this
     * @throws FlashBagNotSetException
     */
    public function withInput(array $input): self
    {
        if (is_null($this->flashBag)) {
            throw new FlashBagNotSetException();
        }

        $this->flashBag->add('_old_inputs', $input);

        return $this;
    }

    /**
     * @param Cookie $cookie
     * @return $this
     */
    public function withCookie(Cookie $cookie): self
    {
        $this->headers->setCookie($cookie);

        return $this;
    }
}
