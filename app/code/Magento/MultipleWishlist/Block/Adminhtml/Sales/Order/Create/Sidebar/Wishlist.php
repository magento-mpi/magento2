<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\MultipleWishlist\Block\Adminhtml\Sales\Order\Create\Sidebar;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml customer orders grid block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Wishlist extends \Magento\Sales\Block\Adminhtml\Order\Create\Sidebar\Wishlist
{
    /**
     * Item collection factory
     *
     * @var \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\MultipleWishlist\Model\Resource\Item\CollectionFactory $itemCollectionFactory,
        array $data = []
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $salesConfig, $data);
    }

    /**
     * Retrieve item collection
     *
     * @return \Magento\MultipleWishlist\Model\Resource\Item\Collection
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        $storeIds = $this->getCreateOrderModel()->getSession()->getStore()->getWebsite()->getStoreIds();
        if (is_null($collection)) {
            /** @var \Magento\MultipleWishlist\Model\Resource\Item\Collection $collection */
            $collection = $this->_itemCollectionFactory->create();
            $collection->addCustomerIdFilter($this->getCustomerId())->addStoreFilter($storeIds)->setVisibilityFilter();
            if ($collection) {
                $collection = $collection->load();
            }
            $this->setData('item_collection', $collection);
        }
        return $collection;
    }

    /**
     * Retrieve list of customer wishlists with items
     *
     * @return array
     */
    public function getWishlists()
    {
        $wishlists = [];
        /* @var $item \Magento\Wishlist\Model\Item */
        foreach ($this->getItemCollection() as $item) {
            $wishlists[$item->getWishlistId()] = $item->getWishlistName();
        }
        ksort($wishlists);
        return $wishlists;
    }
}
