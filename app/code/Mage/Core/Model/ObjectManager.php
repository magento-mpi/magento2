<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_ObjectManager
{
    /**
     * @param Magento_ObjectManager_Factory $factory
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager_Config $instanceConfig
     */
    public function __construct(
        Magento_ObjectManager_Factory $factory,
        Mage_Core_Model_Config_Primary $config,
        Magento_ObjectManager_Config $instanceConfig = null
    ) {
        parent::__construct($factory, $instanceConfig, array(
            'Mage_Core_Model_Config_Primary' => $config,
            'Mage_Core_Model_Dir' => $config->getDirectories()
        ));
        $config->configure($this);
    }
}
