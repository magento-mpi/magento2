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
     * List of constructor parameters, postponed for later initialization
     *
     * @var array
     */
    private $_postponedParams = array();

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
        // Postpone all initialization, so we can make a single point of failure (processRequest method)
        $this->_setPostponedParam('config', $config);
        $this->_setPostponedParam('objectManager', $objectManager);
    }

    /**
     * Save constructor parameter for postponed initialization
     *
     * @param string $name
     * @param mixed $value
     */
    protected function _setPostponedParam($name, $value)
    {
        $this->_postponedParams[$name] = $value;
    }

    /**
     * Retrieve constructor parameter, saved for postponed initialization
     *
     * @param string $name
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getPostponedParam($name)
    {
        if (!array_key_exists($name, $this->_postponedParams)) {
            throw new Mage_Core_Exception("Postponed initialization parameter {$name} doesn't exist");
        }
        return $this->_postponedParams[$name];
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
        $objectManager = $this->_getPostponedParam('objectManager');
        $config = $this->_getPostponedParam('config');

        if (!$objectManager) {
            $definitionFactory = new Mage_Core_Model_ObjectManager_DefinitionFactory();
            $definitions =  $definitionFactory->create($config);
            $objectManager = new Mage_Core_Model_ObjectManager($definitions, $config);
        }
        $this->_objectManager = $objectManager;

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

