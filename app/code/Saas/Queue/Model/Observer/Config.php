<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Queue
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Model_Observer_Config extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * Instance of config model
     *
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_ConfigInterface $config
     */
    public function __construct(Magento_Core_Model_ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Check whether worker instance should notify by email
     *
     * @return bool
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Rebuild whole config and save to fast storage task
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  Magento_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Config
     */
    public function processReinitConfig(Magento_Event_Observer $observer)
    {
        $this->_config->reinit();
        return $this;
    }
}
