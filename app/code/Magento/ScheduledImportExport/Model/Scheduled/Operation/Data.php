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
 * Operation Data model
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Scheduled\Operation;

class Data
{
    /**
     * Pending status constant
     */
    const STATUS_PENDING = 2;

    /**
     * @var \Magento\ImportExport\Model\Import\ConfigInterface
     */
    protected $_importConfig;

    /**
     * @var \Magento\ImportExport\Model\Export\ConfigInterface
     */
    protected $_exportConfig;

    /**
     * @param \Magento\ImportExport\Model\Import\ConfigInterface $importConfig
     * @param \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
     */
    public function __construct(
        \Magento\ImportExport\Model\Import\ConfigInterface $importConfig,
        \Magento\ImportExport\Model\Export\ConfigInterface $exportConfig
    ) {
        $this->_importConfig = $importConfig;
        $this->_exportConfig = $exportConfig;
    }

    /**
     * Get statuses option array
     *
     * @return array
     */
    public function getStatusesOptionArray()
    {
        return array(
            1 => __('Enabled'),
            0 => __('Disabled'),
        );
    }

    /**
     * Get operations option array
     *
     * @return array
     */
    public function getOperationsOptionArray()
    {
        return array(
            'import' => __('Import'),
            'export' => __('Export')
        );
    }

    /**
     * Get frequencies option array
     *
     * @return array
     */
    public function getFrequencyOptionArray()
    {
        return array(
            \Magento\Cron\Model\Config\Source\Frequency::CRON_DAILY
                => __('Daily'),
            \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY
                => __('Weekly'),
            \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY
                => __('Monthly'),
        );
    }

    /**
     * Get server types option array
     *
     * @return array
     */
    public function getServerTypesOptionArray()
    {
        return array(
            'file'  => __('Local Server'),
            'ftp'   => __('Remote FTP')
        );
    }

    /**
     * Get file modes option array
     *
     * @return array
     */
    public function getFileModesOptionArray()
    {
        return array(
            FTP_BINARY  => __('Binary'),
            FTP_ASCII   => __('ASCII'),
        );
    }

    /**
     * Get forced import option array
     *
     * @return array
     */
    public function getForcedImportOptionArray()
    {
        return array(
            0 => __('Stop Import'),
            1 => __('Continue Processing'),
        );
    }

    /**
     * Get operation result option array
     *
     * @return array
     */
    public function getResultOptionArray()
    {
        return array(
            0  => __('Failed'),
            1  => __('Successful'),
            self::STATUS_PENDING  => __('Pending')
        );
    }

    /**
     * Get entities option array
     *
     * @param string $type
     * @return array
     */
    public function getEntitiesOptionArray($type = null)
    {
        $importOptions = array();
        foreach ($this->_importConfig->getEntities() as $entityName => $entityConfig) {
            $importOptions[$entityName] = __($entityConfig['label']);
        }
        $exportOptions = array();
        foreach ($this->_exportConfig->getEntities() as $entityName => $entityConfig) {
            $exportOptions[$entityName] = __($entityConfig['label']);
        }
        switch ($type) {
            case 'import':
                return $importOptions;

            case 'export':
                return $exportOptions;

            default:
                return array_merge($importOptions, $exportOptions);
        }
    }
}
