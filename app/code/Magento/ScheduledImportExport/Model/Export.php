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
 * @method Magento_ScheduledImportExport_Model_Export setRunDate() setRunDate(int $value)
 * @method Magento_ScheduledImportExport_Model_Export setEntity() setEntity(string $value)
 * @method Magento_ScheduledImportExport_Model_Export setOperationType() setOperationType(string $value)
 */
class Magento_ScheduledImportExport_Model_Export extends Magento_ImportExport_Model_Export
    implements Magento_ScheduledImportExport_Model_Scheduled_Operation_Interface
{
    /**
     * Date model instance
     *
     * @var Magento_Core_Model_Date
     */
    protected $_dateModel;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Log_AdapterFactory $adapterFactory
     * @param Magento_ImportExport_Model_Export_ConfigInterface $exportConfig
     * @param Magento_ImportExport_Model_Export_Entity_Factory $entityFactory
     * @param Magento_ImportExport_Model_Export_Adapter_Factory $exportAdapterFac
     * @param Magento_Core_Model_Date $coreDate
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Log_AdapterFactory $adapterFactory,
        Magento_ImportExport_Model_Export_ConfigInterface $exportConfig,
        Magento_ImportExport_Model_Export_Entity_Factory $entityFactory,
        Magento_ImportExport_Model_Export_Adapter_Factory $exportAdapterFac,
        Magento_Core_Model_Date $coreDate,
        array $data = array()
    ) {
        $this->_dateModel = $coreDate;
        parent::__construct($logger, $dir, $adapterFactory, $exportConfig, $entityFactory, $exportAdapterFac, $data);
    }

    /**
     * Date model instance getter
     *
     * @return Magento_Core_Model_Date
     */
    public function getDateModel()
    {
        return $this->_dateModel;
    }

    /**
     * Run export through cron
     *
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return bool
     */
    public function runSchedule(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation)
    {
        $data = $this->export();
        $result = $operation->saveFileSource($this, $data);

        return (bool)$result;
    }

    /**
     * Initialize export instance from scheduled operation
     *
     * @param Magento_ScheduledImportExport_Model_Scheduled_Operation $operation
     * @return Magento_ScheduledImportExport_Model_Export
     */
    public function initialize(Magento_ScheduledImportExport_Model_Scheduled_Operation $operation)
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
