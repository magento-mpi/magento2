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
class Magento_Search_Model_Catalog_Layer extends Magento_Catalog_Model_Layer
{
    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineProvider
     */
    protected $_engineProvider;

    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData;

    /**
     * @param Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($data);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Magento_Search_Model_Resource_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_engineProvider->get()->getResultCollection();
            $collection->setStoreId($this->getCurrentCategory()->getStoreId())
                ->addCategoryFilter($this->getCurrentCategory())
                ->setGeneralDefaultQuery();
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
}
