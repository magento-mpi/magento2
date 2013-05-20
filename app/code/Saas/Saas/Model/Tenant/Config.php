<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configurator of application initialization parameters with context of a tenant
 */
class Saas_Saas_Model_Tenant_Config
{
    /**
     * Tenant enabled
     */
    const STATUS_ENABLED = 0;

    /**
     * Only tenant's backend is available
     */
    const STATUS_DISABLED_FRONTEND = 1;

    /**
     * @var string
     */
    private $_rootDir;

    /**
     * @var Varien_Simplexml_Config
     */
    private $_config;

    /**
     * Tenant configuration
     *
     * @var array
     */
    private $_configArray = array();

    /**
     * Tenant's group configuration
     *
     * @var array
     */
    private $_groupConfiguration = array();

    /**
     * Name of media directory relatively to root
     *
     * @var string
     */
    private $_mediaDir;

    /**
     * Name of dir for static view files
     *
     * @var
     */
    private $_staticDir;

    /**
     * Tenant's status: Enabled or Disabled Frontend
     *
     * @var int
     */
    private $_status;

    /**
     * Maintenance mode data
     *
     * @var array
     */
    private $_maintenanceMode;

    /**
     * Task name prefix
     *
     * @var string
     */
    private $_taskNamePrefix = '';

    /**
     * Constructor
     *
     * @param string $rootDir
     * @param array $tenantData
     * @throws InvalidArgumentException
     */
    public function __construct($rootDir, array $tenantData)
    {
        $this->_rootDir = $rootDir;
        if (!array_key_exists('tenantConfiguration', $tenantData)) {
            throw new InvalidArgumentException('Missing key "tenantConfiguration"');
        }
        $this->_configArray = $tenantData['tenantConfiguration'];
        if (array_key_exists('groupConfiguration', $tenantData)) {
            $this->_groupConfiguration = $tenantData['groupConfiguration'];
        }

        if (array_key_exists('tmt_instance', $tenantData)) {
            $this->_taskNamePrefix = (string)$tenantData['tmt_instance'];
        }

        $this->_config = $this->_mergeConfig(array(
            $this->_getLocalConfig(),
            $this->_getModulesConfig(),
            $this->_getLimitationsConfig(),
        ));

        $dirName = (string)$this->_config->getNode('global/web/dir/media');
        if (!$dirName) {
            throw new InvalidArgumentException('Media directory name is not set');
        }
        $this->_mediaDir = "media/{$dirName}";

        if (empty($tenantData['version_hash'])) {
            throw new InvalidArgumentException('Version hash is not specified');
        }
        $this->_staticDir = 'skin/' . $tenantData['version_hash'];

        if (!isset($tenantData['status'])) {
            throw new InvalidArgumentException('Status is not specified');
        }
        $this->_status = $tenantData['status'];

        if (empty($tenantData['maintenance_mode'])) {
            $tenantData['maintenance_mode'] = array('url' => 'http://golinks.magento.com/noStore');
            //TODO remove previous line and uncomment the following once TMT changes in MAGETWO-9307 are deployed
            //throw new InvalidArgumentException('Maintenance url is not specified');
        }
        $this->_maintenanceMode = $tenantData['maintenance_mode'];
    }

    /**
     * Get a file in context of tenant media directory
     *
     * @param string $fileName
     * @return string
     * @throws InvalidArgumentException
     */
    public function getMediaDirFile($fileName)
    {
        if (!$fileName) {
            throw new InvalidArgumentException('File name cannot be empty');
        }
        return "{$this->_rootDir}/{$this->_mediaDir}/{$fileName}";
    }

    /**
     * Get initialization parameters for application with context of tenant data
     *
     * @return array
     */
    public function getApplicationParams()
    {
        $varDirWorkaround = $this->_config->getNode('global/web/dir/media');
        return array(
            Mage::PARAM_APP_DIRS => array(
                Mage_Core_Model_Dir::MEDIA => "{$this->_rootDir}/{$this->_mediaDir}",
                Mage_Core_Model_Dir::STATIC_VIEW => "{$this->_rootDir}/skin",
                Mage_Core_Model_Dir::VAR_DIR => "{$this->_rootDir}/var/{$varDirWorkaround}",
            ),
            Mage::PARAM_APP_URIS => array(
                Mage_Core_Model_Dir::MEDIA => $this->_mediaDir,
                Mage_Core_Model_Dir::STATIC_VIEW => $this->_staticDir,
            ),
            Mage::PARAM_CUSTOM_LOCAL_CONFIG => $this->_config->getXmlString(),
            'status' => $this->_status,
            'maintenance_mode' => $this->_maintenanceMode,
            Enterprise_Queue_Model_ParamMapper::PARAM_TASK_NAME_PREFIX => $this->_taskNamePrefix,
        );
    }

    /**
     * Merges all Varien_Simplexml_Config objects into one
     *
     * @param array $arrayOfConfigs
     * @return Varien_Simplexml_Config
     */
    private function _mergeConfig(array $arrayOfConfigs)
    {
        $mergedConfig = null;
        foreach ($arrayOfConfigs as $config) {
            if ($config instanceof Varien_Simplexml_Config) {
                if (is_null($mergedConfig)) {
                    $mergedConfig = $config;
                } else {
                    $mergedConfig->extend($config);
                }
            }
        }
        return $mergedConfig;
    }

    /**
     * Get Config object, containing data from 'local' configuration element
     *
     * @return Varien_Simplexml_Config
     * @throws LogicException
     */
    private function _getLocalConfig()
    {
        $config = new Varien_Simplexml_Config();
        if (!array_key_exists('local', $this->_configArray)) {
            throw new LogicException('Local Configuration does not exist');
        }
        $config->loadString($this->_configArray['local']);
        return $config;
    }

    /**
     * Get configuration with list of enabled/disabled modules
     *
     * @return Varien_Simplexml_Config
     */
    private function _getModulesConfig()
    {
        $allModulesConfig = new Varien_Simplexml_Config();

        if (!empty($this->_configArray['modules'])) {
            /**
             * Contains all modules that might be turned on or off
             */
            $availableModules = $this->_getAvailableModules();

            $allModulesConfig->loadString($this->_configArray['modules']);
            if (isset($allModulesConfig->getNode()->modules)) {
                //Remove selected modules that not available to change
                foreach (array_keys((array)$allModulesConfig->getNode('modules')) as $key) {
                    if (!isset($availableModules[$key])) {
                        unset($allModulesConfig->getNode('modules')->$key);
                    }
                }
            }
        }

        return $allModulesConfig;
    }

    /**
     * Get list of modules that can be enabled/disabled via config nodes
     *
     * Tenant's modules configuration has priority over group's modules configuration
     *
     * @return array
     */
    private function _getAvailableModules()
    {
        $modulesArray = array();

        if (array_key_exists('modules', $this->_groupConfiguration)) {
            $modulesArray = array_merge($modulesArray,
                self::_loadModulesFromString($this->_groupConfiguration['modules']));
        }

        if (array_key_exists('tenantModules', $this->_configArray)) {
            $modulesArray = array_merge($modulesArray,
                self::_loadModulesFromString($this->_configArray['tenantModules']));
        }

        /**
         * Contains all modules that might be enabled/disabled
         */
        $availableModules = array();
        if (!empty($modulesArray)) {
            foreach ($modulesArray as $key => $value) {
                if (in_array($value['active'], array(1, 'true'))) {
                    $availableModules[$key] = $key;
                }
            }
        }
        return $availableModules;
    }

    /**
     * Load modules data as array from specific xml string
     *
     * @param  string $xmlString
     * @return array
     */
    private static function _loadModulesFromString($xmlString)
    {
        $nodeModulesConfig = new Varien_Simplexml_Config();
        $nodeModules = array();

        $nodeModulesConfig->loadString($xmlString);
        if ($nodeModulesConfig->getNode('modules')) {
            $nodeModules = $nodeModulesConfig->getNode('modules')->asArray();
            if (!is_array($nodeModules)) {
                $nodeModules = array();
            }
        }
        return $nodeModules;
    }

    /**
     * Get configuration of functional limitations
     *
     * If no limitation configuration exists, empty configuration object is returned
     *
     * @return Varien_Simplexml_Config
     */
    protected function _getLimitationsConfig()
    {
        $config = new Varien_Simplexml_Config();
        if (array_key_exists('limitations', $this->_groupConfiguration)) {
            $config->loadString($this->_groupConfiguration['limitations']);
        }
        return $config;
    }
}
