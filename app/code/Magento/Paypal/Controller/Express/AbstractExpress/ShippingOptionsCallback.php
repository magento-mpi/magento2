<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Express\AbstractExpress;

class ShippingOptionsCallback extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Return shipping options items for shipping address from request
     *
     * @return void
     */
    public function execute()
    {
        try {
            $quoteId = $this->getRequest()->getParam('quote_id');
            $this->_quote = $this->_quoteFactory->create()->load($quoteId);
            $this->_initCheckout();
            $response = $this->_checkout->getShippingOptionsCallbackResponse($this->getRequest()->getParams());
            $this->getResponse()->setBody($response);
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
    }
}
