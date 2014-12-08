<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment;

use Magento\Framework\App\Action\NotFoundException;

class Orders extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * Payment orders ajax grid
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        try {
            $this->_initPayment();
            $this->_view->loadLayout()->renderLayout();
        } catch (\Exception $e) {
            $this->_logger->logException($e);
            throw new NotFoundException();
        }
    }
}
