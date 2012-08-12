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
 * Adminhtml tags detail for customer report blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Tag_Customer_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'report_tag_customer_detail';

        $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($this->getRequest()->getParam('id'));
        $customerName = $this->escapeHtml($customer->getName());
        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Tags Submitted by %s', $customerName);
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/customer/'));
        $this->_addBackButton();
    }

}
