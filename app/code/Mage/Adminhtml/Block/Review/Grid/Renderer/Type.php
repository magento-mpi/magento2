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
 * Adminhtml review grid item renderer for item type
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Grid_Renderer_Type extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Magento_Object $row)
    {

        if (is_null($row->getCustomerId())) {
            if ($row->getStoreId() == Mage_Core_Model_AppInterface::ADMIN_STORE_ID) {
                return Mage::helper('Mage_Review_Helper_Data')->__('Administrator');
            } else {
                return Mage::helper('Mage_Review_Helper_Data')->__('Guest');
            }
        } elseif ($row->getCustomerId() > 0) {
            return Mage::helper('Mage_Review_Helper_Data')->__('Customer');
        }
//		return ($row->getCustomerId() ? Mage::helper('Mage_Review_Helper_Data')->__('Customer') : Mage::helper('Mage_Review_Helper_Data')->__('Guest'));
    }
}// Class Mage_Adminhtml_Block_Review_Grid_Renderer_Type END
