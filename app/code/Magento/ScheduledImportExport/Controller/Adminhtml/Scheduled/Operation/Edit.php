<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class Edit extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Edit operation action.
     *
     * @return void
     */
    public function execute()
    {
        $this->_initAction();

        /** @var $operation \Magento\ScheduledImportExport\Model\Scheduled\Operation */
        $operation = $this->_coreRegistry->registry('current_operation');
        $operationType = $operation->getOperationType();

        /** @var $helper \Magento\ScheduledImportExport\Helper\Data */
        $helper = $this->_objectManager->get('Magento\ScheduledImportExport\Helper\Data');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $helper->getOperationHeaderText($operationType, 'edit')
        );

        $this->_view->renderLayout();
    }
}
