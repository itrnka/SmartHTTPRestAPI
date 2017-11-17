<?php
declare(strict_types=1);

namespace ha\SmartHTTPRestAPI;

use ha\Access\HTTP\Error\HTTP405Error;
use ha\Access\HTTP\Router\Route\HTTPRoute;

abstract class AbstractSmartController implements SmartController
{

    /** @var HTTPRoute */
    protected $route;

    /**
     * Constructor.
     *
     * @param HTTPRoute $route
     */
    final public function __construct(HTTPRoute $route)
    {
        $this->route = $route;
        $this->bootstrap();
    }

    /**
     * Bootstrap controller after construct.
     */
    abstract protected function bootstrap() : void;

    /**
     * Checks whether a request method in input request is allowed. Throws a HTTP405Error if is not allowed.
     *
     * @param string[] $allowedMethods
     *
     * @throws \Error
     * @throws \ha\Access\HTTP\Error\HTTP405Error
     */
    protected function checkAllowedRequestMethods(array $allowedMethods) : void
    {
        try {
            if (!count($allowedMethods)) {
                throw new \Error('Empty array $allowedMethods');
            }
            foreach ($allowedMethods AS $allowedMethod) {
                if ($this->route->getRequest()->typeof($allowedMethod)) {
                    return;
                }
            }
        } catch (\Exception $e) {
            throw new \Error('Invalid $allowedMethod', null, $e);
        }
        throw new HTTP405Error(['Allow: '.implode(',', $allowedMethods)]);
    }

    /**
     * Returns true, if client accept one or more mime types from entered list in response, false otherwise.
     *
     * @param string[] $mimeList Accept mime types list, example: ['text/html', 'text/plain', ...]
     *
     * @return bool
     * @throws \Error
     */
    protected function clientAcceptsMimeTypeFromList(array $mimeList): bool
    {
        foreach ($mimeList AS $refMime) {
            foreach ($this->route->getRequest()->getAccept() AS $mime) {
                if (strcasecmp($refMime, $mime) === 0) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns true, if client accepts response in HTML or text format.
     *
     * @param bool $HTMLOnly If is true, only mime 'text/html' is valid, 'text/ *' or '* / *' otherwise.
     * @return bool
     */
    protected function clientAcceptsHTMLResponse(bool $HTMLOnly = false): bool
    {
        if ($HTMLOnly === true) {
            return $this->clientAcceptsMimeTypeFromList(['text/html']);
        }
        return $this->clientAcceptsMimeTypeFromList(['text/html', 'text/*', '*/*']);
    }

    /**
     * Returns true, if client accepts response in plain text format.
     * @return bool
     */
    protected function clientAcceptsPlainTextResponse(): bool
    {
        return $this->clientAcceptsMimeTypeFromList(['text/plain']);
    }

    /**
     * Returns bool: whether accept mime type is compatible with input request.
     * @return bool
     */
    protected function clientAcceptsJSONResponse(): bool
    {
        return $this->clientAcceptsMimeTypeFromList(['application/json']);
    }

    /**
     * Returns bool: whether accept mime type is compatible with input request.
     * @return bool
     */
    protected function clientAcceptsXMLResponse(): bool
    {
        return $this->clientAcceptsMimeTypeFromList(['application/xml']);
    }

}