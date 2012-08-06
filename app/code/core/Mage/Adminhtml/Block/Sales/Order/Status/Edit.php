<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Sales_Order_Status_Edit extends Mage_Adminhtml_Block_Sales_Order_Status_New
{
    public function __construct()
    {
        parent::__construct();
        $this->_mode = 'edit';
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Mage_Sales_Helper_Data')->__('Edit Order Status');
    }
}
