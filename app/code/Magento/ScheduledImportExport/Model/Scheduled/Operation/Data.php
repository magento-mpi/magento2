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
class Magento_ScheduledImportExport_Model_Scheduled_Operation_Data
{
    /**
     * Pending status constant
     */
    const STATUS_PENDING = 2;

    /**
     * Import/export config model
     *
     * @var Magento_ImportExport_Model_Config
     */
    protected $_importExportConfig;

    /**
     * Import entity model
     *
     * @var Magento_ImportExport_Model_Import
     */
    protected $_importModel;

    /**
     * Constructor
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_importExportConfig = isset($data['import_export_config']) ? $data['import_export_config']
            : Mage::getModel('Magento_ImportExport_Model_Config');
        $this->_importModel = isset($data['import_model']) ? $data['import_model']
            : Mage::getModel('Magento_ImportExport_Model_Import');
    }

    /**
     * Get statuses option array
     *
     * @return array
     */
    public function getStatusesOptionArray()
    {
        return array(
            1 => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Enabled'),
            0 => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Disabled'),
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
            'import' => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Import'),
            'export' => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Export')
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
            Magento_Cron_Model_Config_Source_Frequency::CRON_DAILY
                => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Daily'),
            Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY
                => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Weekly'),
            Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY
                => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Monthly'),
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
            'file'  => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Local Server'),
            'ftp'   => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Remote FTP')
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
            FTP_BINARY  => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Binary'),
            FTP_ASCII   => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('ASCII'),
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
            0 => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Stop Import'),
            1 => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Continue Processing'),
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
            0  => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Failed'),
            1  => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Successful'),
            self::STATUS_PENDING  => Mage::helper('Magento_ScheduledImportExport_Helper_Data')->__('Pending')
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
        $importEntities = Magento_ImportExport_Model_Config::getModelsArrayOptions(
            Magento_ImportExport_Model_Import::CONFIG_KEY_ENTITIES
        );
        $exportEntities = Magento_ImportExport_Model_Config::getModelsArrayOptions(
            Magento_ImportExport_Model_Export::CONFIG_KEY_ENTITIES
        );
        switch ($type) {
            case 'import':
                return $importEntities;

            case 'export':
                return $exportEntities;

            default:
                return array_merge($importEntities, $exportEntities);
        }
    }
}
