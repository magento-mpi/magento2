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
    
    static private $_stopDispatchFlag = false;

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
            #self::$_events[$name] = new Mage_Core_Event_Dispatcher(array('name'=>$name));
            self::$_events[$name] = array();
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
        $eventName = strtolower($eventName);
        
        if (!self::getEvent($eventName)) {
            self::addEvent($eventName);
        }
        
        #$observer = new Mage_Core_Event_Observer($callback, $arguments);
        $observer = array($callback, $arguments);
        
        #self::getEvent($eventName)->addObserver($observer, $observerName);
        if (''===$observerName) {
            self::$_events[$eventName][] = $observer;
        } else {
            self::$_events[$eventName][$observerName] = $observer;
        }
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public static function addMultiObserver($eventRegex, $callback, $observerName='')
    {
        $eventRegex = strtolower($eventRegex);
        
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
    public static function dispatchEvent($eventName, array $eventArgs=array())
    {
        #$event = self::getEvent($name);
        #if ($event && $event->getObservers()) {
        #    $event->dispatch($args);
        #}
        $eventName = strtolower($eventName);
        
        if (!isset(self::$_events[$eventName])) {
            return false;
        }
        
        $observers = self::$_events[$eventName];
        foreach ($observers as $observer) {
            $arguments = $eventArgs;
            if (!empty($observer[1])) {
                $arguments = $observer[1];
            }
		    call_user_func_array($observer[0], $observer[1]);
		    if (self::$_stopDispatchFlag) {
		        self::$_stopDispatchFlag = false;
		        break;
		    }
		}

        $args['_eventName'] = $eventName;
        foreach (self::$_multiObservers as $regex=>$callback) {
            if (preg_match('#'.$regex.'#i', $eventName)) {
                call_user_func_array($callback, $eventArgs);
    		    if (self::$_stopDispatchFlag) {
    		        self::$_stopDispatchFlag = false;
    		        break;
    		    }
            }
        }
    }
    
    public static function stopDispatch()
    {
        $this->_stopDispatchFlag = true;
    }
    
    public static function loadEventsConfig($area)
    {
        $events = Mage::getConfig('/')->global->$area->events;
        foreach ($events->children() as $event) {
            $eventName = $event->getName();
            foreach ($event->observers->children() as $observer) {
                $callback = array((string)$observer->class, (string)$observer->method);
                $args = array_values((array)$observer->args);
                self::addObserver($eventName, $callback, $args, $observer->getName());
            }
        }
    }
}