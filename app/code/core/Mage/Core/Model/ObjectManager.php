<?php
/**
 * Magento application object manager. Configures and application application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_ObjectManager extends Magento_ObjectManager_Zend
{
    /**
     * @param Magento_ObjectManager_Configuration $configuration
     * @param string $baseDir
     * @param Magento_Di $diInstance
     * @param Magento_Di_InstanceManager $instanceManager
     */
    public function __construct(
        Magento_ObjectManager_Configuration $configuration,
        $baseDir,
        Magento_Di $diInstance = null,
        Magento_Di_InstanceManager $instanceManager = null
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php', $diInstance, $instanceManager);
        Mage::setObjectManager($this);
        $configuration->configure($this);
    }
}
