<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Event
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Event observer collection
 * 
 * @category   Magento
 * @package    Magento_Event
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Event_Observer_Collection
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
     * @return Magento_Event_Observer
     */
    public function getObserverByName($observerName)
    {
        return $this->_observers[$observerName];
    }
    
    /**
     * Adds an observer to the collection
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Event_Observer_Collection
     */
    public function addObserver(Magento_Event_Observer $observer)
    {
        $this->_observers[$observer->getName()] = $observer;
        return $this;
    }
    
    /**
     * Removes an observer from the collection by its name
     *
     * @param string $observerName
     * @return Magento_Event_Observer_Collection
     */
    public function removeObserverByName($observerName)
    {
        unset($this->_observers[$observerName]);
        return $this;
    }
    
    /**
     * Dispatches an event to all observers in the collection
     *
     * @param Magento_Event $event
     * @return Magento_Event_Observer_Collection
     */
    public function dispatch(Magento_Event $event)
    {
        foreach ($this->_observers as $observer) {
            $observer->dispatch($event);
        }
        return $this;
    }
}