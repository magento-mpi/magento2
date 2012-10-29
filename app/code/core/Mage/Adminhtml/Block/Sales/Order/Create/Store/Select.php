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
 * Adminhtml sales order create select store block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Store_Select extends Mage_Adminhtml_Block_Store_Switcher
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sc_store_select');
    }
}
