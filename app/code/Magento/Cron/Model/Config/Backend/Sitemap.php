<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Magento
 * @package    Magento_Cron
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cron_Model_Config_Backend_Sitemap extends Magento_Core_Model_Config_Value
{
    /**
     * Cron string path
     */
    const CRON_STRING_PATH = 'crontab/jobs/sitemap_generate/schedule/cron_expr';

    /**
     * Cron mode path
     */
    const CRON_MODEL_PATH  = 'crontab/jobs/sitemap_generate/run/model';

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
     * @return Magento_Core_Model_Abstract
     * @throws Exception
     */
    protected function _afterSave()
    {
        $time = $this->getData('groups/generate/fields/time/value');
        $frequency = $this->getData('groups/generate/frequency/value');

        $cronExprArray = array(
            intval($time[1]), //Minute
            intval($time[0]), //Hour
            ($frequency == Magento_Cron_Model_Config_Source_Frequency::CRON_MONTHLY) ? '1' : '*', //Day of the Month
            '*', //Month of the Year
            ($frequency == Magento_Cron_Model_Config_Source_Frequency::CRON_WEEKLY) ? '1' : '*', //# Day of the Week
        );

        $cronExprString = join(' ', $cronExprArray);

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
            throw new Exception(__('We can\'t save the cron expression.'));
        }
    }
}
