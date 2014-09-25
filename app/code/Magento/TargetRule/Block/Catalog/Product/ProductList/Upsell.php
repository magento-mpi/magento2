<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Catalog Product List Upsell Block
 *
 */
namespace Magento\TargetRule\Block\Catalog\Product\ProductList;

class Upsell extends \Magento\TargetRule\Block\Catalog\Product\ProductList\AbstractProductList
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\TargetRule\Model\Resource\Index $index
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\TargetRule\Model\IndexFactory $indexFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\TargetRule\Model\Resource\Index $index,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\TargetRule\Model\IndexFactory $indexFactory,
        \Magento\Checkout\Model\Cart $cart,
        array $data = array()
    ) {
        $this->_cart = $cart;
        parent::__construct(
            $context,
            $index,
            $targetRuleData,
            $productCollectionFactory,
            $visibility,
            $indexFactory,
            $data
        );
    }

    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return \Magento\TargetRule\Model\Rule::UP_SELLS;
    }

    /**
     * Retrieve related product collection assigned to product
     *
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getLinkCollection()
    {
        if (is_null($this->_linkCollection)) {
            parent::getLinkCollection();
            /**
             * Updating collection with desired items
             */
            $this->_eventManager->dispatch(
                'catalog_product_upsell',
                array(
                    'product' => $this->getProduct(),
                    'collection' => $this->_linkCollection,
                    'limit' => $this->getPositionLimit()
                )
            );
        }

        return $this->_linkCollection;
    }

    /**
     * Get ids of all related products
     *
     * @return array
     */
    public function getAllIds()
    {
        if (is_null($this->_allProductIds)) {
            if (!$this->isShuffled()) {
                return parent::getAllIds();
            }

            $ids = parent::getAllIds();
            $ids = new \Magento\Framework\Object(array('items' => array_flip($ids)));
            /**
             * Updating collection with desired items
             */
            $this->_eventManager->dispatch(
                'catalog_product_upsell',
                array('product' => $this->getProduct(), 'collection' => $ids, 'limit' => null)
            );

            $this->_allProductIds = array_keys($ids->getItems());
            shuffle($this->_allProductIds);
        }

        return $this->_allProductIds;
    }

    /**
     * Get all items
     *
     * @return array
     */
    public function getAllItems()
    {
        $collection = parent::getAllItems();
        $collectionMock = new \Magento\Framework\Object(array('items' => $collection));
        $this->_eventManager->dispatch(
            'catalog_product_upsell',
            array(
                'product'       => $this->getProduct(),
                'collection'    => $collectionMock,
                'limit'         => null
            )
        );
        return $collectionMock->getItems();
    }

    /**
     * Retrieve array of exclude product ids
     * Rewrite for exclude shopping cart products
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        if (is_null($this->_excludeProductIds)) {
            $cartProductIds = $this->_cart->getProductIds();
            $this->_excludeProductIds = array_merge($cartProductIds, array($this->getProduct()->getEntityId()));
        }
        return $this->_excludeProductIds;
    }
}
