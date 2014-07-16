<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller\Noroute;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Noroute application handler
     *
     * @return void
     */
    public function execute()
    {
        $status = $this->getRequest()->getParam('__status__');
        if (!$status instanceof \Magento\Framework\Object) {
            $status = new \Magento\Framework\Object();
        }

        $this->_eventManager->dispatch('controller_action_noroute', array('action' => $this, 'status' => $status));

        if ($status->getLoaded() !== true || $status->getForwarded() === true) {
            $this->_view->loadLayout(array('default', 'noroute'));
            $this->_view->renderLayout();
        } else {
            $status->setForwarded(true);
            $request = $this->getRequest();
            $request->initForward();
            $request->setParams(array('__status__' => $status));
            $request->setControllerName($status->getForwardController());
            $request->setModuleName($status->getForwardModule());
            $request->setActionName($status->getForwardAction())->setDispatched(false);
        }
    }
}
