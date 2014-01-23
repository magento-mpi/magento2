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
        Response $response
    ) {
        $this->_eventManager = $eventManager;
        $this->_state = $state;
        $this->_response = $response;
    }

    /**
     * Run application
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_state->setAreaCode('crontab');
        $this->_eventManager->dispatch('default');
        $this->_response->setCode(0);
        return $this->_response;
    }
}
