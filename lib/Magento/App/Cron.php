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

class Cron implements \Magento\AppInterface
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
     * @var Console\Request
     */
    protected $_request;

    /**
     * @param ManagerInterface $eventManager
     * @param State $state
     * @param Console\Request $request
     * @param array $parameters
     */
    public function __construct(
        ManagerInterface $eventManager,
        State $state,
        Console\Request $request,
        array $parameters = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_state = $state;
        $this->_request = $request;
        $this->_request->setParam($parameters);
    }

    /**
     * Execute application
     *
     * @return int
     */
    public function execute()
    {
        $this->_state->setAreaCode('crontab');
        $this->_eventManager->dispatch('default');
        return 0;
    }
}
