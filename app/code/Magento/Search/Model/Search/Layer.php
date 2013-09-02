<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog layer model integrated with search engine
 */
class Magento_Search_Model_Search_Layer extends Magento_CatalogSearch_Model_Layer
{
    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * Constructor
     *
     * By default is looking for first argument as array and assigns it as object
     * attributes This behavior may change in child classes
     *
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        Magento_Search_Helper_Data $searchData,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_searchData = $searchData;
        parent::__construct($catalogSearchData, $data);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_catalogSearchData->getEngine()->getResultCollection();
            $collection->setStoreId($this->getCurrentCategory()->getStoreId());
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = array_merge($additionalTags, array(
            Magento_Catalog_Model_Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Magento_Catalog_Model_Resource_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /* @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = Mage::getResourceModel('Magento_Catalog_Model_Resource_Product_Attribute_Collection')
            ->setItemObjectClass('Magento_Catalog_Model_Resource_Eav_Attribute');

        if ($this->_searchData->getTaxInfluence()) {
            $collection->removePriceFilter();
        }

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel(Mage::app()->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
}
