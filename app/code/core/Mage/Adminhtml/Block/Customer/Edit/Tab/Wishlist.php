<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml customer orders grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Default sort field
     *
     * @var string
     */
    protected $_defaultSort = 'added_at';

    /**
     * List of helpers to show options for product cells
     *
     * @var array
     */
    protected $_productHelpers = array();

    /**
     * Initialize Grid
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);
        $this->addProductConfigurationHelper('default', 'Mage_Catalog_Helper_Product_Configuration');
    }

    /**
     * Retrieve current customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Add column filter to collection
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _addColumnFilterToCollection($column)
    {
        /* @var $collection Mage_Wishlist_Model_Resource_Item_Collection */
        $collection = $this->getCollection();
        $value = $column->getFilter()->getValue();
        if ($collection && $value) {
            switch ($column->getId()) {
                case 'product_name':
                    $collection->addProductNameFilter($value);
                    break;
                case 'store':
                    $collection->addStoreFilter($value);
                    break;
                case 'days':
                    $collection->addDaysFilter($value);
                    break;
                default:
                    $collection->addFieldToFilter($column->getIndex(), $column->getFilter()->getCondition());
                    break;
            }
        }
        return $this;
    }

    /**
     * Sets sorting order by some column
     *
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            switch ($column->getId()) {
                case 'product_name':
                    $collection->setOrderByProductName($column->getDir());
                    break;
                default:
                    parent::_setCollectionOrder($column);
                    break;
            }
        }
        return $this;
    }

    /**
     * Adds product type helper depended on product type (used to show options in item cell)
     *
     * @param string $productType
     * @param string $helperName
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_Wishlist
     */
    public function addProductConfigurationHelper($productType, $helperName)
    {
        $this->_productHelpers[$productType] = $helperName;
        return $this;
    }

    /**
     * Returns array of product configuration helpers
     *
     * @return array
     */
    public function getProductConfigurationHelpers()
    {
        return $this->_productHelpers;
    }

    /**
     * Initialize grid
     */
    protected function _prepareGrid()
    {
        if (false == Mage::app()->isSingleStoreMode()) {
            $blockId = 'store';
            $column = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Column');
            $column->setData(array(
                    'header' => Mage::helper('Mage_Wishlist_Helper_Data')->__('Added From'),
                    'index'  => 'store_id',
                    'type'   => 'store',
                    'width'  => '160px',
                    'sort_order' => 50
                )
            );

            $this->getColumnSet()->insert($column, 'customer.wishlist.edit.tab.columnSet.column.qty', true, $blockId);
        }

        parent::_prepareGrid();
    }
}
