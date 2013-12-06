<?php
/**
 * Application installation plugin. Should be used by applications that require module install/upgrade.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\FrontController\Plugin;

class Install
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @var \Magento\Module\UpdaterInterface
     */
    protected $_updater;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\Module\UpdaterInterface $dbUpdater
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\Module\UpdaterInterface $dbUpdater
    ) {
        $this->_appState = $appState;
        $this->_cache = $cache;
        $this->_dbUpdater = $dbUpdater;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch($arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        if ($this->_appState->isInstalled() && !$this->_cache->load('data_upgrade')) {
            $this->_dbUpdater->updateScheme();
            $this->_dbUpdater->updateData();
            $this->_cache->save('true', 'data_upgrade');
        }
        return $invocationChain->proceed($arguments);
    }
}