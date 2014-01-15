<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Block\Catalog\Product\ProductList;

/**
 * TargetRule Catalog Product List Abstract Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 * @SuppressWarnings(PHPMD.LongVariable)
 */
abstract class AbstractProductList
    extends \Magento\TargetRule\Block\Product\AbstractProduct
{
    /**
     * TargetRule Index instance
     *
     * @var \Magento\TargetRule\Model\Index
     */
    protected $_currentIndex;

    /**
     * Array of exclude Product Ids
     *
     * @var array
     */
    protected $_excludeProductIds;

    /**
     * Array of all product ids in list
     *
     * @var null|array
     */
    protected $_allProductIds = null;

    /**
     * @var \Magento\TargetRule\Model\IndexFactory
     */
    protected $_indexFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\TargetRule\Model\Resource\Index $index
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\TargetRule\Model\IndexFactory $indexFactory
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\TargetRule\Model\Resource\Index $index,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\TargetRule\Model\IndexFactory $indexFactory,
        array $data = array()
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_visibility = $visibility;
        $this->_indexFactory = $indexFactory;
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $cartHelper,
            $wishlistHelper,
            $compareProduct,
            $layoutHelper,
            $imageHelper,
            $index,
            $targetRuleData,
            $data
        );
    }


    /**
     * Retrieve current product instance (if actual and available)
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Retrieve Catalog Product List Type prefix
     * without last underscore
     *
     * @return string
     */
    protected function _getTypePrefix()
    {
        switch ($this->getProductListType()) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $prefix = 'related';
                break;

            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $prefix = 'upsell';
                break;

            default:
                throw new \Magento\Core\Exception(__('Undefined Catalog Product List Type'));
        }
        return $prefix;
    }

    /**
     * Retrieve Target Rule Index instance
     *
     * @return \Magento\TargetRule\Model\Index
     */
    protected function _getTargetRuleIndex()
    {
        if (is_null($this->_currentIndex)) {
            $this->_currentIndex = $this->_indexFactory->create();
        }
        return $this->_currentIndex;
    }

    /**
     * Retrieve position limit product attribute name
     *
     * @return string
     */
    protected function _getPositionLimitField()
    {
        return sprintf('%s_tgtr_position_limit', $this->_getTypePrefix());
    }

    /**
     * Retrieve position behavior product attribute name
     *
     * @return string
     */
    protected function _getPositionBehaviorField()
    {
        return sprintf('%s_tgtr_position_behavior', $this->_getTypePrefix());
    }

    /**
     * Retrieve Maximum Number Of Product
     *
     * @return int
     */
    public function getPositionLimit()
    {
        $limit = $this->getProduct()->getData($this->_getPositionLimitField());
        if (is_null($limit)) { // use configuration settings
            $limit = $this->_targetRuleData->getMaximumNumberOfProduct($this->getProductListType());
            $this->getProduct()->setData($this->_getPositionLimitField(), $limit);
        }
        return $this->_targetRuleData->getMaxProductsListResult($limit);
    }

    /**
     * Retrieve Position Behavior
     *
     * @return int
     */
    public function getPositionBehavior()
    {
        $behavior = $this->getProduct()->getData($this->_getPositionBehaviorField());
        if (is_null($behavior)) { // use configuration settings
            $behavior = $this->_targetRuleData->getShowProducts($this->getProductListType());
            $this->getProduct()->setData($this->_getPositionBehaviorField(), $behavior);
        }
        return $behavior;
    }

    /**
     * Retrieve array of exclude product ids
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        if (is_null($this->_excludeProductIds)) {
            $this->_excludeProductIds = array($this->getProduct()->getEntityId());
        }
        return $this->_excludeProductIds;
    }

    /**
     * Get link collection with limit parameter
     *
     * @throws \Magento\Core\Exception
     * @param null|int $limit
     * @return \Magento\Catalog\Model\Resource\Product\Link\Product\Collection|null
     */
    protected function _getPreparedTargetLinkCollection($limit = null)
    {
        $linkCollection = null;
        switch ($this->getProductListType()) {
            case \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS:
                $linkCollection = $this->getProduct()
                    ->getRelatedProductCollection();
                break;

            case \Magento\TargetRule\Model\Rule::UP_SELLS:
                $linkCollection = $this->getProduct()
                    ->getUpSellProductCollection();
                break;

            default:
                throw new \Magento\Core\Exception(__('Undefined Catalog Product List Type'));
        }

        if (!is_null($limit)) {
            $this->_addProductAttributesAndPrices($linkCollection);
            $linkCollection->setPageSize($limit);
        }

        $linkCollection
            ->setVisibility($this->_visibility->getVisibleInCatalogIds())
            ->setFlag('do_not_use_category_id', true);

        $excludeProductIds = $this->getExcludeProductIds();
        if ($excludeProductIds) {
            $linkCollection->addAttributeToFilter('entity_id', array('nin' => $excludeProductIds));
        }

        return $linkCollection;
    }

    /**
     * Get link collection for related and up-sell
     *
     * @return \Magento\Catalog\Model\Resource\Product\Collection|null
     */
    protected function _getTargetLinkCollection()
    {
        return $this->_getPreparedTargetLinkCollection($this->getPositionLimit());
    }

    /**
     * Retrieve count of related linked products assigned to product
     *
     * @return int
     */
    public function getLinkCollectionCount()
    {
        return count($this->getLinkCollection()->getItems());
    }

    /**
     * Get target rule collection ids
     *
     * @param null|int $limit
     * @return array
     */
    protected function _getTargetRuleProductIds($limit = null)
    {
        $excludeProductIds = $this->getExcludeProductIds();
        if (!is_null($this->_items)) {
            $excludeProductIds = array_merge(array_keys($this->_items), $excludeProductIds);
        }
        $indexModel = $this->_getTargetRuleIndex()
            ->setType($this->getProductListType())
            ->setLimit($limit)
            ->setProduct($this->getProduct())
            ->setExcludeProductIds($excludeProductIds);
        if (!is_null($limit)) {
            $indexModel->setLimit($limit);
        }

        return $indexModel->getProductIds();
    }

    /**
     * Get target rule collection for related and up-sell
     *
     * @return array
     */
    protected function _getTargetRuleProducts()
    {
        $limit = $this->getPositionLimit();

        $productIds = $this->_getTargetRuleProductIds($limit);

        $items = array();
        if ($productIds) {
            /** @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
            $collection = $this->_productCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
            $this->_addProductAttributesAndPrices($collection);

            $collection->setPageSize($limit)
                ->setFlag('do_not_use_category_id', true)
                ->setVisibility($this->_visibility->getVisibleInCatalogIds());

            foreach ($collection as $item) {
                $items[$item->getEntityId()] = $item;
            }
        }

        return $items;
    }

    /**
     * Check is has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemsCount() > 0;
    }

    /**
     * Retrieve count of product in collection
     *
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->getItemCollection());
    }

    /**
     * Get ids of all assigned products
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_allProductIds)) {
            if (!$this->isShuffled()) {
                $this->_allProductIds = array_keys($this->getItemCollection());
                return $this->_allProductIds;
            }

            $targetRuleProductIds = $this->_getTargetRuleProductIds();
            $linkProductCollection = $this->_getPreparedTargetLinkCollection();
            $linkProductIds = array();
            foreach ($linkProductCollection as $item) {
                $linkProductIds[] = $item->getEntityId();
            }
            $this->_allProductIds = array_unique(array_merge($targetRuleProductIds, $linkProductIds));
            shuffle($this->_allProductIds);
        }

        return $this->_allProductIds;
    }
}
