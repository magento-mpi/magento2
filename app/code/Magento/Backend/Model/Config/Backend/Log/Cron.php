<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log Cron Backend Model
 */
class Magento_Backend_Model_Config_Backend_Log_Cron extends Magento_Core_Model_Config_Value
{
    const CRON_STRING_PATH  = 'crontab/jobs/log_clean/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/log_clean/run/model';

    /**
     * @var Magento_Core_Model_Config_ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Config_ValueFactory $configValueFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Config_ValueFactory $configValueFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configValueFactory = $configValueFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Cron settings after save
     *
     * @return Magento_Backend_Model_Config_Backend_Log_Cron
     * @throws Magento_Core_Exception
     */
    protected function _afterSave()
    {
        $enabled    = $this->getData('groups/log/fields/enabled/value');
        $time       = $this->getData('groups/log/fields/time/value');
        $frequncy   = $this->getData('groups/log/fields/frequency/value');

        $frequencyWeekly    = Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY;
        $frequencyMonthly   = Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY;

        if ($enabled) {
            $cronExprArray = array(
                intval($time[1]),                                   # Minute
                intval($time[0]),                                   # Hour
                ($frequncy == $frequencyMonthly) ? '1' : '*',       # Day of the Month
                '*',                                                # Month of the Year
                ($frequncy == $frequencyWeekly) ? '1' : '*',        # Day of the Week
            );
            $cronExprString = join(' ', $cronExprArray);
        } else {
            $cronExprString = '';
        }

        try {
            /** @var $configValue Magento_Core_Model_Config_Value */
            $configValue = $this->_configValueFactory->create();
            $configValue->load(self::CRON_STRING_PATH, 'path');
            $configValue->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            /** @var $configValue Magento_Core_Model_Config_Value */
            $configValue = $this->_configValueFactory->create();
            $configValue->load(self::CRON_MODEL_PATH, 'path');
            $configValue->setValue((string) $this->_config->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        } catch (Exception $e) {
            throw new Magento_Core_Exception(__('We can\'t save the Cron expression.'));
        }
    }
}
