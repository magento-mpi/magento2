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
     */
    public function __construct(
        $baseDir, array $params = array()
    ) {
        if (!array_key_exists(Mage::PARAM_BASEDIR, $params)) {
            $params[Mage::PARAM_BASEDIR] = $baseDir;
        }
        $this->_objectManager = new Mage_Core_Model_ObjectManager(
            new Mage_Core_Model_ObjectManager_Config(
                $params
            ),
            $baseDir
        );
    }

    /**
     * Process request to application
     */
    abstract public function processRequest();
}

