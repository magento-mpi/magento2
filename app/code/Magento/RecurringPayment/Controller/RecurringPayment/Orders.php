<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\RecurringPayment;

class Orders extends \Magento\RecurringPayment\Controller\RecurringPayment
{
    /**
     * Payment related orders view
     *
     * @return void
     */
    public function execute()
    {
        $this->_viewAction();
    }
}
