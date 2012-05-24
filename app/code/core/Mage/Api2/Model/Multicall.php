<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 model for multiple internal calls to subresources of specified resource
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Multicall
{

    /**
     * @var Mage_Api2_Model_Request
     */
    protected $_parentCallRequest;

    /**
     * @var string
     */
    protected $_parentResourceId;

    /**
     * @var string
     */
    protected $_parentResourceName;

    /**
     * Multicall to subresources of specified resource
     *
     * @param string $parentResourceId
     * @param string $parentResourceName
     * @param Mage_Api2_Model_Request $parentCallRequest
     * @return Mage_Api2_Model_Response
     */
    public function call($parentResourceId, $parentResourceName, Mage_Api2_Model_Request $parentCallRequest)
    {
        $this->_parentResourceName = $parentResourceName;
        $this->_parentResourceId   = $parentResourceId;
        $this->_parentCallRequest  = $parentCallRequest;
        $subresources = $this->_getDeclaredSubresources($parentResourceName);
        foreach ($subresources as $subresource) {
            $this->_callSubresource($subresource);
        }

        return $this->_getResponse();
    }

    /**
     * Make call to specified subresource with data from request
     *
     * @param array $subresource
     * @return Mage_Api2_Model_Multicall
     */
    protected function _callSubresource($subresource)
    {
        $bodyParams = $this->_getRequest()->getBodyParams();
        // check if subresource data exists in request
        $requestParamName = $subresource['request_param_name'];
        if (!(is_array($bodyParams) && array_key_exists($requestParamName, $bodyParams)
            && is_array($bodyParams[$requestParamName]))
        ) {
            return $this;
        }
        // make internal call
        $requestData = $bodyParams[$requestParamName];
        foreach ($requestData as $subresourceData) {
            $this->_internalCall($subresource, $subresourceData);
        }
        return $this;
    }

    /**
     * Make internal call to specified subresource on with specified data via API2 server
     *
     * @param array $subresource
     * @param array $requestData
     * @throws Mage_Api2_Exception
     * @return Mage_Api2_Model_Multicall
     */
    protected function _internalCall($subresource, $requestData)
    {
        try {
            if (!is_array($requestData)) {
                throw new Mage_Api2_Exception('Invalid data format', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
            $subresourceIdKey = $subresource['id_param_name'];
            /** @var $server Mage_Api2_Model_Server */
            $server = Mage::getSingleton('Mage_Api2_Model_Server');

            // create subresource item before linking it to main resource
            if (!array_key_exists($subresourceIdKey, $requestData)) {
                $subresourceCreateResourceName = $subresource['create_resource_name'];
                $internalRequest = $this->_prepareRequest($subresourceCreateResourceName, $requestData);
                /** @var $internalCreateResponse Mage_Api2_Model_Response */
                $internalCreateResponse = Mage::getModel('Mage_Api2_Model_Response');
                $server->internalCall($internalRequest, $internalCreateResponse);
                $createdSubresourceInstanceId = $this->_getCreatedResourceId($internalCreateResponse);
                if (empty($createdSubresourceInstanceId)) {
                    throw new Mage_Api2_Exception('Error during subresource creation',
                        Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
                }
                $requestData[$subresourceIdKey] = $createdSubresourceInstanceId;
            }

            // link subresource to main resource
            $subresourceName = $subresource['name'];
            $parentResourceIdFieldName = $subresource['parent_resource_id_field_name'];
            $internalRequest = $this->_prepareRequest($subresourceName, $requestData, $parentResourceIdFieldName);

            /** @var $internalResponse Mage_Api2_Model_Response */
            $internalResponse = Mage::getModel('Mage_Api2_Model_Response');
            $server->internalCall($internalRequest, $internalResponse);
            foreach ($internalResponse->getHeaders() as $header) {
                if ($header['name'] == 'Location') {
                    $this->_getResponse()->addMessage('Subresource created.', Mage_Api2_Model_Server::HTTP_OK, array(
                        'Location' => $header['value']), Mage_Api2_Model_Response::MESSAGE_TYPE_SUCCESS);
                }
            }
        } catch (Exception $e) {
            if ($subresource['rollback_on_fail'] && isset($createdSubresourceInstanceId)) {
                $this->_rollbackCreatedSubresource($subresource, $createdSubresourceInstanceId);
            }
            $this->_getResponse()->addMessage($subresource['name'] . ': ' . $e->getMessage(), $e->getCode());
            $this->_getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_CREATED);
        }

        return $this;
    }

    /**
     * Rollback created subresource if linking it to parent resource failed.
     *
     * @param array $subresource
     * @param int $createdSubresourceInstanceId
     */
    protected function _rollbackCreatedSubresource($subresource, $createdSubresourceInstanceId)
    {
        $rollbackRequest = $this->_prepareDeleteRequest($subresource['create_resource_name'],
            $subresource['create_resource_id_param'], $createdSubresourceInstanceId);
        /** @var $rollbackResponse Mage_Api2_Model_Response */
        $rollbackResponse = Mage::getModel('Mage_Api2_Model_Response');
        try {
            /** @var $server Mage_Api2_Model_Server */
            $server = Mage::getSingleton('Mage_Api2_Model_Server');
            $server->internalCall($rollbackRequest, $rollbackResponse);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    /**
     * Prepare internal request
     *
     * @param string $subresourceName
     * @param array $data
     * @param string|null $parentResourceIdFieldName
     * @return Mage_Api2_Model_Request_Internal
     */
    protected function _prepareRequest($subresourceName, $data, $parentResourceIdFieldName = null)
    {
        $subresourceUri = $this->_createSubresourceUri($data, $subresourceName, $parentResourceIdFieldName);
        /** @var $internalRequest Mage_Api2_Model_Request_Internal */
        $internalRequest = Mage::getModel('Mage_Api2_Model_Request_Internal');
        $internalRequest->setRequestUri($subresourceUri);
        $internalRequest->setBodyParams($data);
        $internalRequest->setMethod('POST');
        return $internalRequest;
    }

    /**
     * Generate subresource uri
     *
     * @param array $data
     * @param string $subresourceName
     * @param string $parentResourceIdFieldName
     * @return string
     */
    protected function _createSubresourceUri($data, $subresourceName, $parentResourceIdFieldName = null)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->_getConfig()->getRouteWithCollectionTypeAction($subresourceName))
        );
        $params = array();
        $params['api_type'] = 'rest';
        if (null !== $parentResourceIdFieldName) {
            $params[$parentResourceIdFieldName] = $this->_parentResourceId;
        }
        $routeParamsLink = $this->_getConfig()->getSubresourceRouteParamsLink($this->_parentResourceName,
            $subresourceName);
        foreach ($routeParamsLink as $routeParam => $requestLink) {
            if (isset($data[$requestLink])) {
                $params[$routeParam] = $data[$requestLink];
            }
        }
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Prepare internal request for resource delete
     *
     * @param string $resourceName
     * @param string $resourceIdFieldName
     * @param int $resourceId
     * @return Mage_Api2_Model_Request_Internal
     */
    protected function _prepareDeleteRequest($resourceName, $resourceIdFieldName, $resourceId)
    {
        $subresourceDeleteUri = $this->_createResourceDeleteUri($resourceName, $resourceIdFieldName, $resourceId);
        /** @var $internalRequest Mage_Api2_Model_Request_Internal */
        $internalRequest = Mage::getModel('Mage_Api2_Model_Request_Internal');
        $internalRequest->setRequestUri($subresourceDeleteUri);
        $internalRequest->setMethod('DELETE');
        return $internalRequest;
    }

    /**
     * Generate resource delete uri
     *
     * @param string $resourceName
     * @param string $resourceIdFieldName
     * @param int $resourceId
     * @return string
     */
    protected function _createResourceDeleteUri($resourceName, $resourceIdFieldName, $resourceId)
    {
        /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
        $apiTypeRoute = Mage::getModel('Mage_Api2_Model_Route_ApiType');

        $chain = $apiTypeRoute->chain(
            new Zend_Controller_Router_Route($this->_getConfig()->getRouteWithEntityTypeAction($resourceName))
        );
        $params = array();
        $params['api_type'] = 'rest';
        $params[$resourceIdFieldName] = $resourceId;
        $uri = $chain->assemble($params);

        return '/' . $uri;
    }

    /**
     * Retrieve list of subresources declared in configuration
     *
     * @param string $parentResourceName
     * @return array
     */
    protected function _getDeclaredSubresources($parentResourceName)
    {
        return $this->_getConfig()->getResourceSubresources($parentResourceName);
    }

    /**
     * Retrieve API2 config
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('Mage_Api2_Model_Config');
    }

    /**
     * Retrieve global response
     *
     * @return Mage_Api2_Model_Response
     */
    protected function _getResponse()
    {
        return Mage::getSingleton('Mage_Api2_Model_Response');
    }

    /**
     * Retrieve parent request
     *
     * @return Mage_Api2_Model_Request
     */
    protected function _getRequest()
    {
        return $this->_parentCallRequest;
    }

    /**
     * Retrieve created resource id from response
     *
     * @param Mage_Api2_Model_Response $response
     * @return string|int
     */
    protected function _getCreatedResourceId($response)
    {
        $resourceId = 0;
        $headers = $response->getHeaders();
        foreach ($headers as $header) {
            if ($header['name'] == 'Location') {
                list($resourceId) = array_reverse(explode('/', $header['value']));
                break;
            }
        }
        return $resourceId;
    }
}
