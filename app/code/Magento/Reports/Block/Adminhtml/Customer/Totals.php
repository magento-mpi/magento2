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
 * Backend customers by totals report content block
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Reports\Block\Adminhtml\Customer;

class Totals extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected $_blockGroup = 'Magento_Reports';

    protected function _construct()
    {
        $this->_controller = 'report_customer_totals';
        $this->_headerText = __('Customers by Orders Total');
        parent::_construct();
        $this->_removeButton('add');
    }
}
