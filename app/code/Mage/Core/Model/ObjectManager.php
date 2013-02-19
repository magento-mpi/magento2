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
        Magento_Profiler::start('di');
        Magento_Profiler::start('definitions');
        parent::__construct($baseDir . '/var/di/definitions.php', $diInstance, $instanceManager);
        Magento_Profiler::stop('definitions');
        Mage::setObjectManager($this);
        Magento_Profiler::start('configuration');
        $configuration->configure($this);
        Magento_Profiler::stop('configuration');
        Magento_Profiler::stop('di');
    }
}
