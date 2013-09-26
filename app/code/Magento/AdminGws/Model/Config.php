<?php
/**
 * AdminGws configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Model;

class Config extends \Magento\Config\Data\Scoped implements \Magento\AdminGws\Model\ConfigInterface
{
    /**
     * @param \Magento\AdminGws\Model\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        \Magento\AdminGws\Model\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
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
