<?php
class Enterprise_Logging_ConfigurationController extends Mage_Adminhtml_Controller_Action 
{
    public function indexAction() {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('logging/configuration'));
        $this->renderLayout();
    }
}