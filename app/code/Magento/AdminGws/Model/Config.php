<?php
/**
 * AdminGws configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdminGws_Model_Config extends Magento_Config_Data implements Magento_AdminGws_Model_ConfigInterface
{
    /**
     * @param Magento_AdminGws_Model_Config_Reader $reader
     * @param Magento_Config_ScopeInterface $configScope
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_AdminGws_Model_Config_Reader $reader,
        Magento_Config_ScopeInterface $configScope,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'admingws_config'
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get callback list by group name
     *
     * @param string $groupName
     * @return array
     */
    public function getCallbacks($groupName)
    {
        return $this->get('callbacks/' . $groupName, array());
    }

    /**
     * Get deny acl level rules
     *
     * @param string $level
     * @return array
     */
    public function getDeniedAclResources($level)
    {
        return $this->get('acl/' . $level, array());
    }
}
