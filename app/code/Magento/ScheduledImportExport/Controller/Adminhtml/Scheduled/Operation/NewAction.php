<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class NewAction extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Create new operation action.
     *
     * @return void
     */
    public function execute()
    {
        $operationType = $this->getRequest()->getParam('type');
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $this->_objectManager->get(
                'Magento\ScheduledImportExport\Helper\Data'
            )->getOperationHeaderText(
                $operationType,
                'new'
            )
        );

        $this->_view->renderLayout();
    }
}
