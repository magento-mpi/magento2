<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Express\AbstractExpress;

class UpdateShippingMethods extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Update Order (combined action for ajax and regular request)
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initCheckout();
            $this->_checkout->prepareOrderReview($this->_initToken());
            $this->_view->loadLayout('paypal_express_review');

            $this->getResponse()->setBody(
                $this->_view
                    ->getLayout()
                    ->getBlock('express.review.shipping.method')
                    ->setQuote($this->_getQuote())
                    ->toHtml()
            );
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t update shipping method.'));
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
        }
        $this->getResponse()->setBody(
            '<script type="text/javascript">window.location.href = ' . $this->_url->getUrl('*/*/review') . ';</script>'
        );
    }
}
