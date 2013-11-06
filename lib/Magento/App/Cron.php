<?php
/**
 * Cron application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

use \Magento\Config\ScopeInterface,
    \Magento\App\ObjectManager\ConfigLoader,
    \Magento\Event\ManagerInterface;

class Cron implements \Magento\AppInterface
{
    /**
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @param ScopeInterface $configScope
     * @param ManagerInterface $eventManager
     * @param State $state
     */
    public function __construct(
        ScopeInterface $configScope,
        ManagerInterface $eventManager,
        State $state
    ) {
        $this->_configScope = $configScope;
        $this->_eventManager = $eventManager;
        $this->_state = $state;
    }

    /**
     * Execute application
     *
     * @return int
     */
    public function execute()
    {
        $this->_state->setAreaCode('crontab');
        $this->_configScope->setCurrentScope('crontab');
        $this->_eventManager->dispatch('default');
        return 0;
    }
}
