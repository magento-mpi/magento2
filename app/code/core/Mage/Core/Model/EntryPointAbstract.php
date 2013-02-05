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
     * @param string $baseDir
     * @param array $params
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        $baseDir, array $params = array(), Magento_ObjectManager $objectManager = null
    ) {
        Magento_Profiler::start('mage');
        if (!array_key_exists(Mage::PARAM_BASEDIR, $params)) {
            $params[Mage::PARAM_BASEDIR] = $baseDir;
        }
        $this->_objectManager = $objectManager ?: new Mage_Core_Model_ObjectManager(
            new Mage_Core_Model_ObjectManager_Config($params),
            $baseDir
        );
    }

    /**
     * Entry point specific processing
     */
    abstract protected function _processRequest();

    /**
     * Process request to application
     */
    final public function processRequest()
    {
        $this->_processRequest();
        Magento_Profiler::stop('mage');
    }
}

