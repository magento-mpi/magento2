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
     * @var string
     */
    private $_rootDir;

    /**
     * @var Varien_Simplexml_Config
     */
    private $_config;

    /**
     * Configuration array, taken from external configuration storage (legacy format)
     *
     * @var array
     */
    private $_configArray = array();

    /**
     * Name of media directory relatively to root
     *
     * @var string
     */
    private $_mediaDir;

    /**
     * Constructor
     *
     * @param string $rootDir
     * @param array $tenantData
     * @throws LogicException
     */
    public function __construct($rootDir, array $tenantData)
    {
        $this->_rootDir = $rootDir;
        if (!array_key_exists('tenantConfiguration', $tenantData)) {
            throw new LogicException('Missing key "tenantConfiguration"');
        }
        $this->_configArray = $tenantData['tenantConfiguration'];
        $this->_config = $this->_mergeConfig(array($this->_getLocalConfig(), $this->_getModulesConfig()));
        $dirName = (string)$this->_config->getNode('global/web/dir/media');
        if (!$dirName) {
            throw new LogicException('Media directory name is not set');
        }
        $this->_mediaDir = "media/{$dirName}";
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
                Mage_Core_Model_Dir::VAR_DIR => "{$this->_rootDir}/var/{$varDirWorkaround}",
            ),
            Mage::PARAM_APP_URIS => array(
                Mage_Core_Model_Dir::MEDIA => $this->_mediaDir,
            ),
            Mage::PARAM_CUSTOM_LOCAL_CONFIG => $this->_config->getXmlString(),
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
     * Get Config object, containing data from 'modules' configuration element
     *
     * Contains Legacy logic.
     * Only if modules are enabled in tenantModules or groupModules node, they can be affected by modules node
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
     * Get all modules that can be turned on or off via config nodes
     *
     * @return array
     */
    private function _getAvailableModules()
    {
        $modulesArray = array();
        foreach (array('groupModules', 'tenantModules') as $node) {
            if (array_key_exists($node, $this->_configArray)) {
                $modulesArray = array_merge($modulesArray, self::_loadModulesFromString($this->_configArray[$node]));
            }
        }

        /**
         * Contains all modules that might be turned on or off
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
}
