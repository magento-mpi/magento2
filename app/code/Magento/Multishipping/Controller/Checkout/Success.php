<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout;

use \Magento\Multishipping\Model\Checkout\Type\Multishipping\State;

class Success extends \Magento\Multishipping\Controller\Checkout
{
    /**
     * Multishipping checkout success page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_getState()->getCompleteStep(State::STEP_OVERVIEW)) {
            $this->_redirect('*/*/addresses');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $ids = $this->_getCheckout()->getOrderIds();
        $this->_eventManager->dispatch('multishipping_checkout_controller_success_action', array('order_ids' => $ids));
        $this->_view->renderLayout();
    }
}
