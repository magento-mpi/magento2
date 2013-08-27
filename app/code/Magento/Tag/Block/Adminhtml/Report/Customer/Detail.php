<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tags detail for customer report blocks content block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Customer_Detail extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_report_customer_detail';

        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($this->getRequest()->getParam('id'));
        $customerName = $this->escapeHtml($customer->getName());
        $this->_headerText = __('Tags Submitted by %1', $customerName);
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/customer/'));
        $this->_addBackButton();
    }
}
