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
 * Adminhtml grid product name column renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Grid_Renderer_Product extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $rendered       =  parent::render($row);
        $listType = $this->getColumn()->getGrid()->getListType();
        if ($row instanceof Magento_Catalog_Model_Product) {
            $product = $row;
        } else if (($row instanceof Magento_Wishlist_Model_Item) || ($row instanceof Magento_Sales_Model_Order_Item)) {
            $product = $row->getProduct();
        }
        if ($product->canConfigure()) {
            $style = '';
            $prodAttributes = sprintf('list_type = "%s" item_id = %s', $listType, $row->getId());
        } else {
            $style = 'disabled';
            $prodAttributes = 'disabled="disabled"';
        }
        return sprintf('<a href="javascript:void(0)" %s class="action-configure %s">%s</a>',
            $style, $prodAttributes, __('Configure')) . $rendered;
    }
}
