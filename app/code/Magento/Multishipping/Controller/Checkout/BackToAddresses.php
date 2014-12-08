<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Multishipping\Controller\Checkout;

use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;

class BackToAddresses extends \Magento\Multishipping\Controller\Checkout
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_getState()->setActiveStep(State::STEP_SELECT_ADDRESSES);
        $this->_getState()->unsCompleteStep(State::STEP_SHIPPING);
        $this->_redirect('*/*/addresses');
    }
}
