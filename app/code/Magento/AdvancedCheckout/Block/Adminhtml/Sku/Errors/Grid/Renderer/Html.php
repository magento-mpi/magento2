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
 * Description renderer
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Html
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Return data "as is", don't escape HTML
     *
     * @param \Magento\Object $row
     * @return mixed
     */
    public function render(\Magento\Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
