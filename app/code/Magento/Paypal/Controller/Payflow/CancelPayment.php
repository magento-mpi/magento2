<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Controller\Payflow;

class CancelPayment extends \Magento\Paypal\Controller\Payflow
{
    /**
     * When a customer cancel payment from payflow gateway.
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_view->getLayout()->getBlock($this->_redirectBlockName);
        $redirectBlock->setGotoSection($gotoSection);
        $this->_view->renderLayout();
    }
}
