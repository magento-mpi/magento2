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
 * Adminhtml customers online grid renderer for customer type.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Online_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Magento_Object $row)
    {
        return ($row->getCustomerId() > 0 ) ? Mage::helper('Mage_Customer_Helper_Data')->__('Customer') : Mage::helper('Mage_Customer_Helper_Data')->__('Visitor') ;
    }

}
