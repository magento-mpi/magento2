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
     * Configuration params
     *
     * @var array
     */
    protected $_params;

    /**
     * @param string $baseDir
     * @param array $params
     * @param string $objectManagerClass
     */
    public function __construct(
        $baseDir, array $params = array(), $objectManagerClass = 'Mage_Core_Model_ObjectManager'
    ) {
        $this->_params = $params;
        $this->_objectManager = new $objectManagerClass(
            $baseDir,
            $this->_getParam('MAGE_RUN_CODE', ''),
            $this->_getParam('MAGE_RUN_TYPE', 'store'),
            $this->_getParam('app_dirs', array()),
            $this->_getParam('app_uris', array()),
            $this->_getParam('allowed_modules', array()),
            $this->_getParam('cache_options', array()),
            $this->_getParam('global_ban_use_cache', false),
            $this->_getParam('custom_local_xml_file', false),
            $this->_getParam('custom_local_config', false)
        );
    }

    /**
     * Get init param
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function _getParam($name, $defaultValue = null)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $defaultValue;
    }

    /**
     * Process request to application
     */
    abstract public function processRequest();
}

