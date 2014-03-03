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

use \Magento\AppInterface;

class ApplicationInitializer
{
    /**
     * @var \Magento\AppInterface
     */
    protected $_application;

    /**
     * @var \Magento\Session\SidResolverInterface
     */
    protected $_sidResolver;

    /**
     * @param AppInterface $application
     * @param \Magento\Session\SidResolverInterface $sidResolver
     */
    public function __construct(
        AppInterface $application,
        \Magento\Session\SidResolverInterface $sidResolver
    ) {
        $this->_application = $application;
        $this->_sidResolver = $sidResolver;
    }

    /**
     * Perform required checks before cron run
     *
     * @param \Magento\App\Cron $subject
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeLaunch(\Magento\App\Cron $subject)
    {
        $this->_sidResolver->setUseSessionInUrl(false);
        $this->_application->requireInstalledInstance();
    }
}

