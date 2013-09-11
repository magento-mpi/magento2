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
 * Adminhtml review main block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review;

class Main extends \Magento\Adminhtml\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_addButtonLabel = __('Add New Review');
        parent::_construct();

        $this->_controller = 'review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($customerId);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerName = $this->escapeHtml($customerName);
        }
        $productId = $this->getRequest()->getParam('productId', false);
        $productName = null;
        if ($productId) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($productId);
            $productName =  $this->escapeHtml($product->getName());
        }

        if( \Mage::registry('usePendingFilter') === true ) {
            if ($customerName) {
                $this->_headerText = __('Pending Reviews of Customer `%1`', $customerName);
            } else {
                $this->_headerText = __('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = __('All Reviews of Customer `%1`', $customerName);
            } elseif ($productName) {
                $this->_headerText = __('All Reviews of Product `%1`', $productName);
            } else {
                $this->_headerText = __('All Reviews');
            }
        }
    }
}
