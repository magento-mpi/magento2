<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation;

class LogClean extends \Magento\ScheduledImportExport\Controller\Adminhtml\Scheduled\Operation
{
    /**
     * Run log cleaning through http request.
     *
     * @return void
     */
    public function execute()
    {
        $schedule = new \Magento\Framework\Object();
        $result = $this->_objectManager->get(
            'Magento\ScheduledImportExport\Model\Observer'
        )->scheduledLogClean(
            $schedule,
            true
        );
        if ($result) {
            $this->messageManager->addSuccess(__('We deleted the history files.'));
        } else {
            $this->messageManager->addError(__('Something went wrong deleting the history files.'));
        }
        $this->_redirect('adminhtml/system_config/edit', ['section' => $this->getRequest()->getParam('section')]);
    }
}
