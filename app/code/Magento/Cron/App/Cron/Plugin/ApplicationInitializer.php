<?php
/**
 * Cron application plugin
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\App\Cron\Plugin;

use \Magento\App\State;

class ApplicationInitializer
{
    /**
     * @var State
     */
    protected $_appState;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @param State $appState
     * @param \Magento\Session\SidResolverInterface $sidResolver
     */
    public function __construct(
        State $appState,
        \Magento\Session\SidResolverInterface $sidResolver
    ) {
        $this->_appState = $appState;
        $this->_sidResolver = $sidResolver;
    }

    /**
     * Perform required checks before cron run
     *
     * @param \Magento\App\Cron $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Exception
     */
    public function beforeLaunch(\Magento\App\Cron $subject)
    {
        $this->_sidResolver->setUseSessionInUrl(false);
        if (false == $this->_appState->isInstalled()) {
            throw new \Magento\Exception('Application is not installed yet, please complete the installation first.');
        }
    }
}

