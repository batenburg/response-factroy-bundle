<?php

namespace Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Contract;

use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\RedirectResponse;
use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

interface ResponseFactoryInterface
{

    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function content(string $content, int $status = Response::HTTP_OK, array $headers = []): Response;

    /**
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function noContent(int $status = Response::HTTP_NO_CONTENT, array $headers = []): Response;

    /**
     * @param string $view
     * @return bool
     */
    public function exists(string $view): bool;

    /**
     * @param string $view
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function render(
        string $view,
        array $parameters = [],
        int $status = Response::HTTP_OK,
        array $headers = []
    ): Response;

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect(
        string $url,
        int $status = Response::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse;

    /**
     * @param string $route
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirectToRoute(
        string $route,
        array $parameters = [],
        int $status = Response::HTTP_FOUND,
        array $headers = []
    ): RedirectResponse;

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param bool $json
     * @return JsonResponse
     */
    public function json(
        $data = [],
        int $status = Response::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): JsonResponse;
}
