<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

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
     * @var \Magento\Core\Model\Db\UpdaterInterface
     */
    protected $_updater;

    /**
     * @param \Magento\App\State $appState
     * @param \Magento\Cache\FrontendInterface $cache
     * @param \Magento\Core\Model\Db\UpdaterInterface $dbUpdater
     */
    public function __construct(
        \Magento\App\State $appState,
        \Magento\Cache\FrontendInterface $cache,
        \Magento\Core\Model\Db\UpdaterInterface $dbUpdater
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