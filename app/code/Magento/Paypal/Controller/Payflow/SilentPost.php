<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Payflow;

class SilentPost extends \Magento\Paypal\Controller\Payflow
{
    /**
     * Get response from PayPal by silent post method
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel \Magento\Paypal\Model\Payflowlink */
            $paymentModel = $this->_payflowModelFactory->create();
            try {
                $paymentModel->process($data);
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }
    }
}
