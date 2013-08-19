<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend new accounts report page content block
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_Reports_Block_Adminhtml_Customer_Accounts extends Mage_Backend_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'Mage_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_accounts';
        $this->_headerText = __('New Accounts');
        parent::_construct();
        $this->_removeButton('add');
    }

}
