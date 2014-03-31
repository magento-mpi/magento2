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

use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Catalog\Model\Resource\Product\CollectionFactory;

class Promotion extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Layer factory
     *
     * @var \Magento\Catalog\Model\LayerFactory
     */
    protected $_layerFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Layer\Category $catalogLayer
     * @param \Magento\Catalog\Model\LayerFactory $layerFactory
     * @param CollectionFactory $productCollectionFactory
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Core\Helper\PostData $postDataHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Layer\Category $catalogLayer,
        \Magento\Catalog\Model\LayerFactory $layerFactory,
        CollectionFactory $productCollectionFactory,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_layerFactory = $layerFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $categoryFactory,
            $catalogLayer,
            $data,
            $priceBlockTypes
        );
    }

    /**
     * @return Collection
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            /** @var Collection $collection */
            $collection = $this->_productCollectionFactory->create();
            $this->_layerFactory->create()->prepareProductCollection($collection);

            $collection->addAttributeToFilter('promotion', 1)->addStoreFilter();

            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
}
