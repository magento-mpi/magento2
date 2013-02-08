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
 * Tenant model
 *
 * @category   Saas
 * @package    Saas
 */
class Saas_Saas_Model_Tenant
{
    /**
     * Config model
     *
     * @var Varien_Simplexml_Config
     */
    protected $_config;

    /**
     * Configuration array, taken from external configuration storage (legacy format)
     *
     * @var array
     */
    protected $_configArray = array();

    /**
     * Tenant's media dir. Relative path inside media folder
     *
     * @var string
     */
    protected $_mediaDir;

    /**
     * Constructor
     *
     * @param array $configArray
     */
    public function __construct(array $configArray)
    {
        $this->_configArray = $configArray;
        $this->_config = $this->_mergeConfig(array($this->_getLocalConfig(), $this->_getModulesConfig()));
    }

    /**
     * Get merged configuration as one xml string
     *
     * @return string
     */
    public function getConfigString()
    {
        return $this->_config->getXmlString();
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
     * @throws Exception
     */
    private function _getLocalConfig()
    {
        $config = new Varien_Simplexml_Config();
        if (!array_key_exists('local', $this->_configArray)) {
            throw new Exception('Local Configuration does not exist');
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

        if ($this->_configArray['modules']) {
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

    /**
     * Get relative tenant's media dir
     *
     * @return string
     */
    public function getMediaDir()
    {
        if (is_null($this->_mediaDir)) {
            $this->_mediaDir = (string)$this->_config->getNode('global/web/dir/media');
        }
        return $this->_mediaDir;
    }

    /**
     * Get relative tenant's var dir
     *
     * @return string
     */
    public function getVarDir()
    {
        return $this->getMediaDir();
    }

}
