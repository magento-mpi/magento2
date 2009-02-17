<?php
class Enterprise_Logging_Block_Configuration extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/logging/configuration.phtml');
    }

    public function setEvents($events) {
        $this->_events = $events;
    }
    
    public function getEvents() {

        foreach($this->_events as $data) {
            $event = new Varien_Object();
            $event->setName(@$data['label']);
            $event->setChecked($data['is_active']);
            $event->setCode($data['event_code']);
            $events[] = $event;
        }
        return $events;
    }
}
