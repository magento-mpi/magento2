<?php
/**
 * Event configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Event\Config;

class Data extends \Magento\Config\Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @var \Magento\Core\Model\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Core\Model\Event\Config\Reader $reader
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\Core\Model\App\State $appState
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Core\Model\Event\Config\Reader $reader,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\Config\CacheInterface $cache,
        \Magento\Core\Model\App\State $appState,
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
