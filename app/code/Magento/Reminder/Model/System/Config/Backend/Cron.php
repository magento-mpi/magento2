<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reminder Cron Backend Model
 */
class Magento_Reminder_Model_System_Config_Backend_Cron extends Magento_Core_Model_Config_Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/send_notification/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/send_notification/run/model';

    /**
     * Configuration Value Factory
     *
     * @var Magento_Core_Model_Config_ValueFactory
     */
    protected $_valueFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Config_ValueFactory $valueFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Config_ValueFactory $valueFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
        $this->_valueFactory = $valueFactory;
    }


    /**
     * Cron settings after save
     *
     * @return Magento_Reminder_Model_System_Config_Backend_Cron
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        $cronExprString = '';

        if ($this->getFieldsetDataValue('enabled')) {
            $minutely = Magento_Reminder_Model_Observer::CRON_MINUTELY;
            $hourly   = Magento_Reminder_Model_Observer::CRON_HOURLY;
            $daily    = Magento_Reminder_Model_Observer::CRON_DAILY;

            $frequency  = $this->getFieldsetDataValue('frequency');

            if ($frequency == $minutely) {
                $interval = (int)$this->getFieldsetDataValue('interval');
                $cronExprString = "*/{$interval} * * * *";
            }
            elseif ($frequency == $hourly) {
                $minutes = (int)$this->getFieldsetDataValue('minutes');
                if ($minutes >= 0 && $minutes <= 59){
                    $cronExprString = "{$minutes} * * * *";
                }
                else {
                    throw new Magento_Core_Exception(__('Please specify a valid number of minute.'));
                }
            }
            elseif ($frequency == $daily) {
                $time = $this->getFieldsetDataValue('time');
                $timeMinutes = intval($time[1]);
                $timeHours = intval($time[0]);
                $cronExprString = "{$timeMinutes} {$timeHours} * * *";
            }
        }

        try {
            $this->_valueFactory->create()
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            $this->_valueFactory->create()
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) $this->_config->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }

        catch (Exception $e) {
            throw new Magento_Core_Exception(__('Unable to save Cron expression'));
        }
    }
}
