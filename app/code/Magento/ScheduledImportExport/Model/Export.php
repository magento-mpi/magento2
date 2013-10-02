<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method string getOperationType() getOperationType()
 * @method int getRunDate() getRunDate()
 * @method \Magento\ScheduledImportExport\Model\Export setRunDate() setRunDate(int $value)
 * @method \Magento\ScheduledImportExport\Model\Export setEntity() setEntity(string $value)
 * @method \Magento\ScheduledImportExport\Model\Export setOperationType() setOperationType(string $value)
 */
namespace Magento\ScheduledImportExport\Model;

class Export extends \Magento\ImportExport\Model\Export
    implements \Magento\ScheduledImportExport\Model\Scheduled\Operation\OperationInterface
{
    /**
     * Date model instance
     *
     * @var \Magento\Core\Model\Date
     */
    protected $_dateModel;

    /**
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Core\Model\Dir $dir
     * @param \Magento\Core\Model\Log\AdapterFactory $adapterFactory
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     * @param \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory
     * @param \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac
     * @param \Magento\Core\Model\Date $coreDate
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Logger $logger,
        \Magento\Core\Model\Dir $dir,
        \Magento\Core\Model\Log\AdapterFactory $adapterFactory,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig,
        \Magento\ImportExport\Model\Export\Entity\Factory $entityFactory,
        \Magento\ImportExport\Model\Export\Adapter\Factory $exportAdapterFac,
        \Magento\Core\Model\Date $coreDate,
        array $data = array()
    ) {
        $this->_dateModel = $coreDate;
        parent::__construct($logger, $dir, $adapterFactory, $exportConfig, $entityFactory, $exportAdapterFac, $data);
    }

    /**
     * Date model instance getter
     *
     * @return \Magento\Core\Model\Date
     */
    public function getDateModel()
    {
        return $this->_dateModel;
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
     * @return \Magento\ScheduledImportExport\Model\Export
     */
    public function initialize(\Magento\ScheduledImportExport\Model\Scheduled\Operation $operation)
    {
        $fileInfo  = $operation->getFileInfo();
        $attributes = $operation->getEntityAttributes();
        $data = array(
            'entity'                 => $operation->getEntityType(),
            'file_format'            => $fileInfo['file_format'],
            'export_filter'          => $attributes['export_filter'],
            'operation_type'         => $operation->getOperationType(),
            'run_at'                 => $operation->getStartTime(),
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
        return $this->getDateModel()->date('Y-m-d_H-i-s', $runDate) . '_' . $this->getOperationType() . '_'
            . $this->getEntity();
    }
}
