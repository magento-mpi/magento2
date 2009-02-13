<?php
class Enterprise_Logging_Block_Configuration extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/logging/configuration.phtml');
    }

    public function getEvents() {
        $events = array();
        foreach(array('products', 'categories', 'customers') as $name) {
            $event = new Varien_Object();
            $event->setName($name);
            $event->setChecked(0);
            $events[] = $event;
        }
        return $events;
    }
}
