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
 * Accordion grid for products in wishlist
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_MultipleWishlist_Block_Adminhtml_Manage_Accordion_Wishlist
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Wishlist
{
    /**
     * Item collection factory
     *
     * @var Magento_MultipleWishlist_Model_Resource_Item_Collection
     */
    protected $_itemCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Data_CollectionFactory $collectionFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Model_ItemFactory $itemFactory
     * @param Magento_MultipleWishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory
     * @param array $data
     */
    public function __construct(
        Magento_Data_CollectionFactory $collectionFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Model_ItemFactory $itemFactory,
        Magento_MultipleWishlist_Model_Resource_Item_CollectionFactory $itemCollectionFactory,
        array $data = array()
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($collectionFactory, $coreData, $context, $storeManager, $urlModel, $coreRegistry,
            $itemFactory, $data);
    }

    /**
     * Return items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createItemsCollection()
    {
        return $this->_itemCollectionFactory->create();
    }

    /**
     * Prepare Grid columns
     *
     * @return Magento_MultipleWishlist_Block_Adminhtml_Manage_Accordion_Wishlist
     */
    protected function _prepareColumns()
    {
        $this->addColumn('wishlist_name', array(
            'header'    => __('Wishlist'),
            'index'     => 'wishlist_name',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }
}
