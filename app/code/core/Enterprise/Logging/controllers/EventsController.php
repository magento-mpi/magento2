<?php
class Enterprise_Logging_EventsController extends Mage_Adminhtml_Controller_Action {
    
    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('logging/events_grid')->toHtml()
        );
    }

}
?>