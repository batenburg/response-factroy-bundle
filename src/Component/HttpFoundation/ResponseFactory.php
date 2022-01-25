<?php

namespace Batenburg\ResponseFactoryBundle\Component\HttpFoundation;

use Batenburg\ResponseFactoryBundle\Component\HttpFoundation\Contract\ResponseFactoryInterface;
use SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ResponseFactory implements ResponseFactoryInterface
{

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack)
    {
        $this->twig         = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function content(string $content, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function noContent(int $status = Response::HTTP_NO_CONTENT, array $headers = []): Response
    {
        return $this->content('', $status, $headers);
    }

    /**
     * @param string $view
     * @return bool
     */
    public function exists(string $view): bool
    {
        return $this->twig->getLoader()->exists($view);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param int $status
     * @param array $headers
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(
        string $view,
        array $parameters = [],
        int $status = Response::HTTP_OK,
        array $headers = []
    ): Response {
        return $this->content($this->twig->render($view, $parameters), $status, $headers);
    }

    /**
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return RedirectResponse
     */
    public function redirect(string $url, int $status = Response::HTTP_FOUND, array $headers = []): RedirectResponse
    {
        $response = new RedirectResponse($url, $status, $headers);

        try {
            $session = $this->requestStack->getSession();
            if ($session instanceof Session) {
                $response->setFlashBag($session->getFlashBag());
            }
        } catch (SessionNotFoundException $exception) {
        }

        return $response;
    }

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
    ): RedirectResponse {
        return $this->redirect($this->urlGenerator->generate($route, $parameters), $status, $headers);
    }

    /**
     * @param array $data
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
    ): JsonResponse {
        return new JsonResponse($data, $status, $headers, $json);
    }

    /**
     * @param SplFileInfo|string $file
     * @param array $headers
     * @return BinaryFileResponse
     */
    public function file($file, array $headers = []): BinaryFileResponse
    {
        return new BinaryFileResponse($file, Response::HTTP_OK, $headers);
    }

    /**
     * @param SplFileInfo|string $file
     * @param array $headers
     * @param string|null $fileName
     * @param string $disposition
     * @return BinaryFileResponse
     */
    public function download(
        $file,
        array $headers = [],
        ?string $fileName = null,
        string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT
    ): BinaryFileResponse {
        $response = new BinaryFileResponse($file, Response::HTTP_OK, $headers, true, $disposition);

        $response->setContentDisposition(
            $disposition,
            is_null($fileName) ? $response->getFile()->getFilename() : $fileName
        );

        return $response;
    }
}
