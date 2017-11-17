<?php
declare(strict_types=1);

namespace ha\SmartHTTPRestAPI\JSON;


interface JSONRestAPIController
{
    /**
     * Handle POST request without item id in URL - save item(s) defined in request body.
     * @param string $projectName
     */
    public function createItems(string $projectName): void;

    /**
     * Handle DELETE request with item id in URL. Please do not use body in your request.
     *
     * @param string $projectName
     * @param string $itemId Item id in string format from URL
     */
    public function deleteItem(string $projectName, string $itemId): void;

    /**
     * Handle DELETE request without item id in URL. Please do not use body in your request. Specification to delete
     * must be set in $_GET.
     * @param string $projectName
     */
    public function deleteItems(string $projectName): void;

    /**
     * Handle GET request with item id in URL.
     *
     * @param string $projectName
     * @param string $itemId Item id in string format from URL
     */
    public function getItem(string $projectName, string $itemId): void;

    /**
     * Handle GET request without item id in URL (e.g. search, items list, ...). Specification and filter must be set
     * in $_GET.
     * @param string $projectName
     */
    public function getItems(string $projectName): void;

    /**
     * Handle PUT request with item id in URL (this is idempotent!), save data from request body.
     *
     * @param string $projectName
     * @param string $itemId Item id in string format from URL
     */
    public function updateItem(string $projectName, string $itemId): void;

    /**
     * Handle PUT request without item id in URL (this is idempotent!), save data from request body.
     *
     * @param string $projectName
     */
    public function updateItems(string $projectName): void;
}