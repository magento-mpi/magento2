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
 * Adminhtml review grid item renderer for item type
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Review_Grid_Renderer_Type extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(\Magento\Object $row)
    {

        if (is_null($row->getCustomerId())) {
            if ($row->getStoreId() == Magento_Core_Model_AppInterface::ADMIN_STORE_ID) {
                return __('Administrator');
            } else {
                return __('Guest');
            }
        } elseif ($row->getCustomerId() > 0) {
            return __('Customer');
        }
//		return ($row->getCustomerId() ? __('Customer') : __('Guest'));
    }
}// Class Magento_Adminhtml_Block_Review_Grid_Renderer_Type END
