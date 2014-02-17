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

use Magento\Cache\FrontendInterface;
use Magento\Module\UpdaterInterface;
use Magento\App\State;
use Magento\Code\Plugin\InvocationChain;

class Install
{
    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var FrontendInterface
     */
    protected $_cache;

    /**
     * @var UpdaterInterface
     */
    protected $_updater;

    /**
     * @param State $appState
     * @param FrontendInterface $cache
     * @param UpdaterInterface $dbUpdater
     */
    public function __construct(
        State $appState,
        FrontendInterface $cache,
        UpdaterInterface $dbUpdater
    ) {
        $this->_appState = $appState;
        $this->_cache = $cache;
        $this->_dbUpdater = $dbUpdater;
    }

    /**
     * @param array $arguments
     * @param InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(\Magento\App\FrontController $subject, \Closure $proceed, \Magento\App\RequestInterface $request)
    {
        if ($this->_appState->isInstalled() && !$this->_cache->load('data_upgrade')) {
            $this->_dbUpdater->updateScheme();
            $this->_dbUpdater->updateData();
            $this->_cache->save('true', 'data_upgrade');
        }
        return $invocationChain->proceed($arguments);
    }
}
