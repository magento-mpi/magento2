<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_title->add($helper->getOperationHeaderText($operationType, 'edit'));

        $this->_view->renderLayout();
    }
}
