<?php
declare(strict_types=1);

namespace ha\SmartHTTPRestAPI;

use ha\Access\HTTP\Router\Route\HTTPRoute;

interface SmartController
{
    /**
     * Controller constructor.
     *
     * @param HTTPRoute $route
     */
    public function __construct(HTTPRoute $route);
}