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
 * Adminhtml review main block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Review_Main extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_addButtonLabel = Mage::helper('Mage_Review_Helper_Data')->__('Add New Review');
        parent::_construct();

        $this->_controller = 'review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = Mage::getModel('Mage_Customer_Model_Customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerName = $this->escapeHtml($customerName);
        }
        $productId = $this->getRequest()->getParam('productId', false);
        $productName = null;
        if ($productId) {
            $product = Mage::getModel('Mage_Catalog_Model_Product')->load($productId);
            $productName =  $this->escapeHtml($product->getName());
        }

        if( Mage::registry('usePendingFilter') === true ) {
            if ($customerName) {
                $this->_headerText = Mage::helper('Mage_Review_Helper_Data')->__('Pending Reviews of Customer `%1`', $customerName);
            } else {
                $this->_headerText = Mage::helper('Mage_Review_Helper_Data')->__('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = Mage::helper('Mage_Review_Helper_Data')->__('All Reviews of Customer `%1`', $customerName);
            } elseif ($productName) {
                $this->_headerText = Mage::helper('Mage_Review_Helper_Data')->__('All Reviews of Product `%1`', $productName);
            } else {
                $this->_headerText = Mage::helper('Mage_Review_Helper_Data')->__('All Reviews');
            }
        }
    }
}
