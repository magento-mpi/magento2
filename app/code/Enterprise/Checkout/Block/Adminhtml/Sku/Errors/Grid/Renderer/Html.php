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
 * Description renderer
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Grid_Renderer_Html
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Return data "as is", don't escape HTML
     *
     * @param Magento_Object $row
     * @return mixed
     */
    public function render(Magento_Object $row)
    {
        return $row->getData($this->getColumn()->getIndex());
    }
}
