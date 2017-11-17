<?php
declare(strict_types=1);

namespace ha\SmartHTTPRestAPI\JSON;

use ha\Access\HTTP\Error\HTTPError;
use ha\Access\HTTP\Router\Route\HTTPRoute;
use ha\Access\HTTP\Router\Route\HTTPRouteDefaultAbstract;

class JSONRestAPIRoute extends HTTPRouteDefaultAbstract implements HTTPRoute
{
    /**
     * @var string Required regexp vars: project, controller, id
     */
    protected $allowedURLRegexp;

    /** @var JSONRestAPIController Controller instance initialized by self::bootstrap(). */
    protected $controller;

    /** @var string Action name in controller. */
    protected $controllerAction;

    /** @var array Arguments for action in controller. */
    protected $controllerActionArgs;

    /** @var JSONRestAPIController[] */
    protected $controllerHashMap = [];

    /** @var string[] Extra headers for every response, e.g. ORIGIN headers, etc. */
    protected $extraHeaders = [];

    /**
     * Determine whether this route is compatible with URL. If false, router skips this route.
     * @return bool
     */
    public function URLIsCompatible(): bool
    {
        // if regexp is not defined
        if (!isset($this->allowedURLRegexp)) {
            return false;
        }

        // test URL and required parts
        $fullURL = $this->request->getUrl()->scheme . '://' . $this->request->getUrl()->host . $this->request->getUrl(
            )->path;
        if (!preg_match($this->allowedURLRegexp, $fullURL, $matches)) {
            return false;
        }
        #ddd($matches);
        if (!isset($matches['controller']) || !isset($matches['id']) || !isset($matches['project'])
            || !isset($this->controllerHashMap["{$matches['project']}-{$matches['controller']}"])
        ) {
            return false;
        }

        $this->controller = $this->controllerHashMap["{$matches['project']}-{$matches['controller']}"];
        $project = $matches['project'];
        $itemId = $matches['id'];
        if ($project === '') {
            return false;
        }
        if ($itemId === '') {
            $itemId = null;
        }

        // id in URL
        if (isset($itemId)) {
            $this->allowedRequestMethods = ['GET', 'PUT', 'DELETE'];
            $this->controllerActionArgs = [$project, $itemId];
            if ($this->request->typeof('get')) {
                $this->controllerAction = 'getItem';
            }
            if ($this->request->typeof('PUT')) {
                $this->controllerAction = 'updateItem';
            }
            if ($this->request->typeof('DELETE')) {
                $this->controllerAction = 'deleteItem';
            }
        }
        else {
            $this->allowedRequestMethods = ['GET', 'POST', 'PUT', 'DELETE'];
            $this->controllerActionArgs = [$project];
            if ($this->request->typeof('get')) {
                $this->controllerAction = 'getItems';
            }
            if ($this->request->typeof('POST')) {
                $this->controllerAction = 'createItems';
            }
            if ($this->request->typeof('PUT')) {
                $this->controllerAction = 'updateItems';
            }
            if ($this->request->typeof('DELETE')) {
                $this->controllerAction = 'deleteItems';
            }
        }

        return true;
    }

    /**
     * Determine whether Route accepts mime type from Request headers.
     * @return bool
     */
    public function checkAcceptRequestHeader(): bool
    {
        foreach ($this->request->getAccept() AS $mime) {
            if (strcasecmp('application/json', $mime) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine whether Route supports content type from Request headers.
     * @return bool
     */
    public function checkContentTypeRequestHeader(): bool
    {
        if ($this->request->typeof('get')) {
            return true;
        }
        if (strcasecmp('application/json', $this->request->getContentType()) === 0) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether Route supports request method.
     * @return bool
     */
    public function checkRequestMethod(): bool
    {
        foreach ($this->getAllowedRequestMethods() AS $method) {
            if ($this->request->typeof($method)) {
                return true;
                break;
            }
            unset($method);
        }
        return false;
    }

    /**
     * Get collection of allowed request methods for this route URL. It is very important for handling HTTP405 error.
     * @return array
     */
    public function getAllowedRequestMethods(): array
    {
        return $this->allowedRequestMethods;
    }

    /**
     * Setup response headers and body by your controller or other logic.
     */
    public function prepareResponse(): void
    {
        try {
            call_user_func_array([$this->controller, $this->controllerAction], $this->controllerActionArgs);
        } catch (\Throwable $e) {
            if ($e instanceof HTTPError) {
                throw $e;
            }
            $this->response->resetHeaders();
            $this->response->setCharset('UTF-8');
            $this->response->setContentType('application/json');
            $this->response->setStatusCode(500);
            $data = [
                'error' => true,
                'errorMessage' => strval($e->getMessage()),
                'errorData' => strval($e),
            ];
            $this->response->setBody(json_encode($data));
        }
        foreach ($this->extraHeaders AS $header) {
            $this->response->addHeader($header);
        }
    }

    /**
     * Add controller to route.
     *
     * @param string $URIControllerId
     * @param JSONRestAPIController $controller
     */
    public function addController(string $URIControllerId, JSONRestAPIController $controller): void
    {
        $this->controllerHashMap[$URIControllerId] = $controller;
    }

    /**
     * Set extra headers for every request, e.g. ORIGIN headers, etc.
     *
     * @param string[] $headers
     *
     * @throws \TypeError
     */
    public function setExtraHeaders(array $headers): void
    {
        foreach ($headers AS $header) {
            if (is_string($header)) {
                $this->extraHeaders[] = $header;
            }
            else {
                throw new \TypeError("Header must be s string value");
            }
        }
    }

}