<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event observer collection
 * 
 * @category   Varien
 * @package    Varien_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Varien_Event_Observer_Collection
{
    /**
     * Array of observers
     *
     * @var array
     */
    protected $_observers;
    
    /**
     * Initializes observers
     *
     */
    public function __construct()
    {
        $this->_observers = array();
    }
    
    /**
     * Returns all observers in the collection
     *
     * @return array
     */
    public function getAllObservers()
    {
        return $this->_observers;
    }
    
    /**
     * Returns observer by its name
     *
     * @param string $observerName
     * @return Varien_Event_Observer
     */
    public function getObserverByName($observerName)
    {
        return $this->_observers[$observerName];
    }
    
    /**
     * Adds an observer to the collection
     *
     * @param Varien_Event_Observer $observer
     * @return Varien_Event_Observer_Collection
     */
    public function addObserver(Varien_Event_Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
        return $this;
    }
    
    /**
     * Removes an observer from the collection by its name
     *
     * @param string $observerName
     * @return Varien_Event_Observer_Collection
     */
    public function removeObserverByName($observerName)
    {
        unset($this->_observers[$observerName]);
        return $this;
    }
    
    /**
     * Dispatches an event to all observers in the collection
     *
     * @param Varien_Event $event
     * @return Varien_Event_Observer_Collection
     */
    public function dispatch(Varien_Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}