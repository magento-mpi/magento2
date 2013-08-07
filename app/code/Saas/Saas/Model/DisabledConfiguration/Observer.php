<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Observer model to verify, that saving a configuration is allowed
 */
class Saas_Saas_Model_DisabledConfiguration_Observer
{
    /**
     * @var Saas_Saas_Model_DisabledConfiguration_Config
     */
    protected $_config;

    public function __construct(Saas_Saas_Model_DisabledConfiguration_Config $config)
    {
        $this->_disabledConfig = $config;
    }

    /**
     * Reaction on a try to save config data
     *
     * @param Magento_Event_Observer $observer
     * @throws Saas_Saas_Exception
     */
    public function checkConfigSaveAllowed(Magento_Event_Observer $observer)
    {
        /** @var $object Mage_Core_Model_Config_Data */
        $object = $observer->getEvent()->getConfigData();
        if ($this->_disabledConfig->isPathDisabled($object->getPath())) {
            throw new Saas_Saas_Exception('Modification is not permitted');
        }
    }
}
