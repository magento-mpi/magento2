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
 * Configuration entry point for client using
 */
class Magento_Cron_Model_Config implements Magento_Cron_Model_ConfigInterface
{
    /**
     * Cron config data
     *
     * @var Magento_Cron_Model_Config_Data
     */
    protected $_configData;

    /**
     * Initialize needed parameters
     *
     * @param Magento_Cron_Model_Config_Data $configData
     */
    public function __construct(Magento_Cron_Model_Config_Data $configData)
    {
        $this->_configData = $configData;
    }

    /**
     * Return cron full cron jobs
     *
     * @return array|mixed
     */
    public function getJobs()
    {
        return $this->_configData->getJobs();
    }
}
