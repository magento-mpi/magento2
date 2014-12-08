<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment;

use Magento\Framework\Model\Exception as CoreException;

class Grid extends \Magento\RecurringPayment\Controller\Adminhtml\RecurringPayment
{
    /**
     * Payments ajax grid
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_view->loadLayout()->renderLayout();
            return;
        } catch (CoreException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
        $this->_redirect('sales/*/');
    }
}
