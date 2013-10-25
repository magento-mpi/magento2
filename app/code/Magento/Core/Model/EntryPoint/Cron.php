<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class Cron extends \Magento\Core\Model\AbstractEntryPoint
{
    /**
     * Process request to application
     */
    protected function _processRequest()
    {
        /** @var $app \Magento\Core\Model\App */
        $app = $this->_objectManager->get('Magento\Core\Model\App');
        $app->setUseSessionInUrl(false);
        $app->requireInstalledInstance();

        /** @var $eventManager \Magento\Event\ManagerInterface */
        $eventManager = $this->_objectManager->get('Magento\Event\ManagerInterface');
        /** @var \Magento\Config\Scope $configScope */
        $configScope = $this->_objectManager->get('Magento\Config\ScopeInterface');
        $configScope->setCurrentScope('crontab');
        $eventManager->dispatch('default');
    }
}
