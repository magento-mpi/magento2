<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend new accounts report page content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Reports_Block_Adminhtml_Customer_Accounts extends Magento_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Magento_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_accounts';
        $this->_headerText = __('New Accounts');
        parent::_construct();
        $this->_removeButton('add');
    }

}
