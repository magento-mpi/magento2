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
 * Adminhtml sales transactions block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Transactions extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_transactions';
        $this->_headerText = __('Transactions');
        parent::_construct();
        $this->_removeButton('add');
    }

}
