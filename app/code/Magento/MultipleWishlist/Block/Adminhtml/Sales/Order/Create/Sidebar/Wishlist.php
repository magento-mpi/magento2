<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml customer orders grid block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_MultipleWishlist_Block_Adminhtml_Sales_Order_Create_Sidebar_Wishlist
    extends Magento_Adminhtml_Block_Sales_Order_Create_Sidebar_Wishlist
{
    /**
     * Item collection factory
     *
     * @var Magento_MultipleWishlist_Model_Resource_Item_CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_MultipleWishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        Magento_MultipleWishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($coreData, $context, $coreConfig, $data);
    }

    /**
     * Retrieve item collection
     *
     * @return Magento_MultipleWishlist_Model_Resource_Item_Collection
     */
    public function getItemCollection()
    {
        $collection = $this->getData('item_collection');
        $storeIds = $this->getCreateOrderModel()->getSession()->getStore()->getWebsite()->getStoreIds();
        if (is_null($collection)) {
            /** @var Magento_MultipleWishlist_Model_Resource_Item_Collection $collection */
            $collection = $this->_itemCollectionFactory->create();
            $collection->addCustomerIdFilter($this->getCustomerId())
                ->addStoreFilter($storeIds)
                ->setVisibilityFilter();
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
        $wishlists = array();
        /* @var $item Magento_Wishlist_Model_Item */
        foreach ($this->getItemCollection() as $item) {
            $wishlists[$item->getWishlistId()] = $item->getWishlistName();
        }
        ksort($wishlists);
        return $wishlists;
    }
}

