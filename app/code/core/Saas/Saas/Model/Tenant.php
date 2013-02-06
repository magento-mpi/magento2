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
     * @var Saas_Saas_Model_Tenant_Config
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
        $this->_config = new Saas_Saas_Model_Tenant_Config();
        $this->_configArray = $configArray;
    }

    /**
     * Get merged configuration as one xml string
     *
     * @return string
     */
    public function getConfigString()
    {
        return $this->_config->merge(array($this->_configArray['local'], $this->getTenantModules()))->getXmlString();
    }

    /**
     * Legacy logic.
     * Only if modules are enabled in tenantModules or groupModules node, they can be affected by modules node
     *
     * @return string
     */
    public function getTenantModules()
    {
        /**
         * Contains all modules that might be turned on or off
         */
        $availableModules = $this->_getAvailableModules();

        $allModulesConfig = new Varien_Simplexml_Config();

        if ($this->_configArray['modules']) {
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

        return $allModulesConfig->getXmlString();
    }

    /**
     * Get all modules that can be turned on or off via config nodes
     *
     * @return array
     */
    protected function _getAvailableModules()
    {
        $modulesArray = array();
        foreach (array('groupModules', 'tenantModules') as $node) {
            if (array_key_exists($node, $this->_configArray)) {
                $modulesArray =
                    array_merge($modulesArray, $this->_config->loadModulesFromString($this->_configArray[$node]));
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
     * Get relative tenant's media dir
     *
     * @return string
     */
    public function getMediaDir()
    {
        if (is_null($this->_mediaDir)) {
            $node = new SimpleXMLElement($this->_configArray['local']);
            $this->_mediaDir = (string)$node->global->web->dir->media;
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
