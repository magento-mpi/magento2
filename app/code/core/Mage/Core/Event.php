<?php

#Mage::loadInterface('Mage_Core_Event_Interface');


/**
 * Event class
 * 
 * Mage::addEvent(new Mage_Event('core.modules.onload'));
 * ...
 * Mage::getEvent('core.modules.onload')->addObserver(new Mage_Event_Observer(array($this, 'Run')));
 * ...
 * Mage::getEvent('core.modules.onload')->dispatch(array('arg1'=>'value1'));
 * 
 * @author Moshe Gurvich <moshe@varien.com>
 */
class Mage_Core_Event
{

    /**
     * Collection of events objects
     *
     * @var array
     */
    static private $_events = array();

    /**
     * Collection of observers to watch for multiple events by regex
     *
     * @var unknown_type
     */
    static private $_multiObservers = array();

    /**
     * Retrieve event object
     *
     * @param string $name
     * @return Mage_Core_Event
     */
    public static function getEvent($name)
    {
        $name = strtolower($name);

        if (isset(self::$_events[$name])) {
            return self::$_events[$name];
        }
        return false;
    }

    /**
     * Add event object
     *
     * @param unknown_type $name
     */
    public static function addEvent($name)
    {
        $name = strtolower($name);

        if (!self::getEvent($name)) {
            self::$_events[$name] = new Mage_Core_Event_Dispatcher(array('name'=>$name));
        }
    }

    /**
     * Add observer to even object
     *
     * @param string $eventName
     * @param callback $callback
     * @param array $arguments
     * @param string $observerName
     */
    public static function addObserver($eventName, $callback, array $arguments=array(), $observerName='')
    {
        if (!self::getEvent($eventName)) {
            self::addEvent($eventName);
        }
        $observer = new Mage_Core_Event_Observer($callback, $arguments);
        self::getEvent($eventName)->addObserver($observer, $observerName);
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public static function addMultiObserver($eventRegex, $callback, $observerName='')
    {
        $this->_multiObservers[$eventRegex] = $callback;
    }

    /**
     * Dispatch event
     *
     * Calls all observer callbacks registered for this event
     * and multiobservers matching event name pattern
     *
     * @param string $name
     * @param array $args
     */
    public static function dispatchEvent($name, array $args=array())
    {
        $event = self::getEvent($name);
        if ($event && $event->getObservers()) {
            $event->dispatch($args);
        }

        $args['_eventName'] = $name;
        foreach (self::$_multiObservers as $regex=>$callback) {
            if (preg_match('#'.$regex.'#i', $name)) {
                call_user_func_array($callback, $args);
            }
        }
    }
    
    public static function loadObserversConfig($config)
    {
        foreach ($config as $eventName=>$observers) {
            foreach ($observers as $observerName=>$observerInfo) {
                $callback = explode('::', $observerInfo->callback);
                $args = array();
    
                if (isset($observerInfo->arg)) {
                    $args = $observerInfo->arg->asArray();
                }
    
                self::addObserver($eventName, $callback, $args, $observerName);
            }
        }
    }
}