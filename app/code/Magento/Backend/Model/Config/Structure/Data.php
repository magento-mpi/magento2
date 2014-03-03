<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Backend\Model\Config\Structure;

class Data extends \Magento\Config\Data\Scoped
{
    /**
     * @param Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        $cacheId
    ) {
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Merge additional config
     *
     * @param array $config
     * @return void
     */
    public function merge(array $config)
    {
        if (isset($config['config']['system'])) {
            $config = $config['config']['system'];
        }
        parent::merge($config);
    }
}
