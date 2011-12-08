<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in wishlist
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Wishlist
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Abstract
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
     * Initialize Grid
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('source_wishlist');
        $this->setDefaultSort('added_at');
        $this->setData('open', true);
        if ($this->_getStore()) {
            $this->setHeaderText(
                Mage::helper('Enterprise_Checkout_Helper_Data')->__('Wishlist (%s)', $this->getItemsCount())
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
     * Return items collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist')->loadByCustomer($this->_getCustomer())
                ->setStore($this->_getStore())
                ->setSharedStoreIds($this->_getStore()->getWebsite()->getStoreIds());
            if ($wishlist->getId()) {
                $collection = $wishlist->getItemCollection()
                    ->setSalableFilter()
                    ->resetSortOrder();
            } else {
                $collection = parent::getItemsCollection();
            }
            foreach ($collection as $item) {
                if ($item->getProduct()) {
                    $item->setName($item->getProduct()->getName());
                    $item->setPrice($item->getProduct()->getPrice());
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
     * @return Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Wishlist
     */
    protected function _addControlColumns()
    {
        parent::_addControlColumns();

        $this->addColumn('qty', array(
            'sortable'  => false,
            'header'    => Mage::helper('Enterprise_Checkout_Helper_Data')->__('Qty To Add'),
            'renderer'  => 'Enterprise_Checkout_Block_Adminhtml_Manage_Grid_Renderer_Wishlist_Qty',
            'name'      => 'qty',
            'inline_css'=> 'qty',
            'align'     => 'right',
            'type'      => 'input',
            'validate_class' => 'validate-number',
            'index'     => 'qty',
            'width'     => '1',
        ));

        return $this;
    }
}
