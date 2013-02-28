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
     * @param Magento_ObjectManager_Definition $definitions
     * @param Mage_Core_Model_Config_Primary $config
     */
    public function __construct(Magento_ObjectManager_Definition $definitions, Mage_Core_Model_Config_Primary $config)
    {
        parent::__construct($definitions, array(), array(
            'Mage_Core_Model_Config_Primary' => $config,
            'Mage_Core_Model_Dir' => $config->getDirectories()
        ));
        $config->configure($this);
    }
}
