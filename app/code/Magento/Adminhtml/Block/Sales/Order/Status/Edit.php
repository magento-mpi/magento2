<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Sales_Order_Status_Edit extends Magento_Adminhtml_Block_Sales_Order_Status_New
{
    protected function _construct()
    {
        parent::_construct();
        $this->_mode = 'edit';
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Edit Order Status');
    }
}
