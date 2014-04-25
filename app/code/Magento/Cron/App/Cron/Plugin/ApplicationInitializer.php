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

use \Magento\Framework\App\State;

class ApplicationInitializer
{
    /**
     * @var State
     */
    protected $_appState;

    /**
     * @var \Magento\Framework\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @param State $appState
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     */
    public function __construct(State $appState, \Magento\Framework\Session\SidResolverInterface $sidResolver)
    {
        $this->_appState = $appState;
        $this->_sidResolver = $sidResolver;
    }

    /**
     * Perform required checks before cron run
     *
     * @param \Magento\Framework\App\Cron $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws \Magento\Framework\Exception
     */
    public function beforeLaunch(\Magento\Framework\App\Cron $subject)
    {
        $this->_sidResolver->setUseSessionInUrl(false);
        if (false == $this->_appState->isInstalled()) {
            throw new \Magento\Framework\Exception('Application is not installed yet, please complete the installation first.');
        }
    }
}
