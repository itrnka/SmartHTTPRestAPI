<?php
declare(strict_types=1);

namespace ha\SmartHTTPRestAPI\JSON;

use ha\SmartHTTPRestAPI\AbstractSmartController;

abstract class AbstractJSONRestAPIController extends AbstractSmartController implements JSONRestAPIController
{

    /** @var bool $prettyPrintResponse Set rendering to pretty print if $prettyPrintResponse = true. */
    protected $prettyPrintResponse = false;

    /**
     * @inheritdoc
     */
    function bootstrap(): void
    {
    }

    /**
     * Make controller action unavailable.
     */
    private function disableAction(): void
    {
        $response = $this->route->getResponse();
        $response->resetHeaders();
        $response->setCharset('UTF-8');
        $response->setContentType('application/json');
        $this->route->getResponse()->setStatusCode(501);
        $data = [
            'error' => true,
            'errorMessage' => 'Not implemented',
            'errorData' => 'Method disallowed or under construction',
        ];
        $response->setBody($this->renderJSON($data));
    }

    /**
     * renderJSON.
     *
     * @param $data
     *
     * @return string
     */
    private function renderJSON($data): string
    {
        if ($this->prettyPrintResponse === true) {
            return json_encode($data, JSON_PRETTY_PRINT);
        }
        return json_encode($data);
    }

    /**
     * Set response data.
     *
     * @param int $HTTPStatusCode
     * @param mixed $data
     */
    protected function setResponseData(int $HTTPStatusCode, $data): void
    {
        $response = $this->route->getResponse();
        $response->resetHeaders();
        $response->setCharset('UTF-8');
        $response->setContentType('application/json');
        $response->setStatusCode($HTTPStatusCode);
        $response->setBody($this->renderJSON($data));
    }

    protected function getRequestBodyData()
    {
        // TODO read request body and return json_decoded data from this request in concrete implementation
    }

    /**
     * @inheritdoc
     */
    public function createItems(string $projectName): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function deleteItem(string $projectName, string $itemId): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function deleteItems(string $projectName): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function getItem(string $projectName, string $itemId): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function getItems(string $projectName): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function updateItem(string $projectName, string $itemId): void
    {
        $this->disableAction();
    }

    /**
     * @inheritdoc
     */
    public function updateItems(string $projectName): void
    {
        $this->disableAction();
    }

}