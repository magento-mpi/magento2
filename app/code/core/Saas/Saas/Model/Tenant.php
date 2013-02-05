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
     * Constructor
     *
     * @param array $configArray
     */
    public function __construct(array $configArray)
    {
        if (is_null($this->_config)) {
            $this->_config = new Saas_Saas_Model_Tenant_Config();
        }
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
     * Strange legacy logic.
     * Only if modules are enabled in tenantModules or groupModules node, they can be affected by modules node
     *
     * @return string
     */
    public function getTenantModules()
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

        return $this->_config->getModulesConfig($this->_configArray['modules'], $availableModules);
    }

    /**
     * Get relative tenant's media dir
     *
     * @return string
     */
    public function getMediaDir()
    {
        return $this->_configArray['media_dir'];
    }

    /**
     * Get relative tenant's var dir
     *
     * @return string
     */
    public function getVarDir()
    {
        return $this->_configArray['media_dir'];
    }

}
