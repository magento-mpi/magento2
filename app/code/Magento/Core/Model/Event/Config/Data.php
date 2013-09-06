<?php
/**
 * Event configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Event_Config_Data extends \Magento\Config\Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_Event_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param Magento_Core_Model_App_State $appState
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Event_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        Magento_Core_Model_App_State $appState,
        $cacheId = "event_config_cache"
    ) {
        $this->_appState = $appState;
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get config value by key
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get($path = null, $default = null)
    {
        if (!$this->_appState->isInstalled()
            && !in_array($this->_configScope->getCurrentScope(), array('global', 'install'))
        ) {
            return $default;
        }
        return parent::get($path, $default);
    }
}
