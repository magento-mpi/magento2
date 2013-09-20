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
 * Backend model for import/export log cleaning schedule options
 *
 * @category   Magento
 * @package    Magento_ScheduledImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_ScheduledImportExport_Model_System_Config_Backend_Logclean_Cron extends Magento_Core_Model_Config_Value
{
    /**
     * Cron expression configuration path
     */
    const CRON_STRING_PATH = 'crontab/jobs/magento_scheduled_import_export_log_clean/schedule/cron_expr';

    /**
     * @var Magento_Core_Model_Config_ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @param Magento_Core_Model_Config_ValueFactory $configValueFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Config_ValueFactory $configValueFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Add cron task
     *
     * @throws Exception
     * @return void
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/magento_scheduled_import_export_log/fields/time/value');
        $frequency = $this->getData('groups/magento_scheduled_import_export_log/fields/frequency/value');

        $frequencyDaily   = Magento_Cron_Model_Config_Source_Frequency::CRON_DAILY;
        $frequencyWeekly  = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        $cronExprArray = array(
            intval($time[1]),                                   # Minute
            intval($time[0]),                                   # Hour
            ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
            '*',                                                # Month of the Year
            ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Exception(__('We were unable to save the cron expression.'));
        }
    }
}
