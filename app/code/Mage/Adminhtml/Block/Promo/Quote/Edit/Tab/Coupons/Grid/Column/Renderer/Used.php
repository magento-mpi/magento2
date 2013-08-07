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
 * Coupon codes grid "Used" column renderer
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Coupons_Grid_Column_Renderer_Used
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    public function render(Magento_Object $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        return empty($value) ? Mage::helper('Mage_Adminhtml_Helper_Data')->__('No') : Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes');
    }
}
