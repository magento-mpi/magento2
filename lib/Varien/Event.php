<?php

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
class Varien_Event
{

    /**
     * Collection of events objects
     *
     * @var array
     */
    private $_events = array();

    /**
     * Collection of observers to watch for multiple events by regex
     *
     * @var unknown_type
     */
    private $_multiObservers = array();
    
    private $_stopDispatchFlag = false;

    /**
     * Retrieve event object
     *
     * @param string $name
     * @return Mage_Core_Event
     */
    public function getEvent($name='')
    {
        if (''===$name) {
            return $this->_events;
        } else {
            $name = strtolower($name);
    
            if (isset($this->_events[$name])) {
                return $this->_events[$name];
            }
        }
        return false;
    }

    /**
     * Add event object
     *
     * @param unknown_type $name
     */
    public function addEvent($name)
    {
        $name = strtolower($name);

        if (!self::getEvent($name)) {
            #$this->_events[$name] = new Mage_Core_Event_Dispatcher(array('name'=>$name));
            $this->_events[$name] = array();
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
    public function addObserver($eventName, $callback, $arguments=array(), $observerName='')
    {
        $eventName = strtolower($eventName);
        
        if (!self::getEvent($eventName)) {
            self::addEvent($eventName);
        }
        
        #$observer = new Mage_Core_Event_Observer($callback, $arguments);
        $observer = array($callback, $arguments);
        
        #self::getEvent($eventName)->addObserver($observer, $observerName);
        if (''===$observerName) {
            $this->_events[$eventName][] = $observer;
        } else {
            $this->_events[$eventName][$observerName] = $observer;
        }
    }

    /**
     * Add observer to watch for multiple events matching regex pattern
     *
     * @param string $eventRegex
     * @param callback $callback
     */
    public function addMultiObserver($eventRegex, $callback, $observerName='')
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
    public function dispatchEvent($eventName, $eventArgs=array())
    {
        #$event = self::getEvent($name);
        #if ($event && $event->getObservers()) {
        #    $event->dispatch($args);
        #}
        $eventName = strtolower($eventName);
        
        if (!isset($this->_events[$eventName])) {
            return false;
        }
        
        $observers = $this->_events[$eventName];
        #echo "<pre>dispatch:".$eventName. print_r($observers,1)."</pre><hr>";
        foreach ($observers as $observer) {
            $arguments = $eventArgs;
            if (!empty($observer[1])) {
                $arguments = $observer[1];
            }
		    call_user_func_array($observer[0], $observer[1]);
		    if ($this->_stopDispatchFlag) {
		        $this->_stopDispatchFlag = false;
		        break;
		    }
		}

        $args['_eventName'] = $eventName;
        foreach ($this->_multiObservers as $regex=>$callback) {
            if (preg_match('#'.$regex.'#i', $eventName)) {
                call_user_func_array($callback, $eventArgs);
    		    if ($this->_stopDispatchFlag) {
    		        $this->_stopDispatchFlag = false;
    		        break;
    		    }
            }
        }
    }
    
    public function stopDispatch()
    {
        $this->_stopDispatchFlag = true;
    }
}