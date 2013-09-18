<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backup by cron backend model
 */
class Magento_Backup_Model_Config_Backend_Cron extends Magento_Core_Model_Config_Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/system_backup/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/system_backup/run/model';

    const XML_PATH_BACKUP_ENABLED       = 'groups/backup/fields/enabled/value';
    const XML_PATH_BACKUP_TIME          = 'groups/backup/fields/time/value';
    const XML_PATH_BACKUP_FREQUENCY     = 'groups/backup/fields/frequency/value';

    /**
     * Config value factory
     *
     * @var Magento_Core_Model_Config_Value
     */
    protected $_configValueFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Config_ValueFactory $configValueFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Config_ValueFactory $configValueFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_configValueFactory = $configValueFactory;
    }

    /**
     * Cron settings after save
     *
     * @return Magento_Backend_Model_Config_Backend_Log_Cron
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        $enabled   = $this->getData(self::XML_PATH_BACKUP_ENABLED);
        $time      = $this->getData(self::XML_PATH_BACKUP_TIME);
        $frequency = $this->getData(self::XML_PATH_BACKUP_FREQUENCY);

        $frequencyWeekly  = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequency == $frequencyMonthly) ? '1' : '*',      # Day of the Month
                '*',                                                # Month of the Year
                ($frequency == $frequencyWeekly) ? '1' : '*',       # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            $this->_configValueFactory->create()
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            $this->_configValueFactory->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) $this->_coreConfig->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Magento_Core_Exception(__('We can\'t save the Cron expression.'));
        }
    }
}
