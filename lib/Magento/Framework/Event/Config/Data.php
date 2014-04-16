<?php
/**
 * Event configuration data container
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Event\Config;

class Data extends \Magento\Framework\Config\Data\Scoped
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\Framework\Event\Config\Reader $reader
     * @param \Magento\Framework\Config\ScopeInterface $configScope
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param \Magento\Framework\App\State $appState
     * @param string $cacheId
     */
    public function __construct(
        \Magento\Framework\Event\Config\Reader $reader,
        \Magento\Framework\Config\ScopeInterface $configScope,
        \Magento\Framework\Config\CacheInterface $cache,
        \Magento\Framework\App\State $appState,
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
        if (!$this->_appState->isInstalled() && !in_array(
            $this->_configScope->getCurrentScope(),
            array('global', 'install')
        )
        ) {
            return $default;
        }
        return parent::get($path, $default);
    }
}
