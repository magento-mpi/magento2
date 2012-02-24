<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Collection resource model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Resource_Collection extends Mage_Api2_Model_Resource
{
    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 10;

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR     = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_ORDERING_ERROR   = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR  = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**
     * Validate filter data and apply it to collection if possible
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    protected function _applyFilter(Varien_Data_Collection_Db $collection)
    {
        $filter = $this->getRequest()->getFilter();

        if (!method_exists($collection, 'addAttributeToFilter') || !$filter) {
            return $this;
        }
        if (!is_array($filter)) {
            $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
        }
        $allowedAttributes = $this->getFilter()->getAllowedAttributes(self::OPERATION_ATTRIBUTE_READ);

        foreach ($filter as $filterEntry) {
            if (!is_array($filter)
                || !array_key_exists('attribute', $filterEntry)
                || !in_array($filterEntry['attribute'], $allowedAttributes)) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
            $attributeCode = $filterEntry['attribute'];

            unset($filterEntry['attribute']);

            try {
                $collection->addAttributeToFilter($attributeCode, $filterEntry);
            } catch(Exception $e) {
                $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
            }
        }
        return $this;
    }

    /**
     * Delete method not allowed for this type of resource
     */
    final protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Set navigation parameters and apply filters from URL params
     *
     * @param Varien_Data_Collection_Db $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    final protected function _applyCollectionModifiers(Varien_Data_Collection_Db $collection)
    {
        $request    = $this->getRequest();
        $pageNumber = $request->getPageNumber();
        $orderField = $request->getOrderField();

        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }
        if (null !== $orderField) {
            if (!is_string($orderField) || !array_key_exists($orderField, $this->getAvailableAttributes())) {
                $this->_critical(self::RESOURCE_COLLECTION_ORDERING_ERROR);
            }
            $collection->setOrder($orderField, $request->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize(self::DEFAULT_PAGE_SIZE);

        return $this->_applyFilter($collection);
    }

    /**
     * Get resource location
     *
     * @param Mage_Core_Model_Abstract $resource
     * @return string URL
     */
    protected function _getLocation(Mage_Core_Model_Abstract $resource)
    {
         /** @var $apiTypeRoute Mage_Api2_Model_Route_ApiType */
         $apiTypeRoute = Mage::getModel('api2/route_apiType');

        $instanceResourceType = $this->getConfig()->getResourceInstance($this->getResourceType());
        $chain = $apiTypeRoute->chain(
             new Zend_Controller_Router_Route($this->getConfig()->getMainRoute($instanceResourceType))
         );
         $params = array(
             'api_type' => $this->getRequest()->getApiType(),
             'id'       => $resource->getId()
         );
         $uri = $chain->assemble($params);

         return '/' . $uri;
    }

    /**
     * Add collection specific errors
     *
     * @return array
     */
    protected function _getCriticalErrors()
    {
        $errors = parent::_getCriticalErrors();

        $errors[self::RESOURCE_COLLECTION_PAGING_ERROR] = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_ORDERING_ERROR] = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_FILTERING_ERROR] = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;
        $errors[self::RESOURCE_COLLECTION_ATTRIBUTES_ERROR] = Mage_Api2_Model_Server::HTTP_BAD_REQUEST;

        return $errors;
    }

    /**
     * Internal "collection" resource model dispatch
     */
    final public function dispatch()
    {
        switch ($this->getRequest()->getOperation()) {
            case self::OPERATION_UPDATE:
                $requestData  = $this->getRequest()->getBodyParams();
                $filteredData = $this->getFilter()->collectionIn($requestData);

                $this->_update($filteredData);
                $this->_render($this->getResponse()->getMessages());
                break;
            case self::OPERATION_DELETE:
                $this->_delete(array());
                break;
            case self::OPERATION_CREATE:
                $requestData     = $this->getRequest()->getBodyParams();
                $filteredData    = $this->getFilter()->in($requestData);
                $newItemLocation = $this->_create($filteredData);

                $this->getResponse()->setHeader('Location', $newItemLocation);
                break;
            case self::OPERATION_RETRIEVE:
                $retrievedData = $this->_retrieve();
                $filteredData  = $this->getFilter()->collectionOut($retrievedData);

                $this->_render($filteredData);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * Get available attributes of API resource
     *
     * @param string|null $userType
     * @param string|null $operation
     * @return array
     */
    public function getAvailableAttributes($userType = null, $operation = null)
    {
        return $this->getResourceInstance()->getAvailableAttributes($userType, $operation);
    }

    /**
     * Get available attributes of API resource from configuration file
     *
     * @return array
     * @throw Exception
     */
    public function getAvailableAttributesFromConfig()
    {
        $instanceResourceType = $this->getInstanceResourceType();

        if (!$instanceResourceType) {
            throw new Exception(sprintf("Can not find instance node name for resource '%s'", $this->getResourceType()));
        }
        return $this->getConfig()->getResourceAttributes($instanceResourceType);
    }

    /**
     * Get instance class for this collection
     *
     * @return string
     */
    public function getInstanceResourceType()
    {
        return $this->getConfig()->getResourceInstance($this->getResourceType());
    }

    /**
     * Get instance object for this collection
     *
     * @throws Exception
     * @return Mage_Api2_Model_Resource_Instance
     */
    public function getResourceInstance()
    {
        $instanceClassPath = $this->getConfig()->getResourceModel($this->getInstanceResourceType());

        /** @var $instance Mage_Api2_Model_Resource_Instance */
        $instance = Mage::getModel($instanceClassPath);

        if (!$instance) {
            throw new Exception('Invalid instance object.');
        }

        $instance->setResourceType($this->getInstanceResourceType());

        return $instance;
    }
}
