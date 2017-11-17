# SmartHTTPRestAPI
Rest API handling for `ha` framework. Routing and controller abstraction by ha framework stabdards. Currently is supported only JSON body in requests and responses.

## How it works
For Rest API functionionality is required only one route, which is appended to ha router. This route is instance of `ha\SmartHTTPRestAPI\JSON\JSONRestAPIRoute` and maps requests by URL to required controllers.

Required area of functionality (product handling, category handling, ...) represent one controller, which implements `ha\SmartHTTPRestAPI\JSON\JSONRestAPIController`. All, what is needed, is add the controller into JSONRestAPIRoute route. This controller must implement `ha\SmartHTTPRestAPI\JSON\JSONRestAPIController` or must be extended from `ha\SmartHTTPRestAPI\JSON\AbstractJSONRestAPIController` (this abstract provides default required functionality for Rest API).



### interface JSONRestAPIController
Associated route automatically maps request to these methods in controllers (every controller must implement interface):

```
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
```

### Example controller

```
<?php
declare(strict_types=1);

use ha\SmartHTTPRestAPI\JSON\AbstractJSONRestAPIController;

class ExampleAPIController extends AbstractJSONRestAPIController
{

    /**
     * Handle GET request with item id in URL.
     *
     * @param string $projectName
     * @param string $itemId Item id in string format from URL
     */
    public function getItem(string $projectName, string $itemId): void
    {
        /* TODO load and set data to $data by $id and $projectName via associated service, if item does not exists,
         * then throw new HTTP404Error();
         */
        $data = [
            'project' => $projectName,
            'itemData' => ['id' => $itemId, 'prop1' => 'item data here', 'prop2' => 'other item data here']
        ];
        $this->setResponseData(200, $data);
    }

    /**
     * Handle GET request without item id in URL (e.g. search, items list, ...). Specification and filter must be set
     * in $_GET.
     * @param string $projectName
     */
    public function getItems(string $projectName): void
    {
        // TODO load and set data to $data by $_GET filter and $projectName via associated service
        $data = [
            'project' => $projectName,
            'items' => [],
        ];
        $this->setResponseData(200, $data);
    }

}

```