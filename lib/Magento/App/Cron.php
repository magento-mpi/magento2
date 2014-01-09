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

use \Magento\App\ObjectManager\ConfigLoader,
    \Magento\Event\ManagerInterface;

class Cron implements \Magento\LauncherInterface
{
    /**
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var State
     */
    protected $_state;

    /**
     * @param ManagerInterface $eventManager
     * @param State $state
     */
    public function __construct(
        ManagerInterface $eventManager,
        State $state
    ) {
        $this->_eventManager = $eventManager;
        $this->_state = $state;
    }

    /**
     * Execute application
     *
     * @return int
     */
    public function launch()
    {
        $this->_state->setAreaCode('crontab');
        $this->_eventManager->dispatch('default');
        return 0;
    }
}
