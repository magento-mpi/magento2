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

use \Magento\Core\Model\AppInterface;

class ApplicationInitializer
{
    /**
     * @var \Magento\Core\Model\AppInterface
     */
    protected $_application;

    /**
     * @param AppInterface $application
     */
    public function __construct(
        AppInterface $application
    ) {
        $this->_application = $application;
    }

    /**
     * Perform required checks before cron run
     *
     * @param array $methodArguments
     * @return array
     */
    public function beforeExecute(array $methodArguments)
    {
        $this->_application->setUseSessionInUrl(false);
        $this->_application->requireInstalledInstance();
        return $methodArguments;
    }
}

