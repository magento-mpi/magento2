<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

class Noroute extends Action
{
    /**
     * @param string $action
     */
    public function dispatch($action)
    {
        $status = $this->getRequest()->getParam('__status__');
        if (!$status instanceof \Magento\Object) {
            $status = new \Magento\Object();
        }

        $this->_eventManager->dispatch('controller_action_noroute', array('action' => $this, 'status' => $status));

        if ($status->getLoaded() !== true || $status->getForwarded() === true) {
            $this->loadLayout(array('default', 'noroute'));
            $this->renderLayout();
        } else {
            $status->setForwarded(true);
            $request = $this->getRequest();
            $request->initForward();
            $request->setParams(array('__status__' => $status));
            $request->setControllerName($status->getForwardController());
            $request->setModuleName($status->getForwardModule());
            $request->setActionName($status->getForwardAction())
                ->setDispatched(false);
        }
    }
}