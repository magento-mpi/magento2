<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model;

/**
 * Export model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getOperationType() getOperationType()
 * @method int getRunDate() getRunDate()
 * @method \Magento\ScheduledImportExport\Model\Export setRunDate() setRunDate(int $value)
 * @method \Magento\ScheduledImportExport\Model\Export setEntity() setEntity(string $value)
 * @method \Magento\ScheduledImportExport\Model\Export setOperationType() setOperationType(string $value)
 */
class Export extends \Magento\ImportExport\Model\Export implements
    \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
{
    /**
     * Date model instance
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateModel;

    /**
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\App\Filesystem $filesystem
     * @param \Magento\Framework\Logger\AdapterFactory $adapterFactory
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     * @param \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory
     * @param \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Logger $logger,
        \Magento\Framework\App\Filesystem $filesystem,
        \Magento\Framework\Logger\AdapterFactory $adapterFactory,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory,
        \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        array $data = array()
    ) {
        $this->_dateModel = $coreDate;
        parent::__construct(
            $logger,
            $filesystem,
            $adapterFactory,
            $exportConfig,
            $entityFactory,
            $exportAdapterFac,
            $data
        );
    }

    /**
     * Run export through cron
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return bool
     */
    public function runSchedule(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $data = $this->export();
        $result = $operation->saveFileSource($this, $data);

        return (bool)$result;
    }

    /**
     * Initialize export instance from scheduled operation
     *
     * @param \Magento\ScheduledImportExport\Model\Scheduled\Operation $operation
     * @return $this
     */
    public function initialize(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $fileInfo = $operation->getFileInfo();
        $attributes = $operation->getEntityAttributes();
        $data = array(
            'entity' => $operation->getEntityType(),
            'file_format' => $fileInfo['file_format'],
            'export_filter' => $attributes['export_filter'],
            'operation_type' => $operation->getOperationType(),
            'run_at' => $operation->getStartTime(),
            'scheduled_operation_id' => $operation->getId()
        );
        if (isset($attributes['skip_attr'])) {
            $data['skip_attr'] = $attributes['skip_attr'];
        }
        $this->setData($data);
        return $this;
    }

    /**
     * Get file name for scheduled running
     *
     * @return string file name without extension
     */
    public function getScheduledFileName()
    {
        $runDate = $this->getRunDate() ? $this->getRunDate() : null;
        return $this->_dateModel->date(
            'Y-m-d_H-i-s',
            $runDate
        ) . '_' . $this->getOperationType() . '_' . $this->getEntity();
    }
}
