<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product\ProductList;

/**
 * Catalog product random items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Random extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Layer factory
     *
     * @var \Magento\Catalog\Model\LayerFactory
     */
    protected $_layerFactory;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Layer $catalogLayer
     * @param \Magento\Catalog\Model\LayerFactory $layerFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Math\Random $mathRandom,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Layer $catalogLayer,
        \Magento\Catalog\Model\LayerFactory $layerFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        array $data = array()
    ) {
        $this->_layerFactory = $layerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $storeManager,
            $catalogConfig,
            $coreRegistry,
            $taxData,
            $catalogData,
            $coreData,
            $context,
            $mathRandom,
            $categoryFactory,
            $catalogLayer,
            $data
        );
    }

    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            /** @var \Magento\Catalog\Model\Resource\Product\Collection $collection */
            $collection = $this->_productCollectionFactory->create();
            $this->_layerFactory->create()->prepareProductCollection($collection);
            $collection->getSelect()->order('rand()');
            $collection->addStoreFilter();
            $numProducts = $this->getNumProducts() ? $this->getNumProducts() : 0;
            $collection->setPage(1, $numProducts);

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
