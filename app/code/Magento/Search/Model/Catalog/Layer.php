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
namespace Magento\Search\Model\Catalog;

class Layer extends \Magento\Catalog\Model\Layer
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData;

    /**
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($data);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Search\Model\Resource\Collection
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
            \Magento\Catalog\Model\Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
    }
}
