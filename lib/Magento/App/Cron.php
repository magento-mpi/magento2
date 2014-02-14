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
     * @var Console\Request
     */
    protected $_request;

    /**
     * @var Console\Response
     */
    protected $_response;

    /**
     * @param ManagerInterface $eventManager
     * @param State $state
     * @param Console\Request $request
     * @param Console\Response $response
     * @param array $parameters
     */
    public function __construct(
        ManagerInterface $eventManager,
        State $state,
        Console\Request $request,
        Console\Response $response,
        array $parameters = array()
    ) {
        $this->_eventManager = $eventManager;
        $this->_state = $state;
        $this->_request = $request;
        $this->_request->setParam($parameters);
        $this->_response = $response;
    }

    /**
     * Run application
     *
     * @return ResponseInterface
     */
    public function launch()
    {
        $this->_state->setAreaCode('crontab');
        $this->_eventManager->dispatch('default');
        $this->_response->setCode(0);
        return $this->_response;
    }
}
