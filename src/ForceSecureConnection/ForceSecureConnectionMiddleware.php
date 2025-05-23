<?php

declare(strict_types=1);

namespace Yiisoft\HttpMiddleware\ForceSecureConnection;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Redirects insecure requests from HTTP to HTTPS and adds headers necessary to enhance the security policy.
 *
 * Middleware adds an HTTP `Strict-Transport-Security` (HSTS) header to each response. This header tells the browser
 * that your site works with HTTPS only.
 *
 * The `Content-Security-Policy` (CSP) header can force the browser to load page resources only through a secure
 * connection, even if links in the page layout are specified with an unprotected protocol.
 *
 * Note: Prefer forcing HTTPS via web server in case you aren't creating an installable product such as CMS and aren't
 * hosting the project on a server where you don't have access to web server configuration.
 */
final class ForceSecureConnectionMiddleware implements MiddlewareInterface
{
    public const DEFAULT_CSP_HEADER = 'upgrade-insecure-requests; default-src https:';

    /**
     * @param ResponseFactoryInterface $responseFactory The response factory to create responses.
     * @param Redirection|null $redirection The redirection from HTTP to HTTPS.
     * @param string|null $cspHeader The `Content-Security-Policy` header to be added to the response.
     * @param HstsHeader|null $hstsHeader The `Strict-Transport-Security` header to be added to the response.
     */
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ?Redirection $redirection = new Redirection(),
        private readonly ?string $cspHeader = self::DEFAULT_CSP_HEADER,
        private readonly ?HstsHeader $hstsHeader = new HstsHeader(),
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->shouldRedirect($request)) {
            $response = $this->createRedirectResponse($request, $this->redirection);
            return $this->addHsts($response);
        }

        $response = $handler->handle($request);
        $response = $this->addCsp($response);
        return $this->addHsts($response);
    }

    /**
     * @psalm-assert-if-true !null $this->redirection
     */
    private function shouldRedirect(ServerRequestInterface $request): bool
    {
        return $this->redirection !== null && strcasecmp($request->getUri()->getScheme(), 'http') === 0;
    }

    private function createRedirectResponse(
        ServerRequestInterface $request,
        Redirection $redirection,
    ): ResponseInterface {
        $url = (string) $request->getUri()->withScheme('https')->withPort($redirection->port);
        return $this->responseFactory
            ->createResponse($redirection->statusCode)
            ->withHeader('Location', $url);
    }

    private function addCsp(ResponseInterface $response): ResponseInterface
    {
        if ($this->cspHeader === null) {
            return $response;
        }
        return $response->withHeader('Content-Security-Policy', $this->cspHeader);
    }

    private function addHsts(ResponseInterface $response): ResponseInterface
    {
        if ($this->hstsHeader === null) {
            return $response;
        }
        return $response->withHeader(
            'Strict-Transport-Security',
            $this->hstsHeader->getValue(),
        );
    }
}
