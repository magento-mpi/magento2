<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Billing\Agreement;

class Index extends \Magento\Paypal\Controller\Billing\Agreement
{
    /**
     * View billing agreements
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Billing Agreements'));
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}
