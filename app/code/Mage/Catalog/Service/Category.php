<?php
/**
 * API Product service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Service_Category extends Mage_Core_Service_Abstract
{
    const SERVICE_ID = 'Mage_Catalog_Service_Category';

    /**
     * Return resource object or resource object data.
     *
     * @adapter context | standard | legacy
     * @param mixed $args
     * @return mixed
     */
    public function getItem($args = null, $asObject = true)
    {
        $result = $this->_getItem($args);
        if (!$asObject) {
            $result = $this->_getObjectData($result);
        }

        return $result;
    }

    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    protected function _getServiceId()
    {
        return self::SERVICE_ID;
    }

    /**
     * Returns model which operated by current service.
     *
     * @param Mage_Core_Service_Args $args
     * @return Mage_Catalog_Service_Category
     */
    protected function _getItem(Mage_Core_Service_Args $args)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->_objectManager->create('Mage_Catalog_Model_Category');

        $id = $args->getId();

        // `set` methods are creating troubles
        foreach ($args->getData() as $k => $v) {
            $category->setDataUsingMethod($k, $v);
        }

        if (false !== $id) {
            // TODO: Depends on MDS-167
            //$fieldset = $args->getFieldset();
            //$category->setFieldset($fieldset);

            $category->load($id);
        }

        if (!$category->getId()) {
            // TODO: so what to do?
        }

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param mixed $args
     * @return mixed
     */
    public function getItems($args = null, $asObject = true)
    {
        $result = $this->_getItems($args);
        if (!$asObject) {
            $result = $this->_getCollectionData($result);
        }

        return $result;
    }

    /**
     * Get collection object of the current service
     *
     * @param Mage_Core_Service_Args $args
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getItems(Mage_Core_Service_Args $args)
    {
        $collection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection');

        // Depends on MDS-167
        //$fieldset = $args->getFieldset();
        // $collection->setFieldset($fieldsetId);

        $categoryIds = $args->getCategoryIds();
        $collection->addIdFilter($categoryIds);

        $filters = $args->getFilters();
        $collection->addAttributeToFilter($filters);

        // TODO or not TODO
        //$collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     *
     * @param Mage_Catalog_Service_Category $category
     * @return array
     */
    protected function _getDictionary($category)
    {
        if (empty($this->_dictionary)) {

            $this->_dictionary = array();
        }

        return $this->_dictionary;
    }
}
