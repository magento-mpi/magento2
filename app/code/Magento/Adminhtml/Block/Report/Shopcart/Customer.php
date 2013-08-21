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
 * Adminhtml Shopping cart customers report page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Report_Shopcart_Customer extends Magento_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_controller = 'report_shopcart_customer';
        $this->_headerText = __('Customers');
        parent::_construct();
        $this->_removeButton('add');
    }
}
