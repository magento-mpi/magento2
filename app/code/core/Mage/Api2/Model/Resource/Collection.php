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
 * Base class for all API collection resources
 */
abstract class Mage_Api2_Model_Resource_Collection extends Mage_Api2_Model_Resource
{
    /**
     * Default page size
     */
    const DEFAULT_PAGE_SIZE = 10;

    /**#@+
     * Parameters for pager
     */
    const HTTP_PARAM_PAGE    = 'page';
    const HTTP_PARAM_ORDER   = 'order';
    const HTTP_PARAM_FILTER  = 'filter';
    /**#@-*/

    /**#@+
     *  Default collection resources error messages
     */
    const RESOURCE_COLLECTION_PAGING_ERROR      = 'Resource collection paging error.';
    const RESOURCE_COLLECTION_ORDERING_ERROR    = 'Resource collection ordering error.';
    const RESOURCE_COLLECTION_FILTERING_ERROR   = 'Resource collection filtering error.';
    const RESOURCE_COLLECTION_ATTRIBUTES_ERROR  = 'Resource collection including additional attributes error.';
    /**#@-*/

    /**
     * Internal "collection" resource model dispatch
     */
    final public function dispatch()
    {
        $operation = $this->getRequest()->getOperation();
        switch ($operation) {
            //not exist for this kind of resource
            case self::OPERATION_UPDATE:
            case self::OPERATION_DELETE:
                $this->$operation(array());
                break;

            case self::OPERATION_CREATE:

                $data = $this->getRequest()->getBodyParams();
                $filtered = $this->getFilter()->in($data);
                $location = $this->$operation($filtered);

                //TODO change to "Location"
                $this->getResponse()->setHeader('Location2', $location);
                //$this->getResponse()->setHeader('Location', 'http://google.com');
                break;

            case self::OPERATION_RETRIEVE:
                $result = $this->_retrieve();

                //$this->render($result);

                //TODO We need filtering below cause real columns can't be removed ...
                //TODO ... by $collection->removeAttributeToSelect()
                $filtered = $this->getFilter()->collectionOut($result);
                $this->_render($filtered);
                break;
        }
    }

    /**
     * Update method not allowed for this type of resource
     *
     * @param array $data
     */
    final protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
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
        $request = $this->getRequest();
        $order   = $request->getParam(self::HTTP_PARAM_ORDER);
        $filter  = $request->getParam(self::HTTP_PARAM_FILTER);

        $collection->setCurPage($request->getParam(self::HTTP_PARAM_PAGE, 1))
            ->setPageSize(self::DEFAULT_PAGE_SIZE);

        if (null !== $order) {
            $collection->setOrder($order, Varien_Data_Collection::SORT_ORDER_DESC);
        }
        if (method_exists($collection, 'addAttributeToFilter')) {
            /*$filter = array(
                array('attribute'=>'status', 'in' => array(1)),
                //array('attribute'=>'lastname', 'like'  => $this->getQuery().'%'),
                //array('attribute'=>'company', 'like'   => $this->getQuery().'%'),
            );*/

            if ($filter) {
                //TODO validate filter?
                try {
                    $collection->addAttributeToFilter($filter);
                } catch(Exception $e) {
                    $this->_critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
                }
            }
        }
        return $this;
    }

    /**
     * Apply filtering for item attributes from Attribute Filter
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    final protected function _applyAttributeFilters(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        //TODO validate &include
        $collection->removeAttributeToSelect();
        foreach ($this->getFilter()->getAttributesToInclude() as $attribute) {
            //echo $attribute.PHP_EOL;
            $collection->addAttributeToSelect($attribute);
        }

        /*
        $collection->removeAttributeToSelect()
        $collection->addAttributeToFilter()
        $collection->addAttributeToSelect()
        $collection->addAttributeToSort()*/

        return $this;
    }

    /**
     * Get resource location
     *
     * @abstract
     * @param Mage_Catalog_Model_Abstract $resource
     * @return string URL
     */
    abstract protected function _getLocation(Mage_Catalog_Model_Abstract $resource);

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
}
