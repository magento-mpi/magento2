<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Product Downloads Purchases Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Report_Product_Downloads_Renderer_Purchases
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Purchases value
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        if ( ($value = $row->getData($this->getColumn()->getIndex())) > 0) {
            return $value;
        }
        return __('Unlimited');
    }
}
