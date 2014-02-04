<?php
/**
 * Event configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Event\Config;

class Data extends \Magento\Config\Data\Scoped
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Event\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param string $cacheId
     * @param \Magento\App\State $appState
     */
    public function __construct(
        \Magento\Event\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        \Magento\App\State $appState,
        $cacheId = 'event_config_cache'
    ) {
        $this->_appState = $appState;
        parent::__construct($reader, $configScope, $cache, $cacheId);
    }

    /**
     * Get config value by key
     *
     * @param null|string $path
     * @param null|mixed $default
     * @return null|mixed
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
