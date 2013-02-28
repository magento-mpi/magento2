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
     * @param Magento_ObjectManager_Configuration $configuration
     * @param string $baseDir
     */
    public function __construct(
        Magento_ObjectManager_Configuration $configuration,
        $baseDir
    ) {
        Magento_Profiler::start('di');
        Magento_Profiler::start('definitions');
        if (is_readable($baseDir . '/var/di/definitions.php')) {
            $definitions = new Magento_ObjectManager_Definition_Compiled(
                unserialize(file_get_contents($baseDir . '/var/di/definitions.php'))
            );
        } else {
            $definitions = new Magento_ObjectManager_Definition_Runtime();
        }
        parent::__construct($definitions);
        Magento_Profiler::stop('definitions');
        Mage::setObjectManager($this);
        Magento_Profiler::start('configuration');
        $configuration->configure($this);
        Magento_Profiler::stop('configuration');
        Magento_Profiler::stop('di');
    }
}
