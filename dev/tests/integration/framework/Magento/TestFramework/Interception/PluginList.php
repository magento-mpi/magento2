<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Interception;

class PluginList extends \Magento\Framework\Interception\PluginList\PluginList
{
    /**
     * @var array
     */
    protected $_originScopeScheme = array();

    /**
     * @param \Magento\Framework\Config\ReaderInterface $reader
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param \Magento\Framework\ObjectManager\Relations $relations
     * @param \Magento\Framework\ObjectManager\Config $omConfig
     * @param \Magento\Framework\Interception\Definition $definitions
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\ObjectManager\Definition $classDefinitions
     * @param array $scopePriorityScheme
     * @param string $cacheId
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Config\ReaderInterface $reader,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Config\CacheInterface $cache,
        \Magento\Framework\ObjectManager\Relations $relations,
        \Magento\Framework\ObjectManager\Config $omConfig,
        \Magento\Framework\Interception\Definition $definitions,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\Definition $classDefinitions,
        array $scopePriorityScheme,
        $cacheId = 'plugins'
    ) {
        parent::__construct(
            $reader,
            $configScope,
            $cache,
            $relations,
            $omConfig,
            $definitions,
            $objectManager,
            $classDefinitions,
            $scopePriorityScheme,
            $cacheId
        );
        $this->_originScopeScheme = $this->_scopePriorityScheme;
    }

    /**
     * Reset internal cache
     */
    public function reset()
    {
        $this->_scopePriorityScheme = $this->_originScopeScheme;
        $this->_data = array();
        $this->_loadedScopes = array();
    }
}
