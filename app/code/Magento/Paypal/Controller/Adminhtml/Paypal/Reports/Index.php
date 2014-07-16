<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Controller\Adminhtml\Paypal\Reports;

class Index extends \Magento\Paypal\Controller\Adminhtml\Paypal\Reports
{
    /**
     * Grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
