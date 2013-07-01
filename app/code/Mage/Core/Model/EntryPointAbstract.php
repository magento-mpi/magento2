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
     * Application configuration
     *
     * @var Mage_Core_Model_Config_Primary
     */
    protected $_config;

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
        $this->_config = $config;
        $this->_objectManager = $objectManager;
    }

    /**
     * Process request by the application
     */
    public function processRequest()
    {
        $this->_init();
        $this->_processRequest();
    }

    /**
     * Initializes the entry point, so a Magento application is ready to be used
     */
    protected function _init()
    {
        $this->_initObjectManager();
        $this->_verifyDirectories();
    }

    /**
     * Initialize object manager for the application
     */
    protected function _initObjectManager()
    {
        if (!$this->_objectManager) {
            $definitionFactory = new Mage_Core_Model_ObjectManager_DefinitionFactory();
            $definitions =  $definitionFactory->create($this->_config);
            $config = new Magento_ObjectManager_Config();
            $factory = new Magento_ObjectManager_Interception_FactoryDecorator(
                new Magento_ObjectManager_Factory_Factory($config, null, $definitions),
                $config,
                null,
                new Magento_ObjectManager_Interception_Definition_Runtime()
            );
            $this->_objectManager = new Mage_Core_Model_ObjectManager($factory, $this->_config, $config);
        }

        $this->_setGlobalObjectManager();
    }

    /**
     * Set globally-available variable
     *
     * The method is isolated in order to make safe testing possible, by mocking this method in the tests.
     */
    protected function _setGlobalObjectManager()
    {
        Mage::setObjectManager($this->_objectManager);
    }

    /**
     * Verify existence and write access to the application directories
     */
    protected function _verifyDirectories()
    {
        /** @var $verification Mage_Core_Model_Dir_Verification */
        $verification = $this->_objectManager->get('Mage_Core_Model_Dir_Verification');
        $verification->createAndVerifyDirectories();
    }

    /**
     * Template method to process request according to the actual entry point rules
     */
    protected abstract function _processRequest();
}

