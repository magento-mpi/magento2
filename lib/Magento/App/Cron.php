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

use Magento\App\Console\Response;
use \Magento\App\ObjectManager\ConfigLoader,
    \Magento\Event\ManagerInterface;
use Magento\ObjectManager\ObjectManager;

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
     * @var Console\Response
     */
    protected $_response;

    /**
     * @param ManagerInterface $eventManager
     * @param State $state
     * @param Response $response
     */
    public function __construct(
        ManagerInterface $eventManager,
        State $state,
        Response $response,
        ObjectManager $objectManager
    ) {
        $this->_eventManager = $eventManager;
        $this->_state = $state;
        $this->_response = $response;
        $this->_objectManager = $objectManager;
    }

    /**
     * Run application
     *
     * @return ResponseInterface
     */
    public function launch()
    {
        $this->_objectManager->
        $this->_state->setAreaCode('crontab');
        $this->_eventManager->dispatch('default');
        $this->_response->setCode(0);
        return $this->_response;
    }
}
