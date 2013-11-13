<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action\Plugin;


class Events
{
    /**
     * @var \Magento\Event\Manager
     */
    protected $_eventManager;

    /**
     * @param \Magento\Event\Manager $eventManager
     */
    public function __construct(\Magento\Event\Manager $eventManager)
    {
        $this->_eventManager = $eventManager;
    }

    /**
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return mixed
     */
    public function aroundDispatch(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        $request = $arguments[0];
        $this->_eventManager->dispatch('controller_action_predispatch', array('controller_action' => $this));
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getRouteName(),
            array('controller_action' => $this)
        );
        $this->_eventManager->dispatch(
            'controller_action_predispatch_' . $request->getActionName() . 'Action', array('controller_action' => $this)
        );
        $result = $invocationChain->proceed($arguments);
        \Magento\Profiler::start('postdispatch');
        if (!$this->getFlag('', self::FLAG_NO_POST_DISPATCH)) {
            $this->_eventManager->dispatch(
                'controller_action_postdispatch_' . $this->getFullActionName(),
                array('controller_action' => $this)
            );
            $this->_eventManager->dispatch(
                'controller_action_postdispatch_' . $request->getRouteName(),
                array('controller_action' => $this)
            );
            $this->_eventManager->dispatch('controller_action_postdispatch',
                array('controller_action' => $this));
        }
        \Magento\Profiler::stop('postdispatch');
        return $result;
    }
}