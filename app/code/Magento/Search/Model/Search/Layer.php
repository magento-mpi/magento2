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
namespace Magento\Search\Model\Search;

class Layer extends \Magento\CatalogSearch\Model\Layer
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider;

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogSearch\Model\Resource\Fulltext\CollectionFactory $fulltextCollectionFactory
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $searchData
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\StateFactory $layerStateFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogSearch\Model\Resource\Fulltext\CollectionFactory $fulltextCollectionFactory,
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchData,
        array $data = array()
    ) {
        $this->_engineProvider = $engineProvider;
        $this->_searchData = $searchData;
        parent::__construct($layerStateFactory, $categoryFactory, $attributeCollectionFactory, $catalogProduct,
            $storeManager, $catalogProductVisibility, $catalogConfig, $customerSession, $coreRegistry,
            $fulltextCollectionFactory, $catalogSearchData);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_engineProvider->get()->getResultCollection();
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
            \Magento\Catalog\Model\Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->_attributeCollectionFactory->create()
            ->setItemObjectClass('Magento\Catalog\Model\Resource\Eav\Attribute');

        if ($this->_searchData->getTaxInfluence()) {
            $collection->removePriceFilter();
        }

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel($this->_storeManager->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
}
