<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backup\Controller\Adminhtml\Index
{
    /**
     * Delete backups mass action
     *
     * @return \Magento\Backend\App\Action
     */
    public function execute()
    {
        $backupIds = $this->getRequest()->getParam('ids', array());

        if (!is_array($backupIds) || !count($backupIds)) {
            return $this->_redirect('backup/*/index');
        }

        $resultData = new \Magento\Framework\Object();
        $resultData->setIsSuccess(false);
        $resultData->setDeleteResult(array());
        $this->_coreRegistry->register('backup_manager', $resultData);

        $deleteFailMessage = __('We couldn\'t delete one or more backups.');

        try {
            $allBackupsDeleted = true;

            foreach ($backupIds as $id) {
                list($time, $type) = explode('_', $id);
                $backupModel = $this->_backupModelFactory->create($time, $type)->deleteFile();

                if ($backupModel->exists()) {
                    $allBackupsDeleted = false;
                    $result = __('failed');
                } else {
                    $result = __('successful');
                }

                $resultData->setDeleteResult(
                    array_merge($resultData->getDeleteResult(), array($backupModel->getFileName() . ' ' . $result))
                );
            }

            $resultData->setIsSuccess(true);
            if ($allBackupsDeleted) {
                $this->messageManager->addSuccess(__('The selected backup(s) has been deleted.'));
            } else {
                throw new \Exception($deleteFailMessage);
            }
        } catch (\Exception $e) {
            $resultData->setIsSuccess(false);
            $this->messageManager->addError($deleteFailMessage);
        }

        return $this->_redirect('backup/*/index');
    }
}
