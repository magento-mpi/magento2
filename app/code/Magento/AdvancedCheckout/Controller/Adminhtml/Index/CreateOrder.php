<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\Framework\Model\Exception;

class CreateOrder extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Redirect to order creation page based on current quote
     *
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        if (!$this->_authorization->isAllowed('Magento_Sales::create')) {
            throw new Exception(__('You do not have access to this.'));
        }
        try {
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }
            $activeQuote = $this->getCartModel()->getQuote();
            $quote = $this->getCartModel()->copyQuote($activeQuote);
            if ($quote->getId()) {
                $session = $this->_objectManager->get('Magento\Sales\Model\AdminOrder\Create')->getSession();
                $session->setQuoteId(
                    $quote->getId()
                )->setStoreId(
                    $quote->getStoreId()
                )->setCustomerId(
                    $quote->getCustomerId()
                );
            }
            $this->_redirect(
                'sales/order_create',
                [
                    'customer_id' => $this->_registry->registry('checkout_current_customer')->getId(),
                    'store_id' => $this->_registry->registry('checkout_current_store')->getId()
                ]
            );
            return;
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            $this->messageManager->addError(__('An error has occurred. See error log for details.'));
        }
        $this->_redirect('checkout/*/error');
    }
}
