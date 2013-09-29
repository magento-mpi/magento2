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
 * Adminhtml creditmemo create form
 */

namespace Magento\Adminhtml\Block\Sales\Order\Creditmemo\Create;

class Form extends \Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getSource()
    {
        return $this->getCreditmemo();
    }

    /**
     * Retrieve creditmemo model instance
     *
     * @return Magento_Sales_Model_Creditmemo
     */
    public function getCreditmemo()
    {
        return $this->_coreRegistry->registry('current_creditmemo');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true));
    }
}
