<?php
class Enterprise_Logging_ConfigurationController extends Mage_Adminhtml_Controller_Action 
{

    public function indexAction() {
        $this->loadLayout();
        $model = Mage::getResourceModel('logging/event_configuration');
        $events = $model->getAllEvents();
        $block = $this->getLayout()->createBlock('logging/configuration');
        $block->setEvents($events);
        $this->_addContent($block);
        $this->renderLayout();
    }


    public function saveAction() {
        $events = $this->getRequest()->getParam('event');
        $model = Mage::getResourceModel('logging/event_configuration');
        $model->updateEvents($events);
        $this->_redirect('*/*/');
    }
}