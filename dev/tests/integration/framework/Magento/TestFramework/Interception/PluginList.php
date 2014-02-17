<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\Interception;

class PluginList extends \Magento\Interception\PluginList\PluginList
{
    /**
     * @var array
     */
    protected $_originScopeScheme = array();

    /**
     * @param \Magento\Config\ReaderInterface $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\ObjectManager\Relations $relations
     * @param \Magento\ObjectManager\Config $omConfig
     * @param \Magento\Interception\Definition $definitions
     * @param \Magento\ObjectManager $objectManager
     * @param array $scopePriorityScheme
     * @param string $cacheId
     * @param \Magento\ObjectManager\Definition\Compiled $classDefinitions
     */
    public function __construct(
        \Magento\Config\ReaderInterface $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        \Magento\ObjectManager\Relations $relations,
        \Magento\ObjectManager\Config $omConfig,
        \Magento\Interception\Definition $definitions,
        \Magento\ObjectManager $objectManager,
        array $scopePriorityScheme,
        $cacheId = 'plugins',
        \Magento\ObjectManager\Definition\Compiled $classDefinitions = null
    ) {
        parent::__construct($reader, $configScope, $cache, $relations, $omConfig,
            $definitions, $objectManager, $scopePriorityScheme, $cacheId, $classDefinitions
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
