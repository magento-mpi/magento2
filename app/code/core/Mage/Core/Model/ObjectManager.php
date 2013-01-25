<?php
/**
 * Magento Web application object manager. Configures and composes application application to serve http requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * @param Mage_Core_Model_ObjectManager_Config $configuration
     * @param string $baseDir
     * @param Magento_Di $diInstance
     * @param Magento_Di_InstanceManager $instanceManager
     */
    public function __construct(
        Mage_Core_Model_ObjectManager_Config $configuration,
        $baseDir,
        Magento_Di $diInstance = null,
        Magento_Di_InstanceManager $instanceManager = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php', $diInstance, $instanceManager);
        Mage::setObjectManager($this);
        $configuration->configure($this);
    }
}
