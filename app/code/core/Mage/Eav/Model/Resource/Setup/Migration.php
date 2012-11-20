<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model with methods needed for migration process between Magento versions
 */
class Mage_Eav_Model_Resource_Setup_Migration extends Mage_Core_Model_Resource_Setup_Migration
{
    /**
     * Default module name that used this setup resource module
     */
    const DEFAULT_MODULE_NAME = 'Mage_Eav';

    /**
     * Key of config that keep path to custom map file
     */
    const CONFIG_KEY_PATH_TO_CUSTOM_MAP_FILE = 'global/migration/eav/path_to_aliases_map_file';

    /**
     * Core config model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Path to module dir
     *
     * @var string
     */
    protected $_moduleDir;

    /**
     * Name of module that used this setup resource model
     *
     * @var string
     */
    protected $_moduleName;

    /**
     * Constructor
     *
     * @param string $resourceName
     * @param Mage_Core_Model_Config $config
     * @param string $moduleName
     * @param array $data
     */
    public function __construct($resourceName,
        Mage_Core_Model_Config $config,
        $moduleName = self::DEFAULT_MODULE_NAME,
        array $data = array()
    ) {
        $this->_config = $config;
        $this->_moduleName = $moduleName;

        parent::__construct($resourceName, $data);
    }

    /**
     * Init aliases map configuration
     *
     * @param array $data
     */
    protected function _initAliasesMapConfiguration(array $data = array())
    {
        parent::_initAliasesMapConfiguration($data);

        $pathToMapFile = $this->_baseDir . DS . $this->_pathToMapFile;
        if (!file_exists($pathToMapFile) && !$this->_aliasesMap) {
            $this->_baseDir = $this->_config->getModuleDir('', $this->_moduleName);
            $this->_pathToMapFile = $this->_config->getNode(self::CONFIG_KEY_PATH_TO_CUSTOM_MAP_FILE);
        }
    }
}
