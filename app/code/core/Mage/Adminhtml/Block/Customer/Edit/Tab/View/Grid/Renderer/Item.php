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
 * Adminhtml customers wishlist grid item renderer for name/options cell
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Constructor to set default template
     *
     * @return Mage_Adminhtml_Block_Customer_Edit_Tab_View_Grid_Renderer_Item
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('customer/edit/tab/view/grid/item.phtml');
        return $this;
    }

    /**
     * Returns helper for product type
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Helper_Product_Configuration_Interface
     */
    protected function _getProductHelper($product)
    {
        // Retrieve whole array of renderers
        $productHelpers = $this->getProductHelpers();
        if (!is_array($productHelpers)) {
            $column = $this->getColumn();
            if ($column) {
                $grid = $column->getGrid();
                if ($grid) {
                    $productHelpers = $grid->getProductConfigurationHelpers();
                    $this->setProductHelpers($productHelpers ? $productHelpers : array());
                }
            }
        }

        // Check whether we have helper for our product
        $productType = $product->getTypeId();
        if (isset($productHelpers[$productType])) {
            $helperName = $productHelpers[$productType];
        } else if (isset($productHelpers['default'])) {
            $helperName = $productHelpers['default'];
        } else {
            $helperName = 'Mage_Catalog_Helper_Product_Configuration';
        }

        $helper = Mage::helper($helperName);
        if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
            Mage::throwException($this->__("Helper for options rendering doesn't implement required interface."));
        }

        return $helper;
    }

    /*
     * Returns product associated with this block
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    /**
     * Returns list of options and their values for product configuration
     *
     * @return array
     */
    protected function getOptionList()
    {
        $item = $this->getItem();
        $product = $item->getProduct();
        $helper = $this->_getProductHelper($product);
        return $helper->getOptions($item);
    }

    /**
     * Returns formatted option value for an item
     *
     * @param Mage_Wishlist_Item_Option
     * @return array
     */
    protected function getFormattedOptionValue($option)
    {
        $params = array(
            'max_length' => 55
        );
        return Mage::helper('Mage_Catalog_Helper_Product_Configuration')->getFormattedOptionValue($option, $params);
    }

    /*
     * Renders item product name and its configuration
     *
     * @param Mage_Catalog_Model_Product_Configuration_Item_Interface $item
     * @return string
     */
    public function render(Varien_Object $item)
    {
        $this->setItem($item);
        return $this->toHtml();
    }
}
