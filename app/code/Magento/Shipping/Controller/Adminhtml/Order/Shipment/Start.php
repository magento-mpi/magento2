<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Controller\Adminhtml\Order\Shipment;

class Start extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::shipment');
    }

    /**
     * Start create shipment action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Clear old values for shipment qty's
         */
        $this->_redirect('*/*/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }
}
