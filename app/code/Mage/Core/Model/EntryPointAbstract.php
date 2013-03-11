<?php
/**
 * Abstract application entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Model_EntryPointAbstract
{
    /**
     * Application object manager
     *
     * @var Mage_Core_Model_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Mage_Core_Model_Config_Primary $config
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Mage_Core_Model_Config_Primary $config, Magento_ObjectManager $objectManager = null)
    {
        try {
            if (!$objectManager) {
                $definitionFactory = new Mage_Core_Model_ObjectManager_DefinitionFactory();
                $definitions =  $definitionFactory->create($config);
                $objectManager = new Mage_Core_Model_ObjectManager($definitions, $config);
            }
            $this->_objectManager = $objectManager;
            Mage::setObjectManager($objectManager);
        } catch (Magento_BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            die($e->getMessage());
        }
    }

    /**
     * Process request to application
     */
    public abstract function processRequest();
}

