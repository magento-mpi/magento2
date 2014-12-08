<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment;

use Magento\Customer\Controller\RegistryConstants;

class CustomerGrid extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * Customer grid ajax action
     *
     * @return void
     */
    public function execute()
    {
        $customerId = (int)$this->getRequest()->getParam(self::PARAM_CUSTOMER_ID);

        if ($customerId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, $customerId);
        }

        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
