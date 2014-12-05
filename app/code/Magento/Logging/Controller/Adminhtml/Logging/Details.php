<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Controller\Adminhtml\Logging;

class Details extends \Magento\Logging\Controller\Adminhtml\Logging
{
    /**
     * View logging details
     *
     * @return void
     */
    public function execute()
    {
        $eventId = $this->getRequest()->getParam('event_id');
        /** @var \Magento\Logging\Model\Event $model */
        $model = $this->_eventFactory->create()->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_coreRegistry->register('current_event', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__("Log Entry #%1", $eventId));
        $this->_view->renderLayout();
    }
}
