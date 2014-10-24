<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;

class Import extends \Magento\ImportExport\Model\Import implements
    \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
{
    /**
     * Run import through cron
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return bool
     */
    public function runSchedule(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $sourceFile = $operation->getFileSource($this);
        $result = false;
        if ($sourceFile) {
            $result = $this->validateSource(
                \Magento\ImportExport\Model\Import\Adapter::findAdapterFor(
                    $sourceFile,
                    $this->_filesystem->getDirectoryWrite(DirectoryList::SYS_TMP)
                )
            );
        }
        $isAllowedForcedImport = $operation->getForceImport() &&
            $this->getProcessedRowsCount() != $this->getInvalidRowsCount();
        if ($isAllowedForcedImport || $result) {
            $result = $this->importSource();
        }
        if ($result) {
            $this->invalidateIndex();
        }
        return (bool)$result;
    }

    /**
     * Initialize import instance from scheduled operation
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return $this
     */
    public function initialize(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $this->setData(
            array(
                'entity' => $operation->getEntityType(),
                'behavior' => $operation->getBehavior(),
                'operation_type' => $operation->getOperationType(),
                'run_at' => $operation->getStartTime(),
                'scheduled_operation_id' => $operation->getId()
            )
        );
        return $this;
    }
}
