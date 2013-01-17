<?php
/**
 * Abstract object manager initializer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Core_Model_ObjectManager_ConfigAbstract implements Magento_ObjectManager_Configuration
{
    protected $_baseDir;
    protected $_scopeCode;
    protected $_scopeType;
    protected $_customDirs = array();
    protected $customUris = array();
    protected $allowedModules = array();
    protected $cacheOptions = array();
    protected $banCache = false;
    protected $customLocalXmlFile = null;
    protected $customLocalConfig = null;

    /**
     * @param string $baseDir
     * @param string $scopeCode
     * @param $scopeType
     * @param array $customDirs
     * @param array $customUris
     * @param array $allowedModules
     * @param array $cacheOptions
     * @param bool $banCache
     * @param null $customLocalXmlFile
     * @param null $customLocalConfig
     */
    public function __construct(
        $baseDir,
        $scopeCode,
        $scopeType,
        array $customDirs = array(),
        array $customUris = array(),
        array $allowedModules = array(),
        array $cacheOptions = array(),
        $banCache = false,
        $customLocalXmlFile = null,
        $customLocalConfig = null
    ) {
        $this->_baseDir = $baseDir;
        $this->_scopeCode = $scopeCode;
        $this->_scopeType = $scopeType;
        $this->_customDirs = $customDirs;
        $this->_customUris = $customUris;
        $this->_allowedModules = $allowedModules;
        $this->_cacheOptions = $cacheOptions;
        $this->_banCache = $banCache;
        $this->_customLocalXmlFile = $customLocalXmlFile;
        $this->_customLocalConfig = $customLocalConfig;
    }
}
