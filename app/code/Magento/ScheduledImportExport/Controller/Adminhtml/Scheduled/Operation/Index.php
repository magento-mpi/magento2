<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class Index extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Index action.
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();
        $this->_view->renderLayout();
    }
}
