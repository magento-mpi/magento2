<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in wishlist
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion;

class Wishlist
    extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\AbstractAccordion
{
    /**
     * Collection field name for using in controls
     * @var string
     */
    protected $_controlFieldName = 'wishlist_item_id';

    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'wishlist';

    /**
     * Url to configure this grid's items
     */
    protected $_configureRoute = '*/checkout/configureWishlistItem';

    /**
     * Wishlist item factory
     *
     * @var Magento_Wishlist_Model_ItemFactory
     */
    protected $_itemFactory = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Wishlist_Model_ItemFactory $itemFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Wishlist_Model_ItemFactory $itemFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $coreRegistry, $data);
        $this->_itemFactory = $itemFactory;
    }

    /**
     * Initialize Grid
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_wishlist');
        $this->setDefaultSort('added_at');
        $this->setData('open', true);
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Wish List (%1)', $this->getItemsCount())
            );
        }
    }

    /**
     * Return custom object name for js grid
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return 'wishlistItemsGrid';
    }

    /**
     * Create wishlist item collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createItemsCollection()
    {
        return $this->_itemFactory->create()->getCollection();
    }

    /**
     * Return items collection
     *
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $collection = $this->_createItemsCollection()
                ->addCustomerIdFilter($this->_getCustomer()->getId())
                ->addStoreFilter($this->_getStore()->getWebsite()->getStoreIds())
                ->setVisibilityFilter()
                ->setSalableFilter()
                ->resetSortOrder();

            foreach ($collection as $item) {
                $product = $item->getProduct();
                if ($product) {
                    if (!$product->getStockItem()->getIsInStock() || !$product->isInStock()) {
                        // Remove disabled and out of stock products from the grid
                        $collection->removeItemByKey($item->getId());
                    } else {
                        $item->setName($product->getName());
                        $item->setPrice($product->getPrice());
                    }
                }

            }
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewWishlist', array('_current'=>true));
    }

    /**
     * Add columns with controls to manage added products and their quantity
     * Uses inherited methods, but modifies Qty column to change renderer
     *
     * @return \Magento\AdvancedCheckout\Block\Adminhtml\Manage\Accordion\Wishlist
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();
        $this->getColumn('qty')->addData(array(
            'renderer' => 'Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer\Wishlist\Qty'
        ));

        return $this;
    }
}
