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
 * Adminhtml grid product name column renderer
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage\Grid\Renderer;

class Product extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render product name to add Configure link
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $rendered       =  parent::render($row);
        $listType = $this->getColumn()->getGrid()->getListType();
        if ($row instanceof \Magento\Catalog\Model\Product) {
            $product = $row;
        } else if (($row instanceof \Magento\Wishlist\Model\Item) || ($row instanceof \Magento\Sales\Model\Order\Item)) {
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
