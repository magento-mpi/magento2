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
 * Adminhtml customers online grid renderer for customer type.
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Online_Grid_Renderer_Type extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(\Magento\Object $row)
    {
        return ($row->getCustomerId() > 0 ) ? __('Customer') : __('Visitor') ;
    }

}
