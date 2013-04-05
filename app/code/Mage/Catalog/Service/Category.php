<?php
/**
 * Catalog Category Service.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @service true
 */
class Mage_Catalog_Service_Category extends Mage_Core_Service_Abstract
{
    const SERVICE_ID = 'catalogCategory';

    /**
     * Return resource object or resource object data.
     *
     * @param Mage_Core_Service_Args $args
     * @return Varien_Object | array
     */
    public function item(Mage_Core_Service_Args $args)
    {
        $result = $this->_getItem($args);

        if ($args->getAsArray()) {
            // fake
            $result = $result->getData();
        }

        return $result;
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
        $category = Mage::getModel('Mage_Catalog_Model_Category');

        // `set` methods are creating troubles
        foreach ($args->getData() as $k => $v) {
            $category->setDataUsingMethod($k, $v);
        }

        $id = $category->getId();
        if ($id) {
            // TODO: we need this trick as because of inproper handling when target record doesn't exist
            $category->setId(null);

            // TODO: Depends on MDS-167
            //$fieldset = $args->getFieldset();
            //$category->setFieldset($fieldset);
            $category->load($id);
        }

        return $category;
    }

    /**
     * Returns collection of resource objects.
     *
     * @param Mage_Core_Service_Args $args
     * @return mixed
     */
    public function getItems(Mage_Core_Service_Args $args)
    {
        $result = $this->_getItems($args);
        if (!$args->getAsObject()) {
            // fake
            $result = $result->toArray();
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

    /**
     * Returns unique service identifier.
     *
     * @return string
     */
    protected function _getServiceId()
    {
        return self::SERVICE_ID;
    }
}
