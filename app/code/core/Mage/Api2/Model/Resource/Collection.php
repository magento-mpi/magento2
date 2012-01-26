<?php

/**
 * Base class for all API collection resources
 */
abstract class Mage_Api2_Model_Resource_Collection extends Mage_Api2_Model_Resource
{
    const PAGE_SIZE = 2;

    const HTTP_PARAM_PAGE    = 'page';
    const HTTP_PARAM_ORDER   = 'order';
    const HTTP_PARAM_FILTER  = 'filter';

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
                $result = $this->retrieve();

                //$this->render($result);

                //TODO We need filtering below cause real columns can't be removed ...
                //TODO ... by $collection->removeAttributeToSelect()
                $filtered = $this->getFilter()->collectionOut($result);
                $this->render($filtered);
                break;
        }
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @return array
     */
    protected function retrieve()
    {
        $this->critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Dummy method to be replaced in descendants
     *
     * @param array $data
     */
    protected function create(array $data)
    {
        $this->critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
    }

    /**
     * Update method not allowed for this type of resource
     *
     * @param array $data
     */
    final protected function update(array $data)
    {
        $this->critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Delete method not allowed for this type of resource
     */
    final protected function delete()
    {
        $this->critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Apply filtering for items from URL params
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Mage_Api2_Model_Resource_Collection
     */
    final protected function applyCollectionModifiers(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $request = $this->getRequest();
        $page = $request->getParam(self::HTTP_PARAM_PAGE, 1);
        $order = $request->getParam(self::HTTP_PARAM_ORDER, null);
        $filter = $request->getParam(self::HTTP_PARAM_FILTER, null);

        $collection->setPage($page, self::PAGE_SIZE);
        if ($order!==null) {
            $collection->setOrder($order, Varien_Data_Collection::SORT_ORDER_DESC); //$collection->addAttributeToSort()
        }

        /*$filter = array(
            array('attribute'=>'status', 'in' => array(1)),
            //array('attribute'=>'lastname', 'like'  => $this->getQuery().'%'),
            //array('attribute'=>'company', 'like'   => $this->getQuery().'%'),
        );*/

        if ($filter!==null && !empty($filter)) {
            //TODO validate filter?
            try {
                $collection->addAttributeToFilter($filter);
            } catch(Exception $e) {
                $this->critical(self::RESOURCE_COLLECTION_FILTERING_ERROR);
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
    final protected function applyAttributeFilters(Mage_Eav_Model_Entity_Collection_Abstract $collection)
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
    abstract protected function getLocation(Mage_Catalog_Model_Abstract $resource);
}
