<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Controller\Adminhtml\Report\Sales;

class RefreshLifetime extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Refresh statistics for all period
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('refreshLifetime', 'report_statistics');
    }
}
