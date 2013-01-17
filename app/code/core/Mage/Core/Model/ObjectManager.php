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
     */
    public function __construct(
        Mage_Core_Model_ObjectManager_Config $configuration,
        $baseDir
    ) {
        parent::__construct($baseDir . '/var/di/definitions.php');
        Mage::setObjectManager($this);
        $configuration->configure($this);
    }
}
