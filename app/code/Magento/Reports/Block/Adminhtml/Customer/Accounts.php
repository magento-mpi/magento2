<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Customer;

/**
 * Backend new accounts report page content block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Accounts extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var string
     */
    protected $_blockGroup = 'Magento_Reports';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_customer_accounts';
        $this->_headerText = __('New Accounts');
        parent::_construct();
        $this->_removeButton('add');
    }

}
