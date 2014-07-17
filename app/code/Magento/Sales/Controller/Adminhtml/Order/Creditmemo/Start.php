<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Creditmemo;

class Start extends \Magento\Backend\App\Action
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Sales::sales_creditmemo');
    }

    /**
     * Start create creditmemo action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * Clear old values for creditmemo qty's
         */
        $this->_redirect('sales/*/new', array('_current' => true));
    }
}
